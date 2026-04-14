<?php

namespace App\Services\Role;

use App\DTOs\Role\RoleDTO;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function getAll(): Collection
    {
        return Role::with('permissions')->orderBy('name')->get();
    }

    public function findOrFail(int $id): Role
    {
        return Role::with('permissions')->findOrFail($id);
    }

    public function create(RoleDTO $dto): Role
    {
        $role = Role::create(['name' => $dto->name, 'guard_name' => 'web']);

        if (! empty($dto->permissions)) {
            $role->syncPermissions($dto->permissions);
        }

        return $role->load('permissions');
    }

    public function update(int $id, RoleDTO $dto): Role
    {
        $role = Role::findOrFail($id);
        $role->update(['name' => $dto->name]);
        $role->syncPermissions($dto->permissions);

        return $role->load('permissions');
    }

    public function delete(int $id): void
    {
        $role = Role::findOrFail($id);
        $role->delete();
    }
}
