<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Activity Log</h1>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Entity</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)) : ?>
                        <?php foreach ($logs as $log) : ?>
                        <tr>
                            <td><small class="text-muted">#<?= $log->id ?></small></td>
                            <td>
                                <?php if ($log->user_id): ?>
                                    <span class="fw-medium"><?= esc($log->user_name ?? $log->username ?? 'User #' . $log->user_id) ?></span>
                                <?php else: ?>
                                    <span class="text-muted fst-italic">System</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?= esc($log->action) ?></span>
                            </td>
                            <td>
                                <small>
                                    <?= esc($log->entity_type) ?>
                                    <?php if ($log->entity_id): ?>
                                        #<?= $log->entity_id ?>
                                    <?php endif; ?>
                                </small>
                            </td>
                            <td><small><?= esc($log->description) ?></small></td>
                            <td><small class="text-muted"><?= esc($log->ip_address ?? '—') ?></small></td>
                            <td><small class="text-muted"><?= date('d M Y H:i', strtotime($log->created_at)) ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-activity fs-3 d-block mb-2"></i>
                                No activity logs found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
