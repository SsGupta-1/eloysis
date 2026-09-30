@extends('layouts.website.master')

@section('title', 'Home')

@section('content')

@forelse($pageData['sections'] ?? [] as $section)
    @php
        $viewPath = "components.website.sections.{$section['type']}.{$section['layout']}";
        $fallbackView = "components.website.sections.{$section['type']}.{$section['type']}_01";
    @endphp

    @if(view()->exists($viewPath))
        @include($viewPath, ['section' => $section, 'pageData' => $pageData])
    @elseif(view()->exists($fallbackView))
        @include($fallbackView, ['section' => $section, 'pageData' => $pageData])
    @endif
@empty
    <div class="container py-5 text-center">
        <div class="p-5 bg-light rounded-4">
            <i class="bi bi-info-circle text-primary fs-1 mb-3"></i>
            <h3 class="fw-bold">Welcome to {{ $pageData['institute']['name'] ?? 'Our School' }}</h3>
            <p class="text-muted">Homepage sections are currently being configured by the administration.</p>
        </div>
    </div>
@endforelse

@endsection