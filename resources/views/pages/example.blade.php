@extends('layouts.master')

@section('content')
    <div class="section-header">
        <h1>Example Page</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item">Example Page</div>
        </div>
    </div>

    <div class="section-body">
        <h2 class="section-title">This is Example</h2>
        <p class="section-lead">This page is just an example for you to create your own page.</p>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Card Header</h4>
                    </div>
                    <div class="card-body">
                        <p>This is some text within a card body.</p>
                    </div>
                    <div class="card-footer bg-whitesmoke">
                        Card Footer
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    {{-- Add page specific styles here --}}
    <link rel="stylesheet" href="{{ asset('assets/modules/datatables/datatables.min.css') }}">
@endpush

@push('scripts')
    {{-- Add page specific scripts here --}}
    <script src="{{ asset('assets/modules/datatables/datatables.min.js') }}"></script>
    <script>
        console.log('Example page loaded!');
    </script>
@endpush
