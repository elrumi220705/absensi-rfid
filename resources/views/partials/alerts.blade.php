{{-- resources/views/partials/alerts.blade.php --}}
{{-- Flash alerts tanpa Alpine, z-index super tinggi, auto-dismiss --}}
<div id="flash-toasts"
     style="position:fixed;left:1rem;right:1rem;top:8rem;z-index:2147483647;display:flex;justify-content:center;pointer-events:none">
  <div style="width:100%;max-width:28rem;display:flex;flex-direction:column;gap:.5rem">

    @if (session('success'))
      <div class="rounded-lg border px-4 py-3 shadow-lg"
           style="background:#ecfdf5;border-color:#bbf7d0;color:#065f46;display:flex;gap:.75rem;align-items:flex-start;pointer-events:auto">
        <span class="ri-checkbox-circle-line" style="font-size:1.25rem;color:#059669;margin-top:.125rem"></span>
        <div style="font-size:.9rem">{{ session('success') }}</div>
        <button type="button" onclick="this.closest('.rounded-lg').remove()"
                style="margin-left:auto;color:#065f46;opacity:.7" aria-label="Tutup">
          <i class="ri-close-line" style="font-size:1.1rem"></i>
        </button>
      </div>
    @endif

    @if (session('error'))
      <div class="rounded-lg border px-4 py-3 shadow-lg"
           style="background:#fef2f2;border-color:#fecaca;color:#7f1d1d;display:flex;gap:.75rem;align-items:flex-start;pointer-events:auto">
        <span class="ri-error-warning-line" style="font-size:1.25rem;color:#dc2626;margin-top:.125rem"></span>
        <div style="font-size:.9rem">{{ session('error') }}</div>
        <button type="button" onclick="this.closest('.rounded-lg').remove()"
                style="margin-left:auto;color:#7f1d1d;opacity:.7" aria-label="Tutup">
          <i class="ri-close-line" style="font-size:1.1rem"></i>
        </button>
      </div>
    @endif

    @if (session('status'))
      <div class="rounded-lg border px-4 py-3 shadow-lg"
           style="background:#eff6ff;border-color:#bfdbfe;color:#1e40af;display:flex;gap:.75rem;align-items:flex-start;pointer-events:auto">
        <span class="ri-information-line" style="font-size:1.25rem;color:#2563eb;margin-top:.125rem"></span>
        <div style="font-size:.9rem">{{ session('status') }}</div>
        <button type="button" onclick="this.closest('.rounded-lg').remove()"
                style="margin-left:auto;color:#1e40af;opacity:.7" aria-label="Tutup">
          <i class="ri-close-line" style="font-size:1.1rem"></i>
        </button>
      </div>
    @endif

    @if ($errors->any())
      <div class="rounded-lg border px-4 py-3 shadow-lg"
           style="background:#fffbeb;border-color:#fde68a;color:#713f12;pointer-events:auto">
        <div style="display:flex;gap:.75rem;align-items:flex-start">
          <span class="ri-alert-line" style="font-size:1.25rem;color:#d97706;margin-top:.125rem"></span>
          <div style="font-size:.9rem">
            <div style="font-weight:600;margin-bottom:.25rem">Periksa kembali isian kamu:</div>
            <ul style="list-style:disc;margin-left:1.25rem">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
          <button type="button" onclick="this.closest('.rounded-lg').remove()"
                  style="margin-left:auto;color:#713f12;opacity:.7" aria-label="Tutup">
            <i class="ri-close-line" style="font-size:1.1rem"></i>
          </button>
        </div>
      </div>
    @endif

  </div>
</div>

<script>
  // Auto-dismiss 4–8 detik
  (function () {
    const boxes = document.querySelectorAll('#flash-toasts .rounded-lg');
    boxes.forEach((el) => {
      const isError = el.querySelector('.ri-error-warning-line') || el.querySelector('.ri-alert-line');
      const timeout = isError ? 8000 : 4000;
      setTimeout(() => { try { el.remove(); } catch (e) {} }, timeout);
    });
  })();
</script>
