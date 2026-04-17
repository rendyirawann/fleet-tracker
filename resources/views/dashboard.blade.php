@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    
    <!-- Top Header Card -->
    <div class="glass-card rounded-[2.5rem] p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 p-8 opacity-10">
            <i data-lucide="satellite" class="w-32 h-32 text-blue-500"></i>
        </div>
        
        <div class="relative z-10">
            <h2 class="text-3xl font-extrabold text-white tracking-tight mb-2">Pusat Kendali Tracking</h2>
            
            @if(auth()->user()->activeVehicle)
                <div class="flex items-center gap-3 text-blue-400 font-semibold bg-blue-500/10 w-fit px-4 py-2 rounded-full border border-blue-500/20">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                    </span>
                    <span>{{ auth()->user()->activeVehicle->name }} • {{ auth()->user()->activeVehicle->license_plate }}</span>
                </div>
            @else
                <div class="flex items-center gap-3 text-amber-400 font-bold bg-amber-500/10 px-6 py-4 rounded-3xl border border-amber-500/20">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                    <span>Pilih kendaraan di Garasi untuk memulai pelacakan.</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Main Control Card -->
    <div class="glass-card rounded-[2.5rem] p-10 text-center relative">
        <div id="status-box" class="inline-flex items-center gap-3 px-6 py-3 rounded-2xl bg-white/5 text-gray-400 font-bold uppercase tracking-widest text-xs mb-8 transition-all duration-500 border border-white/5">
            <div id="status-dot" class="w-2 h-2 rounded-full bg-gray-500"></div>
            <span id="status-text">System Ready</span>
        </div>

        <div class="flex flex-col sm:flex-row gap-6 justify-center">
            <button id="btn-start" class="btn-premium group flex items-center justify-center gap-4 px-10 py-5 rounded-[2rem] text-white font-extrabold text-xl shadow-2xl shadow-blue-500/20 active:scale-95 disabled:opacity-30 disabled:grayscale transition-all {{ !auth()->user()->activeVehicle ? 'cursor-not-allowed' : '' }}" {{ !auth()->user()->activeVehicle ? 'disabled' : '' }}>
                <i data-lucide="play" class="w-6 h-6 fill-current"></i>
                <span>Mulai Sesi</span>
            </button>
            <button id="btn-stop" class="bg-rose-600 hover:bg-rose-500 group flex items-center justify-center gap-4 px-10 py-5 rounded-[2rem] text-white font-extrabold text-xl shadow-2xl shadow-rose-500/20 active:scale-95 transition-all hidden">
                <i data-lucide="square" class="w-6 h-6 fill-current"></i>
                <span>Berhenti</span>
            </button>
        </div>

        <!-- Coordinates Grid -->
        <div class="grid grid-cols-2 gap-4 mt-12">
            <div class="bg-white/5 p-4 rounded-3xl border border-white/5 text-left">
                <span class="text-[10px] uppercase tracking-widest font-bold text-gray-500 block mb-1">Latitude</span>
                <span id="val-lat" class="font-mono text-white text-sm font-bold truncate">-</span>
            </div>
            <div class="bg-white/5 p-4 rounded-3xl border border-white/5 text-left">
                <span class="text-[10px] uppercase tracking-widest font-bold text-gray-500 block mb-1">Longitude</span>
                <span id="val-lng" class="font-mono text-white text-sm font-bold truncate">-</span>
            </div>
        </div>
    </div>

    <!-- Distance Display -->
    <div class="glass-card rounded-[2.5rem] p-10 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 opacity-5 rotate-12">
            <i data-lucide="gauge" class="w-64 h-64 text-white"></i>
        </div>
        
        <div class="relative z-10 text-center">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-[0.3em] mb-4">Akumulasi Jarak Tempuh</h3>
            <div class="flex items-center justify-center gap-3">
                <span id="val-km" class="text-7xl font-black text-transparent bg-clip-text bg-gradient-to-br from-white to-gray-500 tabular-nums">
                    {{ auth()->user()->activeVehicle ? number_format(auth()->user()->activeVehicle->current_accumulated_km, 2) : '0.00' }}
                </span>
                <span class="text-2xl font-bold text-blue-500 mb-[-1.5rem]">KM</span>
            </div>
        </div>
    </div>

    <div id="service-warning" class="hidden animate-bounce">
        <div class="bg-rose-600 text-white p-6 rounded-[2rem] flex items-center gap-6 shadow-2xl shadow-rose-900/40">
            <div class="p-4 bg-white/20 rounded-2xl">
                <i data-lucide="wrench" class="w-8 h-8"></i>
            </div>
            <div>
                <h4 class="font-black text-xl">Waktunya Servis!</h4>
                <p class="text-rose-100 text-sm">Kendaraan Anda telah melampaui batas jarak tempuh aman.</p>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    let wakeLock = null;
    let watchId = null;

    const btnStart = document.getElementById('btn-start');
    const btnStop = document.getElementById('btn-stop');
    const statusBox = document.getElementById('status-box');
    const statusDot = document.getElementById('status-dot');
    const statusText = document.getElementById('status-text');
    const valLat = document.getElementById('val-lat');
    const valLng = document.getElementById('val-lng');

    async function requestWakeLock() {
        try {
            if ('wakeLock' in navigator) {
                wakeLock = await navigator.wakeLock.request('screen');
            }
        } catch (err) { console.error(`${err.name}, ${err.message}`); }
    }

    btnStart.addEventListener('click', async () => {
        if (!navigator.geolocation) { alert('Browser Anda tidak mendukung GPS!'); return; }
        await requestWakeLock();

        statusBox.classList.remove('bg-white/5', 'text-gray-400');
        statusBox.classList.add('bg-blue-500/20', 'text-blue-400', 'border-blue-500/30');
        statusDot.classList.remove('bg-gray-500');
        statusDot.classList.add('bg-blue-400', 'animate-pulse');
        statusText.innerText = "TRACKING LIVE";

        btnStart.classList.add('hidden');
        btnStop.classList.remove('hidden');

        watchId = navigator.geolocation.watchPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                valLat.innerText = lat.toFixed(6);
                valLng.innerText = lng.toFixed(6);
                sendToBackend(lat, lng);
            },
            (error) => { alert('Gagal mendapatkan lokasi. Pastikan izin GPS menyala.'); },
            { enableHighAccuracy: true, maximumAge: 0 }
        );
    });

    btnStop.addEventListener('click', () => {
        if (watchId) navigator.geolocation.clearWatch(watchId);
        if (wakeLock) wakeLock.release().then(() => wakeLock = null);

        statusBox.className = "inline-flex items-center gap-3 px-6 py-3 rounded-2xl bg-white/5 text-gray-400 font-bold uppercase tracking-widest text-xs mb-8 transition-all duration-500 border border-white/5";
        statusDot.className = "w-2 h-2 rounded-full bg-gray-500";
        statusText.innerText = "SESSION ENDED";
        
        btnStop.classList.add('hidden');
        btnStart.classList.remove('hidden');
    });

    function sendToBackend(lat, lng) {
        fetch('{{ route('track.location') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ lat: lat, lng: lng })
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                document.getElementById('val-km').innerText = data.total_km;
                if(data.needs_service) document.getElementById('service-warning').classList.remove('hidden');
            }
        })
        .catch(err => console.error('Gagal kirim:', err));
    }
    
    // Refresh icons
    lucide.createIcons();
</script>
@endpush