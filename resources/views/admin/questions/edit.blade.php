@extends('layouts.admin.master')

@section('title', 'Edit Question')

@section('content')
<div class="container-fluid">

    <x-ui.page-header
        title="Edit Question"
        subtitle="Modify existing question details and options">
        <x-slot:actions>
            <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Question Bank
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <form id="questionForm" method="POST" action="{{ route('admin.questions.update', $question->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('admin.questions.partials.form')

        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary px-4">Cancel</a>
            <button type="submit" class="btn btn-primary px-4" id="btnSaveQuestion">
                <span class="btn-text">
                    <i class="bi bi-check2-circle me-1"></i> Update Question
                </span>
                <span class="spinner-border spinner-border-sm d-none"></span>
            </button>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
    const QUESTION_INDEX_URL = "{{ route('admin.questions.index') }}";
</script>
<script src="{{ asset('assets/admin/js/questions.js') }}"></script>
@endpush
