<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use App\Database\Seeds\HindBiharSeeder;

/**
 * @internal
 */
final class NewsPagesTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate   = true;
    protected $namespace = null;
    protected $seed      = HindBiharSeeder::class;

    public function testHomepageLoadsSuccessfully(): void
    {
        $response = $this->get('en');
        $response->assertStatus(200);
        $response->assertSee('Hind Bihar');
    }

    public function testNewsListingPageLoadsSuccessfully(): void
    {
        $response = $this->get('en/news');
        $response->assertStatus(200);
    }

    public function testSearchPageLoadsSuccessfully(): void
    {
        $response = $this->get('en/search?q=politics');
        $response->assertStatus(200);
        $response->assertSee('Search Results');
    }

    public function testSearchAutocompleteApiReturnsJson(): void
    {
        $response = $this->get('en/search/autocomplete?q=politics');
        $response->assertStatus(200);
        $response->assertHeaderPresent('Content-Type');
    }

    public function testAdminCanViewNewsManagementList(): void
    {
        $sessionData = [
            'user_id'            => 1, // Admin from seed
            'user_role'          => 'admin',
            'is_logged_in'       => true,
            'is_admin_logged_in' => true,
        ];

        $response = $this->withSession($sessionData)->get('en/admin/news');
        $response->assertStatus(200);
        $response->assertSee('News Management');
    }

    public function testAdminCanCreateArticleAsDraft(): void
    {
        $sessionData = [
            'user_id'            => 1, // Admin from seed
            'user_role'          => 'admin',
            'is_logged_in'       => true,
            'is_admin_logged_in' => true,
        ];

        $response = $this->withSession($sessionData)->post('en/admin/news/create', [
            'title_en'     => 'New Draft Article',
            'title_hi'     => 'नया ड्राफ्ट लेख',
            'slug'         => 'new-draft-article',
            'content_en'   => 'Some English content.',
            'content_hi'   => 'कुछ हिंदी सामग्री।',
            'excerpt_en'   => 'Brief summary',
            'excerpt_hi'   => 'संक्षिप्त विवरण',
            'category_id'  => 1,
            'language'     => 'both',
            'news_section' => 'local',
            'save_type'    => 'draft',
        ]);

        $response->assertRedirectTo('en/admin/news');

        $model = new \App\Models\ArticleModel();
        $article = $model->where('slug', 'new-draft-article')->first();
        $this->assertNotNull($article);
        $this->assertEquals('draft', $article->status);
        $this->assertNull($article->published_at);
    }

    public function testAdminCanCreateArticleAsPublished(): void
    {
        $sessionData = [
            'user_id'            => 1, // Admin from seed
            'user_role'          => 'admin',
            'is_logged_in'       => true,
            'is_admin_logged_in' => true,
        ];

        $response = $this->withSession($sessionData)->post('en/admin/news/create', [
            'title_en'     => 'New Published Article',
            'title_hi'     => 'नया प्रकाशित लेख',
            'slug'         => 'new-published-article',
            'content_en'   => 'Some English content.',
            'content_hi'   => 'कुछ हिंदी सामग्री।',
            'excerpt_en'   => 'Brief summary',
            'excerpt_hi'   => 'संक्षिप्त विवरण',
            'category_id'  => 1,
            'language'     => 'both',
            'news_section' => 'local',
            'save_type'    => 'publish',
        ]);

        $response->assertRedirectTo('en/admin/news');

        $model = new \App\Models\ArticleModel();
        $article = $model->where('slug', 'new-published-article')->first();
        $this->assertNotNull($article);
        $this->assertEquals('published', $article->status);
        $this->assertNotNull($article->published_at);
    }

    public function testAdminCanEditArticle(): void
    {
        $sessionData = [
            'user_id'            => 1, // Admin from seed
            'user_role'          => 'admin',
            'is_logged_in'       => true,
            'is_admin_logged_in' => true,
        ];

        $model = new \App\Models\ArticleModel();
        $articleId = $model->insert([
            'title_en'     => 'Initial Title',
            'title_hi'     => 'प्रारंभिक शीर्षक',
            'slug'         => 'initial-article',
            'content_en'   => 'Initial content.',
            'content_hi'   => 'प्रारंभिक सामग्री।',
            'category_id'  => 1,
            'author_id'    => 1,
            'language'     => 'both',
            'news_section' => 'local',
            'status'       => 'draft',
        ]);

        $response = $this->withSession($sessionData)->post('en/admin/news/edit/' . $articleId, [
            'title_en'     => 'Updated Title',
            'title_hi'     => 'अद्यतन शीर्षक',
            'slug'         => 'updated-article',
            'content_en'   => 'Updated content.',
            'content_hi'   => 'अद्यतन सामग्री।',
            'category_id'  => 1,
            'language'     => 'both',
            'news_section' => 'local',
            'save_type'    => 'publish',
        ]);

        $response->assertRedirectTo('en/admin/news');

        $updatedArticle = $model->find($articleId);
        $this->assertEquals('Updated Title', $updatedArticle->title_en);
        $this->assertEquals('published', $updatedArticle->status);
        $this->assertNotNull($updatedArticle->published_at);
    }

    public function testAdminCanDeleteArticle(): void
    {
        $sessionData = [
            'user_id'            => 1, // Admin from seed
            'user_role'          => 'admin',
            'is_logged_in'       => true,
            'is_admin_logged_in' => true,
        ];

        $model = new \App\Models\ArticleModel();
        $articleId = $model->insert([
            'title_en'     => 'Article to Delete',
            'title_hi'     => 'हटाने के लिए लेख',
            'slug'         => 'delete-me',
            'content_en'   => 'Content to delete.',
            'content_hi'   => 'हटाने के लिए सामग्री।',
            'category_id'  => 1,
            'author_id'    => 1,
            'language'     => 'both',
            'news_section' => 'local',
            'status'       => 'draft',
        ]);

        $response = $this->withSession($sessionData)->post('en/admin/news/delete/' . $articleId);
        $response->assertRedirectTo('en/admin/news');

        $deletedArticle = $model->find($articleId);
        $this->assertNull($deletedArticle);
    }
}
