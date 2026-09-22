<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AccessService
{
    /** @return array<string, array<int, string>> */
    public function rolePermissions(): array
    {
        $all = ['admin.access', 'dashboard.view', 'content.view', 'content.manage', 'content.publish', 'operations.view', 'operations.manage', 'vendor.verify', 'finance.view', 'refund.approve', 'payout.approve', 'growth.view', 'growth.manage', 'system.view', 'system.manage', 'users.view', 'users.manage', 'roles.view', 'roles.manage', 'vendor.access'];
        return [
            'super_admin' => $all,
            'content_admin' => ['admin.access', 'dashboard.view', 'content.view', 'content.manage', 'content.publish'],
            'operations_admin' => ['admin.access', 'dashboard.view', 'operations.view', 'operations.manage', 'vendor.verify'],
            'finance_admin' => ['admin.access', 'dashboard.view', 'finance.view', 'refund.approve', 'payout.approve'],
            'growth_admin' => ['admin.access', 'dashboard.view', 'growth.view', 'growth.manage'],
            'vendor_admin' => ['vendor.access'],
            'traveler' => [],
        ];
    }

    public function seed(): void
    {
        DB::transaction(function () {
            foreach ($this->rolePermissions() as $name => $permissions) {
                $role = Role::firstOrCreate(['name' => $name], ['label' => ucwords(str_replace('_', ' ', $name))]);
                $ids = collect($permissions)->map(fn (string $permission) => Permission::firstOrCreate(['name' => $permission])->id);
                if ($role->wasRecentlyCreated) {
                    $role->permissions()->sync($ids);
                }
            }
        });
    }

    public function grant(User $user, string $role): void
    {
        $user->roles()->syncWithoutDetaching([Role::where('name', $role)->firstOrFail()->id]);
        $user->unsetRelation('roles');
    }
}
