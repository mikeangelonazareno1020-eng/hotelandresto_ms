@extends('layout.app')

@section('title', 'Resto Manager Dashboard')

@section('content')
<div class="p-8">
    <h1 class="text-3xl font-bold mb-4">Resto Manager Dashboard</h1>
    <p class="text-gray-700">You are logged in as <strong>Manager (Resto)</strong>.</p>
    <p class="mt-2 text-sm text-gray-500">Sample page for manager_resto role.</p>
    <div class="mt-4 text-green-600">
        @if(session('success'))
            <span>{{ session('success') }}</span>
        @endif
    </div>
</div>
@endsection

