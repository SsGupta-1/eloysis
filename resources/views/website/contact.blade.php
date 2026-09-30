@extends('layouts.website.master')

@section('title', 'Contact Us')

@section('content')

{{-- Breadcrumb / Hero --}}
<div class="bg-primary text-white py-5 text-center mb-5">
    <div class="container">
        <h1 class="fw-bold mb-2">Get In Touch</h1>
        <p class="lead mb-0">Have questions? We are here to help and assist you.</p>
    </div>
</div>

@include('components.website.home.contact')

@endsection
