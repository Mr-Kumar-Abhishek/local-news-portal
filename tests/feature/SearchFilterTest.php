<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use App\Database\Seeds\HindBiharSeeder;

/**
 * @internal
 */
final class SearchFilterTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = true;
    protected $refresh      = false;
    protected $seedOnce     = true;
    protected $namespace   = null;
    protected $seed        = HindBiharSeeder::class;

    public function testBasicSearchReturnsResults(): void
    {
        // Create an article to search for
        $articleModel = new \App\Models\ArticleModel();
        $userModel = new \App\Models\UserModel();
        $categoryModel = new \App\Models\CategoryModel();

        $author = $userModel->where('username', 'admin')->first();
        $category = $categoryModel->where('slug', 'politics')->first();

        $articleModel->insert([
            'title_en'     => 'Unique Search Term Article',
            'title_hi'     => 'अनूठा खोज शब्द लेख',
            'content_en'   => 'Searchable unique content.',
            'content_hi'   => 'खोजने योग्य अनूठी सामग्री।',
            'slug'         => 'unique-search-term-article',
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'en',
            'news_section' => 'national',
            'status'       => 'published',
            'published_at' => date('Y-m-d H:i:s'),
        ]);

        $response = $this->get('en/search?q=Unique+Search+Term');
        $response->assertStatus(200);
        $response->assertSee('Search Results');
        $response->assertSee('Unique Search Term Article');
    }

    public function testSearchWithNoResults(): void
    {
        $response = $this->get('en/search?q=xyznonexistentterm12345');
        $response->assertStatus(200);
        $response->assertSee('Search Results');
    }

    public function testSearchWithDateFilter(): void
    {
        $articleModel = new \App\Models\ArticleModel();
        $userModel = new \App\Models\UserModel();
        $categoryModel = new \App\Models\CategoryModel();

        $author = $userModel->where('username', 'admin')->first();
        $category = $categoryModel->where('slug', 'politics')->first();

        // Article with specific date (use unique slug per test to avoid collisions)
        $articleId = $articleModel->insert([
            'title_en'     => 'June 2026 Date Filter Article',
            'title_hi'     => 'जून 2026 दिनांक लेख',
            'content_en'   => 'June date filter content.',
            'content_hi'   => 'जून दिनांक सामग्री।',
            'slug'         => 'june-2026-date-filter-article',
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'en',
            'news_section' => 'national',
            'status'       => 'published',
            'published_at' => '2026-06-01 10:00:00',
        ]);

        $this->assertNotEmpty($articleId, 'Article should be created');

        // Search with date range that includes the article
        $response = $this->get('en/search?q=June+2026+Date+Filter&date_from=2026-06-01&date_to=2026-06-02');
        $response->assertStatus(200);
        $response->assertSee('June 2026 Date Filter Article');

        // Search with date range that should restrict results
        $response = $this->get('en/search?q=June+2026+Date+Filter&date_from=2026-01-01&date_to=2026-06-01');
        $response->assertStatus(200);
        // Article published on 2026-06-01 10:00:00 should be found within this range
        $response->assertSee('June 2026 Date Filter Article');
    }

    public function testSearchWithCategoryFilter(): void
    {
        $articleModel = new \App\Models\ArticleModel();
        $userModel = new \App\Models\UserModel();
        $categoryModel = new \App\Models\CategoryModel();

        $author = $userModel->where('username', 'admin')->first();
        $category = $categoryModel->where('slug', 'sports')->first();

        $articleModel->insert([
            'title_en'     => 'Sports Filter Article',
            'title_hi'     => 'खेल फ़िल्टर लेख',
            'content_en'   => 'Sports content.',
            'content_hi'   => 'खेल सामग्री।',
            'slug'         => 'sports-filter-article',
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'en',
            'news_section' => 'national',
            'status'       => 'published',
            'published_at' => date('Y-m-d H:i:s'),
        ]);

        $response = $this->get('en/search?q=&category=sports');
        $response->assertStatus(200);
        $response->assertSee('Sports Filter Article');
    }

    public function testSearchWithAuthorFilter(): void
    {
        $articleModel = new \App\Models\ArticleModel();
        $userModel = new \App\Models\UserModel();
        $categoryModel = new \App\Models\CategoryModel();

        $author = $userModel->where('username', 'admin')->first();
        $category = $categoryModel->where('slug', 'politics')->first();

        $articleModel->insert([
            'title_en'     => 'Admin Filtered Article',
            'title_hi'     => 'एडमिन फ़िल्टर्ड लेख',
            'content_en'   => 'Admin content.',
            'content_hi'   => 'एडमिन सामग्री।',
            'slug'         => 'admin-filtered-article',
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'en',
            'news_section' => 'national',
            'status'       => 'published',
            'published_at' => date('Y-m-d H:i:s'),
        ]);

        $response = $this->get('en/search?q=&author=admin');
        $response->assertStatus(200);
        $response->assertSee('Admin Filtered Article');
    }

    public function testSearchWithLanguageFilter(): void
    {
        $articleModel = new \App\Models\ArticleModel();
        $userModel = new \App\Models\UserModel();
        $categoryModel = new \App\Models\CategoryModel();

        $author = $userModel->where('username', 'admin')->first();
        $category = $categoryModel->where('slug', 'politics')->first();

        $articleId = $articleModel->insert([
            'title_en'     => 'English Language Filter Article',
            'title_hi'     => 'अंग्रेजी भाषा लेख',
            'content_en'   => 'English language filter content.',
            'content_hi'   => 'अंग्रेजी भाषा सामग्री।',
            'slug'         => 'english-language-filter-article',
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'en',
            'news_section' => 'national',
            'status'       => 'published',
            'published_at' => date('Y-m-d H:i:s'),
        ]);

        $this->assertNotEmpty($articleId, 'Article should be created');

        // Filter by English - should find it
        $response = $this->get('en/search?q=English+Language+Filter&language=en');
        $response->assertStatus(200);
        $response->assertSee('English Language Filter Article');

        // Filter by both - should also find it
        $response = $this->get('en/search?q=English+Language+Filter&language=both');
        $response->assertStatus(200);
        // 'both' language filter should include 'en' articles
        $this->assertStringNotContainsString('No articles found', $response->response()->getBody());
    }

    public function testSearchWithMultipleFilters(): void
    {
        $articleModel = new \App\Models\ArticleModel();
        $userModel = new \App\Models\UserModel();
        $categoryModel = new \App\Models\CategoryModel();

        $author = $userModel->where('username', 'admin')->first();
        $category = $categoryModel->where('slug', 'politics')->first();

        $articleModel->insert([
            'title_en'     => 'Multi Filter Article',
            'title_hi'     => 'मल्टी फ़िल्टर लेख',
            'content_en'   => 'Multi filter content.',
            'content_hi'   => 'मल्टी फ़िल्टर सामग्री।',
            'slug'         => 'multi-filter-article',
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'en',
            'news_section' => 'national',
            'status'       => 'published',
            'published_at' => '2026-06-01 12:00:00',
        ]);

        // Combine category + author + language + date
        $response = $this->get('en/search?q=Multi&category=politics&author=admin&language=en&date_from=2026-06-01&date_to=2026-06-02');
        $response->assertStatus(200);
        $response->assertSee('Multi Filter Article');
    }

    public function testSearchReturnsEmptyForNonPublishedArticles(): void
    {
        $articleModel = new \App\Models\ArticleModel();
        $userModel = new \App\Models\UserModel();
        $categoryModel = new \App\Models\CategoryModel();

        $author = $userModel->where('username', 'admin')->first();
        $category = $categoryModel->where('slug', 'politics')->first();

        $articleModel->insert([
            'title_en'     => 'Draft Search Article',
            'title_hi'     => 'ड्राफ़्ट खोज लेख',
            'content_en'   => 'Draft content.',
            'content_hi'   => 'ड्राफ़्ट सामग्री।',
            'slug'         => 'draft-search-article',
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'en',
            'news_section' => 'national',
            'status'       => 'draft',
        ]);

        $response = $this->get('en/search?q=Draft+Search');
        $response->assertStatus(200);
        $this->assertStringNotContainsString('Draft Search Article', $response->response()->getBody());
    }
}
