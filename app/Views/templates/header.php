<!DOCTYPE html>
<html lang="<?= $locale ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Hind Bihar') ?> | Hind Bihar</title>
    <meta name="description" content="<?= esc($meta_description ?? 'Hind Bihar - Your trusted source for the latest news in Hindi and English') ?>">
    <meta name="keywords" content="<?= esc($meta_keywords ?? 'news, hindi news, bihar news, hind bihar, indian news') ?>">

    <!-- Open Graph -->
    <meta property="og:title" content="<?= esc($title ?? 'Hind Bihar') ?>">
    <meta property="og:description" content="<?= esc($meta_description ?? 'Hind Bihar - Your trusted source for the latest news') ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= current_url() ?>">
    <?php if (isset($article) && $article->featured_image): ?>
    <meta property="og:image" content="<?= base_url($article->featured_image) ?>">
    <?php endif; ?>

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= esc($title ?? 'Hind Bihar') ?>">

    <!-- Canonical -->
    <link rel="canonical" href="<?= current_url() ?>">

    <!-- RSS Feed -->
    <link rel="alternate" type="application/rss+xml" title="Hind Bihar RSS Feed" href="<?= base_url('/rss') ?>">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&family=Noto+Sans+Devanagari:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <style>
        :root {
            --primary-color: #c0392b;
            --secondary-color: #e74c3c;
            --dark-color: #2c3e50;
            --light-bg: #f8f9fa;
        }

        * {
            font-family: 'Noto Sans', 'Noto Sans Devanagari', sans-serif;
        }

        body {
            background-color: #f4f4f4;
            color: #333;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--primary-color) !important;
        }

        .navbar-brand small {
            font-size: 0.8rem;
            color: #666;
            font-weight: 400;
        }

        .top-bar {
            background: var(--dark-color);
            color: white;
            font-size: 0.85rem;
            padding: 6px 0;
        }

        .top-bar a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
        }

        .top-bar a:hover {
            color: white;
        }

        .breaking-news {
            background: var(--primary-color);
            color: white;
            padding: 8px 0;
            font-size: 0.9rem;
        }

        .breaking-news .label {
            background: white;
            color: var(--primary-color);
            padding: 2px 10px;
            font-weight: 700;
            text-transform: uppercase;
            margin-right: 10px;
        }

        .main-nav {
            background: white !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .main-nav .nav-link {
            color: var(--dark-color) !important;
            font-weight: 500;
            padding: 12px 16px !important;
        }

        .main-nav .nav-link:hover,
        .main-nav .nav-link.active {
            color: var(--primary-color) !important;
        }

        .main-nav .nav-link.dropdown-toggle::after {
            display: none;
        }

        .section-title {
            color: var(--dark-color);
            font-weight: 700;
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 8px;
            margin-bottom: 20px;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--secondary-color);
        }

        .news-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
        }

        .news-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }

        .news-card .card-img-top {
            height: 200px;
            object-fit: cover;
        }

        .news-card .card-body {
            padding: 16px;
        }

        .news-card .category-badge {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .news-card .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            line-height: 1.4;
        }

        .news-card .card-title a {
            color: var(--dark-color);
            text-decoration: none;
        }

        .news-card .card-title a:hover {
            color: var(--primary-color);
        }

        .news-card .card-text {
            font-size: 0.9rem;
            color: #666;
        }

        .featured-news {
            background: var(--dark-color);
            color: white;
            border-radius: 8px;
            overflow: hidden;
        }

        .featured-news .carousel-item {
            min-height: 400px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .featured-news .carousel-caption {
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            left: 0;
            right: 0;
            bottom: 0;
            padding: 40px 20px 20px;
            text-align: left;
        }

        .featured-news .carousel-caption h3 {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .featured-news .carousel-caption p {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .sidebar-widget {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .sidebar-widget .widget-title {
            font-weight: 700;
            color: var(--dark-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .sidebar-widget .list-group-item {
            border: none;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .sidebar-widget .list-group-item:last-child {
            border-bottom: none;
        }

        .sidebar-widget .list-group-item a {
            color: #555;
            text-decoration: none;
        }

        .sidebar-widget .list-group-item a:hover {
            color: var(--primary-color);
        }

        .popular-post-item {
            display: flex;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .popular-post-item:last-child {
            border-bottom: none;
        }

        .popular-post-item img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }

        .popular-post-item .post-title {
            font-size: 0.9rem;
            font-weight: 500;
            line-height: 1.3;
        }

        .popular-post-item .post-title a {
            color: var(--dark-color);
            text-decoration: none;
        }

        .popular-post-item .post-title a:hover {
            color: var(--primary-color);
        }

        .popular-post-item .post-date {
            font-size: 0.75rem;
            color: #999;
        }

        .tag-cloud a {
            display: inline-block;
            background: var(--light-bg);
            color: #555;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            text-decoration: none;
            margin: 3px;
        }

        .tag-cloud a:hover {
            background: var(--primary-color);
            color: white;
        }

        .footer {
            background: var(--dark-color);
            color: rgba(255,255,255,0.8);
            padding: 40px 0 20px;
        }

        .footer h5 {
            color: white;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .footer a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
        }

        .footer a:hover {
            color: white;
        }

        .footer .social-links a {
            display: inline-block;
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 36px;
            margin-right: 8px;
        }

        .footer .social-links a:hover {
            background: var(--primary-color);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
            margin-top: 20px;
            font-size: 0.85rem;
        }

        .page-header {
            background: white;
            padding: 20px 0;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .article-content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .article-content h1 {
            font-weight: 700;
            color: var(--dark-color);
        }

        .article-content .meta {
            color: #888;
            font-size: 0.9rem;
        }

        .article-content .featured-image {
            width: 100%;
            border-radius: 8px;
            margin: 20px 0;
        }

        .article-content .body {
            font-size: 1.05rem;
            line-height: 1.8;
        }

        .article-content .body p {
            margin-bottom: 16px;
        }

        .auth-form {
            max-width: 450px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .auth-form h2 {
            text-align: center;
            margin-bottom: 24px;
            color: var(--dark-color);
            font-weight: 700;
        }

        .pagination-custom {
            display: flex;
            justify-content: center;
            gap: 5px;
        }

        .pagination-custom a, .pagination-custom span {
            padding: 8px 14px;
            border-radius: 4px;
            text-decoration: none;
            background: white;
            color: var(--dark-color);
            border: 1px solid #ddd;
        }

        .pagination-custom a:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .pagination-custom .active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #a93226;
            border-color: #a93226;
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .category-section {
            margin-bottom: 30px;
        }

        .admin-sidebar {
            background: var(--dark-color);
            min-height: 100vh;
            color: white;
        }

        .admin-sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 10px 20px;
        }

        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
        }

        .admin-sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-card .stat-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .stat-card .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark-color);
        }

        .stat-card .stat-label {
            color: #888;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.4rem;
            }
            .featured-news .carousel-item {
                min-height: 250px;
            }
            .featured-news .carousel-caption h3 {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <span id="current-date"></span>
                </div>
                <div class="col-md-6 text-end">
                    <a href="/en" class="me-3 <?= $locale === 'en' ? 'fw-bold text-white' : '' ?>">English</a>
                    <a href="/hi" class="<?= $locale === 'hi' ? 'fw-bold text-white' : '' ?>">हिन्दी</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="bg-white py-3">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <a href="/<?= $locale ?>" class="text-decoration-none">
                        <span class="navbar-brand">Hind <span style="color:#e74c3c;">Bihar</span></span>
                    </a>
                </div>
                <div class="col-md-8 text-end">
                    <form action="/<?= $locale ?>/search" method="GET" class="d-inline-block">
                        <div class="input-group" style="max-width: 300px;">
                            <input type="text" name="q" class="form-control form-control-sm" placeholder="<?= lang('News.search') ?>" value="<?= esc($query ?? '') ?>">
                            <button class="btn btn-primary btn-sm" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                    <?php if (session()->has('is_logged_in')): ?>
                        <a href="/<?= $locale ?>/admin/dashboard" class="btn btn-outline-primary btn-sm ms-2">
                            <i class="bi bi-speedometer2"></i> <?= lang('News.dashboard') ?>
                        </a>
                        <a href="/<?= $locale ?>/logout" class="btn btn-outline-danger btn-sm ms-1">
                            <i class="bi bi-box-arrow-right"></i>
                        </a>
                    <?php else: ?>
                        <a href="/<?= $locale ?>/login" class="btn btn-outline-primary btn-sm ms-2">
                            <i class="bi bi-person"></i> <?= lang('News.login') ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg main-nav">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link <?= current_url() === base_url('/' . $locale) ? 'active' : '' ?>" href="/<?= $locale ?>">
                            <i class="bi bi-house-fill"></i> <?= lang('News.home') ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(current_url(), '/section/international') !== false ? 'active' : '' ?>" href="/<?= $locale ?>/section/international">
                            <?= lang('News.international') ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(current_url(), '/section/national') !== false ? 'active' : '' ?>" href="/<?= $locale ?>/section/national">
                            <?= lang('News.national') ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(current_url(), '/section/local') !== false ? 'active' : '' ?>" href="/<?= $locale ?>/section/local">
                            <?= lang('News.local') ?>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <?= lang('News.categories') ?>
                        </a>
                        <ul class="dropdown-menu">
                            <?php if (isset($categories) && !empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <li>
                                        <a class="dropdown-item" href="/<?= $locale ?>/category/<?= $cat->slug ?>">
                                            <?= $locale === 'hi' ? esc($cat->name_hi) : esc($cat->name_en) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/<?= $locale ?>/news"><?= lang('News.all_news') ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (session()->has('message')): ?>
    <div class="container mt-3">
        <div class="alert alert-success alert-dismissible fade show">
            <?= session('message') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php endif; ?>

    <?php if (session()->has('error')): ?>
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show">
            <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php endif; ?>

    <main>
