@extends('layouts.website.master')

@section('title', 'About Us')

@section('content')

{{-- Breadcrumb / Hero --}}
<div class="bg-primary text-white py-5 text-center mb-5">
    <div class="container">
        <h1 class="fw-bold mb-2">About Our Institute</h1>
        <p class="lead mb-0">{{ $pageData['about']['subtitle'] ?? 'Learn Today, Lead Tomorrow' }}</p>
    </div>
</div>

@include('components.website.home.about')

@include('components.website.home.features')

@include('components.website.home.statistics')

@include('components.website.home.principal')

@endsection
