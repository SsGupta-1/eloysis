{{-- Log Details Payload Modal --}}
<div class="modal fade" id="logDetailsModal" tabindex="-1" aria-labelledby="logDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fs-6 fw-semibold" id="logDetailsModalLabel">
                    <i class="bi bi-shield-check text-primary me-2"></i> Activity Log Audit Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">User</label>
                        <div class="fw-semibold text-dark" id="modalUserName">-</div>
                        <div class="text-muted small" id="modalUserEmail">-</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small mb-1">Module</label>
                        <div id="modalModule">-</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small mb-1">Action</label>
                        <div id="modalAction">-</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">Route & Method</label>
                        <div class="font-monospace small" id="modalRouteMethod">-</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small mb-1">IP Address</label>
                        <div class="font-monospace small" id="modalIp">-</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small mb-1">Duration & Status</label>
                        <div id="modalDurationStatus">-</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted small mb-1">Description / Summary</label>
                        <div class="alert alert-light border py-2 px-3 mb-0 small text-dark" id="modalDescription">-</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted small mb-1">Full Request URL</label>
                        <div class="p-2 bg-light rounded text-break font-monospace small" id="modalUrl">-</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted small mb-1">User Agent (Browser / Device)</label>
                        <div class="text-muted small font-monospace" id="modalUserAgent">-</div>
                    </div>
                </div>

                <div class="border-top pt-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-semibold text-dark mb-0">
                            <i class="bi bi-code-square me-1"></i> Request Payload (Sanitized)
                        </label>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCopyPayload">
                            <i class="bi bi-clipboard me-1"></i> Copy JSON
                        </button>
                    </div>
                    <pre class="bg-dark text-light p-3 rounded small font-monospace mb-0" id="modalPayloadJson" style="max-height: 250px; overflow-y: auto;">{}</pre>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Clear Old Logs Modal --}}
@hasPermission('activity_logs.delete')
<div class="modal fade" id="clearLogsModal" tabindex="-1" aria-labelledby="clearLogsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fs-6 fw-semibold text-white" id="clearLogsModalLabel">
                    <i class="bi bi-trash3 me-2"></i> Clear Old Activity Logs
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="clearLogsForm">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Select log retention period. Activity logs older than the selected timeframe will be permanently deleted from the database.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Retention Period</label>
                        <select name="days" id="clearLogsDays" class="form-select">
                            <option value="30" selected>Delete logs older than 30 days</option>
                            <option value="60">Delete logs older than 60 days</option>
                            <option value="90">Delete logs older than 90 days</option>
                            <option value="7">Delete logs older than 7 days (Aggressive Clean)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm" id="btnConfirmClearLogs">
                        <i class="bi bi-trash3 me-1"></i> Confirm & Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endhasPermission
