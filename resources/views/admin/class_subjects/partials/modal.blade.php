<x-ui.modal
    id="classSubjectModal"
    size="lg">

    <x-slot:title>

        <span id="classSubjectModalTitle">

            Add Academic Class Subject

        </span>

    </x-slot:title>

    <form id="classSubjectForm" autocomplete="off">
        @csrf
        <div class="row">
            <x-ui.form-input type="hidden" name="class_subject_id" id="class_subject_id"> </x-ui.form-input>

             {{-- Class --}}
            <div class="col-md-6">

                <x-ui.select
                    label="Class"
                    name="class_id"
                    id="sub_class_id"
                    :options="$classes"
                    required />

            </div>


            {{-- Subject --}}
            <div class="col-md-6">

                <x-ui.select
                    label="Subject"
                    name="subject_id"
                    id="sub_subject_id"
                    
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
                id="btnSaveClassSubject"
                icon="bi-check-lg">

                Save Subject

            </x-ui.button>

        </div>

    </form>
</x-ui.modal>