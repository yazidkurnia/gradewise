@extends('layouts.master')

@section('content')
    <div class="section-header">
        <h1>{{ $title }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item">Home</div>
        </div>
    </div>

    <div class="section-body">
        {{-- @include('views.components.app-datatable') --}}
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
