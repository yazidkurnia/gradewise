@extends('layouts.master')

@section('content')
    <div class="section-header">
        <h1>{{ $pageTitle }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">{{ $pageTitle }}</a></div>
            <div class="breadcrumb-item">{{ $pageTitle }}</div>
        </div>
    </div>

    <div class="section-body">

    </div>
@endsection

@push('styles')
    {{-- Add page specific styles here --}}
@endpush

@push('scripts')
    {{-- Add page specific scripts here --}}
    <script>
        console.log('Admin Dashboard loaded successfully!');
    </script>
@endpush
