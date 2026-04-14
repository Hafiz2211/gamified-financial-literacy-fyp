{{-- Background Music - Seamless position saving --}}
<script>
(function() {
    // Get saved preference (default ON)
    let musicEnabled = localStorage.getItem('musicEnabled');
    if (musicEnabled === null) {
        musicEnabled = 'true';
        localStorage.setItem('musicEnabled', 'true');
    }
    
    // Get saved position
    let savedTime = parseFloat(localStorage.getItem('musicCurrentTime')) || 0;
    
    // Create ONE audio element
    const audio = new Audio();
    audio.volume = 0.2;
    audio.src = "{{ asset('music/days-off-matrika-main-version-39449-02-56.mp3') }}";
    
    // Restore position (within 1 second of saved)
    if (savedTime > 0 && savedTime < audio.duration) {
        audio.currentTime = savedTime;
    }
    
    // Save position every 0.5 seconds
    let saveInterval = setInterval(() => {
        if (!audio.paused && !audio.ended && audio.currentTime > 0) {
            localStorage.setItem('musicCurrentTime', audio.currentTime);
        }
    }, 500);
    
    // Save on page unload
    window.addEventListener('beforeunload', () => {
        if (!audio.paused) {
            localStorage.setItem('musicCurrentTime', audio.currentTime);
        }
        clearInterval(saveInterval);
    });
    
    // Loop seamlessly when ends
    audio.addEventListener('ended', () => {
        audio.currentTime = 0;
        localStorage.setItem('musicCurrentTime', 0);
        if (musicEnabled === 'true') {
            audio.play();
        }
    });
    
    // Start playing if enabled
    if (musicEnabled === 'true') {
        // Small delay to let page load first
        setTimeout(() => audio.play().catch(() => {}), 500);
    }
    
    // Listen for settings toggle
    window.addEventListener('storage', (e) => {
        if (e.key === 'musicEnabled') {
            musicEnabled = e.newValue === 'true';
            if (musicEnabled) {
                audio.play();
            } else {
                audio.pause();
            }
        }
    });
})();
</script>