<?php

namespace App\Services;

use App\Actions\User\CreateUserAction;
use App\DTOs\UserData;
use App\Models\User;

class UserService
{
    public function __construct(
        private CreateUserAction $createUserAction
    ) {}

    /**
     * Register a new user with additional business logic.
     */
    public function registerUser(UserData $data): User
    {
        // Create user
        $user = $this->createUserAction->execute($data);

        // Additional business logic here
        // Example: Send welcome email, create user profile, etc.

        return $user;
    }
}
