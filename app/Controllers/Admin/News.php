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

            $status = $this->request->getPost('save_type') === 'publish' ? 'published' : 'draft';

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
            ]);

            if ($articleId) {
                // Sync tags
                $tagIds = $this->request->getPost('tags') ?? [];
                $tagModel->syncTags($articleId, $tagIds);

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

            $status = $this->request->getPost('save_type') === 'publish' ? 'published' : 'draft';

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
            ]);

            // Sync tags
            $tagIds = $this->request->getPost('tags') ?? [];
            $tagModel->syncTags($id, $tagIds);

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
        $articleModel->delete($id);

        return redirect()->to('/' . $this->locale . '/admin/news')
                       ->with('message', 'Article deleted successfully');
    }
}
