<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRolesAndPermissions
{
    /**
     * Users belongs to many Roles.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Users belongs to many Permissions directly.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string|Role $role): bool
    {
        if (is_string($role)) {
            return $this->roles->contains('slug', $role);
        }

        return $this->roles->contains('id', $role->id);
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string|Permission $permission): bool
    {
        $slug = is_string($permission) ? $permission : $permission->slug;

        // Super admins always bypass permission checks
        if ($this->hasRole('super-admin')) {
            return true;
        }

        // Check direct permissions assigned to this user
        if ($this->permissions->contains('slug', $slug)) {
            return true;
        }

        // Also check if any role has this permission
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('slug', $slug)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Assign a role to the user.
     */
    public function assignRole(string|Role $role): void
    {
        $roleModel = is_string($role) ? Role::where('slug', $role)->first() : $role;

        if ($roleModel && ! $this->hasRole($roleModel)) {
            $this->roles()->attach($roleModel);
            $this->load('roles'); // reload relation cache
        }
    }

    /**
     * Withdraw a role from the user.
     */
    public function withdrawRole(string|Role $role): void
    {
        $roleModel = is_string($role) ? Role::where('slug', $role)->first() : $role;

        if ($roleModel) {
            $this->roles()->detach($roleModel);
            $this->load('roles'); // reload relation cache
        }
    }

    /**
     * Sync user roles.
     */
    public function syncRoles(array $roles): void
    {
        $roleIds = [];
        foreach ($roles as $role) {
            $roleModel = is_string($role) ? Role::where('slug', $role)->first() : $role;
            if ($roleModel) {
                $roleIds[] = $roleModel->id;
            }
        }

        $this->roles()->sync($roleIds);
        $this->load('roles'); // reload relation cache
    }

    /**
     * Sync direct user permissions.
     */
    public function syncPermissions(array $permissions): void
    {
        $permissionIds = [];
        foreach ($permissions as $perm) {
            $permModel = is_string($perm) ? Permission::where('slug', $perm)->first() : $perm;
            if ($permModel) {
                $permissionIds[] = $permModel->id;
            }
        }

        $this->permissions()->sync($permissionIds);
        $this->load('permissions'); // reload relation cache
    }
}
