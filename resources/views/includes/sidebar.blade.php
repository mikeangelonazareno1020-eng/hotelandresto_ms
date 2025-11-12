<!-- SIDEBAR -->
<aside
  id="sidebar"
  class="sticky top-0 w-64 h-screen bg-[#1B4332]/95 text-white flex flex-col shadow-xl z-10"
  x-data="{ open: null }"
>
  <!-- Logo -->
  <div class="px-6 py-6 border-b border-white/10 flex items-center justify-between">
    <div class="flex items-center gap-2">
      <img src="{{ asset('images/logo_HotelConsuelo.jpg') }}" alt="Logo" class="w-8 h-8 rounded-lg">
      <h1 class="text-lg font-bold tracking-wide text-white">Hotel Consuelo</h1>
    </div>
  </div>

  <!-- Navigation -->
  <nav class="flex-1 overflow-y-auto px-4 py-3 space-y-2 text-[13px] font-medium">
    @if(Auth::check())
      <!-- =========================================================
           ADMINISTRATOR
      ========================================================== -->
      @if(Auth::user()->role === 'Administrator')
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white/10 transition">
          <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
          <span>Dashboard</span>
        </a>

        <!-- HOTEL SECTION -->
        <div>
          <button @click="open = open === 'hotel' ? null : 'hotel'"
                  class="flex items-center justify-between w-full px-2.5 py-1.5 rounded-lg hover:bg-white/10 transition">
            <div class="flex items-center gap-3">
              <i data-lucide="bed-double" class="w-4 h-4"></i>
              <span>Hotel</span>
            </div>
            <i x-bind:class="open === 'hotel' ? 'rotate-180' : ''" data-lucide="chevron-down" class="w-4 h-4"></i>
          </button>
          <div x-show="open === 'hotel'" x-transition class="ml-5 mt-1 space-y-1 text-white/80">
            <a href="{{ route('admin.rooms') }}" class="block py-0.5 hover:text-white">Rooms</a>
            <a href="{{ route('admin.booking') }}" class="block py-0.5 hover:text-white">Bookings</a>
            <a href="{{ route('admin.booking') }}" class="block py-0.5 hover:text-white">Amenities</a>
          </div>
        </div>
        
        <!-- RESTAURANT SECTION -->
        <div>
          <button @click="open = open === 'resto' ? null : 'resto'"
                  class="flex items-center justify-between w-full px-2.5 py-1.5 rounded-lg hover:bg-white/10 transition">
            <div class="flex items-center gap-3">
              <i data-lucide="utensils-crossed" class="w-4 h-4"></i>
              <span>Restaurant</span>
            </div>
            <i x-bind:class="open === 'resto' ? 'rotate-180' : ''" data-lucide="chevron-down" class="w-4 h-4"></i>
          </button>
          <div x-show="open === 'resto'" x-transition class="ml-5 mt-1 space-y-1 text-white/80">
            <a href="#" class="block py-0.5 hover:text-white">Inventory</a>
            <a href="#" class="block py-0.5 hover:text-white">Products</a>
            <a href="#" class="block py-0.5 hover:text-white">Menu</a>
          </div>
        </div>

        <!-- STAFF -->
        <a href="{{ route('admin.staff') }}"
           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white/10 transition">
          <i data-lucide="users" class="w-4 h-4"></i>
          <span>Staff Management</span>
        </a>

        <!-- GPS -->
        <div>
          <button @click="open = open === 'gps' ? null : 'gps'"
                  class="flex items-center justify-between w-full px-2.5 py-1.5 rounded-lg hover:bg-white/10 transition">
            <div class="flex items-center gap-3">
              <i data-lucide="map-pin" class="w-4 h-4"></i>
              <span>GPS Tracking</span>
            </div>
            <i x-bind:class="open === 'gps' ? 'rotate-180' : ''" data-lucide="chevron-down" class="w-4 h-4"></i>
          </button>
          <div x-show="open === 'gps'" x-transition class="ml-5 mt-1 space-y-1 text-white/80">
            <a href="{{ route('admin.gps.locations') }}" class="block py-0.5 hover:text-white">View GPS Locations</a>
            <a href="{{ route('admin.gps.services') }}" class="block py-0.5 hover:text-white">Transport Services</a>
            <a href="{{ route('admin.gps.vehicles') }}" class="block py-0.5 hover:text-white">Vehicles</a>
            <a href="{{ route('admin.gps.trips') }}" class="block py-0.5 hover:text-white">Saved Trips</a>
          </div>
        </div>

        <!-- REPORTS / ANALYTICS / LOGS -->
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white/10 transition">
          <i data-lucide="file-bar-chart" class="w-4 h-4"></i>
          <span>Reports</span>
        </a>
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white/10 transition">
          <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
          <span>Analytics</span>
        </a>
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white/10 transition">
          <i data-lucide="scroll-text" class="w-4 h-4"></i>
          <span>Logs</span>
        </a>
      @elseif(Auth::user()->role === 'Super Admin')
        <a href="{{ route('super.dashboard') }}"
           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white/10 transition">
          <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
          <span>Dashboard</span>
        </a>

        <a href="{{ route('super.accounts') }}"
           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white/10 transition">
          <i data-lucide="shield" class="w-4 h-4"></i>
          <span>Admin Accounts</span>
        </a>

        <a href="{{ route('super.customers') }}"
           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white/10 transition">
          <i data-lucide="users" class="w-4 h-4"></i>
          <span>Customers</span>
        </a>

        <a href="{{ route('super.gps') }}"
           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white/10 transition">
          <i data-lucide="map-pin" class="w-4 h-4"></i>
          <span>GPS Tracking</span>
        </a>

        <a href="{{ route('super.logs') }}"
           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white/10 transition">
          <i data-lucide="scroll-text" class="w-4 h-4"></i>
          <span>Logs</span>
        </a>

      @elseif(Auth::user()->role === 'Hotel Manager')
        <a href="{{ route('hotelmanager.dashboard') }}"
           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white/10 transition">
          <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
          <span>Dashboard</span>
        </a>

        <!-- HOTEL LINKS (Manager, non-accordion) -->
        <a href="{{ route('hotelmanager.rooms') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white/10 transition">
          <i data-lucide="bed-double" class="w-4 h-4"></i>
          <span>Rooms</span>
        </a>
        <a href="{{ route('hotelmanager.booking') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white/10 transition">
          <i data-lucide="calendar-check" class="w-4 h-4"></i>
          <span>Bookings</span>
        </a>
        <a href="{{ route('hotelmanager.onlinebookings') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white/10 transition">
          <i data-lucide="globe" class="w-4 h-4"></i>
          <span>Online Bookings</span>
        </a>
        <a href="{{ route('hotelmanager.amenities') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white/10 transition">
          <i data-lucide="sparkles" class="w-4 h-4"></i>
          <span>Amenities</span>
        </a>
        <a href="{{ route('hotelmanager.reports') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white/10 transition">
          <i data-lucide="file-bar-chart" class="w-4 h-4"></i>
          <span>Reports</span>
        </a>
        @if (Route::has('hotelmanager.logs'))
        <a href="{{ route('hotelmanager.logs') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white/10 transition">
          <i data-lucide="scroll-text" class="w-4 h-4"></i>
          <span>Logs</span>
        </a>
        @endif
      @elseif(Auth::user()->role === 'Restaurant Manager')
        <a href="{{ route('restomanager.dashboard') }}"
           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white/10 transition">
          <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
          <span>Dashboard</span>
        </a>

        <!-- RESTAURANT LINKS (Manager, non-accordion) -->
        <a href="{{ route('restomanager.products') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white/10 transition">
          <i data-lucide="boxes" class="w-4 h-4"></i>
          <span>Products</span>
        </a>
        <a href="{{ route('restomanager.menu') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white/10 transition">
          <i data-lucide="utensils-crossed" class="w-4 h-4"></i>
          <span>Menu</span>
        </a>
        <a href="{{ route('restomanager.reports') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white/10 transition">
          <i data-lucide="file-bar-chart" class="w-4 h-4"></i>
          <span>Reports</span>
        </a>
        <a href="{{ route('restomanager.logs') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white/10 transition">
          <i data-lucide="scroll-text" class="w-4 h-4"></i>
          <span>Logs</span>
        </a>
      @endif
    @endif
  </nav>

  <!-- Bottom Actions -->
  <div class="mt-auto border-t border-white/10 p-4 space-y-3">
    <!-- Account + Logout -->
    <div class="flex items-center gap-2 px-2 py-1.5 rounded-lg bg-white/5">
      <div class="flex items-center gap-1.5 min-w-0 flex-1">
        <i data-lucide="user" class="w-5 h-5 text-white"></i>
        <div class="text-[12px] leading-tight text-white truncate flex-1 min-w-0">
          <div class="flex items-center gap-1.5 min-w-0">
            <span class="truncate flex-1">{{ Auth::user()->name ?? Auth::user()->email }}</span>
            <span class="shrink-0 px-1 py-0.5 rounded border border-white/20 bg-white/10 text-[10px] uppercase tracking-wide text-white">{{ Auth::user()->role ?? '' }}</span>
          </div>
          <span class="block text-[10px] text-white/80 truncate">{{ Auth::user()->email ?? '' }}</span>
        </div>
      </div>
      <form action="{{ route('logout') }}" method="POST" class="shrink-0">
        @csrf
        <button type="button" onclick="confirmLogout(event)"
          class="flex items-center gap-1.5 px-2 py-1 rounded-md text-red-300 hover:bg-red-500/25 transition">
          <i data-lucide="log-out" class="w-5 h-5"></i>
          <span class="text-xs">Logout</span>
        </button>
      </form>
    </div>
  </div>
</aside>

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
        confirmButtonColor: '#D92332',
      }).then((r) => { if (r.isConfirmed) e.target.form.submit(); });
    } else if (confirm('Log out?')) e.target.form.submit();
  }
</script>
