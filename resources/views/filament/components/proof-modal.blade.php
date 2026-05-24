<div class="flex justify-center p-4">
    @if ($image)
        <img
            src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($image) }}"
            alt="Bukti Transaksi"
            style="max-width: 100%; max-height: 70vh; border-radius: 0.5rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); object-fit: contain; margin: 0 auto; display: block;"
        />
    @else
        <div style="text-align: center; color: #6b7280; padding: 2rem 0;">
            <svg style="margin: 0 auto 0.5rem; height: 3rem; width: 3rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p style="font-size: 0.875rem;">Tidak ada bukti transaksi</p>
        </div>
    @endif
</div>
