@extends('layouts.admin.master')

@section('title', 'Activity Audit Logs')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <x-ui.page-header
        title="Activity Audit Logs"
        subtitle="Track system actions, user activities, and security events in real-time">

        <x-slot:actions>
            @hasPermission('activity_logs.delete')
            <button class="btn btn-outline-danger btn-sm" id="btnOpenClearLogsModal">
                <i class="bi bi-trash3 me-1"></i> Clear Old Logs
            </button>
            @endhasPermission

            @hasPermission('logs.view')
            <a href="{{ route('admin.logs.index') }}" class="btn btn-outline-secondary btn-sm ms-2">
                <i class="bi bi-file-earmark-text me-1"></i> Daily Log Files
            </a>
            @endhasPermission
        </x-slot:actions>

    </x-ui.page-header>

    {{-- Filters --}}
    <x-ui.table.filters id="activityFilterForm">

        {{-- Filter User --}}
        <div class="col-md-3">
            <label class="form-label small text-muted mb-1">Filter by User</label>
            <select name="user_id" id="filter_user_id" class="form-select form-select-sm">
                <option value="">All Users</option>
                @foreach($users ?? [] as $u)
                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                @endforeach
            </select>
        </div>

        {{-- Filter Module --}}
        <div class="col-md-2">
            <label class="form-label small text-muted mb-1">Module</label>
            <select name="module" id="filter_module" class="form-select form-select-sm">
                <option value="">All Modules</option>
                @foreach($modules ?? [] as $mod)
                    <option value="{{ $mod }}">{{ ucwords(str_replace('_', ' ', $mod)) }}</option>
                @endforeach
            </select>
        </div>

        {{-- Filter Action --}}
        <div class="col-md-2">
            <label class="form-label small text-muted mb-1">Action</label>
            <select name="action" id="filter_action" class="form-select form-select-sm">
                <option value="">All Actions</option>
                @foreach($actions ?? [] as $act)
                    <option value="{{ $act }}">{{ $act }}</option>
                @endforeach
            </select>
        </div>

        {{-- Date Range --}}
        <div class="col-md-2">
            <label class="form-label small text-muted mb-1">From Date</label>
            <input type="date" name="start_date" id="filter_start_date" class="form-control form-control-sm">
        </div>

        <div class="col-md-2">
            <label class="form-label small text-muted mb-1">To Date</label>
            <input type="date" name="end_date" id="filter_end_date" class="form-control form-control-sm">
        </div>

        {{-- Reset / Apply --}}
        <div class="col-md-1 d-flex align-items-end">
            <button type="button" class="btn btn-secondary btn-sm w-100" id="btnResetFilter" title="Reset Filters">
                <i class="bi bi-arrow-counterclockwise"></i>
            </button>
        </div>

    </x-ui.table.filters>

    {{-- Activity Logs DataTable --}}
    <x-ui.datatable id="activityLogTable">

        <x-ui.table.thead>

            <x-ui.table.col width="50">#</x-ui.table.col>

            <x-ui.table.col width="160">Date & Time</x-ui.table.col>

            <x-ui.table.col width="180">User / Performed By</x-ui.table.col>

            <x-ui.table.col width="130">Module</x-ui.table.col>

            <x-ui.table.col width="120">Action</x-ui.table.col>

            <x-ui.table.col>Description / Summary</x-ui.table.col>

            <x-ui.table.col width="130">IP & Method</x-ui.table.col>

            <x-ui.table.col width="90">Status</x-ui.table.col>

            <x-ui.table.col width="70" class="text-center">Action</x-ui.table.col>

        </x-ui.table.thead>

        <x-ui.table.tbody id="activityLogTableBody">
        </x-ui.table.tbody>

    </x-ui.datatable>

</div>

@include('admin.activity-logs.partials.modal')

@endsection

@push('scripts')

    <script>
        const ACTIVITY_LOG_LIST_URL = "{{ route('admin.activity-logs.list') }}";
        const ACTIVITY_LOG_SHOW_URL = "{{ route('admin.activity-logs.show', ':id') }}";
        const ACTIVITY_LOG_CLEAR_URL = "{{ route('admin.activity-logs.clear') }}";
    </script>

    <script src="{{ asset('assets/admin/js/activity-logs.js') }}"></script>

@endpush
