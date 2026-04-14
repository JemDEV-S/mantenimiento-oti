<?php

namespace App\Services\Employee;

use App\DTOs\Employee\CreateEmployeeDTO;
use App\DTOs\Employee\UpdateEmployeeDTO;
use App\Exceptions\Employee\EmployeeException;
use App\Models\Employee;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EmployeeService
{
    public function getPaginated(array $filters = []): LengthAwarePaginator
    {
        return Employee::with('organizationalUnit')
            ->search($filters['search'] ?? null)
            ->when(isset($filters['is_technician']), fn ($q) =>
                $q->where('is_technician', (bool) $filters['is_technician'])
            )
            ->when(isset($filters['unit_id']), fn ($q) =>
                $q->where('organizational_unit_id', $filters['unit_id'])
            )
            ->when(isset($filters['status']), fn ($q) =>
                $filters['status'] === 'active' ? $q->active() : $q->where('is_active', false)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }

    public function getAllTechnicians(): Collection
    {
        return Employee::technicians()->orderBy('full_name')->get();
    }

    public function create(CreateEmployeeDTO $dto): Employee
    {
        return Employee::create($dto->toArray());
    }

    public function update(Employee $employee, UpdateEmployeeDTO $dto): Employee
    {
        $employee->update($dto->toArray());
        return $employee->fresh();
    }

    public function toggleActive(Employee $employee): Employee
    {
        $employee->update(['is_active' => ! $employee->is_active]);
        return $employee;
    }

    public function delete(Employee $employee): void
    {
        if ($employee->assignedAssets()->exists()) {
            throw EmployeeException::hasAssignedAssets($employee->full_name);
        }

        $employee->delete();
    }
}
