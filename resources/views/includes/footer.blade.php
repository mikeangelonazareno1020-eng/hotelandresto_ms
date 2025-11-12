<footer 
    class="bg-[#1a1a1a]/95 backdrop-blur-sm text-gray-300 py-6 border-t border-[#FFD600]/30">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between px-6 space-y-4 md:space-y-0">

        <!-- SYSTEM INFO -->
        <div class="flex items-center space-x-3">
            <img src="{{ asset('images/logo_HotelConsuelo.jpg') }}" alt="Logo" class="w-10 h-10 rounded-md border border-[#FFD600]/50 shadow-md">
            <div>
                <h2 class="text-lg font-semibold text-[#FFD600]">Hotel Consuelo Management System</h2>
                <p class="text-xs text-gray-400">
                    Integrated Hotel & Transport Administration Panel
                </p>
            </div>
        </div>

        {{-- <!-- ADMIN LINKS -->
        <div class="flex flex-wrap justify-center gap-4 text-sm text-gray-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FFD600] transition">Dashboard</a>
            <a href="{{ route('admin.logs') }}" class="hover:text-[#FFD600] transition">Logs</a>
            <a href="{{ route('admin.reports') }}" class="hover:text-[#FFD600] transition">Reports</a>
            <a href="{{ route('admin.settings') }}" class="hover:text-[#FFD600] transition">Settings</a>
        </div> --}}

        <!-- VERSION / COPYRIGHT -->
        <div class="text-center md:text-right text-sm text-gray-500">
            <p>© 2025 Hotel Consuelo | Admin Panel</p>
            <p class="text-xs">System Version: <span class="text-[#FFD600] font-semibold">v2.4.1</span></p>
        </div>
    </div>
</footer>
