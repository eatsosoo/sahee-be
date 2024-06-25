<?php

namespace App\Repositories;

use App\Models\RolePermission;

class RolePermissionRepository extends BaseRepository
{
    /**
     * get corresponding model class name
     *
     * @return string
     */
    public function getRepositoryModelClass(): string
    {
        return RolePermission::class;
    }
}
