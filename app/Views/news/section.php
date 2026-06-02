<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            <h4 class="section-title"><?= esc($section_name ?? lang('News.all_news')) ?></h4>

            <?php if (isset($articles) && !empty($articles)): ?>
                <div class="row g-4">
                    <?php foreach ($articles as $article): ?>
                    <div class="col-md-6">
                        <div class="news-card">
                            <?php if ($article->featured_image): ?>
                            <img src="<?= base_url($article->featured_image) ?>" class="card-img-top" alt="<?= esc($locale === 'hi' ? $article->title_hi : $article->title_en) ?>">
                            <?php else: ?>
                            <div class="card-img-top" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); height: 200px; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                                <i class="bi bi-newspaper"></i>
                            </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="/<?= $locale ?>/news/<?= $article->slug ?>">
                                        <?= esc($locale === 'hi' ? $article->title_hi : $article->title_en) ?>
                                    </a>
                                </h5>
                                <p class="card-text">
                                    <?= esc(substr($locale === 'hi' ? ($article->excerpt_hi ?? '') : ($article->excerpt_en ?? ''), 0, 120)) ?>
                                </p>
                                <small class="text-muted">
                                    <i class="bi bi-clock"></i> <?= date('d M Y', strtotime($article->published_at ?? $article->created_at)) ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if (isset($pager_links) && count($pager_links) > 1): ?>
                <div class="pagination-custom mt-4">
                    <?php foreach ($pager_links as $link): ?>
                        <?php if ($link['active']): ?>
                            <span class="active"><?= $link['page'] ?></span>
                        <?php else: ?>
                            <a href="<?= $link['url'] ?>"><?= $link['page'] ?></a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-newspaper" style="font-size: 3rem; color: #ccc;"></i>
                    <h5 class="mt-3 text-muted"><?= lang('News.no_news') ?></h5>
                </div>
            <?php endif; ?>
        </div>

        <?= view('templates/sidebar', ['categories' => $categories ?? [], 'tags' => $tags ?? [], 'popular' => $popular ?? [], 'locale' => $locale]) ?>
    </div>
</div>
