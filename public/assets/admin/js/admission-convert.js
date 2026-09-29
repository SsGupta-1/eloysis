const AdmissionConvert = {
    init() {
        this.bindEvents();
    },

    bindEvents() {
        // Image preview
        $('#profile_image').on('change', function () {
            const file = this.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                $('#profilePreview').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        });

        // Auto-fetch suggested roll number when session, class, or section changes
        $('#academic_session_id, #form_class_id, #form_section_id').on('change', () => {
            this.fetchSuggestedRollNumber();
        });

        // Form Submit
        $('#convertAdmissionForm').on('submit', (e) => {
            e.preventDefault();
            this.submit();
        });
    },

    fetchSuggestedRollNumber() {
        const url = BASE_URL + '/admin/students/suggested-roll-number';
        const sessionId = $('#academic_session_id').val();
        const classId = $('#form_class_id').val();
        const sectionId = $('#form_section_id').val();

        if (!sessionId || !classId || !sectionId) {
            return;
        }

        $.ajax({
            url: url,
            type: 'GET',
            data: {
                academic_session_id: sessionId,
                class_id: classId,
                section_id: sectionId
            },
            success: (response) => {
                if (response && response.status && response.suggested_roll_number) {
                    $('#roll_number').val(response.suggested_roll_number);
                }
            }
        });
    },

    submit() {
        const form = $('#convertAdmissionForm');
        const submitBtn = $('#btnSubmitConversion');
        const spinner = submitBtn.find('.spinner-border');
        const btnText = submitBtn.find('.btn-text');

        // Optional SweetAlert confirmation if available, otherwise direct submit
        const proceedSubmit = () => {
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback').remove();

            submitBtn.prop('disabled', true);
            spinner.removeClass('d-none');

            const formData = new FormData(form[0]);

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                },
                success: (response) => {
                    if (typeof Toast !== 'undefined') {
                        Toast.success(response.message || 'Enquiry converted to Admission successfully!');
                    } else {
                        alert(response.message || 'Enquiry converted to Admission successfully!');
                    }

                    setTimeout(() => {
                        window.location.href = response.redirect_url || (window.location.origin + '/admin/students');
                    }, 800);
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

                        // Scroll to first invalid input
                        const firstError = form.find('.is-invalid').first();
                        if (firstError.length) {
                            $('html, body').animate({
                                scrollTop: firstError.offset().top - 120
                            }, 300);
                        }
                    } else {
                        const errorMsg = xhr.responseJSON?.message || 'Failed to convert enquiry. Please verify details.';
                        if (typeof Toast !== 'undefined') {
                            Toast.error(errorMsg);
                        } else {
                            alert(errorMsg);
                        }
                    }
                }
            });
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Convert to Admission?',
                text: 'Are you sure you want to convert this enquiry into a student admission? This will create user login, student profile, and enrollment records.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Convert to Admission',
                cancelButtonText: 'Review Details',
                confirmButtonColor: '#198754'
            }).then((result) => {
                if (result.isConfirmed) {
                    proceedSubmit();
                }
            });
        } else {
            if (confirm('Are you sure you want to convert this enquiry into an admission? This will create user login, student profile, and enrollment records.')) {
                proceedSubmit();
            }
        }
    }
};

$(function () {
    AdmissionConvert.init();
});
