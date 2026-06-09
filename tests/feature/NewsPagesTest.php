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

    protected $migrate     = true;
    protected $migrateOnce = true;
    protected $refresh      = false;
    protected $seedOnce     = true;
    protected $namespace   = null;
    protected $seed        = HindBiharSeeder::class;

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

    public function testTagPageLoadsSuccessfully(): void
    {
        // Tag 'bihar' is created by the seeder
        $response = $this->get('en/tag/bihar');
        $response->assertStatus(200);
    }

    public function testTagPageNotFoundForInvalidTag(): void
    {
        $this->expectException(\CodeIgniter\Exceptions\PageNotFoundException::class);
        $this->get('en/tag/nonexistent-tag-xyz');
    }

    public function testAuthorPageLoadsSuccessfully(): void
    {
        // Admin user is created by the seeder
        $response = $this->get('en/author/admin');
        $response->assertStatus(200);
    }

    public function testAuthorPageNotFoundForInvalidUser(): void
    {
        $this->expectException(\CodeIgniter\Exceptions\PageNotFoundException::class);
        $this->get('en/author/nonexistentuserxyz');
    }

    public function testThreadedCommentSubmission(): void
    {
        // Create a published article first
        $articleModel = new \App\Models\ArticleModel();
        $userModel = new \App\Models\UserModel();
        $categoryModel = new \App\Models\CategoryModel();

        $author = $userModel->where('username', 'admin')->first();
        $category = $categoryModel->where('slug', 'politics')->first();

        $articleId = $articleModel->insert([
            'title_en'     => 'Article For Comments',
            'title_hi'     => 'टिप्पणियों के लिए लेख',
            'content_en'   => 'Content for comments.',
            'content_hi'   => 'टिप्पणियों के लिए सामग्री।',
            'slug'         => 'article-for-comments-' . uniqid(),
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'both',
            'news_section' => 'local',
            'status'       => 'published',
            'published_at' => date('Y-m-d H:i:s'),
            'allow_comments' => 1,
        ]);

        $this->assertIsInt($articleId, 'Article insert should return integer ID');

        // CAPTCHA: set session answer via withSession() so the guest comment passes validation.
        // session()->set() does not carry into the request context in CI4's test environment.
        $response = $this->withSession(['captcha_answer' => 15])->post("en/comment/{$articleId}", [
            'author_name'    => 'Commenter One',
            'author_email'   => 'commenter1@example.com',
            'body'           => 'This is a top-level comment.',
            'captcha_answer' => 15,
        ]);

        $response->assertRedirect();

        // Verify comment was created
        $commentModel = new \App\Models\CommentModel();
        $comments = $commentModel->where('article_id', $articleId)->findAll();
        $this->assertCount(1, $comments);
        $this->assertEquals('pending', $comments[0]->status);
        $this->assertEquals('This is a top-level comment.', $comments[0]->body);

        // CAPTCHA: re-set session answer via withSession() (cleared after first successful comment)
        $response = $this->withSession(['captcha_answer' => 15])->post("en/comment/{$articleId}", [
            'author_name'    => 'Replier One',
            'author_email'   => 'replier1@example.com',
            'body'           => 'This is a reply.',
            'parent_id'      => $comments[0]->id,
            'captcha_answer' => 15,
        ]);

        $response->assertRedirect();

        // Verify reply was created with parent_id
        $replies = $commentModel->where('article_id', $articleId)
                                ->where('parent_id', $comments[0]->id)
                                ->findAll();
        $this->assertCount(1, $replies);
        $this->assertEquals('This is a reply.', $replies[0]->body);
    }

    public function testThreadedCommentsRetrieval(): void
    {
        // Create article
        $articleModel = new \App\Models\ArticleModel();
        $userModel = new \App\Models\UserModel();
        $categoryModel = new \App\Models\CategoryModel();
        $commentModel = new \App\Models\CommentModel();

        $author = $userModel->where('username', 'admin')->first();
        $category = $categoryModel->where('slug', 'politics')->first();

        $articleId = $articleModel->insert([
            'title_en'     => 'Threaded Comments Article',
            'title_hi'     => 'थ्रेडेड टिप्पणियाँ लेख',
            'content_en'   => 'Threaded content.',
            'content_hi'   => 'थ्रेडेड सामग्री।',
            'slug'         => 'threaded-comments-article',
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'both',
            'news_section' => 'local',
            'status'       => 'published',
            'published_at' => date('Y-m-d H:i:s'),
            'allow_comments' => 1,
        ]);

        // Create parent comment
        $parentId = $commentModel->insert([
            'article_id'   => $articleId,
            'author_name'  => 'Parent Commenter',
            'author_email' => 'parent@example.com',
            'body'         => 'Parent comment body.',
            'status'       => 'approved',
        ]);

        // Create child comment
        $commentModel->insert([
            'article_id'   => $articleId,
            'parent_id'    => $parentId,
            'author_name'  => 'Child Commenter',
            'author_email' => 'child@example.com',
            'body'         => 'Child reply body.',
            'status'       => 'approved',
        ]);

        // Get threaded comments
        $threaded = $commentModel->getThreadedComments($articleId);
        $this->assertCount(1, $threaded);
        $this->assertEquals('Parent comment body.', $threaded[0]->body);
        $this->assertNotEmpty($threaded[0]->children);
        $this->assertCount(1, $threaded[0]->children);
        $this->assertEquals('Child reply body.', $threaded[0]->children[0]->body);
        $this->assertEquals($parentId, $threaded[0]->children[0]->parent_id);
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
