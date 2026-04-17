<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Tracking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-md p-4 flex justify-between items-center">
        <h1 class="text-xl font-bold text-gray-800">Fleet Tracker</h1>
        <div class="flex items-center gap-4">
            <span class="text-gray-600">Halo, {{ auth()->user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-semibold">Logout</button>
            </form>
        </div>
    </nav>

    <main class="max-w-md mx-auto mt-10 p-6 bg-white rounded-xl shadow-lg text-center">
        <h2 class="text-2xl font-bold mb-4">Sesi Perjalanan</h2>
        
        <div id="status-box" class="p-4 mb-6 rounded bg-gray-100 text-gray-600 font-medium">
            Status: Menunggu...
        </div>

        <div class="flex flex-col gap-4">
            <button id="btn-start" class="bg-green-500 text-white py-3 rounded-lg font-bold text-lg hover:bg-green-600">
                Mulai Perjalanan
            </button>
            <button id="btn-stop" class="bg-red-500 text-white py-3 rounded-lg font-bold text-lg hover:bg-red-600 hidden">
                Hentikan Perjalanan
            </button>
        </div>

        <div class="mt-6 text-left text-sm text-gray-500">
            <p><strong>Latitude:</strong> <span id="val-lat">-</span></p>
            <p><strong>Longitude:</strong> <span id="val-lng">-</span></p>
        </div>
    </main>

    <script>
        let wakeLock = null;
        let watchId = null;

        const btnStart = document.getElementById('btn-start');
        const btnStop = document.getElementById('btn-stop');
        const statusBox = document.getElementById('status-box');
        const valLat = document.getElementById('val-lat');
        const valLng = document.getElementById('val-lng');

        // Fungsi Meminta Wake Lock (Layar Tetap Nyala)
        async function requestWakeLock() {
            try {
                if ('wakeLock' in navigator) {
                    wakeLock = await navigator.wakeLock.request('screen');
                    console.log('Wake Lock aktif!');
                }
            } catch (err) {
                console.error(`${err.name}, ${err.message}`);
            }
        }

        // Mulai Tracking
        btnStart.addEventListener('click', async () => {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung GPS!');
                return;
            }

            await requestWakeLock();

            statusBox.className = "p-4 mb-6 rounded bg-green-100 text-green-700 font-bold";
            statusBox.innerText = "Tracking Aktif - Layar Terkunci Nyala";
            
            btnStart.classList.add('hidden');
            btnStop.classList.remove('hidden');

            // Baca GPS setiap kali ada pergerakan
            watchId = navigator.geolocation.watchPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    valLat.innerText = lat;
                    valLng.innerText = lng;

                    // Kirim ke Backend Laravel
                    sendToBackend(lat, lng);
                },
                (error) => {
                    console.error('Error GPS:', error);
                    alert('Gagal mendapatkan lokasi. Pastikan izin GPS menyala.');
                },
                { enableHighAccuracy: true, maximumAge: 0 }
            );
        });

        // Hentikan Tracking
        btnStop.addEventListener('click', () => {
            if (watchId) navigator.geolocation.clearWatch(watchId);
            if (wakeLock) {
                wakeLock.release().then(() => wakeLock = null);
            }

            statusBox.className = "p-4 mb-6 rounded bg-gray-100 text-gray-600 font-medium";
            statusBox.innerText = "Tracking Dihentikan";
            
            btnStop.classList.add('hidden');
            btnStart.classList.remove('hidden');
        });

        // Fungsi Kirim Data via AJAX Fetch
        function sendToBackend(lat, lng) {
            fetch('{{ route('track.location') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ lat: lat, lng: lng })
            }).then(response => response.json())
              .then(data => console.log('Sukses kirim:', data))
              .catch(err => console.error('Gagal kirim:', err));
        }
    </script>
</body>
</html>