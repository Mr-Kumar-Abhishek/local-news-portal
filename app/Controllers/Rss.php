<?php

namespace App\Controllers;

use App\Models\ArticleModel;
use App\Models\CategoryModel;

class Rss extends BaseController
{
    protected ArticleModel $articleModel;
    protected CategoryModel $categoryModel;

    public function __construct()
    {
        $this->articleModel  = new ArticleModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        $articles = $this->articleModel->getLatestArticles(50);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
        $xml .= '<channel>';
        $xml .= '<title>Hind Bihar News</title>';
        $xml .= '<link>' . base_url() . '</link>';
        $xml .= '<description>Latest news from Hind Bihar - Your trusted news source</description>';
        $xml .= '<language>' . $this->locale . '</language>';
        $xml .= '<atom:link href="' . base_url('/rss') . '" rel="self" type="application/rss+xml"/>';

        foreach ($articles as $article) {
            $title   = $this->locale === 'hi' ? $article->title_hi : $article->title_en;
            $content = $this->locale === 'hi' ? $article->content_hi : $article->content_en;
            $excerpt = $this->locale === 'hi' ? $article->excerpt_hi : $article->excerpt_en;

            $xml .= '<item>';
            $xml .= '<title>' . xml_escape($title) . '</title>';
            $xml .= '<link>' . base_url('/' . $this->locale . '/news/' . $article->slug) . '</link>';
            $xml .= '<description>' . xml_escape($excerpt ?? $title) . '</description>';
            $xml .= '<pubDate>' . date('r', strtotime($article->published_at ?? $article->created_at)) . '</pubDate>';
            $xml .= '<guid>' . base_url('/' . $this->locale . '/news/' . $article->slug) . '</guid>';

            if ($article->featured_image) {
                $xml .= '<enclosure url="' . base_url($article->featured_image) . '" type="image/jpeg"/>';
            }

            $xml .= '</item>';
        }

        $xml .= '</channel>';
        $xml .= '</rss>';

        return $this->response
                    ->setContentType('application/rss+xml')
                    ->setBody($xml);
    }

    public function category(string $slug): \CodeIgniter\HTTP\ResponseInterface
    {
        $category = $this->categoryModel->where('slug', $slug)->where('status', 1)->first();

        if (!$category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $articles = $this->articleModel->getArticlesByCategory($category->id, 50);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
        $xml .= '<channel>';
        $xml .= '<title>Hind Bihar - ' . ($this->locale === 'hi' ? $category->name_hi : $category->name_en) . '</title>';
        $xml .= '<link>' . base_url('/' . $this->locale . '/category/' . $category->slug) . '</link>';
        $xml .= '<description>' . ($this->locale === 'hi' ? $category->name_hi : $category->name_en) . ' news</description>';
        $xml .= '<language>' . $this->locale . '</language>';

        foreach ($articles as $article) {
            $title   = $this->locale === 'hi' ? $article->title_hi : $article->title_en;
            $excerpt = $this->locale === 'hi' ? $article->excerpt_hi : $article->excerpt_en;

            $xml .= '<item>';
            $xml .= '<title>' . xml_escape($title) . '</title>';
            $xml .= '<link>' . base_url('/' . $this->locale . '/news/' . $article->slug) . '</link>';
            $xml .= '<description>' . xml_escape($excerpt ?? $title) . '</description>';
            $xml .= '<pubDate>' . date('r', strtotime($article->published_at ?? $article->created_at)) . '</pubDate>';
            $xml .= '<guid>' . base_url('/' . $this->locale . '/news/' . $article->slug) . '</guid>';
            $xml .= '</item>';
        }

        $xml .= '</channel>';
        $xml .= '</rss>';

        return $this->response
                    ->setContentType('application/rss+xml')
                    ->setBody($xml);
    }
}
