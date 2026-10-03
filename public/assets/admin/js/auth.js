/**
 * Admin Authentication Scripts
 */
const AdminAuth = {
    init() {
        this.bindPasswordToggle();
        this.bindLogin();
    },

    bindPasswordToggle() {
        $(document).on('click', '.toggle-password-btn', function (e) {
            e.preventDefault();
            const targetSelector = $(this).data('target') || '#password';
            const input = $(targetSelector);
            const icon = $(this).find('i');

            if (!input.length) return;

            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('bi-eye').addClass('bi-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('bi-eye-slash').addClass('bi-eye');
            }
        });
    },

    bindLogin() {
        $(document).on('submit', '#loginForm', function (e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = form.find('#loginSubmitBtn');
            const alertBox = $('#loginAlert');
            const card = $('#loginCard');
            const defaultBtnText = submitBtn.data('original-text') || submitBtn.html();

            // Store original button text if not already stored
            if (!submitBtn.data('original-text')) {
                submitBtn.data('original-text', defaultBtnText);
            }

            // Clear previous errors
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback-custom, .invalid-feedback').remove();
            alertBox.stop(true, true).slideUp(200).removeClass('alert-danger alert-success');

            // Set loading state
            submitBtn.prop('disabled', true).addClass('btn-loading');
            submitBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Authenticating...');

            const csrfToken = $('meta[name="csrf-token"]').attr('content') || form.find('input[name="_token"]').val();

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                success(response) {
                    if (response.status) {
                        // Success state
                        submitBtn.removeClass('btn-loading')
                            .addClass('btn-auth-success')
                            .html('<i class="bi bi-check2-circle me-2"></i> Verified! Redirecting...');

                        alertBox.removeClass('alert-danger')
                            .addClass('alert alert-success d-flex align-items-center')
                            .html('<i class="bi bi-check-circle-fill me-2 fs-5"></i><span>' + (response.message || 'Login successful. Redirecting to dashboard...') + '</span>')
                            .slideDown(250);

                        if (typeof Toast !== 'undefined' && Toast.success) {
                            Toast.success(response.message || 'Login successful.');
                        }

                        const redirectUrl = response.data?.redirect || (window.BASE_URL ? window.BASE_URL + '/admin/dashboard' : '/admin/dashboard');

                        setTimeout(() => {
                            window.location.href = redirectUrl;
                        }, 700);
                    } else {
                        // Server returned status: false (e.g., inactive account or unauth)
                        submitBtn.prop('disabled', false)
                            .removeClass('btn-loading btn-auth-success')
                            .html(submitBtn.data('original-text'));

                        card.addClass('shake-animation');
                        setTimeout(() => card.removeClass('shake-animation'), 600);

                        const msg = response.message || 'Invalid username/email or password.';
                        alertBox.removeClass('alert-success')
                            .addClass('alert alert-danger d-flex align-items-center')
                            .html('<i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i><span>' + msg + '</span>')
                            .slideDown(250);

                        if (typeof Toast !== 'undefined' && Toast.error) {
                            Toast.error(msg);
                        }
                    }
                },
                error(xhr) {
                    submitBtn.prop('disabled', false)
                        .removeClass('btn-loading btn-auth-success')
                        .html(submitBtn.data('original-text'));

                    card.addClass('shake-animation');
                    setTimeout(() => card.removeClass('shake-animation'), 600);

                    let errorMsg = 'Invalid username/email or password.';

                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function (field, messages) {
                            const input = form.find('[name="' + field + '"]');
                            if (input.length) {
                                input.addClass('is-invalid');
                                const container = input.closest('.input-wrapper');
                                const errorEl = $('<div class="invalid-feedback-custom">' + messages[0] + '</div>');
                                if (container.length) {
                                    container.after(errorEl);
                                } else {
                                    input.after(errorEl);
                                }
                            }
                        });
                        errorMsg = xhr.responseJSON.message || 'Please check the highlighted fields.';
                    } else if (xhr.responseJSON?.message) {
                        errorMsg = xhr.responseJSON.message;
                    }

                    alertBox.removeClass('alert-success')
                        .addClass('alert alert-danger d-flex align-items-center')
                        .html('<i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i><span>' + errorMsg + '</span>')
                        .slideDown(250);

                    if (typeof Toast !== 'undefined' && Toast.error) {
                        Toast.error(errorMsg);
                    }
                }
            });
        });
    }
};

$(function () {
    AdminAuth.init();
});
