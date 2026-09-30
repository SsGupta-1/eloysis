const Ajax = {

    request(options) {

        const form = options.form ? $(options.form) : null;
        const button = form ? form.find('[type="submit"]') : null;

        let formData = options.data;
        if (formData === undefined || formData === null) {
            formData = form && form.length && form[0] ? new FormData(form[0]) : null;
        }

        if (options.extraData && formData instanceof FormData) {
            Object.keys(options.extraData).forEach(key => {
                formData.append(key, options.extraData[key]);
            });
        }

        if (form) {
            Helper.clearErrors(form);
        }
        if (button) {
            Helper.loading(button);
        }

        const method = options.method ?? 'POST';
        const isFormData = formData instanceof FormData;

        $.ajax({
            url: options.url,
            type: method,
            data: formData,
            processData: options.processData !== undefined ? options.processData : (isFormData ? false : (method === 'GET')),
            contentType: options.contentType !== undefined ? options.contentType : (isFormData ? false : 'application/x-www-form-urlencoded; charset=UTF-8'),
            cache: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },

            success(response) {
                console.log('success', response);
                if (button) {
                    Helper.stopLoading(button);
                }

                if (response.message) {
                    Toast.success(response.message);
                }

                if (typeof options.success === 'function') {
                    options.success(response);
                }

            },

            error(xhr) {
                // console.log('error', xhr.status);
                console.log(xhr.responseJSON);
                if (button) {

                    Helper.stopLoading(button);

                }

                if (xhr.status === 422) {

                    Helper.showValidation(xhr.responseJSON.errors);
                    Toast.error(
                        xhr.responseJSON?.message ?? 'Validation failed.'
                    );

                    return;

                }

                if (typeof options.error === 'function') {

                    options.error(xhr);

                    return;

                }

                Toast.error(

                    xhr.responseJSON?.message ??

                    'Something went wrong.'

                );

            }

        });

    }

};