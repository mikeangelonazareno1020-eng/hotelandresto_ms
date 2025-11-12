@extends('layout.app')

@section('title', 'Admin Reports')

@section('content')
<main class="p-6 text-sm">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold text-[#B0452D]">Admin Reports</h1>
    @include('reports.print_controls')
  </div>

  <div class="bg-white border rounded-xl shadow p-4" id="report-content">
    <p class="text-gray-700">Use filters on module-specific pages to refine results. Click Print to save this page as PDF.</p>
  </div>
</main>
@endsection
