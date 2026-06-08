<?php

namespace App\Controllers;

use App\Models\ArticleModel;
use App\Models\CategoryModel;
use App\Models\TagModel;
use App\Models\CommentModel;
use App\Models\UserModel;

class News extends BaseController
{
    protected ArticleModel $articleModel;
    protected CategoryModel $categoryModel;
    protected TagModel $tagModel;
    protected CommentModel $commentModel;
    protected UserModel $userModel;

    protected int $perPage = 10;

    public function __construct()
    {
        $this->articleModel  = new ArticleModel();
        $this->categoryModel = new CategoryModel();
        $this->tagModel      = new TagModel();
        $this->commentModel  = new CommentModel();
        $this->userModel     = new UserModel();
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
            'breadcrumbs'   => [
                ['label' => lang('News.nav_home'), 'url' => '/' . $this->locale],
                ['label' => lang('News.all_news'), 'url' => null],
            ],
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
        $comments = $this->commentModel->getThreadedComments($article->id);
        $related  = $this->articleModel->getRelatedArticles($article->id, $article->category_id, 4);

        $articleTitle = $this->locale === 'hi' ? $article->title_hi : $article->title_en;

        $breadcrumbs = [
            ['label' => lang('News.nav_home'), 'url' => '/' . $this->locale],
            ['label' => lang('News.all_news'), 'url' => '/' . $this->locale . '/news'],
        ];

        if ($article->category_name) {
            $breadcrumbs[] = [
                'label' => $this->locale === 'hi' ? $article->category_name_hi : $article->category_name,
                'url'   => '/' . $this->locale . '/category/' . $article->category_slug,
            ];
        }

        $breadcrumbs[] = ['label' => $articleTitle, 'url' => null];

        $data = [
            'article'     => $article,
            'tags'        => $tags,
            'comments'    => $comments,
            'related'     => $related,
            'categories'  => $this->categoryModel->getActiveCategories(),
            'tags_sidebar' => $this->tagModel->getPopularTags(10),
            'popular'     => $this->articleModel->getPopularArticles(5),
            'locale'      => $this->locale,
            'title'       => $articleTitle,
            'meta_description' => $this->locale === 'hi' ? $article->excerpt_hi : $article->excerpt_en,
            'breadcrumbs' => $breadcrumbs,
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

        $categoryName = $this->locale === 'hi' ? $category->name_hi : $category->name_en;

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
            'title'         => $categoryName,
            'breadcrumbs'   => [
                ['label' => lang('News.nav_home'), 'url' => '/' . $this->locale],
                ['label' => $categoryName, 'url' => null],
            ],
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

        $sectionName = $sectionNames[$section][$this->locale];

        $data = [
            'section'       => $section,
            'section_name'  => $sectionName,
            'articles'      => $articles,
            'pager_links'   => $this->generatePagination($total, $page),
            'total'         => $total,
            'currentPage'   => $page,
            'categories'    => $this->categoryModel->getActiveCategories(),
            'tags'          => $this->tagModel->getPopularTags(10),
            'popular'       => $this->articleModel->getPopularArticles(5),
            'locale'        => $this->locale,
            'title'         => $sectionName,
            'breadcrumbs'   => [
                ['label' => lang('News.nav_home'), 'url' => '/' . $this->locale],
                ['label' => $sectionName, 'url' => null],
            ],
        ];

        return view('templates/header', $data)
             . view('news/section', $data)
             . view('templates/sidebar', $data)
             . view('templates/footer');
    }

    public function tag(string $slug): string
    {
        $tag = $this->tagModel->where('slug', $slug)->first();

        if (!$tag) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $page   = $this->request->getGet('page') ?? 1;
        $offset = ($page - 1) * $this->perPage;

        $articles = $this->articleModel->getArticlesByTag($tag->id, $this->perPage, $offset);
        $total    = $this->articleModel->countArticlesByTag($tag->id);

        $tagName = $this->locale === 'hi' ? $tag->name_hi : $tag->name_en;

        $data = [
            'tag'           => $tag,
            'tag_name'      => $tagName,
            'articles'      => $articles,
            'pager_links'   => $this->generatePagination($total, $page),
            'total'         => $total,
            'currentPage'   => $page,
            'categories'    => $this->categoryModel->getActiveCategories(),
            'tags'          => $this->tagModel->getPopularTags(10),
            'popular'       => $this->articleModel->getPopularArticles(5),
            'locale'        => $this->locale,
            'title'         => lang('News.tags') . ': ' . $tagName,
            'breadcrumbs'   => [
                ['label' => lang('News.nav_home'), 'url' => '/' . $this->locale],
                ['label' => lang('News.tags'), 'url' => null],
                ['label' => $tagName, 'url' => null],
            ],
        ];

        return view('templates/header', $data)
             . view('news/tag', $data)
             . view('templates/sidebar', $data)
             . view('templates/footer');
    }

    public function author(string $username): string
    {
        $author = $this->userModel->getUserByUsername($username);

        if (!$author) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $page   = $this->request->getGet('page') ?? 1;
        $offset = ($page - 1) * $this->perPage;

        $articles = $this->articleModel->getArticlesByAuthor($author->id, $this->perPage, $offset);
        $total    = $this->articleModel->countArticlesByAuthor($author->id);

        $displayName = $author->full_name ?: $author->username;

        $data = [
            'author'        => $author,
            'display_name'  => $displayName,
            'articles'      => $articles,
            'pager_links'   => $this->generatePagination($total, $page),
            'total'         => $total,
            'currentPage'   => $page,
            'categories'    => $this->categoryModel->getActiveCategories(),
            'tags'          => $this->tagModel->getPopularTags(10),
            'popular'       => $this->articleModel->getPopularArticles(5),
            'locale'        => $this->locale,
            'title'         => lang('News.by') . ' ' . $displayName,
            'breadcrumbs'   => [
                ['label' => lang('News.nav_home'), 'url' => '/' . $this->locale],
                ['label' => $displayName, 'url' => null],
            ],
        ];

        return view('templates/header', $data)
             . view('news/author', $data)
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

        $data = [
            'article_id'   => $articleId,
            'user_id'      => $this->getCurrentUserId(),
            'author_name'  => $this->request->getPost('author_name'),
            'author_email' => $this->request->getPost('author_email'),
            'body'         => $this->request->getPost('body'),
            'status'       => 'pending',
        ];

        $parentId = $this->request->getPost('parent_id');
        if (!empty($parentId)) {
            $data['parent_id'] = (int) $parentId;
        }

        $this->commentModel->insert($data);

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
