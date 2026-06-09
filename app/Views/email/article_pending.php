<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #e74c3c; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; border: 1px solid #eee; }
        .footer { text-align: center; padding: 15px; color: #888; font-size: 12px; }
        .btn { display: inline-block; padding: 10px 20px; background: #e74c3c; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Article Pending Review</h2>
        </div>
        <div class="content">
            <p>A new article has been submitted for review and requires your attention.</p>

            <p><strong>Article Title:</strong> <?= esc($article_title ?? 'Untitled') ?></p>
            <p><strong>Article ID:</strong> #<?= esc($article_id ?? '—') ?></p>

            <p>
                <a href="<?= site_url($locale . '/admin/news/edit/' . ($article_id ?? 0)) ?>" class="btn">Review Article</a>
                <a href="<?= site_url($locale . '/admin/news') ?>" class="btn">Manage Articles</a>
            </p>
        </div>
        <div class="footer">
            <p>Hind Bihar &mdash; This is an automated notification.</p>
        </div>
    </div>
</body>
</html>
