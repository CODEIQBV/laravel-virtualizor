<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class AclManager
{
    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * List all Administrator ACLs
     *
     * @param  int  $page  Page number
     * @param  int  $perPage  Number of records per page
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function list(int $page = 1, int $perPage = 50, bool $raw = false): array
    {
        try {
            $response = $this->api->listAcls($page, $perPage);

            if ($raw) {
                return $response;
            }

            return $response['acls'] ?? [];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to list ACLs: '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Create a new Administrator ACL
     *
     * @param  string  $name  Name of the ACL
     * @param  array<string, bool>  $permissions  Array of permissions
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function create(string $name, array $permissions = [], bool $raw = false): array
    {
        $params = [
            'name' => $name,
        ];

        // Map all possible ACL permissions
        $aclPermissions = [
            'add_admin_acl', 'addbackupserver', 'addbackup_plan', 'adddnsplan',
            'addippool', 'addips', 'addiso', 'addmg', 'addpdns', 'addplan',
            'addrecipe', 'addserver', 'addsg', 'addtemplate', 'adduser', 'adminacl',
            'addvs', 'add_distro', 'backupservers', 'backup_plans', 'changehostname',
            'cluster_resources', 'cluster_statistics', 'config', 'createssl',
            'createtemplate', 'databackup', 'defaultvsconf', 'delete_admin_acl',
            // ... add all other permissions from the documentation
        ];

        // Set permissions
        foreach ($aclPermissions as $permission) {
            $key = "act_{$permission}";
            $params[$key] = isset($permissions[$permission]) ? (int) $permissions[$permission] : 0;
        }

        try {
            $response = $this->api->addAdminAcl($params);

            if ($raw) {
                return $response;
            }

            if (! empty($response['done'])) {
                return [
                    'success' => true,
                    'name' => $name,
                    'permissions' => $permissions,
                ];
            }

            throw new VirtualizorApiException('Failed to create ACL: Unknown error');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to create ACL: '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Edit an existing Administrator ACL
     *
     * @param  int  $aclId  ACL ID to edit
     * @param  string  $name  New name for the ACL
     * @param  array<string, bool>  $permissions  Array of permissions
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function update(int $aclId, string $name, array $permissions = [], bool $raw = false): array
    {
        $params = [
            'name' => $name,
        ];

        // Map all possible ACL permissions
        $aclPermissions = [
            'add_admin_acl', 'addbackupserver', 'addbackup_plan', 'adddnsplan',
            'addippool', 'addips', 'addiso', 'addmg', 'addpdns', 'addplan',
            'addrecipe', 'addserver', 'addsg', 'addtemplate', 'adduser', 'adminacl',
            'addvs', 'add_distro', 'backupservers', 'backup_plans', 'changehostname',
            'cluster_resources', 'cluster_statistics', 'config', 'createssl',
            'createtemplate', 'databackup', 'defaultvsconf', 'delete_admin_acl',
            // ... add all other permissions from the documentation
        ];

        // Set permissions
        foreach ($aclPermissions as $permission) {
            $key = "act_{$permission}";
            $params[$key] = isset($permissions[$permission]) ? (int) $permissions[$permission] : 0;
        }

        try {
            $response = $this->api->editAdminAcl($aclId, $params);

            if ($raw) {
                return $response;
            }

            if (! empty($response['done'])) {
                return [
                    'success' => true,
                    'id' => $aclId,
                    'name' => $name,
                    'permissions' => $permissions,
                ];
            }

            throw new VirtualizorApiException('Failed to update ACL: Unknown error');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to update ACL {$aclId}: ".$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Delete one or more Administrator ACLs
     *
     * @param  int|array  $aclIds  Single ACL ID or array of ACL IDs
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function delete($aclIds, bool $raw = false): array
    {
        try {
            $response = $this->api->deleteAcls($aclIds);

            if ($raw) {
                return $response;
            }

            if (! empty($response['done'])) {
                return [
                    'success' => true,
                    'deleted' => is_array($response['done'])
                        ? $response['done']
                        : [$response['done']],
                ];
            }

            throw new VirtualizorApiException('Failed to delete ACL(s): Unknown error');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to delete ACL(s): '.$e->getMessage(),
                $e->getContext()
            );
        }
    }
}
