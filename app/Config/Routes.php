<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Disable auto-routing for security
$routes->setAutoRoute(false);

// ============================================================
// Root redirect - detect locale from browser or default to en
// ============================================================
$routes->get('/', 'Home::index');
$routes->get('en', 'Home::index');
$routes->get('hi', 'Home::index');

// ============================================================
// All frontend and admin routes prefixed with language code
// ============================================================
$routes->group('{locale}', static function ($routes) {

    // ---- Frontend ----
    $routes->get('/', 'Home::index');

    // News listing, single view, and filters
    $routes->get('news', 'News::index');
    $routes->get('news/page/(:num)', 'News::index/$1');
    $routes->get('news/(:any)', 'News::view/$1');

    // Category-based listing
    $routes->get('category/(:any)', 'News::category/$1');
    $routes->get('category/(:any)/page/(:num)', 'News::category/$1/$2');

    // Section-based listing
    $routes->get('section/(:any)', 'News::section/$1');
    $routes->get('section/(:any)/page/(:num)', 'News::section/$1/$2');

    // Tag-based listing
    $routes->get('tag/(:any)', 'News::tag/$1');
    $routes->get('tag/(:any)/page/(:num)', 'News::tag/$1/$2');

    // Search
    $routes->get('search', 'Search::index');
    $routes->get('search/autocomplete', 'Search::autocomplete');

    // Authentication
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::loginAttempt');
    $routes->get('register', 'Auth::register');
    $routes->post('register', 'Auth::registerAttempt');
    $routes->get('logout', 'Auth::logout');

    // Comment submission
    $routes->post('comment/(:num)', 'News::addComment/$1');

    // SEO - RSS Feeds
    $routes->get('rss', 'Rss::index');
    $routes->get('rss/(:any)', 'Rss::category/$1');

    // SEO - XML Sitemap
    $routes->get('sitemap\.xml', 'Sitemap::index');

    // ---- Admin Panel (protected by auth filter) ----
    $routes->group('admin', static function ($routes) {

        // Dashboard
        $routes->get('/', 'Admin\Dashboard::index');
        $routes->get('dashboard', 'Admin\Dashboard::index');

        // News CRUD
        $routes->get('news', 'Admin\News::index');
        $routes->get('news/create', 'Admin\News::create');
        $routes->post('news/create', 'Admin\News::save');
        $routes->get('news/edit/(:num)', 'Admin\News::edit/$1');
        $routes->post('news/edit/(:num)', 'Admin\News::update/$1');
        $routes->post('news/delete/(:num)', 'Admin\News::delete/$1');

        // Categories CRUD
        $routes->get('categories', 'Admin\Categories::index');
        $routes->get('categories/create', 'Admin\Categories::create');
        $routes->post('categories/create', 'Admin\Categories::save');
        $routes->get('categories/edit/(:num)', 'Admin\Categories::edit/$1');
        $routes->post('categories/edit/(:num)', 'Admin\Categories::update/$1');
        $routes->post('categories/delete/(:num)', 'Admin\Categories::delete/$1');

        // Tags CRUD
        $routes->get('tags', 'Admin\Tags::index');
        $routes->post('tags/create', 'Admin\Tags::create');
        $routes->post('tags/edit/(:num)', 'Admin\Tags::update/$1');
        $routes->post('tags/delete/(:num)', 'Admin\Tags::delete/$1');

        // Comments Moderation
        $routes->get('comments', 'Admin\Comments::index');
        $routes->post('comments/approve/(:num)', 'Admin\Comments::approve/$1');
        $routes->post('comments/reject/(:num)', 'Admin\Comments::reject/$1');
        $routes->post('comments/delete/(:num)', 'Admin\Comments::delete/$1');

        // Media Management
        $routes->get('media', 'Admin\Media::index');
        $routes->post('media/upload', 'Admin\Media::upload');
        $routes->post('media/delete/(:num)', 'Admin\Media::delete/$1');

        // Settings
        $routes->get('settings', 'Admin\Settings::index');
        $routes->post('settings', 'Admin\Settings::save');

        // Users Management
        $routes->get('users', 'Admin\Users::index');
        $routes->get('users/create', 'Admin\Users::create');
        $routes->post('users/create', 'Admin\Users::save');
        $routes->get('users/edit/(:num)', 'Admin\Users::edit/$1');
        $routes->post('users/edit/(:num)', 'Admin\Users::update/$1');
        $routes->post('users/delete/(:num)', 'Admin\Users::delete/$1');
    });
});
