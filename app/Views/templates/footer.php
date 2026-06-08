    </main>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5>Hind Bihar</h5>
                    <p><?= lang('News.footer_description') ?></p>
                    <div class="social-links">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-twitter-x"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <h5><?= lang('News.quick_links') ?></h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="/<?= $locale ?>"><?= lang('News.home') ?></a></li>
                        <li class="mb-2"><a href="/<?= $locale ?>/section/international"><?= lang('News.international') ?></a></li>
                        <li class="mb-2"><a href="/<?= $locale ?>/section/national"><?= lang('News.national') ?></a></li>
                        <li class="mb-2"><a href="/<?= $locale ?>/section/local"><?= lang('News.local') ?></a></li>
                        <li class="mb-2"><a href="/<?= $locale ?>/news"><?= lang('News.all_news') ?></a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5><?= lang('News.contact') ?></h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-envelope me-2"></i> contact@hindbihar.com</li>
                        <li class="mb-2"><i class="bi bi-telephone me-2"></i> +91-1234567890</li>
                        <li class="mb-2"><i class="bi bi-geo-alt me-2"></i> Patna, Bihar, India</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom text-center">
                <p class="mb-0">&copy; <?= date('Y') ?> Hind Bihar. <?= lang('News.all_rights_reserved') ?></p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
    <script>
        // Display current date
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const today = new Date();
        document.getElementById('current-date').textContent = today.toLocaleDateString('<?= $locale === 'hi' ? 'hi-IN' : 'en-US' ?>', options);
    </script>
</body>
</html>
