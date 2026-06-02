<div class="container py-4">
    <div class="auth-form">
        <h2><?= lang('News.reset_password_title') ?></h2>

        <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?= esc($error) ?>
            <p class="mt-3 mb-0">
                <a href="/<?= $locale ?>/forgot-password" class="btn btn-outline-danger btn-sm"><?= lang('News.forgot_password_title') ?></a>
            </p>
        </div>
        <?php else: ?>

        <?php if (isset($validation)): ?>
        <div class="alert alert-danger">
            <?= $validation->listErrors() ?>
        </div>
        <?php endif; ?>

        <form action="/<?= $locale ?>/reset-password/<?= esc($token) ?>" method="POST">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label"><?= lang('News.login_password') ?></label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><?= lang('News.confirm_password') ?></label>
                <input type="password" name="password_confirm" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100"><?= lang('News.reset_password_button') ?></button>
        </form>
        <?php endif; ?>
    </div>
</div>
