<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fleet Tracker | Premium Fleet Management</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #030712;
            color: #f3f4f6;
            overflow-x: hidden;
        }

        /* Animated Mesh Background */
        .bg-mesh {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1;
            background: radial-gradient(at 0% 0%, #1e1b4b 0, transparent 50%),
                        radial-gradient(at 100% 0%, #312e81 0, transparent 50%),
                        radial-gradient(at 100% 100%, #1e1b4b 0, transparent 50%),
                        radial-gradient(at 0% 100%, #1e1b4b 0, transparent 50%);
            filter: blur(80px);
            opacity: 0.6;
        }

        .bg-blob {
            position: fixed;
            z-index: -1;
            filter: blur(100px);
            opacity: 0.4;
            animation: float 20s infinite alternate;
        }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(10%, 10%) scale(1.2); }
        }

        /* Glassmorphism */
        .glass {
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }

        .glass-card {
            background: rgba(31, 41, 55, 0.5);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.1);
        }

        /* Buttons & Accents */
        .btn-premium {
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4);
        }

        input:focus {
            ring: 2px;
            ring-color: #3b82f6;
            outline: none;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #030712; }
        ::-webkit-scrollbar-thumb { background: #1f2937; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #3b82f6; }

        /* Fix Browser Autofill Background on Dark Mode */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active {
            /* Tahan perubahan warna background selama 5000 detik (efek transparan tetap terjaga) */
            transition: background-color 5000s ease-in-out 0s !important;
            /* Paksa warna teks tetap putih */
            -webkit-text-fill-color: #ffffff !important;
            caret-color: white;
        }

        /* Input Glassmorphism */
        .input-glass {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            color: #ffffff; /* Memastikan font selalu putih */
        }

        .input-glass:focus {
            background: rgba(255, 255, 255, 0.1) !important;
            border-color: #3b82f6;
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.2);
            outline: none;
        }
        
        .input-glass::placeholder {
            color: #6b7280; /* Warna placeholder abu-abu */
        }
    </style>
    
    @stack('head') 
</head>
<body class="min-h-screen relative pb-12">

    <!-- Background Layer -->
    <div class="bg-mesh"></div>
    <div class="bg-blob w-96 h-96 bg-blue-600 top-[-10%] left-[-10%]"></div>
    <div class="bg-blob w-96 h-96 bg-indigo-600 bottom-[-10%] right-[-10%]"></div>

    <!-- Floating Navbar -->
    <nav class="sticky top-4 z-50 max-w-6xl mx-auto px-4">
        <div class="glass rounded-2xl px-6 py-4 flex justify-between items-center mt-4">
            
            <div class="flex items-center space-x-10">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 group">
                    <div class="p-2 bg-blue-600 rounded-lg group-hover:scale-110 transition duration-300">
                        <i data-lucide="zap" class="text-white w-6 h-6"></i>
                    </div>
                    <span class="font-extrabold text-2xl tracking-tighter text-white">
                        FLEET<span class="text-blue-500">TRACK</span>
                    </span>
                </a>
                
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 group {{ request()->routeIs('dashboard') ? 'text-blue-400' : 'text-gray-400 hover:text-white' }} transition font-semibold">
                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                        <span>Tracking</span>
                    </a>
                    <a href="{{ route('vehicles.index') }}" class="flex items-center space-x-2 group {{ request()->routeIs('vehicles.*') ? 'text-blue-400' : 'text-gray-400 hover:text-white' }} transition font-semibold">
                        <i data-lucide="layout-grid" class="w-4 h-4"></i>
                        <span>Garasi</span>
                    </a>
                </div>
            </div>

            <div class="flex items-center space-x-6">
                <div class="hidden sm:flex flex-col items-end">
                    <span class="text-gray-400 text-xs uppercase tracking-widest font-bold">Driver</span>
                    <span class="text-white text-sm font-semibold">{{ auth()->user()->name }}</span>
                </div>
                
                <div class="h-8 w-[1px] bg-gray-700 hidden sm:block"></div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="p-2 rounded-xl text-gray-400 hover:text-rose-500 hover:bg-rose-500/10 transition">
                        <i data-lucide="log-out" class="w-6 h-6"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Mobile Navigation Bar (Bottom) -->
    <div class="md:hidden fixed bottom-6 left-4 right-4 z-50">
        <div class="glass rounded-2xl p-4 flex justify-around items-center">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('dashboard') ? 'text-blue-500' : 'text-gray-400' }}">
                <i data-lucide="map-pin" class="w-6 h-6"></i>
                <span class="text-[10px] font-bold uppercase tracking-tight">Track</span>
            </a>
            <a href="{{ route('vehicles.index') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('vehicles.*') ? 'text-blue-500' : 'text-gray-400' }}">
                <i data-lucide="layout-grid" class="w-6 h-6"></i>
                <span class="text-[10px] font-bold uppercase tracking-tight">Garasi</span>
            </a>
        </div>
    </div>

    <main class="max-w-6xl mx-auto p-4 mt-12 relative z-10">
        @yield('content')
    </main>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();
    </script>
    @stack('scripts')

</body>
</html>
