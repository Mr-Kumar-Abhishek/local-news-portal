<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="<?= site_url($locale . '/admin/news/create') ?>" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-plus-lg"></i> New Article
            </a>
        </div>
        <span class="text-muted small align-self-center">
            Last 30 days
        </span>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-start border-primary border-3 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Articles</h6>
                        <h2 class="mb-0 fw-bold"><?= esc($total_articles ?? 0) ?></h2>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 p-3 rounded">
                        <i class="bi bi-newspaper text-primary fs-4"></i>
                    </div>
                </div>
                <small class="text-success mt-2 d-block">
                    <i class="bi bi-arrow-up"></i> <?= esc($published_articles ?? 0) ?> Published
                </small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-start border-success border-3 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Views</h6>
                        <h2 class="mb-0 fw-bold"><?= esc(number_format($total_views ?? 0)) ?></h2>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 p-3 rounded">
                        <i class="bi bi-eye text-success fs-4"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block">
                    <i class="bi bi-graph-up"></i> Lifetime views
                </small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-start border-warning border-3 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Comments</h6>
                        <h2 class="mb-0 fw-bold"><?= esc($total_comments ?? 0) ?></h2>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 p-3 rounded">
                        <i class="bi bi-chat-dots text-warning fs-4"></i>
                    </div>
                </div>
                <small class="text-danger mt-2 d-block">
                    <i class="bi bi-exclamation-circle"></i> <?= esc($pending_comments ?? 0) ?> Pending
                </small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-start border-info border-3 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Registered Users</h6>
                        <h2 class="mb-0 fw-bold"><?= esc($total_users ?? 0) ?></h2>
                    </div>
                    <div class="stat-icon bg-info bg-opacity-10 p-3 rounded">
                        <i class="bi bi-people text-info fs-4"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block">
                    <i class="bi bi-person-check"></i> <?= esc($active_users ?? 0) ?> Active
                </small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Monthly Article Chart -->
    <div class="col-xl-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Articles Published (Monthly)</h5>
            </div>
            <div class="card-body">
                <canvas id="articlesChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Articles -->
    <div class="col-xl-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Articles</h5>
                <a href="<?= site_url($locale . '/admin/news') ?>" class="btn btn-sm btn-link">View All</a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php if (!empty($recent_articles)) : ?>
                        <?php foreach ($recent_articles as $article) : ?>
                        <li class="list-group-item">
                            <a href="<?= site_url($locale . '/admin/news/edit/' . $article['id']) ?>" class="text-decoration-none">
                                <?= esc(mb_substr($article['title_hi'] ?: $article['title_en'], 0, 50)) ?>
                            </a>
                            <br>
                            <small class="text-muted">
                                <?= date('d M Y', strtotime($article['created_at'])) ?>
                                <?php if ($article['status'] === 'published') : ?>
                                    <span class="badge bg-success ms-1">Published</span>
                                <?php elseif ($article['status'] === 'draft') : ?>
                                    <span class="badge bg-secondary ms-1">Draft</span>
                                <?php else : ?>
                                    <span class="badge bg-warning text-dark ms-1">Archived</span>
                                <?php endif; ?>
                            </small>
                        </li>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <li class="list-group-item text-muted text-center py-4">
                            No articles published yet.
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($monthly_counts)) : ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('articlesChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($monthly_counts, 'month')) ?>,
            datasets: [{
                label: 'Articles',
                data: <?= json_encode(array_column($monthly_counts, 'count')) ?>,
                backgroundColor: 'rgba(13, 110, 253, 0.7)',
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
});
</script>
<?php endif; ?>

