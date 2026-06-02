<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Create Article</h1>
    <a href="<?= site_url($locale . '/admin/news') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to News
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <?= view('App\Views\admin\news\_form', ['action' => site_url($locale . '/admin/news/create'), 'article' => $article ?? []]) ?>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Publishing Options</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" form="article-form" class="form-select">
                        <option value="draft" <?= ($article['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= ($article['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                        <option value="archived" <?= ($article['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Archived</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Section</label>
                    <select name="section" form="article-form" class="form-select" required>
                        <option value="">Select Section</option>
                        <option value="international" <?= ($article['section'] ?? '') === 'international' ? 'selected' : '' ?>>International</option>
                        <option value="national" <?= ($article['section'] ?? '') === 'national' ? 'selected' : '' ?>>National</option>
                        <option value="local" <?= ($article['section'] ?? '') === 'local' ? 'selected' : '' ?>>Local</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" form="article-form" class="form-select">
                        <option value="">Select Category</option>
                        <?php if (!empty($categories)) : ?>
                            <?php foreach ($categories as $cat) : ?>
                                <option value="<?= $cat['id'] ?>" <?= ($article['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= esc($cat['name_hi'] ?: $cat['name_en']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_featured" id="is_featured" form="article-form" class="form-check-input"
                           value="1" <?= ($article['is_featured'] ?? false) ? 'checked' : '' ?>>
                    <label for="is_featured" class="form-check-label">Mark as Featured</label>
                </div>
            </div>
        </div>
    </div>
</div>
