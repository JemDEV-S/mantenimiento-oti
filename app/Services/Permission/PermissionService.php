<?php

namespace App\Services\Permission;

use App\DTOs\Permission\PermissionDTO;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Permission;

class PermissionService
{
    public function getAll(): Collection
    {
        return Permission::all();
    }

    public function findOrFail(int $id): Permission
    {
        return Permission::findOrFail($id);
    }

    public function create(PermissionDTO $dto): Permission
    {
        return Permission::create(['name' => $dto->name, 'guard_name' => 'web']);
    }

    public function update(int $id, PermissionDTO $dto): Permission
    {
        $permission = Permission::findOrFail($id);
        $permission->update(['name' => $dto->name]);

        return $permission;
    }

    public function delete(int $id): void
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();
    }
}
