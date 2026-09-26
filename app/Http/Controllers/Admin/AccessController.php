<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditService;
use App\Services\BackofficeRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class AccessController extends Controller
{
    public function users(Request $request, BackofficeRegistry $registry): Response
    {
        Gate::authorize('users.view');
        $request->validate(['q' => ['nullable', 'string', 'max:100']]);

        return Inertia::render('Admin/Access', [
            'mode' => 'users', 'navigation' => $registry->navigation($request->user()),
            'records' => User::select('id', 'name', 'email', 'status')->with('roles:id,name,label')->when($request->filled('q'), fn ($q) => $q->where('email', 'like', '%'.$request->string('q').'%'))->orderByDesc('id')->paginate(15)->withQueryString(),
            'roles' => Role::select('id', 'name', 'label')->get(), 'permissions' => [],
        ]);
    }

    public function roles(Request $request, BackofficeRegistry $registry): Response
    {
        Gate::authorize('roles.view');

        return Inertia::render('Admin/Access', ['mode' => 'roles', 'navigation' => $registry->navigation($request->user()), 'records' => Role::with('permissions')->orderBy('id')->paginate(15), 'roles' => [], 'permissions' => Permission::orderBy('name')->get()]);
    }

    public function saveUser(Request $request, AuditService $audit): RedirectResponse
    {
        Gate::authorize('users.manage');
        Gate::authorize('roles.manage');
        $data = $request->validate(['id' => ['nullable', 'integer', 'exists:users,id'], 'name' => ['required', 'string', 'max:100'], 'email' => ['required', 'email', Rule::unique('users')->ignore($request->integer('id'))], 'password' => [$request->filled('id') ? 'nullable' : 'required', 'string', 'max:128', Password::min(10)->letters()->numbers()], 'status' => ['required', Rule::in(['active', 'suspended'])], 'roles' => ['required', 'array', 'min:1'], 'roles.*' => ['required', 'integer', 'exists:roles,id']]);
        abort_if($request->integer('id') === $request->user()->id, 422, 'Perubahan akses akun sendiri tidak diizinkan.');
        DB::transaction(function () use ($data, $audit) {
            $user = isset($data['id']) ? User::lockForUpdate()->findOrFail($data['id']) : new User;
            if ($user->exists && $user->roles()->where('name', 'super_admin')->exists()) {
                abort(422, 'Akses super admin hanya dapat dikelola melalui operator server.');
            }
            $user->fill(collect($data)->only(['name', 'email'])->all());
            if (! empty($data['password'])) {
                $user->password = $data['password'];
            }
            $user->status = $data['status'];
            $user->save();
            $user->roles()->sync($data['roles']);
            $audit->record('users.access_updated', $user, ['status' => $user->status, 'roles' => $data['roles']]);
        });

        return back()->with('success', 'Akun dan akses berhasil disimpan.');
    }

    public function saveRole(Request $request, Role $role, AuditService $audit): RedirectResponse
    {
        Gate::authorize('roles.manage');
        abort_if($role->name === 'super_admin', 422, 'Permission super admin dikelola melalui konfigurasi server.');
        abort_if($request->user()->roles()->whereKey($role->id)->exists(), 422, 'Tidak dapat mengubah role yang sedang digunakan sendiri.');
        $data = $request->validate(['permissions' => ['present', 'array'], 'permissions.*' => ['integer', 'exists:permissions,id']]);
        DB::transaction(function () use ($role, $data, $audit) {
            $role->permissions()->sync($data['permissions']);
            $audit->record('roles.permissions_updated', $role, $data);
        });

        return back()->with('success', 'Permission diperbarui.');
    }
}
