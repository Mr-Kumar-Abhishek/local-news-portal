<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use App\Database\Seeds\HindBiharSeeder;

/**
 * @internal
 */
final class AuthorPageTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = true;
    protected $namespace   = null;
    protected $seed        = HindBiharSeeder::class;

    public function testAuthorPageLoads(): void
    {
        // Admin user is created by the seeder
        $response = $this->get('en/author/admin');
        $response->assertStatus(200);
    }

    public function testAuthorPageShowsAuthorName(): void
    {
        $response = $this->get('en/author/admin');
        $response->assertStatus(200);
        // Should display the author's full name
        $response->assertSee('Administrator');
    }

    public function testAuthorPageWithNoArticles(): void
    {
        // Admin has 0 articles initially
        $response = $this->get('en/author/admin');
        $response->assertStatus(200);
    }

    public function testAuthorPageWithArticles(): void
    {
        // Create a published article for the admin author
        $articleModel = new \App\Models\ArticleModel();
        $userModel = new \App\Models\UserModel();
        $categoryModel = new \App\Models\CategoryModel();

        $author = $userModel->where('username', 'admin')->first();
        $category = $categoryModel->where('slug', 'politics')->first();

        $articleModel->insert([
            'title_en'     => 'Author Page Test Article',
            'title_hi'     => 'लेखक पृष्ठ परीक्षण लेख',
            'content_en'   => 'Author content.',
            'content_hi'   => 'लेखक सामग्री।',
            'slug'         => 'author-page-test-article',
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'en',
            'news_section' => 'national',
            'status'       => 'published',
            'published_at' => date('Y-m-d H:i:s'),
        ]);

        $response = $this->get('en/author/admin');
        $response->assertStatus(200);
        $response->assertSee('Author Page Test Article');
    }

    public function testAuthorPageNotFound(): void
    {
        $this->expectException(\CodeIgniter\Exceptions\PageNotFoundException::class);
        $this->get('en/author/nonexistentuserxyz123');
    }

    public function testAuthorPagePagination(): void
    {
        // Should load paginated author page
        $response = $this->get('en/author/admin/page/1');
        $response->assertStatus(200);
    }
}
