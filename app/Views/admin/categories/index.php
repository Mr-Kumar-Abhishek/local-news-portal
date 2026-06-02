<?= $this->extend('admin/templates/header') ?>

<?= $this->section('title') ?>Manage Categories - Hind Bihar<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Categories</h1>
    <a href="<?= site_url($locale . '/admin/categories/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Category
    </a>
</div>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">ID</th>
                        <th>Name (English)</th>
                        <th>नाम (हिंदी)</th>
                        <th width="100">Section</th>
                        <th width="100">Parent</th>
                        <th width="80">Articles</th>
                        <th width="60">Status</th>
                        <th width="100">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categories)) : ?>
                        <?php foreach ($categories as $cat) : ?>
                        <tr>
                            <td><?= $cat['id'] ?></td>
                            <td><?= esc($cat['name_en']) ?></td>
                            <td><?= esc($cat['name_hi']) ?></td>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-info text-capitalize">
                                    <?= esc($cat['section'] ?? '-') ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($cat['parent_id']) : ?>
                                    <?php
                                    $parentName = '-';
                                    foreach ($categories as $p) {
                                        if ($p['id'] == $cat['parent_id']) {
                                            $parentName = $p['name_en'];
                                            break;
                                        }
                                    }
                                    ?>
                                    <span class="text-muted"><?= esc($parentName) ?></span>
                                <?php else : ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($cat['article_count'] ?? 0) ?></td>
                            <td>
                                <?php if ($cat['status'] === 'active') : ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else : ?>
                                    <span class="badge bg-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= site_url($locale . '/admin/categories/edit/' . $cat['id']) ?>"
                                       class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="Delete"
                                            onclick="confirmDelete(<?= $cat['id'] ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-<?= $cat['id'] ?>"
                                      action="<?= site_url($locale . '/admin/categories/delete/' . $cat['id']) ?>"
                                      method="post" style="display:none;">
                                    <?= csrf_field() ?>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-folder fs-3 d-block mb-2"></i>
                                No categories found.
                                <a href="<?= site_url($locale . '/admin/categories/create') ?>" class="d-block mt-1">Create your first category</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this category? Articles in this category will be uncategorized.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
<?= $this->endSection() ?>
