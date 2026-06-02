<div class="container py-4">
    <div class="auth-form">
        <h2><?= lang('News.register') ?></h2>

        <?php if (isset($validation)): ?>
        <div class="alert alert-danger">
            <?= $validation->listErrors() ?>
        </div>
        <?php endif; ?>

        <form action="/<?= $locale ?>/register" method="POST">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label"><?= lang('News.full_name') ?></label>
                <input type="text" name="full_name" class="form-control" value="<?= old('full_name') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label"><?= lang('News.username') ?> *</label>
                <input type="text" name="username" class="form-control" value="<?= old('username') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><?= lang('News.email') ?> *</label>
                <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><?= lang('News.password') ?> *</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100"><?= lang('News.register') ?></button>
        </form>

        <p class="text-center mt-3 mb-0">
            <?= lang('News.have_account') ?> <a href="/<?= $locale ?>/login"><?= lang('News.login') ?></a>
        </p>
    </div>
</div>
