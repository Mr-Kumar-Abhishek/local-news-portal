<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Users</h1>
    <a href="<?= site_url($locale . '/admin/users/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add User
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
                        <th>Name</th>
                        <th>Email</th>
                        <th width="100">Role</th>
                        <th width="80">Articles</th>
                        <th width="80">Status</th>
                        <th width="120">Joined</th>
                        <th width="100">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)) : ?>
                        <?php foreach ($users as $user) : ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-2"
                                         style="width: 32px; height: 32px; font-size: 14px;">
                                        <?= strtoupper(mb_substr($user['full_name'] ?? $user['username'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <strong><?= esc($user['full_name'] ?: $user['username']) ?></strong>
                                        <br><small class="text-muted">@<?= esc($user['username']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?= esc($user['email']) ?></td>
                            <td>
                                <?php if ($user['role'] === 'admin') : ?>
                                    <span class="badge bg-danger">Admin</span>
                                <?php elseif ($user['role'] === 'editor') : ?>
                                    <span class="badge bg-primary">Editor</span>
                                <?php else : ?>
                                    <span class="badge bg-secondary">User</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($user['article_count'] ?? 0) ?></td>
                            <td>
                                <?php if ($user['status'] === 'active') : ?>
                                    <span class="badge bg-success">Active</span>
                                <?php elseif ($user['status'] === 'inactive') : ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php else : ?>
                                    <span class="badge bg-danger">Banned</span>
                                <?php endif; ?>
                            </td>
                            <td><small class="text-muted"><?= date('d M Y', strtotime($user['created_at'])) ?></small></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= site_url($locale . '/admin/users/edit/' . $user['id']) ?>"
                                       class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php if ($user['id'] !== session()->get('user_id')) : ?>
                                        <button type="button" class="btn btn-outline-danger" title="Delete"
                                                onclick="confirmDelete(<?= $user['id'] ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <?php if ($user['id'] !== session()->get('user_id')) : ?>
                                <form id="delete-form-<?= $user['id'] ?>"
                                      action="<?= site_url($locale . '/admin/users/delete/' . $user['id']) ?>"
                                      method="post" style="display:none;">
                                    <?= csrf_field() ?>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-people fs-3 d-block mb-2"></i>
                                No users found.
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
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
