@extends('layout.app')

@section('title', 'Hotel Consuelo')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center relative overflow-hidden"
     style="background-image: url('{{ asset('images/hotel_bg_1.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">

    <!-- Dark Overlay for Text Contrast -->
    <div class="absolute inset-0 bg-linear-to-b from-black/70 via-black/50 to-black/70"></div>

    <!-- Content -->
    <div class="z-10 text-center px-6">
        <h1 class="text-5xl md:text-7xl font-extrabold text-[#FFD600] drop-shadow-2xl mb-6">
            Hotel Consuelo
        </h1>
        <p class="text-gray-100 text-lg md:text-2xl mb-8 max-w-2xl mx-auto leading-relaxed drop-shadow-md">
            Experience comfort, elegance, and hospitality in the heart of the city.
        </p>

        <!-- Get Started Button -->
        <a href="{{ route('login.form') }}"
           class="bg-[#a8432b] text-white px-8 py-3 text-lg rounded-lg hover:bg-[#8c3722] transition duration-300 shadow-lg">
            Get Started
        </a>
    </div>

    <!-- Optional Soft Light Gradient -->
    <div class="absolute inset-0 bg-linear-to-t from-black/60 via-transparent to-black/40"></div>
</div>

<!-- Smooth Fade-In -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.body.classList.add('opacity-0');
    setTimeout(() => document.body.classList.remove('opacity-0'), 100);
});
</script>

<style>
body {
    transition: opacity 1s ease;
}
</style>
@endsection
