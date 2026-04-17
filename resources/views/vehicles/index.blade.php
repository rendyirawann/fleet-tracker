@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700 relative z-10">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
            <div class="p-2 bg-blue-500/20 rounded-xl max-w-fit">
                <i data-lucide="car-front" class="w-8 h-8 text-blue-400"></i>
            </div>
            Manajemen Garasi
        </h1>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 mb-8 rounded-2xl flex items-center gap-3">
            <i data-lucide="check-circle-2" class="w-5 h-5 flex-shrink-0"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="glass-card p-8 rounded-[2rem] border-t-4 border-t-blue-500 relative overflow-hidden">
        <div class="absolute -right-10 -top-10 opacity-5">
            <i data-lucide="plus-circle" class="w-48 h-48 text-white"></i>
        </div>
        <div class="relative z-10">
            <h2 class="font-bold text-white mb-6 flex items-center gap-2 text-xl">
                <i data-lucide="plus" class="w-5 h-5 text-blue-400"></i>
                Tambah Kendaraan Baru
            </h2>
            <form action="{{ route('vehicles.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-5">
                @csrf
                <div class="relative group">
                    <input type="text" name="name" placeholder="Nama (ex: NMAX)" class="w-full pl-4 pr-10 py-3 input-glass rounded-xl text-white placeholder-gray-500 text-sm" required>
                    <i data-lucide="car" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 w-4 h-4 group-focus-within:text-blue-500 transition"></i>
                </div>
                <div class="relative group">
                    <input type="text" name="license_plate" placeholder="Plat Nomor" class="w-full pl-4 pr-10 py-3 input-glass rounded-xl text-white placeholder-gray-500 text-sm" required>
                    <i data-lucide="hash" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 w-4 h-4 group-focus-within:text-blue-500 transition"></i>
                </div>
                <div class="relative group">
                    <input type="number" name="service_interval_km" placeholder="Batas Servis (KM)" class="w-full pl-4 pr-10 py-3 input-glass rounded-xl text-white placeholder-gray-500 text-sm" required>
                    <i data-lucide="settings-2" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 w-4 h-4 group-focus-within:text-blue-500 transition"></i>
                </div>
                <button type="submit" class="btn-premium text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-blue-500/20 active:scale-95 transition-all text-sm flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan
                </button>
            </form>
        </div>
    </div>

    <h2 class="font-bold text-gray-300 mb-4 uppercase tracking-widest text-sm pt-4">Daftar Kendaraan Anda</h2>
    <div class="grid grid-cols-1 gap-6">
        @forelse($vehicles as $v)
        <div class="glass-card p-6 rounded-[1.5rem] flex flex-col md:flex-row justify-between items-start md:items-center transition-all duration-300 relative overflow-hidden group 
            {{ $activeVehicleId == $v->id ? 'border-blue-500/50 shadow-[0_0_30px_rgba(59,130,246,0.15)] bg-blue-900/10' : 'hover:bg-white/5 border-transparent border-t-white/10' }}">
            
            @if($activeVehicleId == $v->id)
                <div class="absolute top-0 left-0 w-1 h-full bg-blue-500 shadow-[0_0_20px_#3b82f6]"></div>
            @endif

            <div class="mb-6 md:mb-0 relative z-10 w-full md:w-7/12 lg:w-2/3 flex flex-col">
                <div class="flex items-center gap-3 mb-2">
                    <h3 class="text-2xl font-black text-white group-hover:text-blue-400 transition-colors">{{ $v->name }}</h3>
                    <span class="bg-white/10 text-gray-300 px-3 py-1 rounded-full text-xs font-mono tracking-wider border border-white/5">{{ $v->license_plate }}</span>
                </div>
                
                <div class="flex flex-col gap-1 mt-3">
                    @php
                        // Logika Kalkulasi Target Servis Berikutnya
                        $targetServiceKm = $v->last_service_km + $v->service_interval_km;
                        $currentIntervalProgress = $v->current_accumulated_km - $v->last_service_km;
                        $percentage = ($v->service_interval_km > 0) ? min(100, ($currentIntervalProgress / $v->service_interval_km) * 100) : 0;
                        
                        $barColor = $percentage >= 100 ? 'bg-gradient-to-r from-rose-500 to-red-500' : ($percentage > 75 ? 'bg-gradient-to-r from-amber-400 to-orange-500' : 'bg-gradient-to-r from-blue-500 to-indigo-500');
                    @endphp
                    
                    <div class="flex justify-between text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">
                        <span>Total: <span class="text-white">{{ number_format($v->current_accumulated_km, 2) }}</span> KM</span>
                        <span>Target Servis: <span class="{{ $percentage >= 100 ? 'text-rose-400' : 'text-white' }}">{{ number_format($targetServiceKm, 2) }}</span> KM</span>
                    </div>
                    
                    <div class="w-full h-2 bg-gray-800 rounded-full overflow-hidden shadow-inner">
                        <div class="h-full {{ $barColor }} transition-all duration-1000 relative" style="width: {{ $percentage }}%">
                            @if($percentage >= 100)
                                <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                            @endif
                        </div>
                    </div>
                </div>
                
                @if($v->current_accumulated_km >= $targetServiceKm)
                    <div class="mt-5 flex flex-col sm:flex-row items-start sm:items-center gap-3">
                        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider flex items-center gap-2 w-fit shadow-[0_0_15px_rgba(225,29,72,0.15)]">
                            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                            Waktunya Servis!
                        </div>
                        <form action="{{ route('vehicles.service', $v->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-rose-600/80 hover:bg-rose-500 text-white border border-rose-500/50 px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-[0_0_15px_rgba(225,29,72,0.3)] hover:shadow-[0_0_25px_rgba(225,29,72,0.5)] flex items-center gap-2 active:scale-95">
                                <i data-lucide="wrench" class="w-4 h-4"></i> Konfirmasi Servis
                            </button>
                        </form>
                    </div>
                @endif

                @if($v->services->count() > 0)
                    <div class="mt-5 border-t border-white/5 pt-4">
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-2 flex items-center gap-2">
                            <i data-lucide="history" class="w-3 h-3"></i> Terakhir Diservis
                        </p>
                        <div class="flex items-center gap-3 text-sm text-gray-400">
                            <span class="bg-white/5 border border-white/10 px-3 py-1 rounded-lg text-white font-mono shadow-sm">
                                {{ number_format($v->services->first()->service_km, 2) }} KM
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 opacity-50"></i>
                                {{ $v->services->first()->created_at->format('d M Y') }}
                            </span>
                        </div>
                    </div>
                @endif
            </div>
            
            <div class="relative z-10 w-full md:w-auto mt-4 md:mt-0 flex justify-end">
     @if($activeVehicleId == $v->id)
                    <div class="flex flex-col gap-3 w-full md:w-auto">
                        <div class="bg-blue-500/20 text-blue-400 border border-blue-500/30 px-6 py-3.5 rounded-xl font-bold flex items-center justify-center gap-3 whitespace-nowrap shadow-[0_0_15px_rgba(59,130,246,0.2)] cursor-default">
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                            </span>
                            Sedang Digunakan
                        </div>
                        
                        <form action="{{ route('vehicles.deactivate') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-gray-500 hover:text-rose-400 text-xs font-bold uppercase tracking-widest flex items-center justify-center gap-2 transition-colors active:scale-95 group">
                                <i data-lucide="x-circle" class="w-4 h-4 group-hover:text-rose-500 transition-colors"></i>
                                Berhenti Gunakan
                            </button>
                        </form>
                    </div>
                @else
                    <form action="{{ route('vehicles.active', $v->id) }}" method="POST" class="w-full md:w-auto">
                        @csrf
                        <button type="submit" class="w-full bg-white/5 border border-white/10 text-gray-300 font-bold px-6 py-3.5 rounded-xl hover:bg-white/10 hover:text-white hover:border-white/20 transition-all flex items-center justify-center gap-2 whitespace-nowrap active:scale-95 group-hover:bg-blue-500 group-hover:text-white group-hover:border-blue-400 group-hover:shadow-[0_0_20px_rgba(59,130,246,0.3)]">
                            <i data-lucide="check-circle" class="w-5 h-5 opacity-50 group-hover:opacity-100"></i>
                            Gunakan Hari Ini
                        </button>
                    </form>
                @endif
            </div>
        </div>
        @empty
        <div class="glass-card text-center py-16 rounded-[2rem] border-dashed border-2 border-white/10">
            <div class="inline-flex justify-center items-center w-20 h-20 rounded-full bg-white/5 mb-4 border border-white/5">
                <i data-lucide="car-off" class="w-10 h-10 text-gray-500"></i>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Garasi Kosong</h3>
            <p class="text-gray-500">Belum ada kendaraan di garasi Anda. Tambahkan sekarang!</p>
        </div>
        @endforelse
    </div>
</div>
@endsection