<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Contracts\Services\UserServiceInterface;


class UserService implements UserServiceInterface
{
    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * List all users with optional filters (Admin API)
     *
     * @param  array  $filters  Array of filters (uid, email, type)
     * @param  int  $page  Page number
     * @param  int  $perPage  Number of records per page
     */
    public function list(array $filters = [], int $page = 1, int $perPage = 50): array
    {
        $params = [
            'uid' => $filters['uid'] ?? '',
            'email' => $filters['email'] ?? '',
            'user_type' => $filters['type'] ?? '',
        ];

        return $this->api->users($page, $perPage, $params);
    }

    /**
     * Get a specific user by ID (Admin API)
     */
    public function get(int $userId): array
    {
        return $this->api->users(1, 1, ['uid' => $userId]);
    }

    /**
     * Create a new user (Admin API)
     */
    public function create(array $userData): array
    {
        return $this->api->adduser($userData);
    }

    /**
     * Update an existing user (Admin API)
     */
    public function update(int $userId, array $userData): array
    {
        return $this->api->edituser($userId, $userData);
    }

    /**
     * Delete a user (Admin API)
     */
    public function delete(int $userId): array
    {
        return $this->api->delete_users($userId);
    }
}
