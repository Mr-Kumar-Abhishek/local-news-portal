<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\UserModel;
use App\Models\CategoryModel;
use App\Models\TagModel;

class HindBiharSeeder extends Seeder
{
    public function run()
    {
        // Enable foreign keys
        $this->db->query('PRAGMA foreign_keys = ON;');

        // ========== Create Admin User ==========
        $userModel = new UserModel();
        $adminData = [
            'username'            => 'admin',
            'email'               => 'admin@hindbihar.com',
            'password'            => 'admin123',
            'full_name'           => 'Administrator',
            'role'                => 'admin',
            'language_preference' => 'en',
            'status'              => 1,
        ];
        $userModel->insert($adminData);
        echo "  - Admin user created (admin@hindbihar.com / admin123)\n";

        // ========== Create Categories ==========
        $categoryModel = new CategoryModel();
        $categories = [
            [
                'name_en'     => 'National',
                'name_hi'     => 'राष्ट्रीय',
                'slug'        => 'national',
                'description' => 'National news from across India',
                'status'      => 1,
            ],
            [
                'name_en'     => 'International',
                'name_hi'     => 'अंतरराष्ट्रीय',
                'slug'        => 'international',
                'description' => 'International news from around the world',
                'status'      => 1,
            ],
            [
                'name_en'     => 'Local',
                'name_hi'     => 'स्थानीय',
                'slug'        => 'local',
                'description' => 'Local and regional news from Bihar',
                'status'      => 1,
            ],
            [
                'name_en'     => 'Politics',
                'name_hi'     => 'राजनीति',
                'slug'        => 'politics',
                'description' => 'Political news and analysis',
                'status'      => 1,
            ],
            [
                'name_en'     => 'Sports',
                'name_hi'     => 'खेल',
                'slug'        => 'sports',
                'description' => 'Sports news and updates',
                'status'      => 1,
            ],
            [
                'name_en'     => 'Entertainment',
                'name_hi'     => 'मनोरंजन',
                'slug'        => 'entertainment',
                'description' => 'Entertainment news, movies, and culture',
                'status'      => 1,
            ],
            [
                'name_en'     => 'Business',
                'name_hi'     => 'व्यापार',
                'slug'        => 'business',
                'description' => 'Business and financial news',
                'status'      => 1,
            ],
            [
                'name_en'     => 'Technology',
                'name_hi'     => 'टेक्नोलॉजी',
                'slug'        => 'technology',
                'description' => 'Technology news and innovations',
                'status'      => 1,
            ],
        ];

        foreach ($categories as $category) {
            $categoryModel->insert($category);
        }
        echo '  - ' . count($categories) . " categories created\n";

        // ========== Create Tags ==========
        $tagModel = new TagModel();
        $tags = [
            ['name_en' => 'Bihar',           'name_hi' => 'बिहार',            'slug' => 'bihar'],
            ['name_en' => 'India',           'name_hi' => 'भारत',             'slug' => 'india'],
            ['name_en' => 'Politics',        'name_hi' => 'राजनीति',          'slug' => 'politics'],
            ['name_en' => 'Breaking News',   'name_hi' => 'ब्रेकिंग न्यूज़',  'slug' => 'breaking-news'],
            ['name_en' => 'Economy',         'name_hi' => 'अर्थव्यवस्था',    'slug' => 'economy'],
            ['name_en' => 'Health',          'name_hi' => 'स्वास्थ्य',        'slug' => 'health'],
            ['name_en' => 'Education',       'name_hi' => 'शिक्षा',           'slug' => 'education'],
            ['name_en' => 'Crime',           'name_hi' => 'अपराध',            'slug' => 'crime'],
            ['name_en' => 'Elections',       'name_hi' => 'चुनाव',            'slug' => 'elections'],
            ['name_en' => 'Culture',         'name_hi' => 'संस्कृति',         'slug' => 'culture'],
        ];

        foreach ($tags as $tag) {
            $tagModel->insert($tag);
        }
        echo '  - ' . count($tags) . " tags created\n";

        echo "\n✅ Seeding completed successfully!\n";
    }
}
