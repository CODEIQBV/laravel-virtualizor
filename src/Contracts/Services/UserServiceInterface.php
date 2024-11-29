<?php

namespace CODEIQ\Virtualizor\Contracts\Services;

interface UserServiceInterface
{
    /**
     * List all users with optional filters
     *
     * @param  array<string, mixed>  $filters  Array of filters (uid, email, type)
     * @param  positive-int  $page  Page number
     * @param  positive-int  $perPage  Number of records per page
     * @return array<string, mixed>
     */
    public function list(array $filters = [], int $page = 1, int $perPage = 50): array;

    /**
     * Get a specific user by ID
     *
     * @param  positive-int  $userId
     * @return array<string, mixed>
     */
    public function get(int $userId): array;

    /**
     * Create a new user
     *
     * @param  array<string, mixed>  $userData
     * @return array<string, mixed>
     */
    public function create(array $userData): array;

    /**
     * Update an existing user
     *
     * @param  positive-int  $userId
     * @param  array<string, mixed>  $userData
     * @return array<string, mixed>
     */
    public function update(int $userId, array $userData): array;

    /**
     * Delete a user
     *
     * @param  positive-int  $userId
     * @return array<string, mixed>
     */
    public function delete(int $userId): array;
}
