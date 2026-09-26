<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditService
{
    /** @param array<string, mixed> $changes */
    public function record(string $action, Model $entity, array $changes = [], ?int $actorId = null): void
    {
        AuditLog::create([
            'user_id' => $actorId ?? auth()->id(),
            'action' => $action,
            'entity_type' => class_basename($entity),
            'entity_id' => $entity->getKey(),
            'changes' => $this->redact($changes),
        ]);
    }

    /** @param array<string|int, mixed> $values
     * @return array<string|int, mixed>
     */
    private function redact(array $values): array
    {
        foreach ($values as $key => $value) {
            if (preg_match('/password|token|secret|document|body/i', (string) $key)) {
                unset($values[$key]);
            } elseif (is_array($value)) {
                $values[$key] = $this->redact($value);
            }
        }

        return $values;
    }
}
