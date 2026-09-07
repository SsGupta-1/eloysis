const Form = {

    init() {

        this.contact();
        this.admissionForm();
        this.admissionReference();

    },

    contact() {

        $(document).on('submit', '#contactForm', function (e) {

            e.preventDefault();

            const form = $(this);

            const submitBtn = $('#contactSubmitBtn');

            const spinner = $('#contactSubmitSpinner');

            const btnText = submitBtn.find('.btn-text');


            /*
            |--------------------------------------------------------------------------
            | Clear Previous Errors
            |--------------------------------------------------------------------------
            */

            form.find('.is-invalid').removeClass('is-invalid');

            form.find('.invalid-feedback').text('');


            /*
            |--------------------------------------------------------------------------
            | Loading
            |--------------------------------------------------------------------------
            */

            submitBtn.prop('disabled', true);

            btnText.text('Sending...');

            spinner.removeClass('d-none');


            /*
            |--------------------------------------------------------------------------
            | AJAX
            |--------------------------------------------------------------------------
            */

            Ajax.request({

                form: $(this),

                url: $(this).attr('action'),

                method: 'POST',

                success(response) {

                    /*
                    |--------------------------------------------------------------------------
                    | Hide Form
                    |--------------------------------------------------------------------------
                    */

                    $('#contactFormContent').addClass('d-none');


                    /*
                    |--------------------------------------------------------------------------
                    | Show Thank You
                    |--------------------------------------------------------------------------
                    */

                    $('#contactThankYou').removeClass('d-none');


                    /*
                    |--------------------------------------------------------------------------
                    | Reset Form
                    |--------------------------------------------------------------------------
                    */

                    form[0].reset();

                },
                error: function (xhr) {

                    /*
                    |--------------------------------------------------------------------------
                    | Validation Error
                    |--------------------------------------------------------------------------
                    */

                    if (xhr.status === 422) {

                        const errors = xhr.responseJSON.errors;

                        $.each(errors, function (field, messages) {

                            const input = form.find(`[name="${field}"]`);

                            input.addClass('is-invalid');

                            form.find(`[data-error="${field}"]`).text(messages[0]);

                        });

                        return;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | General Error
                    |--------------------------------------------------------------------------
                    */

                    alert(xhr.responseJSON?.message ?? 'Something went wrong. Please try again.');

                },

                complete: function () {

                    submitBtn.prop('disabled', false);

                    btnText.text('Send Message');

                    spinner.addClass('d-none');

                }

            });

        });

    },

    admissionForm() {

        $(document).on('submit', '#admissionEnquiryForm', function (e) {

            e.preventDefault();

            const form = $(this);

            const submitBtn = $('#btnAdmissionSubmit');

            const spinner = submitBtn.find('.spinner-border');

            const btnText = submitBtn.find('.btn-text');


            /*
            |--------------------------------------------------------------------------
            | Clear Previous Errors
            |--------------------------------------------------------------------------
            */

            form.find('.is-invalid').removeClass('is-invalid');

            form.find('.invalid-feedback').text('');


            /*
            |--------------------------------------------------------------------------
            | Loading
            |--------------------------------------------------------------------------
            */

            submitBtn.prop('disabled', true);

            btnText.text('Sending...');

            spinner.removeClass('d-none');


            /*
            |--------------------------------------------------------------------------
            | AJAX
            |--------------------------------------------------------------------------
            */

            Ajax.request({

                form: $(this),

                url: $(this).attr('action'),

                method: 'POST',

                success(response) {

                    /*
                    |--------------------------------------------------------------------------
                    | Hide Form
                    |--------------------------------------------------------------------------
                    */

                    $('#admissionEnquiryFormContent').addClass('d-none');


                    /*
                    |--------------------------------------------------------------------------
                    | Show Thank You
                    |--------------------------------------------------------------------------
                    */

                    $('#admissionThankYou').removeClass('d-none');

                    if (response?.data?.enquiry_no) $('#admissionEnquiryNo').text(response.data.enquiry_no);

                    /*
                    |--------------------------------------------------------------------------
                    | Reset Form
                    |--------------------------------------------------------------------------
                    */

                    form[0].reset();

                },
                error: function (xhr) {

                    /*
                    |--------------------------------------------------------------------------
                    | Validation Error
                    |--------------------------------------------------------------------------
                    */

                    if (xhr.status === 422) {

                        const errors = xhr.responseJSON.errors;

                        $.each(errors, function (field, messages) {

                            const input = form.find(`[name="${field}"]`);

                            input.addClass('is-invalid');

                            form.find(`[data-error="${field}"]`).text(messages[0]);

                        });

                        return;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | General Error
                    |--------------------------------------------------------------------------
                    */

                    alert(xhr.responseJSON?.message ?? 'Something went wrong. Please try again.');

                },

                complete: function () {

                    submitBtn.prop('disabled', false);

                    btnText.text('Submit Enquiry');

                    spinner.addClass('d-none');

                }

            });

        });

    },

    /*
|--------------------------------------------------------------------------
| Admission Reference Fields
|--------------------------------------------------------------------------
*/

    admissionReference() {

        $(document).on(
            'change',
            '#admissionSource',
            function () {

                const source = $(this).val();

                const needsReference = [
                    'reference'
                ].includes(source);


                if (needsReference) {

                    $('#referenceTypeWrapper')
                        .removeClass('d-none');

                    $('#referenceNameWrapper')
                        .removeClass('d-none');

                    $('#referencePhoneWrapper')
                        .removeClass('d-none');

                } else {

                    $('#referenceTypeWrapper')
                        .addClass('d-none');

                    $('#referenceNameWrapper')
                        .addClass('d-none');

                    $('#referencePhoneWrapper')
                        .addClass('d-none');

                    /*
                    |--------------------------------------------------------------------------
                    | Clear Reference Values
                    |--------------------------------------------------------------------------
                    */

                    $('#referenceType').val('');

                    $('[name="reference_name"]').val('');

                    $('[name="reference_phone"]').val('');

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Trigger Initial State
        |--------------------------------------------------------------------------
        */

        $('#admissionSource').trigger('change');

    },

};