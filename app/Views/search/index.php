<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            <h4 class="section-title">
                <?= lang('News.search_results') ?>: "<?= esc($query ?? '') ?>"
            </h4>

            <div class="mb-4">
                <form action="/<?= $locale ?>/search" method="GET" class="d-flex">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control form-control-lg" placeholder="<?= lang('News.search') ?>..." value="<?= esc($query ?? '') ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search"></i> <?= lang('News.search') ?>
                        </button>
                    </div>
                </form>
            </div>

            <?php if (!empty($query)): ?>
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
            <?php elseif (!empty($query)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-search" style="font-size: 3rem; color: #ccc;"></i>
                    <h5 class="mt-3 text-muted"><?= lang('News.no_results') ?></h5>
                    <p><?= lang('News.try_different') ?></p>
                </div>
            <?php endif; ?>
        </div>

        <?= view('templates/sidebar', ['categories' => $categories ?? [], 'tags' => $tags ?? [], 'popular' => $popular ?? [], 'locale' => $locale]) ?>
    </div>
</div>
