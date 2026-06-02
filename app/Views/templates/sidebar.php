<!-- Sidebar -->
<aside class="col-md-4">
    <!-- Popular Posts -->
    <?php if (isset($popular) && !empty($popular)): ?>
    <div class="sidebar-widget">
        <h5 class="widget-title"><i class="bi bi-fire text-primary me-2"></i><?= lang('News.popular') ?></h5>
        <?php foreach ($popular as $post): ?>
        <div class="popular-post-item">
            <?php if ($post->featured_image): ?>
            <img src="<?= base_url($post->featured_image) ?>" alt="<?= esc($locale === 'hi' ? $post->title_hi : $post->title_en) ?>">
            <?php endif; ?>
            <div>
                <div class="post-title">
                    <a href="/<?= $locale ?>/news/<?= $post->slug ?>">
                        <?= esc($locale === 'hi' ? $post->title_hi : $post->title_en) ?>
                    </a>
                </div>
                <div class="post-date">
                    <i class="bi bi-eye me-1"></i><?= $post->view_count ?> views
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Categories -->
    <?php if (isset($categories) && !empty($categories)): ?>
    <div class="sidebar-widget">
        <h5 class="widget-title"><i class="bi bi-folder me-2 text-primary"></i><?= lang('News.categories') ?></h5>
        <ul class="list-group list-group-flush">
            <?php foreach ($categories as $cat): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <a href="/<?= $locale ?>/category/<?= $cat->slug ?>">
                    <?= $locale === 'hi' ? esc($cat->name_hi) : esc($cat->name_en) ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- Tags -->
    <?php if (isset($tags) && !empty($tags)): ?>
    <div class="sidebar-widget">
        <h5 class="widget-title"><i class="bi bi-tags me-2 text-primary"></i><?= lang('News.tags') ?></h5>
        <div class="tag-cloud">
            <?php foreach ($tags as $tag): ?>
            <a href="/<?= $locale ?>/tag/<?= $tag->slug ?>">
                <?= $locale === 'hi' ? esc($tag->name_hi) : esc($tag->name_en) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</aside>
