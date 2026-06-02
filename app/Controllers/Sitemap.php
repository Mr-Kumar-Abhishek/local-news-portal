<?php

namespace App\Controllers;

use App\Models\ArticleModel;
use App\Models\CategoryModel;
use App\Models\TagModel;

class Sitemap extends BaseController
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

    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        $articles   = $this->articleModel->getPublished([], 1000);
        $categories = $this->categoryModel->getActiveCategories();
        $tags       = $this->tagModel->findAll();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        $xml .= ' xmlns:xhtml="http://www.w3.org/1999/xhtml">';

        // Homepage
        $xml .= '<url>';
        $xml .= '<loc>' . base_url('/') . '</loc>';
        $xml .= '<priority>1.0</priority>';
        $xml .= '<changefreq>hourly</changefreq>';
        $xml .= '</url>';

        // English home
        $xml .= '<url>';
        $xml .= '<loc>' . base_url('/en') . '</loc>';
        $xml .= '<priority>1.0</priority>';
        $xml .= '<changefreq>hourly</changefreq>';
        $xml .= '</url>';

        // Hindi home
        $xml .= '<url>';
        $xml .= '<loc>' . base_url('/hi') . '</loc>';
        $xml .= '<priority>1.0</priority>';
        $xml .= '<changefreq>hourly</changefreq>';
        $xml .= '</url>';

        // Sections
        foreach (['international', 'national', 'local'] as $section) {
            foreach (['en', 'hi'] as $locale) {
                $xml .= '<url>';
                $xml .= '<loc>' . base_url('/' . $locale . '/section/' . $section) . '</loc>';
                $xml .= '<priority>0.8</priority>';
                $xml .= '<changefreq>hourly</changefreq>';
                $xml .= '</url>';
            }
        }

        // Categories
        foreach ($categories as $category) {
            foreach (['en', 'hi'] as $locale) {
                $xml .= '<url>';
                $xml .= '<loc>' . base_url('/' . $locale . '/category/' . $category->slug) . '</loc>';
                $xml .= '<priority>0.7</priority>';
                $xml .= '<changefreq>daily</changefreq>';
                $xml .= '</url>';
            }
        }

        // Tags
        foreach ($tags as $tag) {
            foreach (['en', 'hi'] as $locale) {
                $xml .= '<url>';
                $xml .= '<loc>' . base_url('/' . $locale . '/tag/' . $tag->slug) . '</loc>';
                $xml .= '<priority>0.5</priority>';
                $xml .= '<changefreq>weekly</changefreq>';
                $xml .= '</url>';
            }
        }

        // Articles
        foreach ($articles as $article) {
            foreach (['en', 'hi'] as $locale) {
                $xml .= '<url>';
                $xml .= '<loc>' . base_url('/' . $locale . '/news/' . $article->slug) . '</loc>';
                $xml .= '<lastmod>' . date('c', strtotime($article->updated_at ?? $article->created_at)) . '</lastmod>';
                $xml .= '<priority>0.6</priority>';
                $xml .= '<changefreq>weekly</changefreq>';
                $xml .= '</url>';
            }
        }

        $xml .= '</urlset>';

        return $this->response
                    ->setContentType('application/xml')
                    ->setBody($xml);
    }
}
