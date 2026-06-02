<?php

namespace App\Controllers\Admin;

use App\Models\ArticleModel;
use App\Models\CategoryModel;
use App\Models\CommentModel;
use App\Models\UserModel;
use App\Models\MediaModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $articleModel = new ArticleModel();
        $commentModel = new CommentModel();
        $userModel    = new UserModel();
        $mediaModel   = new MediaModel();
        $categoryModel = new CategoryModel();

        $data = [
            'locale'            => $this->locale,
            'title'             => 'Admin Dashboard',
            'total_articles'    => $articleModel->getTotalPublished(),
            'total_views'       => $articleModel->getTotalViews(),
            'total_comments'    => $commentModel->getTotalComments(),
            'pending_comments'  => $commentModel->getPendingCount(),
            'total_users'       => $userModel->getTotalUsers(),
            'total_categories'  => count($categoryModel->getActiveCategories()),
            'total_media'       => $mediaModel->getTotalFiles(),
            'recent_articles'   => $articleModel->getLatestArticles(5),
            'recent_comments'   => $commentModel->getRecentComments(5),
            'recent_users'      => $userModel->getRecentUsers(5),
            'popular_articles'  => $articleModel->getPopularArticles(5),
            'monthly_counts'    => $articleModel->getMonthlyArticleCounts(),
            'user_name'         => $this->getCurrentUserName(),
        ];

        return view('admin/templates/header', $data)
             . view('admin/dashboard/index', $data)
             . view('admin/templates/footer');
    }
}
