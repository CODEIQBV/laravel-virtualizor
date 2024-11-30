<?php

namespace CODEIQ\Virtualizor\Api;

class EnduserApi extends BaseApi
{
    /**
     * List all VPS for the enduser
     *
     * @param  int  $page  Page number
     * @param  int  $reslen  Number of records per page
     * @param  array  $search  Search filters
     */
    public function listvs(int $page = 1, int $reslen = 50, array $search = []): array
    {
        $path = 'index.php?act=listvs';

        $params = [
            'page' => $page,
            'reslen' => $reslen,
        ];

        if (! empty($search)) {
            $params['search'] = 1;
            $params = array_merge($params, $search);
        }

        return $this->makeRequest($path, $params);
    }

    public function createVps(array $params)
    {
        return $this->makeRequest('index.php?act=create', $params, 'POST');
    }

    /**
     * Get user profile information
     */
    public function profile(): array
    {
        return $this->makeRequest('index.php?act=profile');
    }

    /**
     * List users for cloud user
     *
     * @param  int  $uid  User ID
     * @param  array  $params  Additional parameters like subuserid
     */
    public function users(int $uid, array $params = []): array
    {
        $path = 'index.php?act=users';

        return $this->makeRequest($path, [
            'uid' => $uid,
            ...$params,
        ]);
    }

    public function userpassword(array $params): array
    {
        return $this->makeRequest('index.php?act=userpassword', $params, 'POST');
    }

    public function usersettings(array $params): array
    {
        return $this->makeRequest('index.php?act=usersettings', $params, 'POST');
    }
}
