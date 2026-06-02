<?= $this->extend('admin/templates/header') ?>

<?= $this->section('title') ?>Manage News - Hind Bihar<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage News</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= site_url($locale . '/admin/news/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add New Article
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="get" action="<?= current_url() ?>" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search articles..."
                       value="<?= esc($search ?? '') ?>">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="published" <?= ($status ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="draft" <?= ($status ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="archived" <?= ($status ?? '') === 'archived' ? 'selected' : '' ?>>Archived</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    <?php if (!empty($categories)) : ?>
                        <?php foreach ($categories as $cat) : ?>
                            <option value="<?= $cat['id'] ?>" <?= ($category ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= esc($cat['name_hi'] ?: $cat['name_en']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="section" class="form-select">
                    <option value="">All Sections</option>
                    <option value="international" <?= ($section ?? '') === 'international' ? 'selected' : '' ?>>International</option>
                    <option value="national" <?= ($section ?? '') === 'national' ? 'selected' : '' ?>>National</option>
                    <option value="local" <?= ($section ?? '') === 'local' ? 'selected' : '' ?>>Local</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Articles Table -->
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="30">
                            <input type="checkbox" id="select-all">
                        </th>
                        <th>Title</th>
                        <th width="100">Section</th>
                        <th width="100">Category</th>
                        <th width="80">Status</th>
                        <th width="60">Views</th>
                        <th width="60">Comments</th>
                        <th width="120">Date</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($articles)) : ?>
                        <?php foreach ($articles as $article) : ?>
                        <tr>
                            <td><input type="checkbox" class="select-item" value="<?= $article['id'] ?>"></td>
                            <td>
                                <a href="<?= site_url($locale . '/admin/news/edit/' . $article['id']) ?>" class="text-decoration-none fw-medium">
                                    <?= esc(mb_substr($article['title_hi'] ?: $article['title_en'], 0, 60)) ?>
                                </a>
                                <?php if ($article['is_featured']) : ?>
                                    <span class="badge bg-warning text-dark ms-1">Featured</span>
                                <?php endif; ?>
                                <br>
                                <small class="text-muted">
                                    By <?= esc($article['author_name'] ?? 'Unknown') ?>
                                    <?php if (!empty($article['tags'])) : ?>
                                        | <?php foreach (explode(',', $article['tags']) as $tag) : ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary"><?= esc(trim($tag)) ?></span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-info text-capitalize">
                                    <?= esc($article['section'] ?? '') ?>
                                </span>
                            </td>
                            <td><?= esc($article['category_name'] ?? '-') ?></td>
                            <td>
                                <?php if ($article['status'] === 'published') : ?>
                                    <span class="badge bg-success">Published</span>
                                <?php elseif ($article['status'] === 'draft') : ?>
                                    <span class="badge bg-secondary">Draft</span>
                                <?php else : ?>
                                    <span class="badge bg-warning text-dark">Archived</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc(number_format($article['view_count'] ?? 0)) ?></td>
                            <td><?= esc($article['comment_count'] ?? 0) ?></td>
                            <td>
                                <small class="text-muted">
                                    <?= date('d M Y', strtotime($article['created_at'])) ?>
                                </small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= site_url($locale . '/admin/news/edit/' . $article['id']) ?>"
                                       class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="Delete"
                                            onclick="confirmDelete(<?= $article['id'] ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-<?= $article['id'] ?>"
                                      action="<?= site_url($locale . '/admin/news/delete/' . $article['id']) ?>"
                                      method="post" style="display:none;">
                                    <?= csrf_field() ?>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-journal-text fs-3 d-block mb-2"></i>
                                No articles found.
                                <a href="<?= site_url($locale . '/admin/news/create') ?>" class="d-block mt-1">Create your first article</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<?php if (!empty($pager)) : ?>
<div class="mt-4">
    <?= $pager->links() ?>
</div>
<?php endif; ?>

<script>
function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this article? This action cannot be undone.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
<?= $this->endSection() ?>
