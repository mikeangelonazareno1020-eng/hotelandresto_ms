<!-- HEADER -->
<header
  id="main-header"
  class="fixed top-0 left-0 w-full z-20 py-3 shadow-md border-b border-[#D89C00]/40 
         bg-[#C45E25]/90 text-[#FFFBEA] backdrop-blur-md 
         transition-all duration-300 font-[Poppins]"
>
  <div class="max-w-6xl mx-auto flex justify-between items-center px-6">
    
    <!-- 🏨 Logo + Brand -->
    <div class="flex items-center space-x-3">
      <img 
        src="{{ asset('images/logo_hotelconsuelo.jpg') }}" 
        alt="Hotel Consuelo Logo" 
        class="w-10 h-10 rounded-md shadow-md object-contain"
      >
      <h1 class="text-2xl font-extrabold tracking-tight flex items-center gap-1">
        <span class="text-[#FFFBEA]">Hotel</span> 
        <span class="text-[#FFD600] drop-shadow-sm">Consuelo</span>
      </h1>
    </div>

    @if(Auth::check())
      <!-- ================= RESTAURANT CASHIER ================= -->
      @if(Auth::user()->role === 'Restaurant Cashier')
        <nav class="flex items-center space-x-6 text-sm font-medium">
          <a href="{{ route('cashier.menu') }}" class="hover:text-[#FFD600] transition flex items-center gap-1">
            <i data-lucide="utensils-crossed" class="w-4 h-4"></i> Menu
          </a>
          <a href="{{ route('cashier.orders') }}" class="hover:text-[#FFD600] transition flex items-center gap-1">
            <i data-lucide="receipt-text" class="w-4 h-4"></i> Orders
          </a>
          <a href="{{ route('cashier.reports') }}" class="hover:text-[#FFD600] transition flex items-center gap-1">
            <i data-lucide="bar-chart-3" class="w-4 h-4"></i> Reports
          </a>
          <a href="{{ route('cashier.logs') }}" class="hover:text-[#FFD600] transition flex items-center gap-1">
            <i data-lucide="clipboard-list" class="w-4 h-4"></i> Logs
          </a>
          <form action="{{ route('logout') }}" method="POST" class="inline" id="logoutForm">
            @csrf
            <button type="button" onclick="confirmLogout(event)"
              class="flex items-center gap-1 px-3 py-1 rounded-lg bg-[#FFFBEA]/20 
                     hover:bg-[#FFFBEA]/30 border border-[#FFFBEA]/40 text-[#FFFBEA] font-semibold transition">
              <i data-lucide="log-out" class="w-4 h-4"></i> Logout
            </button>
          </form>
        </nav>

      <!-- ================= HOTEL FRONTDESK ================= -->
      @elseif(Auth::user()->role === 'Hotel Frontdesk')
        <nav class="flex items-center space-x-6 text-sm font-medium">
          <a href="{{ route('frontdesk.booking') }}" class="hover:text-[#FFD600] transition flex items-center gap-1">
            <i data-lucide="calendar-check-2" class="w-4 h-4"></i> Bookings
          </a>
          <a href="{{ route('frontdesk.rooms') }}" class="hover:text-[#FFD600] transition flex items-center gap-1">
            <i data-lucide="bed-double" class="w-4 h-4"></i> Rooms
          </a>
          <a href="{{ route('frontdesk.transport.index') }}" class="hover:text-[#FFD600] transition flex items-center gap-1">
            <i data-lucide="car" class="w-4 h-4"></i> Transport
          </a>
          <a href="{{ route('frontdesk.reports') }}" class="hover:text-[#FFD600] transition flex items-center gap-1">
            <i data-lucide="bar-chart-3" class="w-4 h-4"></i> Reports
          </a>        
          <a href="#" class="hover:text-[#FFD600] transition flex items-center gap-1">
            <i data-lucide="clipboard-list" class="w-4 h-4"></i> Logs
          </a>
          <form action="{{ route('logout') }}" method="POST" class="inline" id="logoutForm">
            @csrf
            <button type="button" onclick="confirmLogout(event)"
              class="flex items-center gap-1 px-3 py-1 rounded-lg bg-[#FFFBEA]/20 
                     hover:bg-[#FFFBEA]/30 border border-[#FFFBEA]/40 text-[#FFFBEA] font-semibold transition">
              <i data-lucide="log-out" class="w-4 h-4"></i> Logout
            </button>
          </form>
        </nav>
      @endif
    @endif
  </div>
</header>

<!-- Logout Confirmation -->
<script>
  function confirmLogout(e) {
    if (window.Swal) {
      Swal.fire({
        icon: 'warning',
        title: 'Log out?',
        text: 'You will be signed out.',
        showCancelButton: true,
        confirmButtonText: 'Yes, logout',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#B0452D',
      }).then((result) => {
        if (result.isConfirmed) document.getElementById('logoutForm').submit();
      });
    } else if (confirm('Log out?')) {
      document.getElementById('logoutForm').submit();
    }
  }

  // Initialize Lucide icons
  document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) lucide.createIcons();
  });
</script>
