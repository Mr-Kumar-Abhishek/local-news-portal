<div class="container py-4">
    <?php if (isset($breadcrumbs)): ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <?= breadcrumb($breadcrumbs) ?>
        </ol>
    </nav>
    <?php endif; ?>

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-md-3 order-md-1 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0"><i class="bi bi-funnel"></i> <?= lang('News.filters') ?? 'Filters' ?></h6>
                </div>
                <div class="card-body">
                    <form action="/<?= $locale ?>/search" method="GET" id="filter-form">
                        <input type="hidden" name="q" value="<?= esc($query ?? '') ?>">

                        <!-- Category -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold"><?= lang('News.category') ?? 'Category' ?></label>
                            <select name="category" class="form-select form-select-sm">
                                <option value=""><?= lang('News.all_categories') ?? 'All Categories' ?></option>
                                <?php if (isset($categories) && !empty($categories)): ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= esc($cat->slug) ?>" <?= ($filters['category'] ?? '') === $cat->slug ? 'selected' : '' ?>>
                                            <?= $locale === 'hi' ? esc($cat->name_hi) : esc($cat->name_en) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Language -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold"><?= lang('News.language') ?? 'Language' ?></label>
                            <select name="language" class="form-select form-select-sm">
                                <option value="both" <?= ($filters['language'] ?? '') === 'both' || empty($filters['language']) ? 'selected' : '' ?>><?= lang('News.both') ?? 'Both' ?></option>
                                <option value="en" <?= ($filters['language'] ?? '') === 'en' ? 'selected' : '' ?>><?= lang('News.en') ?? 'English' ?></option>
                                <option value="hi" <?= ($filters['language'] ?? '') === 'hi' ? 'selected' : '' ?>><?= lang('News.hi') ?? 'Hindi' ?></option>
                            </select>
                        </div>

                        <!-- Date Range -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold"><?= lang('News.date_from') ?? 'From' ?></label>
                            <input type="date" name="date_from" class="form-control form-control-sm" value="<?= esc($filters['date_from'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold"><?= lang('News.date_to') ?? 'To' ?></label>
                            <input type="date" name="date_to" class="form-control form-control-sm" value="<?= esc($filters['date_to'] ?? '') ?>">
                        </div>

                        <!-- Author -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold"><?= lang('News.author') ?? 'Author' ?></label>
                            <input type="text" name="author" class="form-control form-control-sm" placeholder="<?= lang('News.author_username') ?? 'Username' ?>" value="<?= esc($filters['author'] ?? '') ?>">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-funnel-fill"></i> <?= lang('News.filter') ?? 'Filter' ?>
                            </button>
                            <a href="/<?= $locale ?>/search<?= !empty($query) ? '?q=' . urlencode($query) : '' ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-x-circle"></i> <?= lang('News.clear_filters') ?? 'Clear Filters' ?>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Search Results -->
        <div class="col-md-9 order-md-2">
            <h4 class="section-title">
                <?php if (!empty($query)): ?>
                    <?= lang('News.search_results') ?>: "<?= esc($query) ?>"
                <?php else: ?>
                    <?= lang('News.search') ?? 'Search' ?>
                <?php endif; ?>
            </h4>

            <div class="mb-4 position-relative">
                <form action="/<?= $locale ?>/search" method="GET" class="d-flex" id="search-form">
                    <div class="input-group flex-grow-1">
                        <input type="text" name="q" id="search-input" class="form-control form-control-lg" placeholder="<?= lang('News.search') ?>..." value="<?= esc($query ?? '') ?>" autocomplete="off">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search"></i> <?= lang('News.search') ?>
                        </button>
                    </div>
                </form>
                <!-- Autocomplete dropdown -->
                <div id="autocomplete-results" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1000; top: 100%;"></div>
            </div>

            <?php if (!empty($query) || !empty($filters['date_from']) || !empty($filters['date_to']) || !empty($filters['category']) || !empty($filters['author']) || !empty($filters['language'])): ?>
                <p class="text-muted mb-4">
                    <?= $total ?? 0 ?> <?= lang('News.results_found') ?>
                </p>
            <?php endif; ?>

            <?php if (isset($articles) && !empty($articles)): ?>
                <div class="row g-4">
                    <?php foreach ($articles as $article): ?>
                    <div class="col-12">
                        <div class="news-card">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <?php if ($article->featured_image): ?>
                                    <img src="<?= base_url($article->featured_image) ?>" class="img-fluid h-100 w-100" style="object-fit: cover;" alt="">
                                    <?php else: ?>
                                    <div style="background: var(--dark-color); height: 100%; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem; min-height: 180px;">
                                        <i class="bi bi-newspaper"></i>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <a href="/<?= $locale ?>/news/<?= $article->slug ?>">
                                                <?= esc($locale === 'hi' ? $article->title_hi : $article->title_en) ?>
                                            </a>
                                        </h5>
                                        <p class="card-text">
                                            <?= esc(substr($locale === 'hi' ? ($article->excerpt_hi ?? strip_tags($article->content_hi ?? '')) : ($article->excerpt_en ?? strip_tags($article->content_en ?? '')), 0, 200)) ?>
                                        </p>
                                        <small class="text-muted">
                                            <i class="bi bi-clock"></i> <?= date('d M Y', strtotime($article->published_at ?? $article->created_at)) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php elseif (!empty($query) || !empty($filters['date_from']) || !empty($filters['date_to']) || !empty($filters['category']) || !empty($filters['author']) || !empty($filters['language'])): ?>
                <div class="text-center py-5">
                    <i class="bi bi-search" style="font-size: 3rem; color: #ccc;"></i>
                    <h5 class="mt-3 text-muted"><?= lang('News.no_results') ?></h5>
                    <p><?= lang('News.try_different') ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Debounced autocomplete
let searchTimer;
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('search-input');
    if (!searchInput) return;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimer);
        var query = this.value.trim();
        var resultsContainer = document.getElementById('autocomplete-results');

        if (query.length < 2) {
            resultsContainer.innerHTML = '';
            return;
        }

        searchTimer = setTimeout(function() {
            fetch('/<?= $locale ?>/search/autocomplete?q=' + encodeURIComponent(query))
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    var html = '';
                    data.forEach(function(item) {
                        html += '<a href="' + item.url + '" class="list-group-item list-group-item-action py-2">' +
                                '<i class="bi bi-newspaper me-2 text-muted"></i>' +
                                item.value +
                                '</a>';
                    });
                    resultsContainer.innerHTML = html;
                })
                .catch(function() {
                    resultsContainer.innerHTML = '';
                });
        }, 300);
    });

    // Hide autocomplete when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#search-input') && !e.target.closest('#autocomplete-results')) {
            document.getElementById('autocomplete-results').innerHTML = '';
        }
    });
});
</script>
