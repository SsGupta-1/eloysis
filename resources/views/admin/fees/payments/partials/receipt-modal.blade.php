<x-ui.modal
    id="feeReceiptModal"
    title="Fee Payment Receipt"
    size="lg">

    <div id="receiptModalContent" class="p-2">
        <div class="text-center py-4">
            <span class="spinner-border spinner-border-sm text-primary"></span> Loading receipt...
        </div>
    </div>

    <x-slot:footer>
        <x-ui.button
            variant="secondary"
            data-bs-dismiss="modal">
            Close
        </x-ui.button>
        <a href="#" target="_blank" id="btnModalPrintReceipt" class="btn btn-primary">
            <i class="bi bi-printer me-1"></i> Print Receipt
        </a>
    </x-slot:footer>

</x-ui.modal>
