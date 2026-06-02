<?= $this->extend('admin/templates/header') ?>

<?= $this->section('title') ?>Edit Category - Hind Bihar<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Category</h1>
    <a href="<?= site_url($locale . '/admin/categories') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Categories
    </a>
</div>

<?php if (!empty($errors = session()->getFlashdata('errors'))) : ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1">
            <?php foreach ($errors as $error) : ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?= site_url($locale . '/admin/categories/edit/' . $category['id']) ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $category['id'] ?>">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name_en" class="form-label">Name (English) <span class="text-danger">*</span></label>
                    <input type="text" name="name_en" id="name_en" class="form-control"
                           value="<?= old('name_en', $category['name_en']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="name_hi" class="form-label">नाम (हिंदी) <span class="text-danger">*</span></label>
                    <input type="text" name="name_hi" id="name_hi" class="form-control"
                           value="<?= old('name_hi', $category['name_hi']) ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="slug" class="form-label">URL Slug</label>
                    <input type="text" name="slug" id="slug" class="form-control"
                           value="<?= old('slug', $category['slug']) ?>">
                </div>
                <div class="col-md-3">
                    <label for="section" class="form-label">Section <span class="text-danger">*</span></label>
                    <select name="section" id="section" class="form-select" required>
                        <option value="">Select Section</option>
                        <option value="international" <?= (old('section', $category['section'] ?? '')) === 'international' ? 'selected' : '' ?>>International</option>
                        <option value="national" <?= (old('section', $category['section'] ?? '')) === 'national' ? 'selected' : '' ?>>National</option>
                        <option value="local" <?= (old('section', $category['section'] ?? '')) === 'local' ? 'selected' : '' ?>>Local</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="active" <?= (old('status', $category['status'] ?? 'active')) === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= (old('status', $category['status'] ?? '')) === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="parent_id" class="form-label">Parent Category</label>
                <select name="parent_id" id="parent_id" class="form-select">
                    <option value="">None (Top Level)</option>
                    <?php if (!empty($categories)) : ?>
                        <?php foreach ($categories as $cat) : ?>
                            <?php if ($cat['id'] != $category['id']) : ?>
                                <option value="<?= $cat['id'] ?>" <?= (old('parent_id', $category['parent_id'])) == $cat['id'] ? 'selected' : '' ?>>
                                    <?= esc($cat['name_hi'] ?: $cat['name_en']) ?>
                                    (<?= esc($cat['section'] ?? 'general') ?>)
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" rows="3" class="form-control"><?= old('description', $category['description'] ?? '') ?></textarea>
            </div>

            <div class="d-flex justify-content-between">
                <a href="<?= site_url($locale . '/admin/categories') ?>" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Update Category
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
