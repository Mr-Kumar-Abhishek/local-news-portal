<div class="container py-4">
    <?php if (isset($breadcrumbs)): ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <?= breadcrumb($breadcrumbs) ?>
        </ol>
    </nav>
    <?php endif; ?>
 
    <?php helper('jsonld'); ?>
    <?php if (isset($breadcrumbs)): ?>
    <?= breadcrumb_jsonld($breadcrumbs, $locale) ?>
    <?php endif; ?>
    <?php if (isset($article)): ?>
    <?= article_jsonld($article, $locale) ?>
    <?php endif; ?>
 
    <div class="row">
        <div class="col-md-8">
            <article class="article-content">
                <?php if (isset($article)): ?>
 
                <h1><?= esc($locale === 'hi' ? $article->title_hi : $article->title_en) ?></h1>
                
                <div class="meta mb-4">
                    <span class="me-3">
                        <i class="bi bi-person"></i>
                        <?php if (isset($article->author_name)): ?>
                        <a href="/<?= $locale ?>/author/<?= esc($article->author_name) ?>"><?= esc($article->author_name) ?></a>
                        <?php else: ?>
                        <?= esc(lang('News.anonymous')) ?>
                        <?php endif; ?>
                    </span>
                    <span class="me-3">
                        <i class="bi bi-clock"></i> <?= date('d M Y, H:i', strtotime($article->published_at ?? $article->created_at)) ?>
                    </span>
                    <span class="me-3">
                        <i class="bi bi-eye"></i> <?= $article->view_count ?> <?= lang('News.views') ?>
                    </span>
                    <span>
                        <i class="bi bi-chat"></i> <?= count($comments ?? []) ?> <?= lang('News.comments') ?>
                    </span>
                </div>

                <?php if ($article->featured_image): ?>
                <img src="<?= base_url($article->featured_image) ?>" class="featured-image" alt="<?= esc($locale === 'hi' ? $article->title_hi : $article->title_en) ?>">
                <?php endif; ?>

                <div class="body">
                    <?= $locale === 'hi' ? $article->content_hi : $article->content_en ?>
                </div>

                <!-- Tags -->
                <?php if (isset($tags) && !empty($tags)): ?>
                <div class="mt-4">
                    <strong><?= lang('News.tags') ?>:</strong>
                    <?php foreach ($tags as $tag): ?>
                    <a href="/<?= $locale ?>/tag/<?= $tag->slug ?>" class="badge bg-secondary text-decoration-none me-1">
                        <?= $locale === 'hi' ? esc($tag->name_hi) : esc($tag->name_en) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Share Buttons -->
                <div class="mt-4 d-flex gap-2">
                    <strong class="me-2"><?= lang('News.share') ?>:</strong>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(current_url()) ?>" target="_blank" class="btn btn-primary btn-sm">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?= urlencode(current_url()) ?>&text=<?= urlencode($locale === 'hi' ? $article->title_hi : $article->title_en) ?>" target="_blank" class="btn btn-dark btn-sm">
                        <i class="bi bi-twitter-x"></i>
                    </a>
                    <a href="https://wa.me/?text=<?= urlencode($locale === 'hi' ? $article->title_hi : $article->title_en) ?> <?= urlencode(current_url()) ?>" target="_blank" class="btn btn-success btn-sm">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                </div>
                <?php endif; ?>
            </article>

            <!-- Related Articles -->
            <?php if (isset($related) && !empty($related)): ?>
            <div class="mt-5">
                <h4 class="section-title"><?= lang('News.related_articles') ?></h4>
                <div class="row g-3">
                    <?php foreach ($related as $rel): ?>
                    <div class="col-md-6">
                        <div class="news-card">
                            <div class="row g-0">
                                <div class="col-4">
                                    <?php if ($rel->featured_image): ?>
                                    <img src="<?= base_url($rel->featured_image) ?>" class="img-fluid h-100" style="object-fit: cover;" alt="">
                                    <?php else: ?>
                                    <div style="background: var(--dark-color); height: 100%; display: flex; align-items: center; justify-content: center; color: white;">
                                        <i class="bi bi-newspaper"></i>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-8">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <a href="/<?= $locale ?>/news/<?= $rel->slug ?>">
                                                <?= esc($locale === 'hi' ? $rel->title_hi : $rel->title_en) ?>
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="bi bi-clock"></i> <?= date('d M Y', strtotime($rel->published_at ?? $rel->created_at)) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Comments Section -->
            <div class="mt-5">
                <h4 class="section-title"><?= lang('News.comments') ?> (<?= count($comments ?? []) ?>)</h4>

                <?php
                /**
                 * Recursive helper to render threaded comments.
                 * @param array $comments Array of comment objects with optional ->children
                 * @param int   $depth    Current nesting depth
                 */
                function render_comments(array $comments, int $depth = 0): void {
                    foreach ($comments as $comment):
                        $marginClass = $depth > 0 ? 'ms-4' : '';
                ?>
                    <div class="card mb-2 <?= $marginClass ?>" id="comment-<?= $comment->id ?>">
                        <div class="card-body py-2 px-3">
                            <h6 class="card-subtitle mb-1 small fw-bold">
                                <i class="bi bi-person-circle"></i>
                                <?= esc($comment->author_name ?? lang('News.anonymous')) ?>
                                <?php if (!empty($comment->parent_id)): ?>
                                    <span class="text-muted"> &bull; <?= lang('News.in_reply_to') ?? 'in reply' ?></span>
                                <?php endif; ?>
                            </h6>
                            <small class="text-muted">
                                <i class="bi bi-clock"></i> <?= date('d M Y H:i', strtotime($comment->created_at)) ?>
                            </small>
                            <p class="card-text mt-1 mb-1 small"><?= esc($comment->body) ?></p>
                            <a href="#" class="btn btn-sm btn-link p-0 text-decoration-none reply-toggle"
                               data-comment-id="<?= $comment->id ?>">
                                <i class="bi bi-reply"></i> <?= lang('News.reply') ?? 'Reply' ?>
                            </a>
                            <form action="/<?= $locale ?>/news/report-comment/<?= $comment->id ?>" method="POST" style="display:inline;"
                                  onsubmit="return confirm('<?= lang('News.report_confirm') ?? 'Report this comment?' ?>')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none text-danger ms-2" title="Report">
                                    <i class="bi bi-flag"></i>
                                </button>
                            </form>

                            <!-- Hidden reply form -->
                            <div class="reply-form mt-2" id="reply-form-<?= $comment->id ?>" style="display: none;">
                                <form action="/<?= $locale ?>/news/comment/<?= $article->id ?? 0 ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="parent_id" value="<?= $comment->id ?>">
                                    <?php if (!session()->has('user_id')): ?>
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6">
                                            <input type="text" name="author_name" class="form-control form-control-sm" placeholder="<?= lang('News.your_name') ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="email" name="author_email" class="form-control form-control-sm" placeholder="<?= lang('News.your_email') ?>">
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <div class="mb-2">
                                        <textarea name="body" class="form-control form-control-sm" rows="2" placeholder="<?= lang('News.your_comment') ?>" required></textarea>
                                    </div>
                                    <?php if (!session()->has('user_id') && isset($captcha_num1, $captcha_num2)): ?>
                                    <div class="mb-2">
                                        <label class="form-label small">
                                            <?= lang('News.captcha_question') ?? 'What is' ?> <?= $captcha_num1 ?> + <?= $captcha_num2 ?>? (<?= lang('News.captcha_spam') ?? 'Spam protection' ?>)
                                        </label>
                                        <input type="number" name="captcha_answer" class="form-control form-control-sm" required style="max-width: 120px;">
                                    </div>
                                    <?php endif; ?>
                                    <button type="submit" class="btn btn-primary btn-sm"><?= lang('News.submit') ?></button>
                                    <button type="button" class="btn btn-secondary btn-sm reply-cancel" data-comment-id="<?= $comment->id ?>"><?= lang('News.cancel') ?? 'Cancel' ?></button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php
                        if (!empty($comment->children)) {
                            render_comments($comment->children, $depth + 1);
                        }
                    endforeach;
                }
                ?>

                <?php if (isset($comments) && !empty($comments)): ?>
                    <?php render_comments($comments); ?>
                <?php endif; ?>

                <!-- Main Comment Form -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h5><?= lang('News.leave_comment') ?></h5>
                        <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger">
                            <?php foreach (session('errors') as $error): ?>
                            <p class="mb-0"><?= esc($error) ?></p>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        <form action="/<?= $locale ?>/news/comment/<?= $article->id ?? 0 ?>" method="POST">
                            <?= csrf_field() ?>
                            <?php if (!session()->has('user_id')): ?>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <input type="text" name="author_name" class="form-control" placeholder="<?= lang('News.your_name') ?>">
                                </div>
                                <div class="col-md-6">
                                    <input type="email" name="author_email" class="form-control" placeholder="<?= lang('News.your_email') ?>">
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <textarea name="body" class="form-control" rows="4" placeholder="<?= lang('News.your_comment') ?>" required></textarea>
                            </div>
                            <?php if (!session()->has('user_id') && isset($captcha_num1, $captcha_num2)): ?>
                            <div class="mb-3">
                                <label class="form-label">
                                    <?= lang('News.captcha_question') ?? 'What is' ?> <?= $captcha_num1 ?> + <?= $captcha_num2 ?>? (<?= lang('News.captcha_spam') ?? 'Spam protection' ?>)
                                </label>
                                <input type="number" name="captcha_answer" class="form-control" required style="max-width: 150px;">
                            </div>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-primary"><?= lang('News.submit') ?></button>
                        </form>
                    </div>
                </div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Toggle reply forms
                document.querySelectorAll('.reply-toggle').forEach(function(link) {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        var commentId = this.getAttribute('data-comment-id');
                        var form = document.getElementById('reply-form-' + commentId);
                        if (form) {
                            form.style.display = form.style.display === 'none' ? 'block' : 'none';
                        }
                    });
                });

                // Cancel reply buttons
                document.querySelectorAll('.reply-cancel').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        var commentId = this.getAttribute('data-comment-id');
                        var form = document.getElementById('reply-form-' + commentId);
                        if (form) {
                            form.style.display = 'none';
                        }
                    });
                });
            });
            </script>
        </div>

        <?= view('templates/sidebar', ['categories' => $categories ?? [], 'tags' => $tags_sidebar ?? [], 'popular' => $popular ?? [], 'locale' => $locale]) ?>
    </div>
</div>
