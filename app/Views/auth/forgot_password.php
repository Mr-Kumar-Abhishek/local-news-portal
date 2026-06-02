<div class="container py-4">
    <div class="auth-form">
        <h2><?= lang('News.forgot_password_title') ?></h2>

        <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= esc($error) ?></div>
        <?php endif; ?>

        <?php if (isset($message)): ?>
        <div class="alert alert-success"><?= esc($message) ?></div>
        <?php endif; ?>

        <?php if (isset($validation)): ?>
        <div class="alert alert-danger">
            <?= $validation->listErrors() ?>
        </div>
        <?php endif; ?>

        <form action="/<?= $locale ?>/forgot-password" method="POST">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label"><?= lang('News.email') ?></label>
                <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
            </div>
            <button type="submit" class="btn btn-primary w-100"><?= lang('News.forgot_password_button') ?></button>
        </form>

        <p class="text-center mt-3 mb-0">
            <a href="/<?= $locale ?>/login"><?= lang('News.login_title') ?></a>
        </p>
    </div>
</div>
