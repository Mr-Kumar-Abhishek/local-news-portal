<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Media Library</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
        <i class="bi bi-upload"></i> Upload Media
    </button>
</div>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url($locale . '/admin/media/upload') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Upload Media</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Choose File <span class="text-danger">*</span></label>
                        <input type="file" name="file" id="file" class="form-control" required
                               accept="image/jpeg,image/png,image/webp,image/gif,application/pdf">
                        <small class="text-muted">Allowed: JPG, PNG, WebP, GIF, PDF. Max 5MB.</small>
                    </div>
                    <div class="mb-3">
                        <label for="alt_text" class="form-label">Alt Text</label>
                        <input type="text" name="alt_text" id="alt_text" class="form-control"
                               placeholder="Describe the image for accessibility">
                    </div>
                    <div class="mb-3">
                        <label for="caption" class="form-label">Caption</label>
                        <input type="text" name="caption" id="caption" class="form-control"
                               placeholder="Image caption">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Media Grid -->
<?php if (!empty($media_files)) : ?>
    <div class="row g-3">
        <?php foreach ($media_files as $media) : ?>
        <?php 
            $ext = pathinfo($media['filepath'], PATHINFO_EXTENSION);
            $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
            $altText = ($locale === 'hi') ? ($media['alt_text_hi'] ?: $media['alt_text_en']) : ($media['alt_text_en'] ?: $media['alt_text_hi']);
            $displayAlt = $altText ?: basename($media['filepath']);
            $displaySrc = !empty($media['thumbnail_path']) ? base_url($media['thumbnail_path']) : base_url($media['filepath']);
        ?>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
            <div class="card shadow-sm h-100">
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                    <?php if ($isImage) : ?>
                        <img src="<?= $displaySrc ?>" alt="<?= esc($displayAlt) ?>"
                             class="img-fluid" style="max-height: 150px; object-fit: cover;">
                    <?php else : ?>
                        <i class="bi bi-file-earmark-text fs-1 text-muted"></i>
                    <?php endif; ?>
                </div>
                <div class="card-body p-2">
                    <p class="card-text small mb-1 text-truncate" title="<?= esc($displayAlt) ?>">
                        <?= esc($displayAlt) ?>
                    </p>
                    <small class="text-muted">
                        <?= strtoupper($ext) ?>
                        &middot;
                        <?php if ($media['filesize'] > 1048576) : ?>
                            <?= round($media['filesize'] / 1048576, 1) ?> MB
                        <?php elseif ($media['filesize'] > 1024) : ?>
                            <?= round($media['filesize'] / 1024, 1) ?> KB
                        <?php else : ?>
                            <?= $media['filesize'] ?> B
                        <?php endif; ?>
                    </small>
                    <div class="mt-2 d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-outline-primary"
                                onclick="copyUrl('<?= base_url($media['filepath']) ?>')"
                                title="Copy URL">
                            <i class="bi bi-clipboard"></i>
                        </button>
                        <form action="<?= site_url($locale . '/admin/media/delete/' . $media['id']) ?>" method="post"
                              onsubmit="return confirm('Delete this media permanently?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php else : ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-images fs-1 d-block mb-3"></i>
        <h5>No media uploaded yet</h5>
        <p>Click the "Upload Media" button to get started.</p>
    </div>
<?php endif; ?>

<!-- Pagination -->
<?php if (!empty($pager)) : ?>
<div class="mt-4">
    <?= $pager->links() ?>
</div>
<?php endif; ?>

<script>
function copyUrl(url) {
    navigator.clipboard.writeText(url).then(function() {
        // Simple feedback
        const btn = event.currentTarget;
        const original = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check"></i>';
        setTimeout(function() {
            btn.innerHTML = original;
        }, 2000);
    });
}
</script>
