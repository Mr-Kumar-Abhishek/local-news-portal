<div class="container py-4">
    <div class="auth-form">
        <h2><?= lang('News.login') ?></h2>

        <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= esc($error) ?></div>
        <?php endif; ?>

        <?php if (session()->has('message')): ?>
        <div class="alert alert-success"><?= session('message') ?></div>
        <?php endif; ?>

        <?php if (isset($validation)): ?>
        <div class="alert alert-danger">
            <?= $validation->listErrors() ?>
        </div>
        <?php endif; ?>

        <form action="/<?= $locale ?>/auth/login" method="POST">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label"><?= lang('News.email') ?></label>
                <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><?= lang('News.password') ?></label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100"><?= lang('News.login') ?></button>
        </form>

        <p class="text-center mt-3 mb-0">
            <?= lang('News.no_account') ?> <a href="/<?= $locale ?>/auth/register"><?= lang('News.register') ?></a>
        </p>
    </div>
</div>
