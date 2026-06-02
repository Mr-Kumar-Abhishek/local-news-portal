<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Tags</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTagModal">
        <i class="bi bi-plus-lg"></i> Add Tag
    </button>
</div>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
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
                        <th width="80">Slug</th>
                        <th width="80">Articles</th>
                        <th width="100">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tags)) : ?>
                        <?php foreach ($tags as $tag) : ?>
                        <tr>
                            <td><?= $tag['id'] ?></td>
                            <td><?= esc($tag['name_en']) ?></td>
                            <td><?= esc($tag['name_hi']) ?></td>
                            <td><code><?= esc($tag['slug']) ?></code></td>
                            <td><span class="badge bg-secondary"><?= esc($tag['article_count'] ?? 0) ?></span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" title="Edit"
                                            onclick="editTag(<?= $tag['id'] ?>)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" title="Delete"
                                            onclick="confirmDelete(<?= $tag['id'] ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-<?= $tag['id'] ?>"
                                      action="<?= site_url($locale . '/admin/tags/delete/' . $tag['id']) ?>"
                                      method="post" style="display:none;">
                                    <?= csrf_field() ?>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-tags fs-3 d-block mb-2"></i>
                                No tags found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Tag Modal -->
<div class="modal fade" id="createTagModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url($locale . '/admin/tags/create') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Create Tag</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name_en" class="form-label">Name (English) <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" id="modal_name_en" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="name_hi" class="form-label">नाम (हिंदी) <span class="text-danger">*</span></label>
                        <input type="text" name="name_hi" id="modal_name_hi" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Tag</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this tag?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}

function editTag(id) {
    // For simplicity, redirect to a dedicated edit page or use inline editing
    window.location.href = '<?= site_url($locale . '/admin/tags') ?>?edit=' + id;
}
</script>
