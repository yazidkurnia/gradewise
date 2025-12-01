<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    /**
     * Find user by ID.
     */
    public function findById(int $id): ?User;

    /**
     * Find user by email.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Get all users.
     */
    public function all(): Collection;

    /**
     * Create a new user.
     */
    public function create(array $data): User;

    /**
     * Update user.
     */
    public function update(User $user, array $data): User;

    /**
     * Delete user.
     */
    public function delete(User $user): bool;
}
