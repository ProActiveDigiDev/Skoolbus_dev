<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class WebsiteConfigsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('website_configs')->insert([
            [
                'var_name' => 'site_name',
                'name' => 'Website Name',
                'description' => 'The name of the website or business (without the www or .com)',
                'type' => 'general',
                'var_value' => 'PDC',
                'notes' => '',
            ],
            [
                'var_name' => 'site_tagline',
                'name' => 'Website Tagline',
                'description' => 'The tagline of the website or business',
                'type' => 'general',
                'var_value' => 'Making Laravel startup easy.',
                'notes' => '',
            ],
            [
                'var_name' => 'site_description',
                'name' => 'Site Description',
                'description' => 'Template for Laravel project requiring authentication, authorization, and user management.',
                'type' => 'general',
                'var_value' => 'My Site Description',
                'notes' => 'This is the description of the site',
            ],
            [
                'var_name' => 'site_logo',
                'name' => 'Site Logo',
                'description' => 'The logo of the website or business in .jpg, .png, or .webp format',
                'type' => 'branding',
                'var_value' => 'images/project-branding/logo.png',
                'notes' => '',
            ],
            [
                'var_name' => 'site_favicon',
                'name' => 'Site Favicon',
                'description' => 'The favicon of the website or business in .jpg, .png, or .webp format',
                'type' => 'branding',
                'var_value' => 'images/project-branding/favicon-32x32.png',
                'notes' => '',
            ],
            [
                'var_name' => 'site_brand_color_primary',
                'name' => 'Site Primary Brand Color',
                'description' => 'The primary brand color of the website or business in hex format',
                'type' => 'branding',
                'var_value' => '#54AF38',
                'notes' => '',
            ],
            [
                'var_name' => 'site_brand_color_secondary',
                'name' => 'Site Secondary Brand Color',
                'description' => 'The secondary brand color of the website or business in hex format',
                'type' => 'branding',
                'var_value' => '#54AF38',
                'notes' => '',
            ]
        ]);
    }
}
