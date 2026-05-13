<div class="fi-realtime-clock" id="realtime-clock">
    <svg class="clock-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/>
        <polyline points="12 6 12 12 16 14"/>
    </svg>
    <span class="clock-text" id="clock-text"></span>
</div>

<script>
    function updateClock() {
        const now = new Date();
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        const dayName = days[now.getDay()];
        const date = now.getDate();
        const month = months[now.getMonth()];
        const year = now.getFullYear();
        
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        
        document.getElementById('clock-text').textContent = `${dayName}, ${date} ${month} ${year} - ${hours}:${minutes}:${seconds}`;
    }

    updateClock();
    setInterval(updateClock, 1000);
</script>
