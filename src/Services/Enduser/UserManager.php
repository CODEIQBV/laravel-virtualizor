<?php

namespace CODEIQ\Virtualizor\Services\Enduser;

use CODEIQ\Virtualizor\Api\EnduserApi;

class UserManager
{
    public function __construct(
        private readonly EnduserApi $api
    ) {}

    public function profile(): array
    {
        return $this->api->profile();
    }

    public function updatePassword(string $current, string $new): array
    {
        return $this->api->userpassword([
            'old_pass' => $current,
            'new_pass' => $new,
            'conf_pass' => $new,
        ]);
    }
}
