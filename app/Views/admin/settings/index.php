<?= $this->extend('admin/templates/header') ?>

<?= $this->section('title') ?>Site Settings - Hind Bihar<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Site Settings</h1>
</div>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<form action="<?= site_url($locale . '/admin/settings') ?>" method="post">
    <?= csrf_field() ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">General Settings</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="site_name" class="form-label">Site Name <span class="text-danger">*</span></label>
                    <input type="text" name="site_name" id="site_name" class="form-control"
                           value="<?= old('site_name', $settings['site_name'] ?? 'Hind Bihar') ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="site_tagline" class="form-label">Tagline</label>
                    <input type="text" name="site_tagline" id="site_tagline" class="form-control"
                           value="<?= old('site_tagline', $settings['site_tagline'] ?? '') ?>"
                           placeholder="Site description or tagline">
                </div>
            </div>
            <div class="mb-3">
                <label for="site_description" class="form-label">Meta Description</label>
                <textarea name="site_description" id="site_description" rows="2" class="form-control"
                          placeholder="Default SEO meta description"><?= old('site_description', $settings['site_description'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="site_keywords" class="form-label">Meta Keywords</label>
                <input type="text" name="site_keywords" id="site_keywords" class="form-control"
                       value="<?= old('site_keywords', $settings['site_keywords'] ?? '') ?>"
                       placeholder="Comma-separated keywords">
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">Contact & Social Media</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="contact_email" class="form-label">Contact Email</label>
                    <input type="email" name="contact_email" id="contact_email" class="form-control"
                           value="<?= old('contact_email', $settings['contact_email'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="contact_phone" class="form-label">Contact Phone</label>
                    <input type="text" name="contact_phone" id="contact_phone" class="form-control"
                           value="<?= old('contact_phone', $settings['contact_phone'] ?? '') ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="facebook_url" class="form-label">Facebook URL</label>
                    <input type="url" name="facebook_url" id="facebook_url" class="form-control"
                           value="<?= old('facebook_url', $settings['facebook_url'] ?? '') ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="twitter_url" class="form-label">Twitter URL</label>
                    <input type="url" name="twitter_url" id="twitter_url" class="form-control"
                           value="<?= old('twitter_url', $settings['twitter_url'] ?? '') ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="instagram_url" class="form-label">Instagram URL</label>
                    <input type="url" name="instagram_url" id="instagram_url" class="form-control"
                           value="<?= old('instagram_url', $settings['instagram_url'] ?? '') ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="youtube_url" class="form-label">YouTube URL</label>
                    <input type="url" name="youtube_url" id="youtube_url" class="form-control"
                           value="<?= old('youtube_url', $settings['youtube_url'] ?? '') ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">Appearance & Configuration</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="posts_per_page" class="form-label">Articles Per Page</label>
                    <input type="number" name="posts_per_page" id="posts_per_page" class="form-control"
                           value="<?= old('posts_per_page', $settings['posts_per_page'] ?? '12') ?>"
                           min="5" max="50">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="default_language" class="form-label">Default Language</label>
                    <select name="default_language" id="default_language" class="form-select">
                        <option value="en" <?= (old('default_language', $settings['default_language'] ?? 'en')) === 'en' ? 'selected' : '' ?>>English</option>
                        <option value="hi" <?= (old('default_language', $settings['default_language'] ?? '')) === 'hi' ? 'selected' : '' ?>>हिंदी</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="timezone" class="form-label">Timezone</label>
                    <select name="timezone" id="timezone" class="form-select">
                        <?php
                        $timezones = [
                            'Asia/Kolkata' => 'India (IST)',
                            'UTC' => 'UTC',
                            'America/New_York' => 'Eastern (EST)',
                            'America/Chicago' => 'Central (CST)',
                            'America/Denver' => 'Mountain (MST)',
                            'America/Los_Angeles' => 'Pacific (PST)',
                            'Europe/London' => 'London (GMT)',
                            'Europe/Berlin' => 'Berlin (CET)',
                            'Asia/Dubai' => 'Dubai (GST)',
                            'Asia/Singapore' => 'Singapore (SGT)',
                        ];
                        $currentTz = old('timezone', $settings['timezone'] ?? 'Asia/Kolkata');
                        ?>
                        <?php foreach ($timezones as $tz => $label) : ?>
                            <option value="<?= $tz ?>" <?= $currentTz === $tz ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="google_analytics_id" class="form-label">Google Analytics ID</label>
                    <input type="text" name="google_analytics_id" id="google_analytics_id" class="form-control"
                           value="<?= old('google_analytics_id', $settings['google_analytics_id'] ?? '') ?>"
                           placeholder="G-XXXXXXXXXX">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="custom_css" class="form-label">Custom CSS</label>
                    <textarea name="custom_css" id="custom_css" rows="3" class="form-control"
                              placeholder="Additional CSS rules"><?= old('custom_css', $settings['custom_css'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between mb-4">
        <a href="<?= site_url($locale . '/admin/dashboard') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle"></i> Save Settings
        </button>
    </div>
</form>
<?= $this->endSection() ?>
