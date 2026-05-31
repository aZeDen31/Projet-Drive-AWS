@extends('layouts.app')

@section('title', 'AWS VPC & Subnet Control Panel')

@section('content')
    <div class="max-w-6xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-200/80">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 pb-6 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">Réseau Virtuel</span>
                    <span class="text-xs text-slate-400 font-mono">Region: {{ config('filesystems.disks.s3.region') }}</span>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">AWS VPC & Subnet Manager</h1>
                <p class="text-sm text-slate-500 mt-1">Configurez et isolez vos réseaux avec des VPCs et des sous-réseaux (Subnets) associés.</p>
            </div>
            <div class="flex gap-3 mt-4 md:mt-0">
                <a href="{{ route('vpc.list') }}" class="px-4 py-2 bg-orange-600 text-white rounded-xl hover:bg-orange-700 text-sm font-semibold transition shadow-md shadow-orange-600/10">
                    Actualiser
                </a>
            </div>
        </div>

        @if(isset($error))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3.5 rounded-xl mb-6 text-sm font-mono overflow-x-auto shadow-sm">
                <div class="flex gap-2">
                    <svg class="h-5 w-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span><strong>Erreur de connexion AWS :</strong> {{ $error }}</span>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3.5 rounded-xl mb-6 text-sm font-medium shadow-sm flex gap-2">
                <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3.5 rounded-xl mb-6 text-sm font-medium shadow-sm flex gap-2">
                <svg class="h-5 w-5 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-amber-50 border border-amber-200 text-amber-900 px-4 py-3.5 rounded-xl mb-6 text-sm shadow-sm">
                <div class="flex gap-2">
                    <svg class="h-5 w-5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <span class="font-bold">Veuillez corriger les erreurs suivantes :</span>
                        <ul class="list-disc list-inside mt-1 font-medium">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @php
            $vpcs = json_decode($vpcsJson, true) ?? [];
            $subnets = json_decode($subnetsJson, true) ?? [];
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            <div class="p-6 bg-slate-50 border border-slate-200/80 rounded-2xl shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-1.5 bg-orange-100 text-orange-600 rounded-lg">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900">1. Créer un nouveau VPC</h2>
                </div>
                <form action="{{ route('vpc.create') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="vpc_name" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Nom du VPC</label>
                        <input type="text" name="name" id="vpc_name" placeholder="ex: mon-reseau-principal" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 bg-white text-sm transition-all" max="50">
                    </div>
                    <div>
                        <label for="vpc_cidr_block" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Bloc CIDR (IPv4)</label>
                        <input type="text" name="cidr_block" id="vpc_cidr_block" required placeholder="ex: 10.0.0.0/16" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 bg-white text-sm font-mono transition-all">
                    </div>
                    <button type="submit" class="w-full px-5 py-2.5 bg-orange-600 text-white rounded-xl hover:bg-orange-700 font-semibold transition text-sm flex items-center justify-center gap-1.5 shadow-md shadow-orange-600/15 h-[42px]">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Déployer le VPC
                    </button>
                </form>
            </div>

            <div class="p-6 bg-slate-50 border border-slate-200/80 rounded-2xl shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-1.5 bg-orange-100 text-orange-600 rounded-lg">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 009 11M5 11V9a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900">2. Créer un sous-réseau (Subnet)</h2>
                </div>
                <form action="{{ route('vpc.subnet.create') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="vpc_id" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">VPC Associé (Parent)</label>
                        <select name="vpc_id" id="vpc_id" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 bg-white text-sm transition-all">
                            <option value="">Sélectionnez le VPC parent...</option>
                            @foreach($vpcs as $vpc)
                                <option value="{{ $vpc['id'] }}">{{ $vpc['name'] }} ({{ $vpc['id'] }}) - [{{ $vpc['cidr'] }}]</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="subnet_name" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Nom du Subnet</label>
                            <input type="text" name="name" id="subnet_name" placeholder="ex: public-subnet-1" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 bg-white text-sm transition-all" max="50">
                        </div>
                        <div>
                            <label for="subnet_cidr_block" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Bloc CIDR (ex: /24)</label>
                            <input type="text" name="cidr_block" id="subnet_cidr_block" required placeholder="ex: 10.0.1.0/24" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 bg-white text-sm font-mono transition-all">
                        </div>
                    </div>
                    <div>
                        <label for="availability_zone" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Zone de Disponibilité (Optionnel)</label>
                        <select name="availability_zone" id="availability_zone" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 bg-white text-sm transition-all">
                            <option value="">Sélection automatique par AWS</option>
                            @php
                                $region = config('filesystems.disks.s3.region', 'eu-west-3');
                            @endphp
                            <option value="{{ $region }}a">{{ $region }}a</option>
                            <option value="{{ $region }}b">{{ $region }}b</option>
                            <option value="{{ $region }}c">{{ $region }}c</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full px-5 py-2.5 bg-orange-600 text-white rounded-xl hover:bg-orange-700 font-semibold transition text-sm flex items-center justify-center gap-1.5 shadow-md shadow-orange-600/15 h-[42px]">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Créer le sous-réseau
                    </button>
                </form>
            </div>
        </div>

        <h2 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-1.5">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            1. Virtual Private Clouds (VPC) Détectés
        </h2>
        
        <div class="overflow-hidden border border-slate-200/80 rounded-2xl shadow-sm bg-white mb-12">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Nom du VPC</th>
                        <th class="px-6 py-4">ID de Ressource</th>
                        <th class="px-6 py-4">Bloc CIDR</th>
                        <th class="px-6 py-4">Statut</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    @forelse($vpcs as $vpc)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-900 flex items-center gap-2">
                                <div class="h-2 w-2 rounded-full {{ $vpc['state'] === 'available' ? 'bg-emerald-500' : 'bg-amber-500 animate-pulse' }}"></div>
                                {{ $vpc['name'] }}
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $vpc['id'] }}</td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-xs font-semibold bg-slate-50 text-slate-600 rounded px-2 py-1 border border-slate-100">{{ $vpc['cidr'] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold 
                                    {{ $vpc['state'] === 'available' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $vpc['state'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($vpc['is_default'])
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Par défaut
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        Personnalisé
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                @if($vpc['is_default'])
                                    <span class="text-xs text-slate-400 italic">Protégé</span>
                                @else
                                    <form action="{{ route('vpc.delete', $vpc['id']) }}" method="POST" onsubmit="return confirm('ATTENTION : Êtes-vous sûr de vouloir supprimer définitivement ce VPC ? Cette action supprimera également les sous-réseaux et tables de routage associés.')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Supprimer le VPC" class="p-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl transition-all shadow-sm inline-flex items-center justify-center border border-red-100 hover:border-red-200">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="h-10 w-10 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <span>Aucun VPC détecté dans cette région.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h2 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-1.5">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 009 11M5 11V9a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            2. Sous-réseaux (Subnets) Détectés
        </h2>

        <div class="overflow-hidden border border-slate-200/80 rounded-2xl shadow-sm bg-white">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Nom du Sous-réseau</th>
                        <th class="px-6 py-4">ID de Ressource</th>
                        <th class="px-6 py-4">VPC Parent</th>
                        <th class="px-6 py-4">Bloc CIDR</th>
                        <th class="px-6 py-4">Zone dispo.</th>
                        <th class="px-6 py-4">IPs Libres</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    @forelse($subnets as $sub)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-900 flex items-center gap-2">
                                <div class="h-2 w-2 rounded-full {{ $sub['state'] === 'available' ? 'bg-emerald-500' : 'bg-amber-500 animate-pulse' }}"></div>
                                {{ $sub['name'] }}
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $sub['id'] }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-400">
                                @php
                                    $parentVpc = collect($vpcs)->firstWhere('id', $sub['vpc_id']);
                                    $parentVpcName = $parentVpc ? $parentVpc['name'] : 'Inconnu';
                                @endphp
                                <span title="VPC: {{ $sub['vpc_id'] }}">{{ $parentVpcName }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-xs font-semibold bg-slate-50 text-slate-600 rounded px-2 py-1 border border-slate-100">{{ $sub['cidr'] }}</span>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $sub['availability_zone'] }}</td>
                            <td class="px-6 py-4 font-medium text-slate-800">
                                <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-100 text-xs font-bold">{{ $sub['available_ip_count'] }} IPs</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($sub['is_default'])
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Par défaut
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        Personnalisé
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                @if($sub['is_default'])
                                    <span class="text-xs text-slate-400 italic">Protégé</span>
                                @else
                                    <form action="{{ route('vpc.subnet.delete', $sub['id']) }}" method="POST" onsubmit="return confirm('ATTENTION : Voulez-vous vraiment supprimer ce sous-réseau (Subnet) ? Cette action est irréversible.')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Supprimer le sous-réseau" class="p-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl transition-all shadow-sm inline-flex items-center justify-center border border-red-100 hover:border-red-200">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="h-10 w-10 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 009 11M5 11V9a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <span>Aucun sous-réseau détecté dans cette région.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
