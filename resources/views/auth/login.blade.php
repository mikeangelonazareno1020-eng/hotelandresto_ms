@extends('layout.app')

@section('title', 'Login | Hotel Consuelo')

@section('content')
<div class="min-h-screen flex flex-col md:flex-row relative font-[Poppins]"
     style="background-image: url('{{ asset('images/hotel_bg_1.jpg') }}'); background-size: cover; background-position: center;">

    <!-- Overlay for contrast -->
    <div class="absolute inset-0 bg-linear-to-t from-black/65 via-black/45 to-black/25"></div>

    <!-- Left Side (Hotel Description) -->
    <div class="hidden md:flex w-1/2 flex-col justify-center items-start text-left z-10 p-10">
        <h1 class="text-3xl md:text-4xl font-bold text-[#FFF9E5] mb-4 drop-shadow-lg leading-tight">
            <span class="block text-2xl md:text-3xl font-medium text-[#FFF9E5]/90 tracking-wide">
                Welcome to
            </span>
            <span class="block text-5xl md:text-6xl font-extrabold text-[#FFD600] mt-1">
                Hotel Consuelo
            </span>
        </h1>

        <p class="text-[#FFF9E5] text-lg md:text-lg leading-relaxed max-w-lg drop-shadow-md">
            Experience comfort and elegance at <span class="text-[#FFD600] font-semibold">Hotel Consuelo</span> — where hospitality meets serenity. 
            Enjoy our modern rooms, relaxing amenities, and warm service that make every stay 
            memorable for both local and international guests.
        </p>
    </div>

    <!-- Right Side (Login Form) -->
<div class="w-full md:w-1/3 z-10 flex justify-center items-center py-16 px-4 md:px-0">
  <div class="bg-linear-to-br from-[#fffdf8] to-[#f9f3ea] backdrop-blur-lg border border-[#f1e8da]/70 
              rounded-3xl shadow-2xl w-full max-w-md p-8 font-[Poppins] relative overflow-hidden" 
       x-data="{ show: false }">

    <!-- Accent Glow -->
    <div class="absolute -top-20 -right-20 w-56 h-56 bg-[#FFD600]/20 rounded-full blur-3xl"></div>

    <!-- Header -->
    <h2 class="text-3xl font-extrabold mb-8 text-center text-[#3D3D3D] flex justify-center items-center gap-3 relative z-10">
      <i data-lucide="log-in" class="w-7 h-7 text-[#C45E25]"></i>
      Welcome Back
    </h2>

    {{-- SweetAlert handles session error --}}

    <!-- Login Form -->
    <form action="{{ route('login') }}" method="POST" class="space-y-6 relative z-10" id="loginForm" novalidate>
      @csrf

      <!-- Email -->
      <div>
        <label for="email" class="block text-sm font-semibold text-[#4B4B4B] mb-1">Email Address</label>
        <div class="relative">
          <i data-lucide="mail" class="absolute left-3 top-3.5 w-5 h-5 text-gray-400"></i>
          <input type="email" id="email" name="email"
                 value="{{ old('email') }}"
                 class="w-full pl-10 pr-3 py-2.5 border border-[#e2d7c7] rounded-xl shadow-sm
                        focus:ring-2 focus:ring-[#FFD600]/60 focus:border-[#C45E25] 
                        bg-[#fffdf8]/95 text-[#333] placeholder-gray-400 transition-all"
                 placeholder="Enter your email" required autofocus>
          @error('email')
            <span class="absolute -bottom-5 right-2 text-[11px] text-red-600 pointer-events-none">{{ $message }}</span>
          @enderror
          <span id="emailError" style="display:none" class="absolute -bottom-5 left-2 text-[11px] text-red-600 pointer-events-none"></span>
        </div>
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-sm font-semibold text-[#4B4B4B] mb-1">Password</label>
        <div class="relative">
          <i data-lucide="lock" class="absolute left-3 top-3.5 w-5 h-5 text-gray-400"></i>
          <input :type="show ? 'text' : 'password'" id="password" name="password"
                 class="w-full pl-10 pr-10 py-2.5 border border-[#e2d7c7] rounded-xl shadow-sm
                        focus:ring-2 focus:ring-[#FFD600]/60 focus:border-[#C45E25] 
                        bg-[#fffdf8]/95 text-[#333] placeholder-gray-400 transition-all"
                 placeholder="Enter your password" required>
          <button type="button" @click="show = !show" aria-label="Toggle password visibility"
                  class="absolute right-3 top-3 text-gray-500 hover:text-[#C45E25] focus:outline-none transition">
            <span x-show="!show"><i data-lucide="eye" class="w-5 h-5"></i></span>
            <span x-show="show"><i data-lucide="eye-off" class="w-5 h-5"></i></span>
          </button>
          @error('password')
            <span class="absolute -bottom-5 right-2 text-[11px] text-red-600 pointer-events-none">{{ $message }}</span>
          @enderror
          <span id="passwordError" style="display:none" class="absolute -bottom-5 left-2 text-[11px] text-red-600 pointer-events-none"></span>
        </div>
      </div>

      <!-- Forgot Password -->
      <div class="flex items-center justify-end">
        <a href="#" class="text-sm text-[#B35A2A] hover:text-[#8C3A25] hover:underline transition">
          Forgot Password?
        </a>
      </div>

      <!-- Submit -->
      <button type="submit"
              class="w-full bg-linear-to-r from-[#C45E25] to-[#B0452D] text-[#FFFBEA] py-2.5 rounded-xl 
                     hover:from-[#B0452D] hover:to-[#8C3A25] transition-all duration-300 
                     flex items-center justify-center gap-2 font-semibold tracking-wide
                     shadow-lg hover:shadow-amber-200/30">
        <i data-lucide="log-in" class="w-5 h-5"></i>
        <span>Login</span>
      </button>
    </form>

    <!-- Inline Validation Script -->
    <script>
      (function(){
        const form = document.getElementById('loginForm');
        const email = document.getElementById('email');
        const password = document.getElementById('password');
        const emailErr = document.getElementById('emailError');
        const passErr = document.getElementById('passwordError');

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/i;
        const passRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d!@#$%^&*()_+\-={}\[\]|:;"'<>,.?/`~]{8,}$/;

        function showError(el, msg) {
          el.textContent = msg || '';
          el.style.display = msg ? 'inline' : 'none';
        }

        form.addEventListener('submit', function(e){
          let valid = true;
          showError(emailErr, '');
          showError(passErr, '');

          const emailVal = email.value.trim();
          const passVal = password.value.trim();

          if (!emailVal) {
            valid = false;
            showError(emailErr, 'Email is required.');
          } else if (!emailRegex.test(emailVal)) {
            valid = false;
            showError(emailErr, 'Please enter a valid email address.');
          }

          if (!passVal) {
            valid = false;
            showError(passErr, 'Password is required.');
          } else if (!passRegex.test(passVal)) {
            valid = false;
            showError(passErr, 'Minimum 8 characters with letters and numbers.');
          }

          if (!valid) {
            e.preventDefault();
            if (window.toast) {
              window.toast({ icon: 'error', title: 'Please fill in all required fields.' });
            } else if (window.Swal) {
              Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please correct the highlighted fields and try again.',
              });
            }
          }
        });
      })();

      document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();
      });
    </script>
  </div>
</div>

</div>
@endsection

@push('scripts')
<script>
window.addEventListener('pageshow', function (event) {
    if (event.persisted) {
        window.location.replace("{{ route('expired') }}");
    }
});
</script>
@endpush
