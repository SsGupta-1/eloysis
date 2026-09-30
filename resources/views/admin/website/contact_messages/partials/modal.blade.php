<x-ui.modal id="messageModal" title="View Contact Message" size="lg">

    <div class="row g-3">

        <div class="col-md-6">
            <x-ui.detail-item label="Sender Name" id="msg_name" icon="bi-person" />
        </div>

        <div class="col-md-6">
            <x-ui.detail-item label="Email Address" id="msg_email" icon="bi-envelope" />
        </div>

        <div class="col-md-6">
            <x-ui.detail-item label="Phone Number" id="msg_phone" icon="bi-telephone" />
        </div>

        <div class="col-md-6">
            <x-ui.detail-item label="Received At" id="msg_created_at" icon="bi-clock" />
        </div>

        <div class="col-12"><hr class="my-1"></div>

        <div class="col-12">
            <label class="form-label text-muted small">Subject</label>
            <h6 id="msg_subject" class="fw-semibold text-primary mb-2"></h6>
        </div>

        <div class="col-12">
            <label class="form-label text-muted small">Message</label>
            <div id="msg_body" class="p-3 bg-light rounded border" style="white-space: pre-wrap;"></div>
        </div>

        <div class="col-12 mt-3 d-flex align-items-center gap-2">
            <label class="form-label fw-medium mb-0">Update Status:</label>
            <select id="msg_status_select" class="form-select form-select-sm w-auto">
                <option value="pending">Pending</option>
                <option value="read">Read</option>
                <option value="replied">Replied</option>
            </select>
            <x-ui.button size="sm" id="btnUpdateMsgStatus">
                Update Status
            </x-ui.button>
        </div>

    </div>

    <x-slot:footer>
        <x-ui.button variant="secondary" data-bs-dismiss="modal">
            Close
        </x-ui.button>
    </x-slot:footer>

</x-ui.modal>
