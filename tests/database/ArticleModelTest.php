<?php

namespace Tests\Database;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\ArticleModel;
use App\Models\UserModel;
use App\Models\CategoryModel;
use App\Database\Seeds\HindBiharSeeder;

/**
 * @internal
 */
final class ArticleModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate   = true;
    protected $namespace = null;
    protected $seed      = HindBiharSeeder::class;

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
}
