<x-ui.modal
    id="teacherSubjectModal"
    size="lg">

    <x-slot:title>

        <span id="teacherSubjectModalTitle">

            Add Academic Teacher Subject

        </span>

    </x-slot:title>

    <form id="teacherSubjectForm" autocomplete="off">
        @csrf
        <div class="row">
            <x-ui.form-input type="hidden" name="teacher_subject_id" id="teacher_subject_id"> </x-ui.form-input>

            
             {{-- Teacher --}}
            <div class="col-md-6">

                <x-ui.select
                    label="teacher"
                    name="teacher_id"
                    id="form_teacher_id"
                    :options="$teachers"
                    required />

            </div>

            {{-- Class --}}
            <div class="col-md-6">

                <x-ui.select
                    label="Class"
                    name="class_id"
                    id="form_class_id"
                    :options="$classes"
                    data-section-target="#form_section_id"
                    data-subject-target="#form_subject_id"
                    required />

            </div>

            {{-- Section --}}
            <div class="col-md-6">

                <x-ui.select
                    label="Section"
                    name="section_id"
                    id="form_section_id"
                    :options="$sections"
                    required />

            </div>


            {{-- Subject --}}
            <div class="col-md-6">

                <x-ui.select
                    label="Subject"
                    name="subject_id"
                    id="form_subject_id"
                    :options="$subjects"
                    required />

            </div>

            <div class="col-md-6">

                <x-ui.select
                    label="Status"
                    name="status"
                    id="status"
                    value=1
                    required
                    :options="[
                        1 => 'Active',
                        0 => 'Inactive'
                    ]"/>

            </div>

        </div> 
        <div class="text-end mt-4">

            <x-ui.button
                type="button"
                variant="secondary"
                data-bs-dismiss="modal">

                Cancel

            </x-ui.button>

            <x-ui.button
                type="submit"
                id="btnSaveTeacherSubject"
                icon="bi-check-lg">

                Save Subject

            </x-ui.button>

        </div>

    </form>
</x-ui.modal>