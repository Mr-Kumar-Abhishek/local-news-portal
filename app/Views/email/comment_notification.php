<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1a1a2e; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; border: 1px solid #eee; }
        .footer { text-align: center; padding: 15px; color: #888; font-size: 12px; }
        .btn { display: inline-block; padding: 10px 20px; background: #1a1a2e; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Comment Notification</h2>
        </div>
        <div class="content">
            <p>A new comment has been posted on your article <strong>"<?= esc($article_title ?? 'Unknown') ?>"</strong>.</p>

            <?php if (!empty($comment_author)): ?>
            <p><strong>Author:</strong> <?= esc($comment_author) ?></p>
            <?php endif; ?>

            <p><strong>Comment:</strong></p>
            <blockquote style="border-left: 3px solid #ddd; padding-left: 15px; color: #555;">
                <?= esc($comment_body ?? '') ?>
            </blockquote>

            <p>
                <a href="<?= site_url($locale . '/news/' . ($article_slug ?? '')) ?>" class="btn">View Article</a>
                <a href="<?= site_url($locale . '/admin/comments') ?>" class="btn">Moderate Comments</a>
            </p>
        </div>
        <div class="footer">
            <p>Hind Bihar &mdash; This is an automated notification.</p>
        </div>
    </div>
</body>
</html>
