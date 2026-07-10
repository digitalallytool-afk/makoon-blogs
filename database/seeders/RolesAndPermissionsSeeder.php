<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Define Permissions
        $permissions = [
            [
                'name' => 'View Dashboard',
                'slug' => 'view-dashboard',
                'description' => 'Allows access to the backend admin dashboard panel',
            ],
            [
                'name' => 'View Posts',
                'slug' => 'view-posts',
                'description' => 'Allows listing and viewing articles',
            ],
            [
                'name' => 'Create Posts',
                'slug' => 'create-posts',
                'description' => 'Allows creating new blog posts',
            ],
            [
                'name' => 'Edit Posts',
                'slug' => 'edit-posts',
                'description' => 'Allows editing existing blog posts',
            ],
            [
                'name' => 'Delete Posts',
                'slug' => 'delete-posts',
                'description' => 'Allows deleting blog posts',
            ],
            [
                'name' => 'Manage Categories',
                'slug' => 'manage-categories',
                'description' => 'Allows managing blog categories',
            ],
            [
                'name' => 'Manage Media',
                'slug' => 'manage-media',
                'description' => 'Allows managing files in the media library',
            ],
            [
                'name' => 'Manage Authors',
                'slug' => 'manage-authors',
                'description' => 'Allows managing blog authors',
            ],
            [
                'name' => 'View Stories',
                'slug' => 'view-stories',
                'description' => 'Allows listing and viewing stories',
            ],
            [
                'name' => 'Create Stories',
                'slug' => 'create-stories',
                'description' => 'Allows creating new stories',
            ],
            [
                'name' => 'Edit Stories',
                'slug' => 'edit-stories',
                'description' => 'Allows editing existing stories',
            ],
            [
                'name' => 'Delete Stories',
                'slug' => 'delete-stories',
                'description' => 'Allows deleting stories',
            ],
            [
                'name' => 'Manage Story Categories',
                'slug' => 'manage-story-categories',
                'description' => 'Allows managing story categories',
            ],
            [
                'name' => 'View Printables',
                'slug' => 'view-printables',
                'description' => 'Allows listing and viewing printables',
            ],
            [
                'name' => 'Create Printables',
                'slug' => 'create-printables',
                'description' => 'Allows creating new printables',
            ],
            [
                'name' => 'Edit Printables',
                'slug' => 'edit-printables',
                'description' => 'Allows editing existing printables',
            ],
            [
                'name' => 'Delete Printables',
                'slug' => 'delete-printables',
                'description' => 'Allows deleting printables',
            ],
            [
                'name' => 'View Video Sessions',
                'slug' => 'view-video-sessions',
                'description' => 'Allows listing and viewing video sessions',
            ],
            [
                'name' => 'Create Video Sessions',
                'slug' => 'create-video-sessions',
                'description' => 'Allows creating new video sessions',
            ],
            [
                'name' => 'Edit Video Sessions',
                'slug' => 'edit-video-sessions',
                'description' => 'Allows editing existing video sessions',
            ],
            [
                'name' => 'Delete Video Sessions',
                'slug' => 'delete-video-sessions',
                'description' => 'Allows deleting video sessions',
            ],
            [
                'name' => 'Manage Session Categories',
                'slug' => 'manage-session-categories',
                'description' => 'Allows managing video session categories',
            ],
        ];

        $permissionModels = [];
        foreach ($permissions as $permissionData) {
            $permissionModels[$permissionData['slug']] = Permission::updateOrCreate(
                ['slug' => $permissionData['slug']],
                $permissionData
            );
        }

        // 2. Define Roles (Only Super Admin and Admin)
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Super Administrator with complete access control',
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrator with customized direct permissions',
            ],
        ];

        $roleModels = [];
        foreach ($roles as $roleData) {
            $roleModels[$roleData['slug']] = Role::updateOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }

        // Map all permissions to Super Admin role
        $roleModels['super-admin']->permissions()->sync(
            collect($permissionModels)->pluck('id')->toArray()
        );

        // Clear existing permissions from Admin role (as Admin users will receive direct dynamic permissions)
        $roleModels['admin']->permissions()->sync([]);

        // 3. Seed Default Users
        $users = [
            [
                'name' => 'Super Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'super-admin',
                'permissions' => [], // super-admin gets everything automatically
            ],
            [
                'name' => 'Admin Editor User',
                'email' => 'editor@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'permissions' => ['view-dashboard', 'view-posts', 'create-posts', 'edit-posts', 'manage-categories', 'manage-media', 'manage-authors', 'view-stories', 'create-stories', 'edit-stories', 'manage-story-categories', 'view-printables', 'create-printables', 'edit-printables', 'delete-printables', 'view-video-sessions', 'create-video-sessions', 'edit-video-sessions', 'delete-video-sessions', 'manage-session-categories'],
            ],
            [
                'name' => 'Admin Writer User',
                'email' => 'writer@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'permissions' => ['view-dashboard', 'view-posts', 'create-posts', 'view-stories', 'create-stories', 'view-printables', 'create-printables', 'view-video-sessions', 'create-video-sessions'],
            ],
        ];

        foreach ($users as $userData) {
            $roleSlug = $userData['role'];
            $directPermissions = $userData['permissions'];
            unset($userData['role'], $userData['permissions']);

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            // Assign role
            $user->syncRoles([$roleSlug]);

            // Assign direct permissions
            $user->syncPermissions($directPermissions);
        }
    }
}
