/**
 * Hind Bihar - Admin Panel JavaScript (admin.js)
 * Admin dashboard interactive functionality
 */

(function () {
    'use strict';

    // =========================================================================
    // DOM Ready
    // =========================================================================
    document.addEventListener('DOMContentLoaded', function () {
        initConfirmDialogs();
        initDeleteHandlers();
        initFileUploadPreview();
        initTagInput();
        initStatusFilter();
        initSelectAll();
        initAutoDismissAlerts();
        initBulkActions();
        initFormValidation();
        initSidebarToggle();
    });

    // =========================================================================
    // 1. Confirm Dialogs (Bootstrap Modal)
    // =========================================================================
    function initConfirmDialogs() {
        // Create a reusable confirm modal if it doesn't exist
        if (!document.getElementById('confirmModal')) {
            createConfirmModal();
        }

        // Handle data-confirm-modal buttons
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-confirm-modal]');
            if (!btn) return;

            e.preventDefault();

            var message = btn.getAttribute('data-confirm-message') || 'Are you sure you want to perform this action?';
            var title = btn.getAttribute('data-confirm-title') || 'Confirm Action';
            var confirmText = btn.getAttribute('data-confirm-btn') || 'Yes, proceed';
            var cancelText = btn.getAttribute('data-cancel-btn') || 'Cancel';
            var form = btn.closest('form');
            var href = btn.getAttribute('href');

            showConfirmModal(title, message, confirmText, cancelText, function () {
                if (form) {
                    form.submit();
                } else if (href) {
                    window.location.href = href;
                }
            });
        });
    }

    function createConfirmModal() {
        var modal = document.createElement('div');
        modal.className = 'modal fade modal-confirm';
        modal.id = 'confirmModal';
        modal.tabIndex = -1;
        modal.setAttribute('aria-hidden', 'true');
        modal.innerHTML = '<div class="modal-dialog modal-dialog-centered">' +
            '<div class="modal-content">' +
            '<div class="modal-header">' +
            '<h5 class="modal-title" id="confirmModalTitle">Confirm</h5>' +
            '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
            '</div>' +
            '<div class="modal-body" id="confirmModalBody">' +
            '<p>Are you sure?</p>' +
            '</div>' +
            '<div class="modal-footer">' +
            '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="confirmModalCancel">Cancel</button>' +
            '<button type="button" class="btn btn-danger" id="confirmModalConfirm">Confirm</button>' +
            '</div>' +
            '</div>' +
            '</div>';
        document.body.appendChild(modal);
    }

    function showConfirmModal(title, message, confirmText, cancelText, onConfirm) {
        var modalEl = document.getElementById('confirmModal');

        document.getElementById('confirmModalTitle').textContent = title;
        document.getElementById('confirmModalBody').innerHTML = '<p>' + message + '</p>';
        document.getElementById('confirmModalConfirm').textContent = confirmText;
        document.getElementById('confirmModalCancel').textContent = cancelText;

        var confirmBtn = document.getElementById('confirmModalConfirm');
        var newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

        newConfirmBtn.addEventListener('click', function () {
            var modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
            if (typeof onConfirm === 'function') {
                onConfirm();
            }
        });

        var modal = new bootstrap.Modal(modalEl);
        modal.show();
    }

    // =========================================================================
    // 2. Delete Handlers (Simple confirm)
    // =========================================================================
    function initDeleteHandlers() {
        document.addEventListener('submit', function (e) {
            var form = e.target.closest('form[data-confirm]');
            if (!form) return;

            if (!confirm(form.getAttribute('data-confirm') || 'Are you sure you want to delete this item?')) {
                e.preventDefault();
                return false;
            }
        });

        // Inline delete links (non-form)
        document.addEventListener('click', function (e) {
            var link = e.target.closest('a[data-delete-confirm]');
            if (!link) return;

            var message = link.getAttribute('data-delete-confirm') || 'Are you sure you want to delete this item?';
            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }
        });
    }

    // =========================================================================
    // 3. File Upload Preview
    // =========================================================================
    function initFileUploadPreview() {
        document.addEventListener('change', function (e) {
            var input = e.target.closest('input[type="file"][data-preview]');
            if (!input) return;

            var previewId = input.getAttribute('data-preview');
            var previewContainer = document.getElementById(previewId);
            if (!previewContainer) {
                // Create preview container after the input
                previewContainer = document.createElement('div');
                previewContainer.id = previewId || 'file-preview-' + Date.now();
                previewContainer.className = 'form-file-preview';
                input.parentNode.appendChild(previewContainer);
            }

            previewContainer.innerHTML = '';

            if (input.files && input.files.length > 0) {
                Array.from(input.files).forEach(function (file) {
                    if (!file.type.startsWith('image/')) {
                        var info = document.createElement('div');
                        info.className = 'file-info';
                        info.innerHTML = '<i class="bi bi-file-earmark me-2"></i>' +
                            file.name + ' (' + formatFileSize(file.size) + ')';
                        previewContainer.appendChild(info);
                        return;
                    }

                    var reader = new FileReader();
                    reader.onload = function (e) {
                        var img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = file.name;
                        previewContainer.appendChild(img);

                        var info = document.createElement('div');
                        info.className = 'file-info';
                        info.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
                        previewContainer.appendChild(info);
                    };
                    reader.readAsDataURL(file);
                });
            }
        });
    }

    // =========================================================================
    // 4. Tag Input Enhancement
    // =========================================================================
    function initTagInput() {
        var tagInputs = document.querySelectorAll('.tag-input-wrapper');
        tagInputs.forEach(function (wrapper) {
            var input = wrapper.querySelector('input.tag-input');
            var hiddenInput = wrapper.querySelector('input[type="hidden"]');
            if (!input || !hiddenInput) return;

            var tags = [];

            // Load existing tags from hidden input
            var existingValue = hiddenInput.value;
            if (existingValue) {
                tags = existingValue.split(',').map(function (t) { return t.trim(); }).filter(Boolean);
                tags.forEach(function (tag) { addTagItem(wrapper, tag); });
            }

            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ',') {
                    e.preventDefault();
                    var value = input.value.trim().replace(/,/g, '');
                    if (value && tags.indexOf(value) === -1) {
                        tags.push(value);
                        addTagItem(wrapper, value);
                        input.value = '';
                        updateHiddenInput();
                    }
                } else if (e.key === 'Backspace' && input.value === '' && tags.length > 0) {
                    // Remove last tag on backspace
                    var lastTag = tags.pop();
                    var tagEl = wrapper.querySelector('.tag-item:last-child');
                    if (tagEl) tagEl.remove();
                    updateHiddenInput();
                }
            });

            // Remove tag on click of x button
            wrapper.addEventListener('click', function (e) {
                var removeBtn = e.target.closest('.tag-remove');
                if (!removeBtn) return;

                var tagItem = removeBtn.closest('.tag-item');
                var tagText = tagItem.getAttribute('data-tag');
                tags = tags.filter(function (t) { return t !== tagText; });
                tagItem.remove();
                updateHiddenInput();
                input.focus();
            });

            // Focus input when clicking wrapper
            wrapper.addEventListener('click', function (e) {
                if (e.target === wrapper) {
                    input.focus();
                }
            });

            function addTagItem(container, tagText) {
                var tagItem = document.createElement('span');
                tagItem.className = 'tag-item';
                tagItem.setAttribute('data-tag', tagText);
                tagItem.innerHTML = tagText +
                    '<button type="button" class="tag-remove" aria-label="Remove tag">&times;</button>';
                container.insertBefore(tagItem, input);
            }

            function updateHiddenInput() {
                hiddenInput.value = tags.join(',');
                // Dispatch change event for any listeners
                hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    }

    // =========================================================================
    // 5. Status Filter Interactions
    // =========================================================================
    function initStatusFilter() {
        var statusFilters = document.querySelectorAll('.status-filter');
        statusFilters.forEach(function (filter) {
            filter.addEventListener('change', function () {
                var form = filter.closest('form');
                if (form) {
                    form.submit();
                } else {
                    // Navigate with query parameter
                    var url = new URL(window.location.href);
                    url.searchParams.set(filter.getAttribute('name') || 'status', filter.value);
                    url.searchParams.set('page', '1'); // Reset to first page
                    window.location.href = url.toString();
                }
            });
        });

        // Quick filter links (All, Published, Draft, etc.)
        var quickFilters = document.querySelectorAll('.quick-filter');
        quickFilters.forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();

                // Update active state
                quickFilters.forEach(function (l) { l.classList.remove('active'); });
                link.classList.add('active');

                var status = link.getAttribute('data-status');
                var url = new URL(window.location.href);
                if (status) {
                    url.searchParams.set('status', status);
                } else {
                    url.searchParams.delete('status');
                }
                url.searchParams.set('page', '1');
                window.location.href = url.toString();
            });
        });
    }

    // =========================================================================
    // 6. Select All Checkboxes
    // =========================================================================
    function initSelectAll() {
        document.addEventListener('change', function (e) {
            var selectAll = e.target.closest('#select-all');
            if (!selectAll) return;

            var table = selectAll.closest('table');
            if (!table) return;

            var checkboxes = table.querySelectorAll('.select-item');
            checkboxes.forEach(function (cb) {
                cb.checked = selectAll.checked;
            });

            updateBulkActionState();
        });

        // Individual checkbox changes
        document.addEventListener('change', function (e) {
            var checkbox = e.target.closest('.select-item');
            if (!checkbox) return;

            updateBulkActionState();
        });
    }

    function updateBulkActionState() {
        var checkedCount = document.querySelectorAll('.select-item:checked').length;
        var bulkActions = document.querySelectorAll('.bulk-action-btn');

        bulkActions.forEach(function (btn) {
            btn.disabled = checkedCount === 0;
        });

        var countDisplay = document.getElementById('selected-count');
        if (countDisplay) {
            countDisplay.textContent = checkedCount;
        }
    }

    // =========================================================================
    // 7. Bulk Actions
    // =========================================================================
    function initBulkActions() {
        document.addEventListener('click', function (e) {
            var bulkBtn = e.target.closest('.bulk-action-btn');
            if (!bulkBtn || bulkBtn.disabled) return;

            var action = bulkBtn.getAttribute('data-action');
            var confirmMsg = bulkBtn.getAttribute('data-confirm') ||
                'Are you sure you want to perform this action on selected items?';

            var selectedIds = [];
            document.querySelectorAll('.select-item:checked').forEach(function (cb) {
                selectedIds.push(cb.value);
            });

            if (selectedIds.length === 0) {
                e.preventDefault();
                return;
            }

            if (!confirm(confirmMsg)) {
                e.preventDefault();
                return;
            }

            // Find or create bulk action form
            var bulkForm = document.getElementById('bulk-action-form');
            if (!bulkForm) {
                bulkForm = document.createElement('form');
                bulkForm.id = 'bulk-action-form';
                bulkForm.method = 'POST';
                bulkForm.style.display = 'none';

                // Add CSRF token if available
                var csrfToken = document.querySelector('input[name="' +
                    (document.querySelector('meta[name="csrf-token"]')
                        ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        : 'csrf_token') + '"]');
                if (csrfToken) {
                    var csrfClone = csrfToken.cloneNode(true);
                    bulkForm.appendChild(csrfClone);
                }

                document.body.appendChild(bulkForm);
            }

            // Set action URL
            bulkForm.action = bulkBtn.getAttribute('data-url') || window.location.href;

            // Clear previous hidden inputs
            bulkForm.querySelectorAll('input[name="ids[]"], input[name="action"]').forEach(function (el) {
                el.remove();
            });

            // Add action
            var actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = action;
            bulkForm.appendChild(actionInput);

            // Add IDs
            selectedIds.forEach(function (id) {
                var idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'ids[]';
                idInput.value = id;
                bulkForm.appendChild(idInput);
            });

            e.preventDefault();
            bulkForm.submit();
        });
    }

    // =========================================================================
    // 8. Form Validation Enhancements
    // =========================================================================
    function initFormValidation() {
        var forms = document.querySelectorAll('form[data-validate]');
        forms.forEach(function (form) {
            form.addEventListener('submit', function (e) {
                var isValid = true;

                // Check required fields
                form.querySelectorAll('[required]').forEach(function (field) {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;

                        // Add feedback if not present
                        var feedback = field.nextElementSibling;
                        if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                            feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback';
                            feedback.textContent = field.getAttribute('data-error-msg') || 'This field is required.';
                            field.parentNode.insertBefore(feedback, field.nextSibling);
                        }
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                // Check minlength
                form.querySelectorAll('[minlength]').forEach(function (field) {
                    if (field.value.length < parseInt(field.getAttribute('minlength'))) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    }
                });

                // Check email fields
                form.querySelectorAll('input[type="email"]').forEach(function (field) {
                    if (field.value && !isValidEmail(field.value)) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    // Scroll to first error
                    var firstError = form.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstError.focus();
                    }
                }

                // Clear validation on input
                if (!form.hasAttribute('data-validation-initialized')) {
                    form.setAttribute('data-validation-initialized', 'true');
                    form.addEventListener('input', function (ev) {
                        var field = ev.target.closest('.is-invalid');
                        if (field) {
                            field.classList.remove('is-invalid');
                        }
                    });
                }
            });
        });
    }

    // =========================================================================
    // 9. Auto-dismiss Alerts
    // =========================================================================
    function initAutoDismissAlerts() {
        var alerts = document.querySelectorAll('.alert-dismissible:not(.alert-persistent)');
        alerts.forEach(function (alert) {
            setTimeout(function () {
                var closeBtn = alert.querySelector('.btn-close');
                if (closeBtn) {
                    closeBtn.click();
                } else {
                    fadeOutAndRemove(alert);
                }
            }, 5000);
        });
    }

    // =========================================================================
    // 10. Sidebar Toggle (Mobile)
    // =========================================================================
    function initSidebarToggle() {
        var toggleBtn = document.getElementById('sidebarToggle');
        if (!toggleBtn) {
            // Create toggle button for mobile if not present
            toggleBtn = document.createElement('button');
            toggleBtn.id = 'sidebarToggle';
            toggleBtn.className = 'btn btn-sm btn-outline-light d-lg-none';
            toggleBtn.setAttribute('aria-label', 'Toggle sidebar');
            toggleBtn.innerHTML = '<i class="bi bi-list"></i>';

            var headerRight = document.querySelector('.admin-header .d-flex');
            if (headerRight) {
                headerRight.insertBefore(toggleBtn, headerRight.firstChild);
            }
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function (e) {
                e.preventDefault();
                var sidebar = document.querySelector('.admin-sidebar');
                if (sidebar) {
                    sidebar.classList.toggle('show');
                }
            });
        }
    }

    // =========================================================================
    // 11. Utility Functions
    // =========================================================================
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        var k = 1024;
        var sizes = ['Bytes', 'KB', 'MB', 'GB'];
        var i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function fadeOutAndRemove(el) {
        el.style.transition = 'opacity 0.3s ease';
        el.style.opacity = '0';
        setTimeout(function () {
            if (el.parentNode) {
                el.remove();
            }
        }, 300);
    }

    // =========================================================================
    // 12. TinyMCE Initialization Helper (if present)
    // =========================================================================
    // Expose a helper for pages that include TinyMCE
    window.initTinyMCE = function (selector, options) {
        if (typeof tinymce === 'undefined') {
            console.warn('TinyMCE is not loaded.');
            return;
        }

        var defaults = {
            height: 400,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
                'preview', 'anchor', 'searchreplace', 'visualblocks', 'code',
                'fullscreen', 'insertdatetime', 'media', 'table', 'help',
                'wordcount'
            ],
            toolbar: 'undo redo | blocks | bold italic underline strikethrough | ' +
                'alignleft aligncenter alignright alignjustify | ' +
                'bullist numlist outdent indent | link image media | ' +
                'removeformat fullscreen | help',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, sans-serif; font-size: 15px; line-height: 1.7; }',
            promotion: false
        };

        var mergedOptions = Object.assign({}, defaults, options || {});

        tinymce.init(Object.assign({ selector: selector }, mergedOptions));
    };

})();
