<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ArticleModel;
use App\Models\CategoryModel;
use App\Models\TagModel;

class News extends BaseController
{
    protected ArticleModel $articleModel;
    protected CategoryModel $categoryModel;
    protected TagModel $tagModel;

    protected int $perPage = 10;

    public function __construct()
    {
        $this->articleModel  = new ArticleModel();
        $this->categoryModel = new CategoryModel();
        $this->tagModel      = new TagModel();
    }

    /**
     * GET /api/news — List articles (paginated).
     *
     * Query params: page, per_page, category, tag, language, status
     */
    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = min(50, max(1, (int) ($this->request->getGet('per_page') ?? $this->perPage)));
        $offset  = ($page - 1) * $perPage;

        $filters = [];

        if ($category = $this->request->getGet('category')) {
            $filters['category_id'] = (int) $category;
        }

        if ($language = $this->request->getGet('language')) {
            $filters['language'] = $language;
        }

        $status = $this->request->getGet('status') ?? 'published';

        if ($tagSlug = $this->request->getGet('tag')) {
            $tag = $this->tagModel->where('slug', $tagSlug)->first();
            if ($tag) {
                $articles = $this->articleModel->getArticlesByTag($tag->id, $perPage, $offset);
                $total    = $this->articleModel->countArticlesByTag($tag->id);
            } else {
                $articles = [];
                $total    = 0;
            }
        } else {
            $articles = $this->articleModel->getPublished($filters, $perPage, $offset);
            $total    = $this->articleModel->countPublished($filters);
        }

        $totalPages = max(1, (int) ceil($total / $perPage));

        return $this->respond([
            'status' => 'success',
            'data'   => [
                'articles'   => $articles,
                'pagination' => [
                    'current_page' => $page,
                    'per_page'     => $perPage,
                    'total'        => $total,
                    'total_pages'  => $totalPages,
                    'has_next'     => $page < $totalPages,
                    'has_prev'     => $page > 1,
                ],
            ],
        ]);
    }

    /**
     * GET /api/news/(:segment) — Single article by slug.
     */
    public function show(string $slug): \CodeIgniter\HTTP\ResponseInterface
    {
        $article = $this->articleModel->getArticleBySlug($slug);

        if (!$article) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Article not found',
            ], 404);
        }

        // Attach tags
        $article->tags = $this->articleModel->getArticleTags($article->id);

        return $this->respond([
            'status' => 'success',
            'data'   => [
                'article' => $article,
            ],
        ]);
    }

    /**
     * GET /api/categories — List categories as a tree.
     */
    public function categories(): \CodeIgniter\HTTP\ResponseInterface
    {
        $tree = $this->categoryModel->getCategoryTree();

        return $this->respond([
            'status' => 'success',
            'data'   => [
                'categories' => array_values($tree),
            ],
        ]);
    }

    /**
     * GET /api/tags — List popular tags.
     */
    public function tags(): \CodeIgniter\HTTP\ResponseInterface
    {
        $limit = min(50, (int) ($this->request->getGet('limit') ?? 30));
        $tags  = $this->tagModel->getPopularTags($limit);

        return $this->respond([
            'status' => 'success',
            'data'   => [
                'tags' => $tags,
            ],
        ]);
    }

    /**
     * GET /api/search?q=... — Search articles.
     *
     * Query params: q, page, per_page, category, language
     */
    public function search(): \CodeIgniter\HTTP\ResponseInterface
    {
        $query = $this->request->getGet('q') ?? '';

        if (empty(trim($query))) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Search query parameter "q" is required.',
            ], 422);
        }

        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = min(50, max(1, (int) ($this->request->getGet('per_page') ?? $this->perPage)));
        $offset  = ($page - 1) * $perPage;

        $filters = [];

        if ($category = $this->request->getGet('category')) {
            $filters['category'] = $category;
        }

        if ($language = $this->request->getGet('language')) {
            $filters['language'] = $language;
        }

        $articles = $this->articleModel->searchArticles(trim($query), $filters, $perPage, $offset);
        $total    = $this->articleModel->searchArticlesCount(trim($query), $filters);

        $totalPages = max(1, (int) ceil($total / $perPage));

        return $this->respond([
            'status' => 'success',
            'data'   => [
                'query'      => trim($query),
                'articles'   => $articles,
                'pagination' => [
                    'current_page' => $page,
                    'per_page'     => $perPage,
                    'total'        => $total,
                    'total_pages'  => $totalPages,
                    'has_next'     => $page < $totalPages,
                    'has_prev'     => $page > 1,
                ],
            ],
        ]);
    }

    /**
     * Unified JSON response helper.
     */
    protected function respond(array $data, int $statusCode = 200): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->response
            ->setStatusCode($statusCode)
            ->setContentType('application/json')
            ->setJSON($data);
    }
}
