<?php

namespace App\Controllers;

use App\Models\ArticleModel;
use App\Models\CategoryModel;
use App\Models\TagModel;

class Home extends BaseController
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
        $data['featured_news']   = $this->articleModel->getFeaturedArticles(5);
        $data['latest_news']     = $this->articleModel->getLatestArticles(9);
        $data['international']   = $this->articleModel->getArticlesBySection('international', 4);
        $data['national']        = $this->articleModel->getArticlesBySection('national', 4);
        $data['local']           = $this->articleModel->getArticlesBySection('local', 4);
        $data['popular_articles'] = $this->articleModel->getPopularArticles(5);
        $data['categories']      = $this->categoryModel->getActiveCategories();
        $data['tags']            = $this->tagModel->getPopularTags(10);
        $data['locale']          = $this->locale;
        $data['title']           = lang('News.home_title');

        return view('templates/header', $data)
             . view('home/index', $data)
             . view('templates/sidebar', $data)
             . view('templates/footer');
    }
}
