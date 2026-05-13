<div class="fi-realtime-clock" id="realtime-clock">
    <svg class="clock-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/>
        <polyline points="12 6 12 12 16 14"/>
    </svg>
    <span class="clock-day" id="clock-day"></span>
    <span class="clock-time" id="clock-time"></span>
</div>

<script>
    function updateClock() {
        const now = new Date();
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        
        const dayName = days[now.getDay()];
        const date = now.getDate();
        const month = months[now.getMonth()];
        const year = now.getFullYear();
        
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        
        document.getElementById('clock-day').textContent = `${dayName}, ${date} ${month} ${year}`;
        document.getElementById('clock-time').textContent = `${hours}:${minutes}:${seconds}`;
    }

    updateClock();
    setInterval(updateClock, 1000);
</script>
