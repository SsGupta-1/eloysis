@extends('layouts.admin.master')

@section('title', 'Edit Student')

@section('content')

<div class="container-fluid">

    <x-ui.page-header
        title="Edit Student"
        subtitle="Update student profile">

        <x-slot:actions>

            <a
                href="{{ route('admin.students.index') }}"
                class="btn btn-secondary">

                <i class="bi bi-arrow-left"></i>

                Back

            </a>

        </x-slot:actions>

    </x-ui.page-header>


    <form
        id="studentForm"
        method="POST"
        action="{{ route('admin.students.update', $enrollment->id) }}"
        enctype="multipart/form-data">

        @csrf

        @method('PUT')


        @include(
            'admin.students.partials.form',
            [
                $enrollment
            ]
        )


        <div class="d-flex justify-content-end gap-2 mt-4">

            <a
                href="{{ route('admin.students.index') }}"
                class="btn btn-secondary">

                Cancel

            </a>


            <x-ui.button
                type="submit"
                id="btnUpdateStudent">

                <i class="bi bi-check-lg"></i>

                Update Student

            </x-ui.button>

        </div>

    </form>

</div>

@endsection


@push('scripts')

<script>

    const STUDENT_INDEX_URL ="{{ route('admin.students.index') }}";
    const STUDENT_UPDATE_URL ="{{ route('admin.students.update', $enrollment->id) }}";
    const STUDENT_LIST_URL = "{{ route('admin.students.list') }}";

</script>

<script src="{{ asset('assets/admin/js/students.js') }}"></script>

@endpush