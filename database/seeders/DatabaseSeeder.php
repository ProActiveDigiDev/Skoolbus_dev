<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $adminUser = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('admin'),
        ]);
        $ownerUser = User::factory()->create([
            'name' => 'owner',
            'email' => 'owner@test.com',
            'password' => bcrypt('admin'),
        ]);
        $parentUser = User::factory()->create([
            'name' => 'parent',
            'email' => 'parent@test.com',
            'password' => bcrypt('admin'),
        ]);
        $riderUser = User::factory()->create([
            'name' => 'rider',
            'email' => 'rider@test.com',
            'password' => bcrypt('admin'),
        ]);
        $driverUser = User::factory()->create([
            'name' => 'driver',
            'email' => 'driver@test.com',
            'password' => bcrypt('admin'),
        ]);

        $adminRole = Role::create(['name' => 'Admin']);
        $ownerRole = Role::create(['name' => 'Owner']);
        $parentRole = Role::create(['name' => 'Parent']);
        $riderRole = Role::create(['name' => 'Rider']);
        $driverRole = Role::create(['name' => 'Driver']);

        $adminPermission = Permission::create(['name' => 'Admin View']);
        $ownerPermission = Permission::create(['name' => 'Owner View']);
        $parentPermission = Permission::create(['name' => 'Parent View']);
        $driverPermission = Permission::create(['name' => 'Driver View']);
        

        $adminUser->assignRole($adminRole);
        $ownerUser->assignRole($ownerRole);
        $parentUser->assignRole($parentRole);
        $riderUser->assignRole($riderRole);
        $driverUser->assignRole($driverRole);

        $adminUser->assignRole('Admin');
        $ownerUser->assignRole('Owner');
        $parentUser->assignRole('Parent');
        $riderUser->assignRole('Rider');
        $driverUser->assignRole('Driver');

        $this->call(WebsiteConfigsSeeder::class);
    }
}
