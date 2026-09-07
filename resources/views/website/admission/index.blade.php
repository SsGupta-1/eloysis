@extends('layouts.website.master')

@section('title', 'Admission')

@section('content')

    @include('components.website.admission.hero')

    @include('components.website.admission.information')

    @include('components.website.admission.process')

    @include('components.website.admission.documents')

    @include('components.website.admission.enquiry')

@endsection