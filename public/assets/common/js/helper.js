const Helper = {

    clearErrors(form) {

        $(form)
            .find('.is-invalid')
            .removeClass('is-invalid');

        $(form)
            .find('.invalid-feedback')
            .remove();

    },
    showValidation(errors) {
        console.log('validation error', errors);
        $.each(errors, function (field, messages) {

            // console.log(field, messages);

            let input = $('[name="' + field + '"]');

            input.addClass('is-invalid');

            if (input.next('.invalid-feedback').length === 0) {

                input.after(

                    '<div class="invalid-feedback">' + messages[0] + '</div>'

                );

            }

        });

    },

    loading(button) {

        if (!button || button.length === 0) {

            return;

        }

        button

            .prop('disabled', true)

            .data('html', button.html())

            .html(

                '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...'

            );

    },

    stopLoading(button) {

        if (!button || button.length === 0) {

            return;

        }

        button

            .prop('disabled', false)

            .html(button.data('html'));

    },

    statusSwitch(id, status, canEdit = true) {

        return `
            <div class="form-check form-switch">
                <input
                    class="form-check-input btn-status"
                    type="checkbox"
                    data-id="${id}"
                    ${status ? 'checked' : ''}
                    ${canEdit ? '' : 'disabled title="No permission to change status"'}
                >
            </div>
        `;
    },

    // formatDate(date) {
    //     let parts = date.split('T');
    //     return `${parts[0]}`;
    // },

    formatDate(date) {

        if (!date) {

            return '-';

        }

        return new Date(date)
            .toLocaleDateString(
                'en-IN',
                {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                }
            );

    },


    capitalize(value) {

        return value.charAt(0).toUpperCase()
            + value.slice(1);

    },

};

$(document).on(

    'keyup change',

    'input,textarea,select',

    function () {

        $(this)

            .removeClass('is-invalid')

            .next('.invalid-feedback')

            .remove();

    }

);

document.addEventListener('input', function (e) {

    if (e.target.tagName === 'TEXTAREA') {

        const counter = document.querySelector(

            '.textarea-counter[data-target="' + e.target.id + '"]'

        );

        if (counter && e.target.maxLength > 0) {

            counter.innerHTML =
                e.target.value.length + '/' + e.target.maxLength;

        }

    }

});


/*
|--------------------------------------------------------------------------
| section list by class id
|--------------------------------------------------------------------------
*/

$(document).on('change', '#class_id, #form_class_id, #target_class_id', function () {

    let classId = $(this).val();

    // Kis section dropdown ko update karna hai
    let sectionTarget = $(this).data('section-target');
    let subjectTarget = $(this).data('subject-target');

    // Agar target dropdown nahi hai to API call mat karo
    if (!sectionTarget && !subjectTarget) {
        return;
    }

    let $section = sectionTarget ? $(sectionTarget) : $();
    let $subject = subjectTarget ? $(subjectTarget) : $();


    if ($section.length) {
        $section.empty()
            .append('<option value="">Select Section</option>');
    }

    if ($subject.length) {
        $subject.empty()
            .append('<option value="">Select Subject</option>');
    }
    if (!classId) return;


    Ajax.request({

        // url: SECTION_BY_CLASS_URL.replace(':id', classId),
        url: BASE_URL + '/admin/sections/by-class/' + classId,

        method: 'GET',

        data: {},

        success: (response) => {

            if (response.status && Object.keys(response.data).length > 0) {
                const section_data = response.data.sections ?? {};
                const subject_data = response.data.subjects ?? {};

                if ($section.length) {
                    $.each(section_data, function (id, name) {
                        $section.append(
                            `<option value="${id}">${name}</option>`
                        );
                    });
                }


                if ($subject.length) {
                    $.each(subject_data, function (id, subject_name) {
                        $subject.append(
                            `<option value="${id}">${subject_name}</option>`
                        );
                    });
                }

            } else {
                $section.empty();
                $section.append(
                    '<option value="" disabled selected>This class has no active sections</option>'
                );

            }

        }

    });

});
