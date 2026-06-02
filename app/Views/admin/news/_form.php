<?php if (!empty($errors = session()->getFlashdata('errors'))) : ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="bi bi-exclamation-triangle"></i> Please fix the following errors:</strong>
        <ul class="mb-0 mt-1">
            <?php foreach ($errors as $error) : ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form id="article-form" action="<?= $action ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <?php if (isset($article['id'])) : ?>
        <input type="hidden" name="id" value="<?= $article['id'] ?>">
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">English Content</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="title_en" class="form-label">Title (English) <span class="text-danger">*</span></label>
                <input type="text" name="title_en" id="title_en"
                       class="form-control <?= session()->getFlashdata('errors') && in_array('title_en', array_keys(session()->getFlashdata('errors_list') ?? [])) ? 'is-invalid' : '' ?>"
                       value="<?= old('title_en', $article['title_en'] ?? '') ?>"
                       placeholder="Enter article title in English" required>
            </div>
            <div class="mb-3">
                <label for="slug" class="form-label">URL Slug</label>
                <div class="input-group">
                    <input type="text" name="slug" id="slug"
                           class="form-control"
                           value="<?= old('slug', $article['slug'] ?? '') ?>"
                           placeholder="Auto-generated from title" readonly>
                    <button type="button" class="btn btn-outline-secondary" id="generate-slug" title="Generate from English title">
                        <i class="bi bi-arrow-repeat"></i>
                    </button>
                </div>
                <small class="text-muted">Leave empty to auto-generate from English title.</small>
            </div>
            <div class="mb-3">
                <label for="excerpt_en" class="form-label">Excerpt (English)</label>
                <textarea name="excerpt_en" id="excerpt_en" rows="3"
                          class="form-control"
                          placeholder="Brief summary in English"><?= old('excerpt_en', $article['excerpt_en'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="content_en" class="form-label">Content (English) <span class="text-danger">*</span></label>
                <textarea name="content_en" id="content_en" rows="15"
                          class="form-control" required
                          placeholder="Write your article content in English"><?= old('content_en', $article['content_en'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">हिंदी सामग्री</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="title_hi" class="form-label">शीर्षक (हिंदी) <span class="text-danger">*</span></label>
                <input type="text" name="title_hi" id="title_hi"
                       class="form-control <?= session()->getFlashdata('errors') && in_array('title_hi', array_keys(session()->getFlashdata('errors_list') ?? [])) ? 'is-invalid' : '' ?>"
                       value="<?= old('title_hi', $article['title_hi'] ?? '') ?>"
                       placeholder="हिंदी में लेख का शीर्षक दर्ज करें" required>
            </div>
            <div class="mb-3">
                <label for="excerpt_hi" class="form-label">संक्षिप्त विवरण (हिंदी)</label>
                <textarea name="excerpt_hi" id="excerpt_hi" rows="3"
                          class="form-control"
                          placeholder="हिंदी में संक्षिप्त सारांश"><?= old('excerpt_hi', $article['excerpt_hi'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="content_hi" class="form-label">सामग्री (हिंदी) <span class="text-danger">*</span></label>
                <textarea name="content_hi" id="content_hi" rows="15"
                          class="form-control" required
                          placeholder="हिंदी में अपनी लेख सामग्री लिखें"><?= old('content_hi', $article['content_hi'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">Metadata & SEO</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="meta_title" class="form-label">Meta Title</label>
                    <input type="text" name="meta_title" id="meta_title" class="form-control"
                           value="<?= old('meta_title', $article['meta_title'] ?? '') ?>"
                           placeholder="Leave empty to use English title">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="meta_keywords" class="form-label">Meta Keywords</label>
                    <input type="text" name="meta_keywords" id="meta_keywords" class="form-control"
                           value="<?= old('meta_keywords', $article['meta_keywords'] ?? '') ?>"
                           placeholder="Comma-separated keywords">
                </div>
            </div>
            <div class="mb-3">
                <label for="meta_description" class="form-label">Meta Description</label>
                <textarea name="meta_description" id="meta_description" rows="2" class="form-control"
                          placeholder="SEO description"><?= old('meta_description', $article['meta_description'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">Image & Tags</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="featured_image" class="form-label">Featured Image</label>
                <?php if (!empty($article['featured_image'])) : ?>
                    <div class="mb-2">
                        <img src="<?= base_url($article['featured_image']) ?>" alt="Current featured image"
                             class="img-thumbnail" style="max-height: 150px;">
                    </div>
                <?php endif; ?>
                <input type="file" name="featured_image" id="featured_image" class="form-control"
                       accept="image/jpeg,image/png,image/webp">
                <small class="text-muted">Max 2MB. Recommended: 1200x630px for social sharing.</small>
            </div>
            <div class="mb-3">
                <label for="image_caption" class="form-label">Image Caption</label>
                <input type="text" name="image_caption" id="image_caption" class="form-control"
                       value="<?= old('image_caption', $article['image_caption'] ?? '') ?>"
                       placeholder="Caption for the featured image">
            </div>
            <div class="mb-3">
                <label for="tags" class="form-label">Tags</label>
                <input type="text" name="tags" id="tags" class="form-control"
                       value="<?= old('tags', $article['tags_string'] ?? '') ?>"
                       placeholder="Enter comma-separated tags">
                <small class="text-muted">Separate tags with commas. Existing tags will be suggested.</small>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between mb-4">
        <a href="<?= site_url($locale . '/admin/news') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-x-circle"></i> Cancel
        </a>
        <div>
            <button type="submit" name="save_type" value="draft" class="btn btn-secondary me-2">
                <i class="bi bi-save"></i> Save as Draft
            </button>
            <button type="submit" name="save_type" value="publish" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Publish
            </button>
        </div>
    </div>
</form>

<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate slug from English title
    const titleEn = document.getElementById('title_en');
    const slug = document.getElementById('slug');
    const generateBtn = document.getElementById('generate-slug');

    function generateSlug(text) {
        return text.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_]+/g, '-')
            .replace(/^-+|-+$/g, '')
            || '';
    }

    if (titleEn && slug) {
        titleEn.addEventListener('blur', function() {
            if (!slug.value) {
                slug.value = generateSlug(this.value);
            }
        });
    }

    if (generateBtn && titleEn && slug) {
        generateBtn.addEventListener('click', function() {
            slug.value = generateSlug(titleEn.value);
        });
    }

    // Initialize CKEditor for English content
    if (document.querySelector('#content_en')) {
        ClassicEditor
            .create(document.querySelector('#content_en'))
            .catch(error => {
                console.error(error);
            });
    }

    // Initialize CKEditor for Hindi content
    if (document.querySelector('#content_hi')) {
        ClassicEditor
            .create(document.querySelector('#content_hi'))
            .catch(error => {
                console.error(error);
            });
    }
});
</script>
