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
        $query  = $this->request->getGet('q') ?? '';
        $page   = $this->request->getGet('page') ?? 1;
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $articles = [];
        $total    = 0;

        if (!empty(trim($query))) {
            $articles = $this->articleModel->searchArticles(trim($query), $limit);
            $total    = $this->articleModel->countPublished(['search' => trim($query)]);
        }

        $data = [
            'query'       => $query,
            'articles'    => $articles,
            'total'       => $total,
            'categories'  => $this->categoryModel->getActiveCategories(),
            'tags'        => $this->tagModel->getPopularTags(10),
            'popular'     => $this->articleModel->getPopularArticles(5),
            'locale'      => $this->locale,
            'title'       => lang('News.search_results') . ': ' . esc($query),
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

        $results = $this->articleModel->searchArticles(trim($query), 10);
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
