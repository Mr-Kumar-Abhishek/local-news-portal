<!DOCTYPE html>
<html lang="<?= service('request')->getLocale() ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - <?= lang('News.error_404_title') ?> | Hind Bihar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            text-align: center;
            padding: 40px 20px;
        }
        .error-code {
            font-size: 120px;
            font-weight: 800;
            color: #dc3545;
            line-height: 1;
            margin-bottom: 10px;
            text-shadow: 3px 3px 0 rgba(220, 53, 69, 0.1);
        }
        .error-icon {
            font-size: 60px;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .error-title {
            font-size: 28px;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 15px;
        }
        .error-message {
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 30px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        .error-actions .btn {
            padding: 12px 30px;
            font-weight: 500;
            border-radius: 8px;
            margin: 5px;
        }
        .error-actions .btn-home {
            background: #0d6efd;
            color: #fff;
        }
        .error-actions .btn-home:hover {
            background: #0b5ed7;
            color: #fff;
        }
        .error-actions .btn-back {
            background: #6c757d;
            color: #fff;
        }
        .error-actions .btn-back:hover {
            background: #5c636a;
            color: #fff;
        }
        .search-box {
            max-width: 400px;
            margin: 20px auto;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="bi bi-signpost-2"></i>
        </div>
        <div class="error-code">404</div>
        <h1 class="error-title"><?= lang('News.error_404_title') ?></h1>
        <p class="error-message"><?= lang('News.error_404_message') ?></p>

        <div class="search-box">
            <form action="<?= site_url('/' . (service('request')->getLocale() ?? 'en') . '/search') ?>" method="get" class="input-group">
                <input type="text" name="q" class="form-control form-control-lg"
                       placeholder="<?= lang('News.search_placeholder') ?>"
                       aria-label="Search">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>

        <div class="error-actions">
            <a href="<?= site_url('/' . (service('request')->getLocale() ?? 'en')) ?>"
               class="btn btn-home">
                <i class="bi bi-house-door"></i> <?= lang('News.error_404_go_home') ?>
            </a>
            <button onclick="history.back()" class="btn btn-back">
                <i class="bi bi-arrow-left"></i> Go Back
            </button>
        </div>

        <div class="mt-4">
            <a href="<?= site_url('/' . (service('request')->getLocale() ?? 'en') . '/news') ?>" class="text-decoration-none me-3">
                <i class="bi bi-newspaper"></i> <?= lang('News.nav_news') ?>
            </a>
            <a href="<?= site_url('/' . (service('request')->getLocale() ?? 'en') . '/category/international') ?>" class="text-decoration-none me-3">
                <i class="bi bi-globe"></i> <?= lang('News.section_international') ?>
            </a>
            <a href="<?= site_url('/' . (service('request')->getLocale() ?? 'en') . '/category/national') ?>" class="text-decoration-none me-3">
                <i class="bi bi-flag"></i> <?= lang('News.section_national') ?>
            </a>
            <a href="<?= site_url('/' . (service('request')->getLocale() ?? 'en') . '/category/local') ?>" class="text-decoration-none">
                <i class="bi bi-geo-alt"></i> <?= lang('News.section_local') ?>
            </a>
        </div>
    </div>
</body>
</html>
