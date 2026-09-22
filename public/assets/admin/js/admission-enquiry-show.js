const AdmissionEnquiryDetail = {
    init() {
        this.bindEvents();
    },

    bindEvents() {
        // Handle Staff Assignment
        $(document).on('submit', '#assignStaffForm', (e) => {
            e.preventDefault();
            this.submitForm($('#assignStaffForm'), $('#btnAssignStaff'), 'Staff assigned successfully.');
        });

        // Handle Log Follow-up
        $(document).on('submit', '#addFollowupForm', (e) => {
            e.preventDefault();
            this.submitForm($('#addFollowupForm'), $('#btnSaveFollowup'), 'Follow-up logged successfully.');
        });
    },

    submitForm(form, submitBtn, defaultSuccessMsg) {
        const spinner = submitBtn.find('.spinner-border');
        const btnText = submitBtn.find('.btn-text');

        // Clear previous validation errors
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');

        const formData = new FormData(form[0]);

        $.ajax({
            url: form.attr('action'),
            method: form.attr('method') || 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
            },
            success: (response) => {
                if (typeof Toast !== 'undefined') {
                    Toast.success(response.message || defaultSuccessMsg);
                } else {
                    alert(response.message || defaultSuccessMsg);
                }

                setTimeout(() => {
                    window.location.reload();
                }, 600);
            },
            error: (xhr) => {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON?.errors ?? {};
                    $.each(errors, (field, messages) => {
                        const input = form.find(`[name="${field}"]`);
                        input.addClass('is-invalid');
                        input.after(`<div class="invalid-feedback d-block">${messages[0]}</div>`);
                    });
                } else {
                    const errorMsg = xhr.responseJSON?.message || 'An error occurred. Please try again.';
                    if (typeof Toast !== 'undefined') {
                        Toast.error(errorMsg);
                    } else {
                        alert(errorMsg);
                    }
                }
            }
        });
    }
};

$(function () {
    AdmissionEnquiryDetail.init();
});
