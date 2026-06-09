<div class="container py-4">
    <?php helper('jsonld'); ?>
    <?= breadcrumb_jsonld([
        ['label' => lang('News.nav_home') ?? 'Home', 'url' => '/' . $locale],
        ['label' => lang('News.home_title') ?? 'Hind Bihar', 'url' => null],
    ], $locale) ?>
    <?php if (isset($latest_news)): ?>
    <?= itemlist_jsonld($latest_news, $locale, lang('News.latest_news') ?? 'Latest News') ?>
    <?php endif; ?>
 
    <!-- Featured News Carousel -->
    <?php if (isset($featured_news) && !empty($featured_news)): ?>
    <div id="featuredCarousel" class="carousel slide featured-news mb-4" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <?php foreach ($featured_news as $i => $news): ?>
            <button type="button" data-bs-target="#featuredCarousel" data-bs-slide-to="<?= $i ?>" class="<?= $i === 0 ? 'active' : '' ?>"></button>
            <?php endforeach; ?>
        </div>
        <div class="carousel-inner">
            <?php foreach ($featured_news as $i => $news): ?>
            <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>" style="background-image: url('<?= $news->featured_image ? base_url($news->featured_image) : 'https://via.placeholder.com/1200x400?text=Hind+Bihar' ?>');">
                <div class="carousel-caption">
                    <span class="badge bg-primary mb-2"><?= lang('News.featured') ?></span>
                    <h3>
                        <a href="/<?= $locale ?>/news/<?= $news->slug ?>" class="text-white text-decoration-none">
                            <?= esc($locale === 'hi' ? $news->title_hi : $news->title_en) ?>
                        </a>
                    </h3>
                    <p><?= esc($locale === 'hi' ? $news->excerpt_hi : $news->excerpt_en) ?></p>
                    <small>
                        <i class="bi bi-clock"></i> <?= date('d M Y', strtotime($news->published_at ?? $news->created_at)) ?>
                    </small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#featuredCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#featuredCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <!-- Latest News -->
            <h4 class="section-title"><?= lang('News.latest_news') ?></h4>
            <div class="row g-4 mb-5">
                <?php if (isset($latest_news) && !empty($latest_news)): ?>
                    <?php foreach ($latest_news as $article): ?>
                    <div class="col-md-4">
                        <div class="news-card">
                            <?php if ($article->featured_image): ?>
                            <img src="<?= base_url($article->featured_image) ?>" class="card-img-top" alt="<?= esc($locale === 'hi' ? $article->title_hi : $article->title_en) ?>">
                            <?php else: ?>
                            <div class="card-img-top" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); height: 200px; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                                <i class="bi bi-newspaper"></i>
                            </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <span class="badge bg-primary category-badge">
                                    <?= esc($locale === 'hi' ? ($article->category_name_hi ?? 'समाचार') : ($article->category_name ?? 'News')) ?>
                                </span>
                                <h5 class="card-title">
                                    <a href="/<?= $locale ?>/news/<?= $article->slug ?>">
                                        <?= esc($locale === 'hi' ? $article->title_hi : $article->title_en) ?>
                                    </a>
                                </h5>
                                <p class="card-text">
                                    <?= esc(substr($locale === 'hi' ? ($article->excerpt_hi ?? '') : ($article->excerpt_en ?? ''), 0, 100)) ?>
                                </p>
                                <small class="text-muted">
                                    <i class="bi bi-clock"></i> <?= date('d M Y', strtotime($article->published_at ?? $article->created_at)) ?>
                                    <?php if ($article->author_name): ?>
                                    | <i class="bi bi-person"></i> <?= esc($article->author_name) ?>
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-newspaper" style="font-size: 3rem; color: #ccc;"></i>
                        <h5 class="mt-3 text-muted"><?= lang('News.no_news') ?></h5>
                    </div>
                <?php endif; ?>
            </div>

            <!-- International Section -->
            <?php if (isset($international) && !empty($international)): ?>
            <h4 class="section-title"><?= lang('News.international') ?></h4>
            <div class="row g-4 mb-5">
                <?php foreach ($international as $article): ?>
                <div class="col-md-6">
                    <div class="news-card">
                        <div class="row g-0">
                            <div class="col-4">
                                <?php if ($article->featured_image): ?>
                                <img src="<?= base_url($article->featured_image) ?>" class="img-fluid h-100" style="object-fit: cover;" alt="">
                                <?php else: ?>
                                <div style="background: var(--dark-color); height: 100%; display: flex; align-items: center; justify-content: center; color: white;">
                                    <i class="bi bi-globe"></i>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-8">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a href="/<?= $locale ?>/news/<?= $article->slug ?>">
                                            <?= esc($locale === 'hi' ? $article->title_hi : $article->title_en) ?>
                                        </a>
                                    </h6>
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
            <?php endif; ?>

            <!-- National Section -->
            <?php if (isset($national) && !empty($national)): ?>
            <h4 class="section-title"><?= lang('News.national') ?></h4>
            <div class="row g-4 mb-5">
                <?php foreach ($national as $article): ?>
                <div class="col-md-6">
                    <div class="news-card">
                        <div class="row g-0">
                            <div class="col-4">
                                <?php if ($article->featured_image): ?>
                                <img src="<?= base_url($article->featured_image) ?>" class="img-fluid h-100" style="object-fit: cover;" alt="">
                                <?php else: ?>
                                <div style="background: var(--dark-color); height: 100%; display: flex; align-items: center; justify-content: center; color: white;">
                                    <i class="bi bi-flag"></i>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-8">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a href="/<?= $locale ?>/news/<?= $article->slug ?>">
                                            <?= esc($locale === 'hi' ? $article->title_hi : $article->title_en) ?>
                                        </a>
                                    </h6>
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
            <?php endif; ?>

            <!-- Local Section -->
            <?php if (isset($local) && !empty($local)): ?>
            <h4 class="section-title"><?= lang('News.local') ?></h4>
            <div class="row g-4 mb-5">
                <?php foreach ($local as $article): ?>
                <div class="col-md-6">
                    <div class="news-card">
                        <div class="row g-0">
                            <div class="col-4">
                                <?php if ($article->featured_image): ?>
                                <img src="<?= base_url($article->featured_image) ?>" class="img-fluid h-100" style="object-fit: cover;" alt="">
                                <?php else: ?>
                                <div style="background: var(--dark-color); height: 100%; display: flex; align-items: center; justify-content: center; color: white;">
                                    <i class="bi bi-building"></i>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-8">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a href="/<?= $locale ?>/news/<?= $article->slug ?>">
                                            <?= esc($locale === 'hi' ? $article->title_hi : $article->title_en) ?>
                                        </a>
                                    </h6>
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
            <?php endif; ?>
        </div>

        <?= view('templates/sidebar', ['categories' => $categories ?? [], 'tags' => $tags ?? [], 'popular' => $popular ?? [], 'locale' => $locale]) ?>
    </div>
</div>
