<?= $this->extend('admin/templates/header') ?>

<?= $this->section('title') ?>Edit Article - Hind Bihar<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Article</h1>
    <div>
        <a href="<?= site_url($locale . '/admin/news') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to News
        </a>
        <?php if (!empty($article['slug'])) : ?>
            <a href="<?= site_url($locale . '/news/' . ($article['slug'] ?? '')) ?>" class="btn btn-outline-primary ms-2" target="_blank">
                <i class="bi bi-eye"></i> View Article
            </a>
        <?php endif; ?>
    </div>
</div>

<?= view('App\Views\admin\news\_form', ['action' => site_url($locale . '/admin/news/edit/' . $article['id']), 'article' => $article]) ?>

<?= $this->endSection() ?>
