<?php

namespace App\Controllers;

use App\Models\ArticleModel;
use App\Models\CategoryModel;
use App\Models\TagModel;

class Search extends BaseController
{
    protected ArticleModel $articleModel;
    protected CategoryModel $categoryModel;
    protected TagModel $tagModel;

    public function __construct()
    {
        $this->articleModel  = new ArticleModel();
        $this->categoryModel = new CategoryModel();
        $this->tagModel      = new TagModel();
    }

    public function index(): string
    {
        $query    = $this->request->getGet('q') ?? '';
        $page     = $this->request->getGet('page') ?? 1;
        $limit    = 12;
        $offset   = ($page - 1) * $limit;

        // Advanced filters
        $dateFrom  = $this->request->getGet('date_from') ?? '';
        $dateTo    = $this->request->getGet('date_to') ?? '';
        $category  = $this->request->getGet('category') ?? '';
        $author    = $this->request->getGet('author') ?? '';
        $language  = $this->request->getGet('language') ?? '';

        $articles = [];
        $total    = 0;

        if (!empty(trim($query)) || !empty($dateFrom) || !empty($dateTo) || !empty($category) || !empty($author) || !empty($language)) {
            $filters = [
                'search'    => trim($query),
                'date_from' => $dateFrom,
                'date_to'   => $dateTo,
                'category'  => $category,
                'author'    => $author,
                'language'  => $language,
            ];

            $articles = $this->articleModel->searchArticles(trim($query), $filters, $limit, $offset);
            $total    = $this->articleModel->searchArticlesCount(trim($query), $filters);
        }

        $data = [
            'query'       => $query,
            'filters'     => [
                'date_from' => $dateFrom,
                'date_to'   => $dateTo,
                'category'  => $category,
                'author'    => $author,
                'language'  => $language,
            ],
            'articles'    => $articles,
            'total'       => $total,
            'categories'  => $this->categoryModel->getActiveCategories(),
            'tags'        => $this->tagModel->getPopularTags(10),
            'popular'     => $this->articleModel->getPopularArticles(5),
            'locale'      => $this->locale,
            'title'       => $query ? lang('News.search_results') . ': ' . esc($query) : lang('News.search'),
            'breadcrumbs' => [
                ['label' => lang('News.nav_home'), 'url' => '/' . $this->locale],
                ['label' => lang('News.search_results'), 'url' => null],
            ],
        ];

        return view('templates/header', $data)
             . view('search/index', $data)
             . view('templates/sidebar', $data)
             . view('templates/footer');
    }

    public function autocomplete(): \CodeIgniter\HTTP\ResponseInterface
    {
        $query = $this->request->getGet('q') ?? '';

        if (empty(trim($query))) {
            return $this->response->setJSON([]);
        }

        $results = $this->articleModel->searchArticles(trim($query), [], 10);
        $suggestions = [];

        foreach ($results as $article) {
            $title = $this->locale === 'hi' ? $article->title_hi : $article->title_en;
            $suggestions[] = [
                'value' => $title,
                'url'   => '/' . $this->locale . '/news/' . $article->slug,
            ];
        }

        return $this->response->setJSON($suggestions);
    }
}
