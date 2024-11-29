<?php

namespace CODEIQ\Virtualizor\Services;

use CODEIQ\Virtualizor\Api\AdminApi;

class UserService
{
    protected AdminApi $api;

    public function __construct(AdminApi $api)
    {
        $this->api = $api;
    }

    /**
     * List all users with optional filters
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
     * Get a specific user by ID
     */
    public function get(int $userId): array
    {
        return $this->api->users(1, 1, ['uid' => $userId]);
    }

    /**
     * Create a new user
     */
    public function create(array $userData): array
    {
        return $this->api->adduser($userData);
    }

    /**
     * Update an existing user
     */
    public function update(int $userId, array $userData): array
    {
        return $this->api->edituser($userId, $userData);
    }

    /**
     * Delete a user
     */
    public function delete(int $userId): array
    {
        return $this->api->delete_users($userId);
    }
}
