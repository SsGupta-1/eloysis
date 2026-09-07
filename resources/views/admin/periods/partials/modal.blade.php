<x-ui.modal
    id="periodModal"
    size="lg">

    <x-slot:title>

        <span id="periodModalTitle">

            Add Academic Periods

        </span>

    </x-slot:title>

    <form id="periodForm" autocomplete="off">
        @csrf
        <div class="row">
            <x-ui.form-input type="hidden" name="period_id" id="period_id"> </x-ui.form-input>
            <div class="col-md-6">
                <x-ui.form-input name="name" id="period_name" placeholder="Period Name" label="Period Name"> </x-ui.form-input>
            </div>
            <div class="col-md-6">
                <x-ui.form-input type="time" name="start_time" id="start_time" placeholder="Start Time" label="Start Time"> </x-ui.form-input>
            </div>
            <div class="col-md-6">
                <x-ui.form-input type="time" name="end_time" id="end_time" placeholder="End Time" label="End Time"> </x-ui.form-input>
            </div>
            <div class="col-md-6">
                <x-ui.form-input type="number" name="sort_order" id="sort_order" placeholder="Sort Order" label="Sort Order"> </x-ui.form-input>
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
                id="btnSavePeriod"
                icon="bi-check-lg">

                Save Period

            </x-ui.button>

        </div>

    </form>
</x-ui.modal>