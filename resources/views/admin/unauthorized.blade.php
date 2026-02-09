@extends('admin.layout')
@section('title', 'Unauthorized')
@section('content')
<div class="container text-center py-5">
    <img src="{{ asset('admin/error-page4.html') }}" alt="Unauthorized" style="max-width:400px;">
    <h1 class="display-4 mt-4">403 - Unauthorized</h1>
    <p class="lead">You do not have permission to access this page or perform this action.</p>
    <a href="{{ url()->previous() }}" class="btn btn-primary mt-3">Go Back</a>
</div>
@endsection
