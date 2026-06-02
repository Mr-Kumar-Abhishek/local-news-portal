<?= $this->extend('admin/templates/header') ?>

<?= $this->section('title') ?>Manage Comments - Hind Bihar<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Comments</h1>
    <div>
        <a href="<?= current_url() ?>?status=pending" class="btn btn-outline-warning btn-sm <?= ($status ?? '') === 'pending' ? 'active' : '' ?>">
            <i class="bi bi-exclamation-circle"></i> Pending (<?= esc($pending_count ?? 0) ?>)
        </a>
        <a href="<?= current_url() ?>?status=approved" class="btn btn-outline-success btn-sm <?= ($status ?? '') === 'approved' ? 'active' : '' ?>">
            <i class="bi bi-check-circle"></i> Approved
        </a>
        <a href="<?= current_url() ?>?status=rejected" class="btn btn-outline-danger btn-sm <?= ($status ?? '') === 'rejected' ? 'active' : '' ?>">
            <i class="bi bi-x-circle"></i> Rejected
        </a>
        <a href="<?= current_url() ?>" class="btn btn-outline-secondary btn-sm <?= empty($status) ? 'active' : '' ?>">
            All
        </a>
    </div>
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
                        <th width="30">
                            <input type="checkbox" id="select-all">
                        </th>
                        <th>Comment</th>
                        <th width="150">Article</th>
                        <th width="120">Author</th>
                        <th width="100">Date</th>
                        <th width="90">Status</th>
                        <th width="140">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($comments)) : ?>
                        <?php foreach ($comments as $comment) : ?>
                        <tr class="<?= $comment['status'] === 'pending' ? 'table-warning' : '' ?>">
                            <td><input type="checkbox" class="select-item" value="<?= $comment['id'] ?>"></td>
                            <td>
                                <div class="fw-medium"><?= esc(mb_substr($comment['content'], 0, 100)) ?></div>
                                <small class="text-muted">
                                    <?= mb_strlen($comment['content']) > 100 ? '...' : '' ?>
                                    <?php if ($comment['status'] === 'pending') : ?>
                                        <span class="badge bg-warning text-dark ms-1">Awaiting moderation</span>
                                    <?php endif; ?>
                                </small>
                            </td>
                            <td>
                                <a href="<?= site_url($locale . '/admin/news/edit/' . $comment['article_id']) ?>" class="text-decoration-none small">
                                    <?= esc(mb_substr($comment['article_title'] ?? 'Unknown', 0, 30)) ?>
                                </a>
                            </td>
                            <td>
                                <div class="small">
                                    <strong><?= esc($comment['author_name'] ?? 'Anonymous') ?></strong>
                                    <?php if (!empty($comment['author_email'])) : ?>
                                        <br><span class="text-muted"><?= esc($comment['author_email']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= date('d M Y', strtotime($comment['created_at'])) ?>
                                    <br><?= date('H:i', strtotime($comment['created_at'])) ?>
                                </small>
                            </td>
                            <td>
                                <?php if ($comment['status'] === 'approved') : ?>
                                    <span class="badge bg-success">Approved</span>
                                <?php elseif ($comment['status'] === 'pending') : ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php else : ?>
                                    <span class="badge bg-danger">Rejected</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <?php if ($comment['status'] === 'pending') : ?>
                                        <form action="<?= site_url($locale . '/admin/comments/approve/' . $comment['id']) ?>" method="post" style="display:inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-success" title="Approve">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                        <form action="<?= site_url($locale . '/admin/comments/reject/' . $comment['id']) ?>" method="post" style="display:inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-warning" title="Reject">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <form action="<?= site_url($locale . '/admin/comments/delete/' . $comment['id']) ?>" method="post" style="display:inline;"
                                          onsubmit="return confirm('Delete this comment permanently?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-chat-dots fs-3 d-block mb-2"></i>
                                No comments found.
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
<?= $this->endSection() ?>
