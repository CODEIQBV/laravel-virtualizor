<?php

namespace CODEIQ\Virtualizor\Services\Enduser;

use CODEIQ\Virtualizor\Api\EnduserApi;

class UserService
{
    protected EnduserApi $api;

    public function __construct(EnduserApi $api)
    {
        $this->api = $api;
    }

    /**
     * List all users under the cloud user
     *
     * @param  int  $uid  User ID
     * @param  array|null  $subUserIds  Array of sub-user IDs to filter
     */
    public function list(int $uid, ?array $subUserIds = null): array
    {
        $params = [];
        if ($subUserIds) {
            $params['subuserid'] = implode(',', $subUserIds);
        }

        return $this->api->users($uid, $params);
    }

    /**
     * Get current user profile
     */
    public function profile(): array
    {
        return $this->api->profile();
    }

    /**
     * Update current user settings
     */
    public function updateSettings(array $settings): array
    {
        return $this->api->usersettings($settings);
    }

    /**
     * Update current user password
     */
    public function updatePassword(string $currentPassword, string $newPassword): array
    {
        return $this->api->userpassword([
            'old_pass' => $currentPassword,
            'new_pass' => $newPassword,
            'conf_pass' => $newPassword,
        ]);
    }
}
