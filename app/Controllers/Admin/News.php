<?php

namespace App\Controllers\Admin;

use App\Models\ArticleModel;
use App\Models\CategoryModel;
use App\Models\TagModel;

class News extends BaseController
{
    public function index(): string
    {
        $articleModel = new ArticleModel();
        $categoryModel = new CategoryModel();

        $data = [
            'locale'     => $this->locale,
            'title'      => 'News Management',
            'articles'   => $articleModel->getAllWithDetails(),
            'categories' => $categoryModel->asArray()->getActiveCategories(),
            'user_name'  => $this->getCurrentUserName(),
        ];

        return view('admin/templates/header', $data)
             . view('admin/news/index', $data)
             . view('admin/templates/footer');
    }

    public function create(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $categoryModel = new CategoryModel();
        $tagModel      = new TagModel();

        $data = [
            'locale'     => $this->locale,
            'title'      => 'Create News',
            'categories' => $categoryModel->asArray()->getParentCategories(),
            'all_tags'   => $tagModel->findAll(),
            'user_name'  => $this->getCurrentUserName(),
        ];

        if ($this->request->getMethod() === 'POST') {
            $articleModel = new ArticleModel();

            $rules = [
                'title_en' => 'required|max_length[500]',
                'title_hi' => 'required|max_length[500]',
                'slug'     => 'required|max_length[500]|is_unique[articles.slug]',
            ];

            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
                return view('admin/templates/header', $data)
                     . view('admin/news/create', $data)
                     . view('admin/templates/footer');
            }

            $slug = $this->request->getPost('slug');
            $slug = url_title($slug, '-', true);

            $userRole = $this->getCurrentUserRole();
            $saveType = $this->request->getPost('save_type');

            if ($saveType === 'publish') {
                $status = ($userRole === 'admin') ? 'published' : 'pending';
            } else {
                $status = 'draft';
            }

            // Checkbox fields default to 0 if not present in POST
            $isFeatured    = $this->request->getPost('is_featured') ? 1 : 0;
            $isBreaking    = $this->request->getPost('is_breaking') ? 1 : 0;
            $allowComments = $this->request->getPost('allow_comments') ? 1 : 0;

            $articleId = $articleModel->insert([
                'title_en'       => $this->request->getPost('title_en'),
                'title_hi'       => $this->request->getPost('title_hi'),
                'content_en'     => $this->request->getPost('content_en'),
                'content_hi'     => $this->request->getPost('content_hi'),
                'slug'           => $slug,
                'excerpt_en'     => $this->request->getPost('excerpt_en'),
                'excerpt_hi'     => $this->request->getPost('excerpt_hi'),
                'featured_image' => $this->request->getPost('featured_image'),
                'category_id'    => $this->request->getPost('category_id'),
                'author_id'      => $this->getCurrentUserId(),
                'language'       => $this->request->getPost('language') ?? 'both',
                'news_section'   => $this->request->getPost('news_section') ?? 'local',
                'status'         => $status,
                'published_at'   => $status === 'published' ? date('Y-m-d H:i:s') : null,
                'featured'       => $this->request->getPost('featured') ? 1 : 0,
                'is_featured'    => $isFeatured,
                'is_breaking'    => $isBreaking,
                'allow_comments' => $allowComments,
            ]);

            if ($articleId) {
                // Sync tags
                $tagIds = $this->request->getPost('tags') ?? [];
                $tagModel->syncTags($articleId, $tagIds);

                // Log activity
                $activityLog = new \App\Models\ActivityLogModel();
                $activityLog->log([
                    'user_id' => $this->getCurrentUserId(),
                    'action' => 'article_created',
                    'entity_type' => 'article',
                    'entity_id' => $articleId,
                    'description' => "Article '{$slug}' created",
                    'ip_address' => $this->request->getIPAddress(),
                ]);

                return redirect()->to('/' . $this->locale . '/admin/news')
                               ->with('message', 'Article created successfully');
            }
        }

        return view('admin/templates/header', $data)
             . view('admin/news/create', $data)
             . view('admin/templates/footer');
    }

    public function edit(int $id): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $articleModel = new ArticleModel();
        $categoryModel = new CategoryModel();
        $tagModel      = new TagModel();

        $article = $articleModel->find($id);
        if (!$article) {
            return redirect()->to('/' . $this->locale . '/admin/news')
                           ->with('error', 'Article not found');
        }

        $articleTags = $articleModel->getArticleTags($id);
        $articleTagIds = array_map(fn($t) => $t->id, $articleTags);

        $data = [
            'locale'         => $this->locale,
            'title'          => 'Edit News',
            'article'        => $article,
            'categories'     => $categoryModel->asArray()->getParentCategories(),
            'all_tags'       => $tagModel->findAll(),
            'article_tag_ids' => $articleTagIds,
            'user_name'      => $this->getCurrentUserName(),
        ];

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'title_en' => 'required|max_length[500]',
                'title_hi' => 'required|max_length[500]',
            ];

            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
                return view('admin/templates/header', $data)
                     . view('admin/news/edit', $data)
                     . view('admin/templates/footer');
            }

            $slug = $this->request->getPost('slug') ?? $article->slug;
            $slug = url_title($slug, '-', true);

            $userRole = $this->getCurrentUserRole();
            $saveType = $this->request->getPost('save_type');

            if ($saveType === 'publish') {
                $status = ($userRole === 'admin') ? 'published' : 'pending';
            } else {
                $status = 'draft';
            }

            // Checkbox fields default to 0 if not present in POST
            $isFeatured    = $this->request->getPost('is_featured') ? 1 : 0;
            $isBreaking    = $this->request->getPost('is_breaking') ? 1 : 0;
            $allowComments = $this->request->getPost('allow_comments') ? 1 : 0;

            $articleModel->update($id, [
                'title_en'       => $this->request->getPost('title_en'),
                'title_hi'       => $this->request->getPost('title_hi'),
                'content_en'     => $this->request->getPost('content_en'),
                'content_hi'     => $this->request->getPost('content_hi'),
                'slug'           => $slug,
                'excerpt_en'     => $this->request->getPost('excerpt_en'),
                'excerpt_hi'     => $this->request->getPost('excerpt_hi'),
                'featured_image' => $this->request->getPost('featured_image'),
                'category_id'    => $this->request->getPost('category_id'),
                'language'       => $this->request->getPost('language') ?? 'both',
                'news_section'   => $this->request->getPost('news_section') ?? 'local',
                'status'         => $status,
                'published_at'   => $status === 'published' && !$article->published_at ? date('Y-m-d H:i:s') : $article->published_at,
                'featured'       => $this->request->getPost('featured') ? 1 : 0,
                'is_featured'    => $isFeatured,
                'is_breaking'    => $isBreaking,
                'allow_comments' => $allowComments,
            ]);

            // Sync tags
            $tagIds = $this->request->getPost('tags') ?? [];
            $tagModel->syncTags($id, $tagIds);

            // Log activity
            $activityLog = new \App\Models\ActivityLogModel();
            $activityLog->log([
                'user_id' => $this->getCurrentUserId(),
                'action' => 'article_updated',
                'entity_type' => 'article',
                'entity_id' => $id,
                'description' => "Article '{$slug}' updated",
                'ip_address' => $this->request->getIPAddress(),
            ]);

            // If article status changed to pending, notify admins
            if ($status === 'pending' && $article->status !== 'pending') {
                $userModel = new \App\Models\UserModel();
                $admins = $userModel->where('role', 'admin')->where('status', 1)->findAll();
                $articleTitle = $this->request->getPost('title_en');
                foreach ($admins as $admin) {
                    $message = view('email/article_pending', [
                        'article_title' => $articleTitle,
                        'article_id' => $id,
                        'locale' => $this->locale,
                    ]);
                    send_email($admin->email, 'Article Pending Review: ' . $articleTitle, $message);
                }
            }

            return redirect()->to('/' . $this->locale . '/admin/news')
                           ->with('message', 'Article updated successfully');
        }

        return view('admin/templates/header', $data)
             . view('admin/news/edit', $data)
             . view('admin/templates/footer');
    }

    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $articleModel = new ArticleModel();
        $article = $articleModel->find($id);
        $articleModel->delete($id);

        // Log activity
        if ($article) {
            $activityLog = new \App\Models\ActivityLogModel();
            $activityLog->log([
                'user_id' => $this->getCurrentUserId(),
                'action' => 'article_deleted',
                'entity_type' => 'article',
                'entity_id' => $id,
                'description' => "Article '{$article->title_en}' deleted",
                'ip_address' => $this->request->getIPAddress(),
            ]);
        }

        return redirect()->to('/' . $this->locale . '/admin/news')
                       ->with('message', 'Article deleted successfully');
    }

    public function pending(): string
    {
        $articleModel = new ArticleModel();

        $data = [
            'locale'    => $this->locale,
            'title'     => 'Pending Review',
            'articles'  => $articleModel->getPendingArticles(),
            'user_name' => $this->getCurrentUserName(),
        ];

        return view('admin/templates/header', $data)
             . view('admin/news/index', $data)
             . view('admin/templates/footer');
    }

    public function approve(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $articleModel = new ArticleModel();
        $article = $articleModel->find($id);

        $articleModel->approveArticle($id, (int) $this->getCurrentUserId());

        // Log activity
        if ($article) {
            $activityLog = new \App\Models\ActivityLogModel();
            $activityLog->log([
                'user_id' => $this->getCurrentUserId(),
                'action' => 'article_approved',
                'entity_type' => 'article',
                'entity_id' => $id,
                'description' => "Article '{$article->title_en}' approved",
                'ip_address' => $this->request->getIPAddress(),
            ]);
        }

        return redirect()->to('/' . $this->locale . '/admin/news')
                       ->with('message', 'Article approved successfully');
    }

    public function publish(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $articleModel = new ArticleModel();
        $article = $articleModel->find($id);

        $articleModel->publishArticle($id);

        // Log activity
        if ($article) {
            $activityLog = new \App\Models\ActivityLogModel();
            $activityLog->log([
                'user_id' => $this->getCurrentUserId(),
                'action' => 'article_published',
                'entity_type' => 'article',
                'entity_id' => $id,
                'description' => "Article '{$article->title_en}' published",
                'ip_address' => $this->request->getIPAddress(),
            ]);
        }

        return redirect()->to('/' . $this->locale . '/admin/news')
                       ->with('message', 'Article published successfully');
    }
}
