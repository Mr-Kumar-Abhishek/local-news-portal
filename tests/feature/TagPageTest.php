<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use App\Database\Seeds\HindBiharSeeder;

/**
 * @internal
 */
final class TagPageTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = true;
    protected $refresh      = false;
    protected $seedOnce     = true;
    protected $namespace   = null;
    protected $seed        = HindBiharSeeder::class;

    public function testTagPageLoads(): void
    {
        // The seeder creates tag 'bihar'
        $response = $this->get('en/tag/bihar');
        $response->assertStatus(200);
    }

    public function testTagPageShowsTagName(): void
    {
        $response = $this->get('en/tag/bihar');
        $response->assertStatus(200);
        // Should display the tag name (English: "Bihar")
        $response->assertSee('Bihar');
    }

    public function testTagPageWithNoArticlesShowsEmptyState(): void
    {
        // All tags from seeder should have 0 articles initially
        $response = $this->get('en/tag/health');
        $response->assertStatus(200);

        // Should not show article listing
        $body = $response->response()->getBody();
        $this->assertStringNotContainsString('article-card', $body);
    }

    public function testTagPageWithArticles(): void
    {
        // Create a published article and tag it with 'bihar'
        $articleModel = new \App\Models\ArticleModel();
        $userModel = new \App\Models\UserModel();
        $categoryModel = new \App\Models\CategoryModel();
        $tagModel = new \App\Models\TagModel();

        $author = $userModel->where('username', 'admin')->first();
        $category = $categoryModel->where('slug', 'politics')->first();
        $tag = $tagModel->where('slug', 'bihar')->first();

        $articleId = $articleModel->insert([
            'title_en'     => 'Bihar Tagged Article',
            'title_hi'     => 'बिहार टैग लेख',
            'content_en'   => 'Tagged content.',
            'content_hi'   => 'टैग की गई सामग्री।',
            'slug'         => 'bihar-tagged-article',
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'en',
            'news_section' => 'local',
            'status'       => 'published',
            'published_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->table('article_tags')->insert([
            'article_id' => $articleId,
            'tag_id'     => $tag->id,
        ]);

        $response = $this->get('en/tag/bihar');
        $response->assertStatus(200);
        $response->assertSee('Bihar Tagged Article');
    }

    public function testTagPageNotFound(): void
    {
        $this->expectException(\CodeIgniter\Exceptions\PageNotFoundException::class);
        $this->get('en/tag/nonexistent-tag-xyz-123');
    }

    public function testTagPagePagination(): void
    {
        // The tag page with pagination parameter
        $response = $this->get('en/tag/bihar/page/1');
        $response->assertStatus(200);
    }
}
