<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BackofficeRequest;
use App\Services\{AuditService, BackofficeRegistry};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\{DB, Gate};
use Inertia\{Inertia, Response};

class ResourceController extends Controller
{
    public function __construct(private BackofficeRegistry $registry, private AuditService $audit) {}

    private function query(array $definition): Builder
    {
        $query = $definition['model']::query();
        if ($definition['model'] === \App\Models\MediaAsset::class) {
            $query->where(fn (Builder $query) => $query->where('visibility', 'public')->orWhere('user_id', auth()->id()));
        }
        if (isset($definition['scope'])) { $query->where(...$definition['scope']); }
        return $query;
    }

    public function index(Request $request, string $module): Response
    {
        $definition = $this->registry->get($module);
        Gate::authorize($definition['permission'].'.view');
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', 'string', 'max:30']]);
        $query = $this->query($definition);
        if (! empty($filters['q'])) { $query->where($definition['search'], 'like', '%'.$filters['q'].'%'); }
        if (! empty($filters['status']) && in_array('status', $definition['columns'])) { $query->where('status', $filters['status']); }
        $records = $query->select(array_unique(['id', ...$definition['columns']]))->orderByDesc('id')->paginate(15)->withQueryString();
        return Inertia::render('Admin/Resources', [
            'module' => $module, 'definition' => collect($definition)->except(['model', 'scope'])->all(),
            'records' => $records, 'filters' => $filters,
            'canManage' => $request->user()->hasPermission($definition['permission'].'.manage'),
            'navigation' => $this->registry->navigation($request->user()),
        ]);
    }

    public function show(Request $request, string $module, string $record): Response
    {
        $definition = $this->registry->get($module);
        Gate::authorize($definition['permission'].'.view');
        $item = $this->query($definition)->findOrFail($record);
        $messages = $module === 'support' ? $item->messages()->with('user:id,name')->orderBy('id')->paginate(30) : null;
        return Inertia::render('Admin/Record', [
            'module' => $module, 'definition' => collect($definition)->except(['model', 'scope'])->all(),
            'record' => $item->only(array_unique(['id', ...$definition['columns'], ...array_keys($definition['fields']), ...($definition['detail'] ?? [])])),
            'messages' => $messages, 'canManage' => $request->user()->hasPermission($definition['permission'].'.manage'),
            'navigation' => $this->registry->navigation($request->user()),
        ]);
    }

    public function store(BackofficeRequest $request, string $module): RedirectResponse
    {
        $definition = $this->registry->get($module);
        $data = $request->validated();
        if ($definition['permission'] === 'content' && ($data['status'] ?? '') === 'published') { Gate::authorize('content.publish'); }
        if (isset($definition['scope'])) { $data[$definition['scope'][0]] = $definition['scope'][1]; }
        $item = DB::transaction(function () use ($definition, $data) {
            $item = $definition['model']::create($data);
            $this->audit->record('record.created', $item, $data);
            return $item;
        });
        return to_route('admin.resources.show', [$module, $item->id])->with('success', 'Data berhasil dibuat.');
    }

    public function update(BackofficeRequest $request, string $module, string $record): RedirectResponse
    {
        $definition = $this->registry->get($module);
        DB::transaction(function () use ($request, $definition, $record) {
            $item = $this->query($definition)->lockForUpdate()->findOrFail($record);
            $data = $request->validated();
            if ($definition['permission'] === 'content' && (($data['status'] ?? '') === 'published' || $item->status === 'published')) { Gate::authorize('content.publish'); }
            if ($item instanceof \App\Models\Promotion && $data['usage_limit'] < $item->used_count) {
                throw \Illuminate\Validation\ValidationException::withMessages(['usage_limit' => 'Kuota tidak boleh lebih kecil dari penggunaan saat ini.']);
            }
            $before = $item->only(array_keys($data));
            $item->update($data);
            $this->audit->record('record.updated', $item, ['before' => $before, 'after' => $data]);
        });
        return back()->with('success', 'Perubahan berhasil disimpan.');
    }
}
