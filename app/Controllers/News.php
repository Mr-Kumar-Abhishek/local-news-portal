<?php

namespace App\Controllers;

use App\Models\ArticleModel;
use App\Models\CategoryModel;
use App\Models\TagModel;
use App\Models\CommentModel;

class News extends BaseController
{
    protected ArticleModel $articleModel;
    protected CategoryModel $categoryModel;
    protected TagModel $tagModel;
    protected CommentModel $commentModel;

    protected int $perPage = 10;

    public function __construct()
    {
        $this->articleModel  = new ArticleModel();
        $this->categoryModel = new CategoryModel();
        $this->tagModel      = new TagModel();
        $this->commentModel  = new CommentModel();
    }

    public function index(): string
    {
        $page    = $this->request->getGet('page') ?? 1;
        $offset  = ($page - 1) * $this->perPage;

        $filters = [
            'language' => $this->locale === 'hi' ? 'hi' : 'en',
        ];

        $articles = $this->articleModel->getPublished($filters, $this->perPage, $offset);
        $total    = $this->articleModel->countPublished($filters);

        $data = [
            'articles'      => $articles,
            'pager_links'   => $this->generatePagination($total, $page),
            'total'         => $total,
            'perPage'       => $this->perPage,
            'currentPage'   => $page,
            'categories'    => $this->categoryModel->getActiveCategories(),
            'tags'          => $this->tagModel->getPopularTags(10),
            'popular'       => $this->articleModel->getPopularArticles(5),
            'locale'        => $this->locale,
            'title'         => lang('News.all_news'),
        ];

        return view('templates/header', $data)
             . view('news/index', $data)
             . view('templates/sidebar', $data)
             . view('templates/footer');
    }

    public function view(string $slug): string
    {
        $article = $this->articleModel->getArticleBySlug($slug);

        if (!$article) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Increment view count
        $this->articleModel->incrementViewCount($article->id);

        $tags     = $this->articleModel->getArticleTags($article->id);
        $comments = $this->commentModel->getApprovedComments($article->id);
        $related  = $this->articleModel->getRelatedArticles($article->id, $article->category_id, 4);

        $data = [
            'article'   => $article,
            'tags'      => $tags,
            'comments'  => $comments,
            'related'   => $related,
            'categories' => $this->categoryModel->getActiveCategories(),
            'tags_sidebar' => $this->tagModel->getPopularTags(10),
            'popular'   => $this->articleModel->getPopularArticles(5),
            'locale'    => $this->locale,
            'title'     => $this->locale === 'hi' ? $article->title_hi : $article->title_en,
            'meta_description' => $this->locale === 'hi' ? $article->excerpt_hi : $article->excerpt_en,
        ];

        return view('templates/header', $data)
             . view('news/view', $data)
             . view('templates/sidebar', $data)
             . view('templates/footer');
    }

    public function category(string $slug): string
    {
        $category = $this->categoryModel->where('slug', $slug)->where('status', 1)->first();

        if (!$category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $page   = $this->request->getGet('page') ?? 1;
        $offset = ($page - 1) * $this->perPage;

        $filters = ['category_id' => $category->id];
        $articles = $this->articleModel->getPublished($filters, $this->perPage, $offset);
        $total    = $this->articleModel->countPublished($filters);

        $data = [
            'category'      => $category,
            'articles'      => $articles,
            'pager_links'   => $this->generatePagination($total, $page),
            'total'         => $total,
            'currentPage'   => $page,
            'categories'    => $this->categoryModel->getActiveCategories(),
            'tags'          => $this->tagModel->getPopularTags(10),
            'popular'       => $this->articleModel->getPopularArticles(5),
            'locale'        => $this->locale,
            'title'         => $this->locale === 'hi' ? $category->name_hi : $category->name_en,
        ];

        return view('templates/header', $data)
             . view('news/category', $data)
             . view('templates/sidebar', $data)
             . view('templates/footer');
    }

    public function section(string $section): string
    {
        if (!in_array($section, ['international', 'national', 'local'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $page   = $this->request->getGet('page') ?? 1;
        $offset = ($page - 1) * $this->perPage;

        $filters = ['news_section' => $section];
        $articles = $this->articleModel->getPublished($filters, $this->perPage, $offset);
        $total    = $this->articleModel->countPublished($filters);

        $sectionNames = [
            'international' => ['en' => 'International', 'hi' => 'अंतरराष्ट्रीय'],
            'national'      => ['en' => 'National', 'hi' => 'राष्ट्रीय'],
            'local'         => ['en' => 'Local', 'hi' => 'स्थानीय'],
        ];

        $data = [
            'section'       => $section,
            'section_name'  => $sectionNames[$section][$this->locale],
            'articles'      => $articles,
            'pager_links'   => $this->generatePagination($total, $page),
            'total'         => $total,
            'currentPage'   => $page,
            'categories'    => $this->categoryModel->getActiveCategories(),
            'tags'          => $this->tagModel->getPopularTags(10),
            'popular'       => $this->articleModel->getPopularArticles(5),
            'locale'        => $this->locale,
            'title'         => $sectionNames[$section][$this->locale],
        ];

        return view('templates/header', $data)
             . view('news/section', $data)
             . view('templates/sidebar', $data)
             . view('templates/footer');
    }

    public function comment(int $articleId): \CodeIgniter\HTTP\RedirectResponse
    {
        $rules = [
            'author_name'  => 'permit_empty|max_length[100]',
            'author_email' => 'permit_empty|valid_email|max_length[100]',
            'body'         => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->commentModel->insert([
            'article_id'   => $articleId,
            'user_id'      => $this->getCurrentUserId(),
            'author_name'  => $this->request->getPost('author_name'),
            'author_email' => $this->request->getPost('author_email'),
            'body'         => $this->request->getPost('body'),
            'status'       => 'pending',
        ]);

        return redirect()->back()->with('message', lang('News.comment_success'));
    }

    protected function generatePagination(int $total, int $currentPage): array
    {
        $totalPages = max(1, ceil($total / $this->perPage));
        $links = [];

        for ($i = 1; $i <= $totalPages; $i++) {
            $links[] = [
                'page'    => $i,
                'url'     => current_url() . '?page=' . $i,
                'active'  => $i === (int) $currentPage,
            ];
        }

        return $links;
    }
}
