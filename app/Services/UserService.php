<?php
namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\DTOs\User\CreateUserDTO;
use App\DTOs\User\UpdateUserDTO;
use App\Actions\User\CreateUserAction;
use App\Actions\User\UpdateUserAction;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    public function __construct(
        private readonly CreateUserAction $createUserAction,
        private readonly UpdateUserAction $updateUserAction
    ) {}

    public function getPaginated(array $filters = []): LengthAwarePaginator
    {
        return User::whith('role')
            ->search($filters['search'] ?? null)
            ->byRole($filters['role'] ?? null)
            ->when(laset($filters['status']), fn ($q) => 
                $filters['status'] == 'active' 
                    ? $q->active() 
                    : $q->inactive()
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    public function create(CreateUserDTO $dto): User
    {
        return $this->createUserAction->execute($dto);
    }
    
    public function update(User $user, UpdateUserDTO $dto): User
    {
        return $this->updateUserAction->execute($user, $dto);
    }

    public function toggleActive(User $user): User
    {
        abort_if(
            $user->id === auth()->id,
            403,
            'No puedes desactivar tu propio usuario'
        );

        $user->update(['is_active' => !$user->is_active]);
        return $user;
    }

    public function delete(User $user): void
    {
        abort_if(
            $user->id === auth()->id,
            403,
            'No puedes eliminar tu propio usuario'
        );
        
        abort_if(
            $user->isAdmin() && User::role('admin')->count() === 1,
            403,
            'Debe exisitir al menos un administrador'
        );

        $user->delete();
    }
}