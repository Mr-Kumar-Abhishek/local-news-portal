<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Admin') ?> | Hind Bihar Admin</title>
    <meta name="robots" content="noindex, nofollow">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Admin Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f0f2f5; }
        .admin-sidebar { background: #1a1a2e; min-height: 100vh; position: fixed; left: 0; top: 0; width: 250px; z-index: 1000; }
        .admin-content { margin-left: 250px; padding: 20px; }
        .admin-header { background: white; padding: 15px 25px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .admin-footer { text-align: center; padding: 20px; color: #888; font-size: 0.85rem; }
        .card { border: none; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .card-header { background: white; border-bottom: 1px solid #eee; font-weight: 600; }
        .table th { font-weight: 600; color: #555; border-top: none; }
        .btn-sm { border-radius: 6px; }
        .stat-card { transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-3px); }
        .sidebar-brand { padding: 20px; font-size: 1.3rem; font-weight: 700; color: white; text-decoration: none; display: block; }
        .sidebar-brand:hover { color: #e74c3c; }
        .sidebar-nav { padding: 0; list-style: none; }
        .sidebar-nav li a { display: block; padding: 12px 20px; color: rgba(255,255,255,0.6); text-decoration: none; transition: all 0.2s; }
        .sidebar-nav li a:hover, .sidebar-nav li a.active { background: rgba(255,255,255,0.1); color: white; }
        .sidebar-nav li a i { margin-right: 10px; width: 20px; }
        .sidebar-divider { border-top: 1px solid rgba(255,255,255,0.1); margin: 10px 20px; }
        @media (max-width: 768px) {
            .admin-sidebar { width: 100%; position: relative; min-height: auto; }
            .admin-content { margin-left: 0; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="admin-sidebar">
        <a href="/<?= $locale ?>/admin/dashboard" class="sidebar-brand">Hind Bihar Admin</a>
        <hr class="sidebar-divider">
        <ul class="sidebar-nav">
            <li><a href="/<?= $locale ?>/admin/dashboard" class="<?= strpos(current_url(), '/admin/dashboard') !== false ? 'active' : '' ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li><a href="/<?= $locale ?>/admin/news" class="<?= strpos(current_url(), '/admin/news') !== false ? 'active' : '' ?>"><i class="bi bi-newspaper"></i> News</a></li>
            <li><a href="/<?= $locale ?>/admin/categories" class="<?= strpos(current_url(), '/admin/categories') !== false ? 'active' : '' ?>"><i class="bi bi-folder"></i> Categories</a></li>
            <li><a href="/<?= $locale ?>/admin/tags" class="<?= strpos(current_url(), '/admin/tags') !== false ? 'active' : '' ?>"><i class="bi bi-tags"></i> Tags</a></li>
            <li><a href="/<?= $locale ?>/admin/comments" class="<?= strpos(current_url(), '/admin/comments') !== false ? 'active' : '' ?>"><i class="bi bi-chat"></i> Comments</a></li>
            <li><a href="/<?= $locale ?>/admin/media" class="<?= strpos(current_url(), '/admin/media') !== false ? 'active' : '' ?>"><i class="bi bi-images"></i> Media</a></li>
            <li><a href="/<?= $locale ?>/admin/users" class="<?= strpos(current_url(), '/admin/users') !== false ? 'active' : '' ?>"><i class="bi bi-people"></i> Users</a></li>
            <li><a href="/<?= $locale ?>/admin/settings" class="<?= strpos(current_url(), '/admin/settings') !== false ? 'active' : '' ?>"><i class="bi bi-gear"></i> Settings</a></li>
            <hr class="sidebar-divider">
            <li><a href="/<?= $locale ?>"><i class="bi bi-house"></i> View Site</a></li>
            <li><a href="/<?= $locale ?>/auth/logout"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="admin-content">
        <div class="admin-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0"><?= esc($title ?? 'Dashboard') ?></h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="/<?= $locale ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-house"></i> View Site
                </a>
                <span>
                    <i class="bi bi-person-circle"></i>
                    <?= esc($user_name ?? 'Admin') ?>
                </span>
            </div>
        </div>

        <?php if (session()->has('message')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= session('message') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (session()->has('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
