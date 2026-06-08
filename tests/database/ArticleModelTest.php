<?php

namespace Tests\Database;

use Tests\Support\DatabaseTestCase;
use App\Models\ArticleModel;
use App\Models\UserModel;
use App\Models\CategoryModel;
use App\Models\TagModel;

/**
 * @internal
 */
final class ArticleModelTest extends DatabaseTestCase
{

    public function testCreateAndRetrieveArticle(): void
    {
        $userModel = new UserModel();
        $author = $userModel->where('username', 'admin')->first();
        $this->assertNotNull($author);

        $categoryModel = new CategoryModel();
        $category = $categoryModel->where('slug', 'politics')->first();
        $this->assertNotNull($category);

        $articleModel = new ArticleModel();
        $articleId = $articleModel->insert([
            'title_en'     => 'Sample Politics Article',
            'title_hi'     => 'नमूना राजनीति लेख',
            'content_en'   => 'This is a sample article content.',
            'content_hi'   => 'यह एक नमूना लेख सामग्री है।',
            'slug'         => 'sample-politics-article',
            'excerpt_en'   => 'Excerpt en',
            'excerpt_hi'   => 'Excerpt hi',
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'both',
            'news_section' => 'national',
            'status'       => 'published',
            'published_at' => date('Y-m-d H:i:s'),
        ]);

        $this->assertNotEmpty($articleId);

        // Retrieve
        $article = $articleModel->find($articleId);
        $this->assertNotNull($article);
        $this->assertEquals('Sample Politics Article', $article->title_en);
        $this->assertEquals('published', $article->status);

        // Retrieve through getPublished
        $published = $articleModel->getPublished(['category_id' => $category->id]);
        $this->assertCount(1, $published);
        $this->assertEquals($articleId, $published[0]->id);

        // Count published
        $count = $articleModel->countPublished(['category_id' => $category->id]);
        $this->assertEquals(1, $count);
    }

    public function testFilterByLanguageAndSearch(): void
    {
        $userModel = new UserModel();
        $author = $userModel->where('username', 'admin')->first();
        $categoryModel = new CategoryModel();
        $category = $categoryModel->where('slug', 'politics')->first();

        $articleModel = new ArticleModel();

        // 1. English national published article
        $articleModel->insert([
            'title_en'     => 'English News Title',
            'title_hi'     => 'अंग्रेजी समाचार शीर्षक',
            'content_en'   => 'Content is here',
            'content_hi'   => 'सामग्री यहाँ है',
            'slug'         => 'english-news-title',
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'en',
            'news_section' => 'national',
            'status'       => 'published',
            'published_at' => date('Y-m-d H:i:s'),
        ]);

        // 2. Hindi national published article
        $articleModel->insert([
            'title_en'     => 'Hindi News Title',
            'title_hi'     => 'हिंदी समाचार शीर्षक',
            'content_en'   => 'Content in Hindi',
            'content_hi'   => 'हिंदी में सामग्री',
            'slug'         => 'hindi-news-title',
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'hi',
            'news_section' => 'national',
            'status'       => 'published',
            'published_at' => date('Y-m-d H:i:s'),
        ]);

        // Filter by language
        $enArticles = $articleModel->getPublished(['language' => 'en']);
        $this->assertCount(1, $enArticles);
        $this->assertEquals('english-news-title', $enArticles[0]->slug);

        // Search title
        $searchResults = $articleModel->getPublished(['search' => 'English']);
        $this->assertCount(1, $searchResults);
        $this->assertEquals('english-news-title', $searchResults[0]->slug);
    }

    public function testGetFeaturedArticles(): void
    {
        $userModel = new UserModel();
        $author = $userModel->where('username', 'admin')->first();
        $categoryModel = new CategoryModel();
        $category = $categoryModel->where('slug', 'politics')->first();

        $articleModel = new ArticleModel();

        // Create featured article
        $articleModel->insert([
            'title_en'     => 'Featured Article One',
            'title_hi'     => 'फीचर्ड लेख एक',
            'content_en'   => 'Featured content.',
            'content_hi'   => 'फीचर्ड सामग्री।',
            'slug'         => 'featured-article-one',
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'en',
            'news_section' => 'national',
            'status'       => 'published',
            'is_featured'  => 1,
            'published_at' => date('Y-m-d H:i:s'),
        ]);

        // Create non-featured article
        $articleModel->insert([
            'title_en'     => 'Regular Article',
            'title_hi'     => 'सामान्य लेख',
            'content_en'   => 'Regular content.',
            'content_hi'   => 'सामान्य सामग्री।',
            'slug'         => 'regular-article',
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'en',
            'news_section' => 'national',
            'status'       => 'published',
            'is_featured'  => 0,
            'published_at' => date('Y-m-d H:i:s'),
        ]);

        $featured = $articleModel->getFeaturedArticles(5);
        $this->assertCount(1, $featured);
        $this->assertEquals('featured-article-one', $featured[0]->slug);
        $this->assertEquals(1, $featured[0]->is_featured);
    }

    public function testGetBreakingNews(): void
    {
        $userModel = new UserModel();
        $author = $userModel->where('username', 'admin')->first();
        $categoryModel = new CategoryModel();
        $category = $categoryModel->where('slug', 'politics')->first();

        $articleModel = new ArticleModel();

        // Create breaking news article
        $articleModel->insert([
            'title_en'     => 'Breaking News Alert',
            'title_hi'     => 'ब्रेकिंग न्यूज़ अलर्ट',
            'content_en'   => 'Breaking content.',
            'content_hi'   => 'ब्रेकिंग सामग्री।',
            'slug'         => 'breaking-news-alert',
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'en',
            'news_section' => 'national',
            'status'       => 'published',
            'is_breaking'  => 1,
            'published_at' => date('Y-m-d H:i:s'),
        ]);

        $breaking = $articleModel->getBreakingNews(3);
        $this->assertCount(1, $breaking);
        $this->assertEquals('breaking-news-alert', $breaking[0]->slug);
        $this->assertEquals(1, $breaking[0]->is_breaking);
    }

    public function testGetArticlesByTag(): void
    {
        $userModel = new UserModel();
        $author = $userModel->where('username', 'admin')->first();
        $categoryModel = new CategoryModel();
        $category = $categoryModel->where('slug', 'politics')->first();
        $tagModel = new TagModel();
        $tag = $tagModel->where('slug', 'bihar')->first();
        $this->assertNotNull($tag);

        $articleModel = new ArticleModel();

        // Create an article and associate with tag
        $articleId = $articleModel->insert([
            'title_en'     => 'Bihar News Article',
            'title_hi'     => 'बिहार समाचार लेख',
            'content_en'   => 'Bihar content.',
            'content_hi'   => 'बिहार सामग्री।',
            'slug'         => 'bihar-news-article',
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'both',
            'news_section' => 'local',
            'status'       => 'published',
            'published_at' => date('Y-m-d H:i:s'),
        ]);

        // Insert article-tag association
        $this->db->table('article_tags')->insert([
            'article_id' => $articleId,
            'tag_id'     => $tag->id,
        ]);

        $articles = $articleModel->getArticlesByTag($tag->id, 12, 0);
        $this->assertCount(1, $articles);
        $this->assertEquals('bihar-news-article', $articles[0]->slug);

        $count = $articleModel->countArticlesByTag($tag->id);
        $this->assertEquals(1, $count);
    }

    public function testGetArticlesByAuthor(): void
    {
        $userModel = new UserModel();
        $author = $userModel->where('username', 'admin')->first();
        $categoryModel = new CategoryModel();
        $category = $categoryModel->where('slug', 'politics')->first();

        $articleModel = new ArticleModel();

        // Create article by admin
        $articleModel->insert([
            'title_en'     => 'Admin Article',
            'title_hi'     => 'एडमिन लेख',
            'content_en'   => 'Admin content.',
            'content_hi'   => 'एडमिन सामग्री।',
            'slug'         => 'admin-article',
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'en',
            'news_section' => 'national',
            'status'       => 'published',
            'published_at' => date('Y-m-d H:i:s'),
        ]);

        $articles = $articleModel->getArticlesByAuthor($author->id, 12, 0);
        $this->assertGreaterThanOrEqual(1, count($articles));
        // Verify our specific article is in the results
        $slugs = array_column($articles, 'slug');
        $this->assertContains('admin-article', $slugs);

        $count = $articleModel->countArticlesByAuthor($author->id);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testSearchArticlesWithFilters(): void
    {
        $userModel = new UserModel();
        $author = $userModel->where('username', 'admin')->first();
        $categoryModel = new CategoryModel();
        $category = $categoryModel->where('slug', 'politics')->first();

        $articleModel = new ArticleModel();

        // Create article with specific date
        $articleModel->insert([
            'title_en'     => 'Filterable Search Article',
            'title_hi'     => 'फ़िल्टर करने योग्य खोज लेख',
            'content_en'   => 'Unique searchable content here.',
            'content_hi'   => 'अनूठी खोज योग्य सामग्री यहाँ।',
            'slug'         => 'filterable-search-article',
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'en',
            'news_section' => 'national',
            'status'       => 'published',
            'published_at' => '2026-06-01 12:00:00',
        ]);

        // Test search by text
        $results = $articleModel->searchArticles('Unique searchable', [], 12, 0);
        $this->assertGreaterThanOrEqual(1, count($results));

        // Test search by date range
        $results = $articleModel->searchArticles('', ['date_from' => '2026-06-01', 'date_to' => '2026-06-02'], 12, 0);
        $this->assertGreaterThanOrEqual(1, count($results));
        $slugs = array_column($results, 'slug');
        $this->assertContains('filterable-search-article', $slugs);

        // Test search by date range (before)
        $results = $articleModel->searchArticles('', ['date_from' => '2026-05-01', 'date_to' => '2026-05-31'], 12, 0);
        $this->assertCount(0, $results);

        // Test search by category slug
        $results = $articleModel->searchArticles('', ['category' => 'politics'], 12, 0);
        $this->assertGreaterThanOrEqual(1, count($results));

        // Test search by author username
        $results = $articleModel->searchArticles('', ['author' => 'admin'], 12, 0);
        $this->assertGreaterThanOrEqual(1, count($results));

        // Test search by language
        $results = $articleModel->searchArticles('', ['language' => 'en'], 12, 0);
        $this->assertGreaterThanOrEqual(1, count($results));

        // Test search count
        $count = $articleModel->searchArticlesCount('Filterable', ['category' => 'politics']);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testApproveAndPublishWorkflow(): void
    {
        $userModel = new UserModel();
        $author = $userModel->where('username', 'admin')->first();
        $categoryModel = new CategoryModel();
        $category = $categoryModel->where('slug', 'politics')->first();

        $articleModel = new ArticleModel();

        $articleId = $articleModel->insert([
            'title_en'     => 'Pending Article',
            'title_hi'     => 'लंबित लेख',
            'content_en'   => 'Pending content.',
            'content_hi'   => 'लंबित सामग्री।',
            'slug'         => 'pending-article',
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'en',
            'news_section' => 'national',
            'status'       => 'pending',
        ]);

        // Test pending articles listing
        $pending = $articleModel->getPendingArticles(20);
        $this->assertCount(1, $pending);
        $this->assertEquals('pending', $pending[0]->status);

        // Test approve
        $result = $articleModel->approveArticle($articleId, $author->id);
        $this->assertTrue($result);

        $article = $articleModel->find($articleId);
        $this->assertEquals('approved', $article->status);
        $this->assertEquals($author->id, $article->editor_id);

        // Test publish
        $result = $articleModel->publishArticle($articleId);
        $this->assertTrue($result);

        $article = $articleModel->find($articleId);
        $this->assertEquals('published', $article->status);
        $this->assertNotNull($article->published_at);
    }
}
