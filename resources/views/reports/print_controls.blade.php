<div class="flex items-center gap-2">
  <button type="button" onclick="window.print()" class="px-3 py-1.5 border rounded bg-white hover:bg-gray-50">
    Print / Save as PDF
  </button>
  <style>
    @media print {
      /* Hide app chrome, keep main content */
      body { background: #fff; }
      aside#sidebar, nav, header, footer, .no-print { display: none !important; }
      main { margin: 0; padding: 0; }
      .print\:p-0 { padding: 0 !important; }
      .print\:shadow-none { box-shadow: none !important; border: none !important; }
    }
  </style>
</div>

