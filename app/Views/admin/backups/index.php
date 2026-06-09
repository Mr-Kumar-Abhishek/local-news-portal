<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Database Backups</h1>
    <form action="<?= site_url($locale . '/admin/backups/create') ?>" method="post">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-cloud-download"></i> Create Backup Now
        </button>
    </form>
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
                        <th>Filename</th>
                        <th>Size</th>
                        <th>Date</th>
                        <th width="160">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($backups)) : ?>
                        <?php foreach ($backups as $backup) : ?>
                        <tr>
                            <td>
                                <i class="bi bi-file-earmark-zip me-2"></i>
                                <?= esc($backup['filename']) ?>
                            </td>
                            <td>
                                <?php
                                $size = $backup['size'];
                                $units = ['B', 'KB', 'MB', 'GB'];
                                $i = 0;
                                while ($size >= 1024 && $i < count($units) - 1) {
                                    $size /= 1024;
                                    $i++;
                                }
                                echo round($size, 2) . ' ' . $units[$i];
                                ?>
                            </td>
                            <td><small class="text-muted"><?= $backup['date'] ?></small></td>
                            <td>
                                <a href="<?= site_url($locale . '/admin/backups/download/' . $backup['filename']) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-download"></i> Download
                                </a>
                                <form action="<?= site_url($locale . '/admin/backups/delete/' . $backup['filename']) ?>" method="post" style="display:inline;"
                                      onsubmit="return confirm('Delete this backup?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <i class="bi bi-cloud-download fs-3 d-block mb-2"></i>
                                No backups found. Click "Create Backup Now" to create one.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
