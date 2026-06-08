/**
 * Hind Bihar - Frontend JavaScript (app.js)
 * News website interactive functionality
 */

(function () {
    'use strict';

    // =========================================================================
    // DOM Ready
    // =========================================================================
    document.addEventListener('DOMContentLoaded', function () {
        initBackToTop();
        initCommentReplies();
        initSearchAutocomplete();
        initLazyLoading();
        initSocialShare();
        initLanguageSwitcher();
        initSmoothScroll();
        initCurrentDate();
        initAlertDismiss();
    });

    // =========================================================================
    // 1. Back to Top Button
    // =========================================================================
    function initBackToTop() {
        // Create button if it doesn't exist
        var btn = document.getElementById('back-to-top');
        if (!btn) {
            btn = document.createElement('button');
            btn.id = 'back-to-top';
            btn.className = 'back-to-top';
            btn.setAttribute('aria-label', 'Back to top');
            btn.innerHTML = '<i class="bi bi-arrow-up"></i>';
            document.body.appendChild(btn);
        }

        var scrollThreshold = 400;
        var ticking = false;

        function updateButtonVisibility() {
            if (window.scrollY > scrollThreshold) {
                btn.classList.add('visible');
            } else {
                btn.classList.remove('visible');
            }
            ticking = false;
        }

        window.addEventListener('scroll', function () {
            if (!ticking) {
                requestAnimationFrame(updateButtonVisibility);
                ticking = true;
            }
        }, { passive: true });

        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // =========================================================================
    // 2. Comment Reply Toggle
    // =========================================================================
    function initCommentReplies() {
        // Use event delegation for reply buttons
        document.addEventListener('click', function (e) {
            var replyBtn = e.target.closest('.reply-btn');
            if (!replyBtn) return;

            e.preventDefault();

            var commentId = replyBtn.getAttribute('data-comment-id');
            var replyForm = document.getElementById('reply-form-' + commentId);
            var parentForm = document.getElementById('comment-form-main');

            // Hide all other reply forms
            document.querySelectorAll('.comment-reply-form').forEach(function (form) {
                if (form !== replyForm) {
                    form.classList.remove('active');
                }
            });

            if (replyForm) {
                var isActive = replyForm.classList.toggle('active');
                replyForm.querySelector('textarea').focus();

                // Scroll to reply form
                if (isActive) {
                    replyForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });

        // Cancel reply buttons
        document.addEventListener('click', function (e) {
            var cancelBtn = e.target.closest('.cancel-reply-btn');
            if (!cancelBtn) return;

            e.preventDefault();

            var replyForm = cancelBtn.closest('.comment-reply-form');
            if (replyForm) {
                replyForm.classList.remove('active');
                replyForm.querySelector('textarea').value = '';
            }
        });
    }

    // =========================================================================
    // 3. Search Autocomplete
    // =========================================================================
    function initSearchAutocomplete() {
        var searchInput = document.querySelector('.search-autocomplete-input');
        if (!searchInput) return;

        var dropdown = document.createElement('div');
        dropdown.className = 'search-autocomplete';
        searchInput.parentNode.style.position = 'relative';
        searchInput.parentNode.appendChild(dropdown);

        var debounceTimer;
        var currentIndex = -1;
        var results = [];
        var baseUrl = searchInput.getAttribute('data-base-url') || '';

        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            var query = searchInput.value.trim();

            if (query.length < 2) {
                dropdown.classList.remove('show');
                return;
            }

            debounceTimer = setTimeout(function () {
                fetchAutocompleteResults(query);
            }, 300);
        });

        searchInput.addEventListener('keydown', function (e) {
            var items = dropdown.querySelectorAll('.search-autocomplete-item');

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                currentIndex = Math.min(currentIndex + 1, items.length - 1);
                updateActiveItem(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                currentIndex = Math.max(currentIndex - 1, -1);
                updateActiveItem(items);
            } else if (e.key === 'Enter') {
                if (currentIndex >= 0 && items[currentIndex]) {
                    e.preventDefault();
                    items[currentIndex].click();
                }
            } else if (e.key === 'Escape') {
                dropdown.classList.remove('show');
                currentIndex = -1;
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!searchInput.parentNode.contains(e.target)) {
                dropdown.classList.remove('show');
                currentIndex = -1;
            }
        });

        function fetchAutocompleteResults(query) {
            var url = baseUrl + '/search/autocomplete?q=' + encodeURIComponent(query);

            fetch(url)
                .then(function (response) {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .then(function (data) {
                    results = data.slice(0, 8);
                    renderResults(results);
                })
                .catch(function () {
                    // Silently fail - autocomplete is progressive enhancement
                    dropdown.classList.remove('show');
                });
        }

        function renderResults(items) {
            dropdown.innerHTML = '';
            currentIndex = -1;

            if (!items || items.length === 0) {
                dropdown.innerHTML = '<div class="search-autocomplete-no-results">' +
                    (searchInput.getAttribute('data-no-results') || 'No results found') +
                    '</div>';
            } else {
                items.forEach(function (item, idx) {
                    var div = document.createElement('div');
                    div.className = 'search-autocomplete-item';
                    div.setAttribute('data-index', idx);
                    div.setAttribute('data-url', item.url || '');

                    var imgHtml = item.image
                        ? '<img src="' + item.image + '" alt="" loading="lazy">'
                        : '';

                    div.innerHTML = imgHtml +
                        '<div><div class="ac-title">' + escapeHtml(item.title) + '</div>' +
                        (item.category ? '<div class="ac-category">' + escapeHtml(item.category) + '</div>' : '') +
                        '</div>';

                    div.addEventListener('click', function () {
                        if (item.url) {
                            window.location.href = item.url;
                        }
                    });

                    dropdown.appendChild(div);
                });
            }

            dropdown.classList.add('show');
        }

        function updateActiveItem(items) {
            items.forEach(function (item, idx) {
                if (idx === currentIndex) {
                    item.classList.add('active');
                    item.scrollIntoView({ block: 'nearest' });
                } else {
                    item.classList.remove('active');
                }
            });
        }
    }

    // =========================================================================
    // 4. Lazy Loading Images
    // =========================================================================
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            var imageObserver = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        var img = entry.target;
                        var src = img.getAttribute('data-src');

                        if (src) {
                            img.src = src;
                            img.removeAttribute('data-src');
                        }

                        img.addEventListener('load', function () {
                            img.classList.add('loaded');
                        });

                        // Handle srcset
                        var srcset = img.getAttribute('data-srcset');
                        if (srcset) {
                            img.srcset = srcset;
                            img.removeAttribute('data-srcset');
                        }

                        imageObserver.unobserve(img);
                    }
                });
            }, {
                rootMargin: '100px 0px',
                threshold: 0.01
            });

            document.querySelectorAll('img[data-src]').forEach(function (img) {
                img.classList.add('lazy-placeholder');
                imageObserver.observe(img);
            });
        } else {
            // Fallback: load all images immediately
            document.querySelectorAll('img[data-src]').forEach(function (img) {
                var src = img.getAttribute('data-src');
                if (src) {
                    img.src = src;
                    img.removeAttribute('data-src');
                }
                var srcset = img.getAttribute('data-srcset');
                if (srcset) {
                    img.srcset = srcset;
                    img.removeAttribute('data-srcset');
                }
                img.classList.add('loaded');
            });
        }

        // Also handle native lazy loading images
        document.querySelectorAll('img[loading="lazy"]').forEach(function (img) {
            if (img.complete) {
                img.classList.add('loaded');
            } else {
                img.addEventListener('load', function () {
                    img.classList.add('loaded');
                });
            }
        });
    }

    // =========================================================================
    // 5. Social Share Buttons
    // =========================================================================
    function initSocialShare() {
        document.addEventListener('click', function (e) {
            var shareBtn = e.target.closest('.share-btn');
            if (!shareBtn) return;

            var platform = shareBtn.getAttribute('data-platform');
            var url = shareBtn.getAttribute('data-url') || window.location.href;
            var title = shareBtn.getAttribute('data-title') || document.title;

            if (platform === 'copy') {
                e.preventDefault();
                copyToClipboard(url, shareBtn);
                return;
            }

            if (platform === 'facebook') {
                e.preventDefault();
                var fbUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url);
                openShareWindow(fbUrl, 'facebook');
            } else if (platform === 'twitter') {
                e.preventDefault();
                var twUrl = 'https://twitter.com/intent/tweet?url=' + encodeURIComponent(url) +
                    '&text=' + encodeURIComponent(title);
                openShareWindow(twUrl, 'twitter');
            } else if (platform === 'whatsapp') {
                e.preventDefault();
                var waUrl = 'https://wa.me/?text=' + encodeURIComponent(title + ' ' + url);
                window.open(waUrl, '_blank', 'noopener');
            } else if (platform === 'telegram') {
                e.preventDefault();
                var tgUrl = 'https://t.me/share/url?url=' + encodeURIComponent(url) +
                    '&text=' + encodeURIComponent(title);
                openShareWindow(tgUrl, 'telegram');
            }
        });
    }

    function openShareWindow(url, name) {
        var width = 600;
        var height = 400;
        var left = (window.innerWidth - width) / 2;
        var top = (window.innerHeight - height) / 2;
        window.open(
            url,
            name + '-share',
            'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top +
            ',toolbar=0,status=0,menubar=0'
        );
    }

    function copyToClipboard(text, btn) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function () {
                showCopyFeedback(btn);
            }).catch(function () {
                fallbackCopy(text, btn);
            });
        } else {
            fallbackCopy(text, btn);
        }
    }

    function fallbackCopy(text, btn) {
        var textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        try {
            document.execCommand('copy');
            showCopyFeedback(btn);
        } catch (err) {
            // Copy failed silently
        }
        document.body.removeChild(textarea);
    }

    function showCopyFeedback(btn) {
        var originalText = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check-lg"></i> ' + (btn.getAttribute('data-copied') || 'Copied!');
        setTimeout(function () {
            btn.innerHTML = originalText;
        }, 2000);
    }

    // =========================================================================
    // 6. Language Switcher Enhancement
    // =========================================================================
    function initLanguageSwitcher() {
        var langSwitcher = document.querySelector('.language-switcher');
        if (!langSwitcher) return;

        langSwitcher.addEventListener('change', function () {
            var selectedLang = langSwitcher.value;
            if (selectedLang) {
                // Preserve current path but change locale prefix
                var currentPath = window.location.pathname;
                var pathParts = currentPath.split('/').filter(Boolean);

                // Replace locale prefix (en/hi)
                if (pathParts.length > 0 && (pathParts[0] === 'en' || pathParts[0] === 'hi')) {
                    pathParts[0] = selectedLang;
                } else {
                    pathParts.unshift(selectedLang);
                }

                window.location.href = '/' + pathParts.join('/');
            }
        });
    }

    // =========================================================================
    // 7. Smooth Scroll for Anchor Links
    // =========================================================================
    function initSmoothScroll() {
        document.addEventListener('click', function (e) {
            var link = e.target.closest('a[href^="#"]');
            if (!link) return;

            var targetId = link.getAttribute('href').substring(1);
            if (!targetId) return;

            var target = document.getElementById(targetId);
            if (!target) return;

            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });

            // Update URL hash without scrolling
            if (history.pushState) {
                history.pushState(null, null, '#' + targetId);
            }
        });
    }

    // =========================================================================
    // 8. Current Date Display
    // =========================================================================
    function initCurrentDate() {
        var dateEl = document.getElementById('current-date');
        if (!dateEl) return;

        var locale = document.documentElement.lang || 'en';
        var options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };

        try {
            dateEl.textContent = new Date().toLocaleDateString(
                locale === 'hi' ? 'hi-IN' : 'en-US',
                options
            );
        } catch (e) {
            dateEl.textContent = new Date().toDateString();
        }
    }

    // =========================================================================
    // 9. Auto-dismiss Alerts
    // =========================================================================
    function initAlertDismiss() {
        var alerts = document.querySelectorAll('.alert-dismissible:not(.alert-persistent)');
        alerts.forEach(function (alert) {
            setTimeout(function () {
                var closeBtn = alert.querySelector('.btn-close');
                if (closeBtn) {
                    closeBtn.click();
                } else {
                    alert.style.transition = 'opacity 0.3s ease';
                    alert.style.opacity = '0';
                    setTimeout(function () {
                        alert.remove();
                    }, 300);
                }
            }, 5000);
        });
    }

    // =========================================================================
    // 10. Utility Functions
    // =========================================================================
    function escapeHtml(text) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    // =========================================================================
    // 11. Reading Time Estimation (if .reading-time elements exist)
    // =========================================================================
    function estimateReadingTime() {
        var elements = document.querySelectorAll('.reading-time');
        elements.forEach(function (el) {
            var text = el.getAttribute('data-content') || el.textContent || '';
            var wordCount = text.trim().split(/\s+/).length;
            var wordsPerMinute = 200;
            var minutes = Math.max(1, Math.ceil(wordCount / wordsPerMinute));
            el.textContent = minutes + ' min read';
        });
    }

    // Also handle any dynamic reading time
    if (document.readyState === 'complete') {
        estimateReadingTime();
    } else {
        window.addEventListener('load', estimateReadingTime);
    }

})();
