<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SeedRolesAndPermissionsData extends Migration
{
    public function up()
    {
        // 先清除缓存
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // 创建权限
        Permission::create(['name' => 'manage_contents']);
        Permission::create(['name' => 'manage_users']);
        Permission::create(['name' => 'edit_settings']);

        // 创建角色并赋予权限
        $founder = Role::create(['name' => 'founder']);
        $founder->givePermissionTo(['manage_contents', 'manage_users', 'edit_settings']);

        $maintainer = Role::create(['name' => 'maintainer']);
        $maintainer->givePermissionTo('manage_contents');
    }

    public function down()
    {
        // 需清除缓存，否则会报错
        app(Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $tableNames = config('permission.table_names');

        Model::unguard();
        DB::table($tableNames['role_has_permissions'])->truncate();
        DB::table($tableNames['model_has_roles'])->truncate();
        DB::table($tableNames['model_has_permissions'])->truncate();
        DB::table($tableNames['roles'])->delete();
        DB::table($tableNames['permissions'])->delete();
        Model::reguard();
    }
}
