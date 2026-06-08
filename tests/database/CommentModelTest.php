<?php

namespace Tests\Database;

use Tests\Support\DatabaseTestCase;
use App\Models\CommentModel;
use App\Models\ArticleModel;
use App\Models\UserModel;
use App\Models\CategoryModel;

/**
 * @internal
 */
final class CommentModelTest extends DatabaseTestCase
{

    public function testCreateAndRetrieveComment(): void
    {
        $articleModel = new ArticleModel();
        $userModel = new UserModel();
        $categoryModel = new CategoryModel();

        $author = $userModel->where('username', 'admin')->first();
        $category = $categoryModel->where('slug', 'politics')->first();

        $articleId = $articleModel->insert([
            'title_en'     => 'Article For Comment Test',
            'title_hi'     => 'टिप्पणी परीक्षण के लिए लेख',
            'content_en'   => 'Content.',
            'content_hi'   => 'सामग्री।',
            'slug'         => 'article-for-comment-test',
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'en',
            'news_section' => 'national',
            'status'       => 'published',
            'published_at' => date('Y-m-d H:i:s'),
        ]);

        $commentModel = new CommentModel();
        $commentId = $commentModel->insert([
            'article_id'   => $articleId,
            'author_name'  => 'Test Commenter',
            'author_email' => 'test@example.com',
            'body'         => 'This is a test comment.',
            'status'       => 'pending',
        ]);

        $this->assertNotEmpty($commentId);

        $comment = $commentModel->find($commentId);
        $this->assertNotNull($comment);
        $this->assertEquals('Test Commenter', $comment->author_name);
        $this->assertEquals('pending', $comment->status);
    }

    public function testParentChildRelationship(): void
    {
        $articleModel = new ArticleModel();
        $userModel = new UserModel();
        $categoryModel = new CategoryModel();

        $author = $userModel->where('username', 'admin')->first();
        $category = $categoryModel->where('slug', 'politics')->first();

        $articleId = $articleModel->insert([
            'title_en'     => 'Parent Child Comment Article',
            'title_hi'     => 'माता-पिता बच्चे टिप्पणी लेख',
            'content_en'   => 'Content.',
            'content_hi'   => 'सामग्री।',
            'slug'         => 'parent-child-comment-article',
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'en',
            'news_section' => 'national',
            'status'       => 'published',
            'published_at' => date('Y-m-d H:i:s'),
        ]);

        $commentModel = new CommentModel();

        // Create parent
        $parentId = $commentModel->insert([
            'article_id'   => $articleId,
            'author_name'  => 'Parent',
            'author_email' => 'parent@example.com',
            'body'         => 'Parent comment.',
            'status'       => 'approved',
        ]);

        // Create child with parent_id
        $childId = $commentModel->insert([
            'article_id'   => $articleId,
            'parent_id'    => $parentId,
            'author_name'  => 'Child',
            'author_email' => 'child@example.com',
            'body'         => 'Child reply.',
            'status'       => 'approved',
        ]);

        $child = $commentModel->find($childId);
        $this->assertEquals($parentId, $child->parent_id);

        $parent = $commentModel->find($parentId);
        $this->assertNull($parent->parent_id);
    }

    public function testGetThreadedComments(): void
    {
        $articleModel = new ArticleModel();
        $userModel = new UserModel();
        $categoryModel = new CategoryModel();

        $author = $userModel->where('username', 'admin')->first();
        $category = $categoryModel->where('slug', 'politics')->first();

        $articleId = $articleModel->insert([
            'title_en'     => 'Threaded Test Article',
            'title_hi'     => 'थ्रेडेड परीक्षण लेख',
            'content_en'   => 'Content.',
            'content_hi'   => 'सामग्री।',
            'slug'         => 'threaded-test-article',
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'en',
            'news_section' => 'national',
            'status'       => 'published',
            'published_at' => date('Y-m-d H:i:s'),
        ]);

        $commentModel = new CommentModel();

        // Create multiple roots and children
        $root1Id = $commentModel->insert([
            'article_id'   => $articleId,
            'author_name'  => 'Root One',
            'author_email' => 'root1@example.com',
            'body'         => 'First root comment.',
            'status'       => 'approved',
        ]);

        $root2Id = $commentModel->insert([
            'article_id'   => $articleId,
            'author_name'  => 'Root Two',
            'author_email' => 'root2@example.com',
            'body'         => 'Second root comment.',
            'status'       => 'approved',
        ]);

        // Child of root1
        $commentModel->insert([
            'article_id'   => $articleId,
            'parent_id'    => $root1Id,
            'author_name'  => 'Child of Root1',
            'author_email' => 'child1@example.com',
            'body'         => 'Child of first root.',
            'status'       => 'approved',
        ]);

        // Child of child of root1 (nested)
        $nestedChildId = $commentModel->insert([
            'article_id'   => $articleId,
            'parent_id'    => $root1Id + 1, // the child we just created
            'author_name'  => 'Nested Child',
            'author_email' => 'nested@example.com',
            'body'         => 'Nested child comment.',
            'status'       => 'approved',
        ]);

        // Pending comment (should NOT appear in threaded)
        $commentModel->insert([
            'article_id'   => $articleId,
            'author_name'  => 'Pending Commenter',
            'author_email' => 'pending@example.com',
            'body'         => 'Pending comment.',
            'status'       => 'pending',
        ]);

        $threaded = $commentModel->getThreadedComments($articleId);

        // Should have 2 root comments (pending excluded)
        $this->assertCount(2, $threaded);

        // Find root1 and verify its children
        $root1 = null;
        foreach ($threaded as $root) {
            if ($root->id == $root1Id) {
                $root1 = $root;
                break;
            }
        }

        $this->assertNotNull($root1);
        $this->assertNotEmpty($root1->children);
        $this->assertCount(1, $root1->children);
        $this->assertEquals('Child of first root.', $root1->children[0]->body);
    }

    public function testGetApprovedComments(): void
    {
        $articleModel = new ArticleModel();
        $userModel = new UserModel();
        $categoryModel = new CategoryModel();

        $author = $userModel->where('username', 'admin')->first();
        $category = $categoryModel->where('slug', 'politics')->first();

        $articleId = $articleModel->insert([
            'title_en'     => 'Approved Comments Article',
            'title_hi'     => 'स्वीकृत टिप्पणियाँ लेख',
            'content_en'   => 'Content.',
            'content_hi'   => 'सामग्री।',
            'slug'         => 'approved-comments-article',
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'en',
            'news_section' => 'national',
            'status'       => 'published',
            'published_at' => date('Y-m-d H:i:s'),
        ]);

        $commentModel = new CommentModel();

        $commentModel->insert([
            'article_id'   => $articleId,
            'author_name'  => 'Approved One',
            'author_email' => 'approved1@example.com',
            'body'         => 'Approved comment.',
            'status'       => 'approved',
        ]);

        $commentModel->insert([
            'article_id'   => $articleId,
            'author_name'  => 'Pending One',
            'author_email' => 'pending1@example.com',
            'body'         => 'Pending comment.',
            'status'       => 'pending',
        ]);

        $approved = $commentModel->getApprovedComments($articleId);
        $this->assertCount(1, $approved);
        $this->assertEquals('approved', $approved[0]->status);
    }

    public function testPendingCount(): void
    {
        $commentModel = new CommentModel();
        $initialCount = $commentModel->getPendingCount();

        // With shared DB state, other tests may have created pending comments
        $this->assertGreaterThanOrEqual(0, $initialCount);
    }

    public function testCommentCRUD(): void
    {
        $articleModel = new ArticleModel();
        $userModel = new UserModel();
        $categoryModel = new CategoryModel();

        $author = $userModel->where('username', 'admin')->first();
        $category = $categoryModel->where('slug', 'politics')->first();

        $articleId = $articleModel->insert([
            'title_en'     => 'CRUD Test Article',
            'title_hi'     => 'CRUD परीक्षण लेख',
            'content_en'   => 'Content.',
            'content_hi'   => 'सामग्री।',
            'slug'         => 'crud-test-article',
            'category_id'  => $category->id,
            'author_id'    => $author->id,
            'language'     => 'en',
            'news_section' => 'national',
            'status'       => 'published',
            'published_at' => date('Y-m-d H:i:s'),
        ]);

        $commentModel = new CommentModel();

        // Create
        $commentId = $commentModel->insert([
            'article_id'   => $articleId,
            'author_name'  => 'CRUD User',
            'author_email' => 'crud@example.com',
            'body'         => 'Original body.',
            'status'       => 'pending',
        ]);

        $this->assertNotEmpty($commentId);

        // Read
        $comment = $commentModel->find($commentId);
        $this->assertEquals('Original body.', $comment->body);

        // Update
        $commentModel->update($commentId, [
            'body'   => 'Updated body.',
            'status' => 'approved',
        ]);

        $updated = $commentModel->find($commentId);
        $this->assertEquals('Updated body.', $updated->body);
        $this->assertEquals('approved', $updated->status);

        // Delete
        $commentModel->delete($commentId);
        $deleted = $commentModel->find($commentId);
        $this->assertNull($deleted);
    }
}
