<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Fleet Tracker Portfolio</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #030712; color: #f3f4f6; overflow: hidden; }
        .bg-mesh { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: -1; background: radial-gradient(at 0% 0%, #1e1b4b 0, transparent 50%), radial-gradient(at 100% 0%, #312e81 0, transparent 50%), radial-gradient(at 100% 100%, #1e1b4b 0, transparent 50%), radial-gradient(at 0% 100%, #1e1b4b 0, transparent 50%); filter: blur(80px); opacity: 0.6; }
        .glass { background: rgba(17, 24, 39, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3); }
        .btn-premium { background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .btn-premium:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4); }
        .input-glass { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); transition: all 0.3s; }
        .input-glass:focus { background: rgba(255, 255, 255, 0.07); border-color: #3b82f6; box-shadow: 0 0 15px rgba(59, 130, 246, 0.1); outline: none; }
    </style>
</head>
<body class="flex items-center justify-center h-screen relative">
    <div class="bg-mesh"></div>
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-600/20 blur-[100px] animate-pulse"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-indigo-600/20 blur-[100px] animate-pulse"></div>

    <div class="glass p-10 rounded-3xl w-full max-w-md relative z-10 mx-4">
        <div class="text-center mb-10">
            <div class="inline-flex p-4 bg-blue-600 rounded-2xl mb-4 shadow-xl shadow-blue-900/40">
                <i data-lucide="shield-check" class="text-white w-10 h-10"></i>
            </div>
            <h1 class="text-4xl font-extrabold tracking-tighter text-white">
                FLEET<span class="text-blue-500">TRACK</span>
            </h1>
            <p class="text-gray-400 mt-2 font-medium">Silakan masuk ke dashboard kendali</p>
        </div>
        
        @if($errors->any())
            <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-4 rounded-2xl mb-6 text-sm flex items-center gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-gray-400 text-xs font-bold mb-2 uppercase tracking-widest pl-1">Email Address</label>
                <div class="relative group">
                    <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 w-5 h-5 group-focus-within:text-blue-500 transition"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required 
                        placeholder="nama@email.com" 
                        class="w-full pl-12 pr-4 py-4 input-glass rounded-2xl text-white placeholder-gray-600">
                </div>
            </div>

            <div>
                <div class="flex justify-between items-center mb-2 pl-1">
                    <label class="block text-gray-400 text-xs font-bold uppercase tracking-widest">Password</label>
                </div>
                <div class="relative group">
                    <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 w-5 h-5 group-focus-within:text-blue-500 transition"></i>
                    <input type="password" name="password" required 
                        placeholder="••••••••"
                        class="w-full pl-12 pr-4 py-4 input-glass rounded-2xl text-white placeholder-gray-600">
                </div>
            </div>

            <button type="submit" class="w-full btn-premium text-white font-bold py-4 px-6 rounded-2xl text-lg flex items-center justify-center gap-3 active:scale-95">
                <span>Akses Dashboard</span>
                <i data-lucide="chevron-right" class="w-5 h-5"></i>
            </button>
        </form>

        <div class="mt-8 pt-8 border-t border-white/5 text-center px-4">
            <p class="text-gray-500 text-xs leading-relaxed">
                Aplikasi Pelacakan Armada & Manajemen Servis Kendaraan Profesional
            </p>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>