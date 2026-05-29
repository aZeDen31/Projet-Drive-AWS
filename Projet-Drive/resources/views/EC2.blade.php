<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test EC2 - Simple List</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Test de Connexion EC2</h1>
                <p class="text-sm text-gray-500">Page pour lister les instances EC2</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('drive.list') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm font-semibold transition">
                    Retour au Drive
                </a>
                <a href="{{ route('ec2.list') }}" class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700 text-sm font-semibold transition">
                    Actualiser
                </a>
            </div>
        </div>

        @if(isset($error))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 text-sm font-mono overflow-x-auto">
                <strong>Erreur AWS :</strong> {{ $error }}
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 text-sm font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase border-b border-gray-200">
                        <th class="px-6 py-3">Nom</th>
                        <th class="px-6 py-3">ID Instance</th>
                        <th class="px-6 py-3">État</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Date de Lancement</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                    @php
                        $instances = json_decode($instancesJson, true) ?? [];
                    @endphp
                    @forelse($instances as $inst)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-bold text-gray-900">{{ $inst['name'] }}</td>
                            <td class="px-6 py-4 font-mono text-xs">{{ $inst['id'] }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded text-xs font-bold 
                                    {{ $inst['state'] === 'running' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $inst['state'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs">{{ $inst['type'] }}</td>
                            <td class="px-6 py-4 text-xs text-gray-500">{{ $inst['launch_time'] }}</td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex justify-end gap-1.5">
                                    @if($inst['state'] === 'stopped')
                                        <form action="{{ route('ec2.start', $inst['id']) }}" method="POST" onsubmit="return confirm('Confirmer le démarrage de cette instance ?')" class="inline">
                                            @csrf
                                            <button type="submit" title="Démarrer" class="p-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded transition shadow-sm inline-flex items-center justify-center">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('ec2.stop', $inst['id']) }}" method="POST" onsubmit="return confirm('Confirmer l\'arrêt de cette instance ?')" class="inline">
                                            @csrf
                                            <button type="submit" title="Stopper" class="p-1.5 bg-slate-600 hover:bg-slate-700 text-white rounded transition shadow-sm inline-flex items-center justify-center" {{ in_array($inst['state'], ['stopping', 'pending', 'shutting-down', 'terminated']) ? 'disabled' : '' }}>
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <rect x="6" y="6" width="12" height="12" rx="1.5" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('ec2.restart', $inst['id']) }}" method="POST" onsubmit="return confirm('Confirmer le redémarrage de cette instance ?')" class="inline">
                                        @csrf
                                        <button type="submit" title="Redémarrer" class="p-1.5 bg-orange-600 hover:bg-orange-700 text-white rounded transition shadow-sm inline-flex items-center justify-center">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                            </svg>
                                        </button>
                                    </form>

                                    <form action="{{ route('ec2.delete', $inst['id']) }}" method="POST" onsubmit="return confirm('ATTENTION : Voulez-vous vraiment résilier (supprimer) définitivement cette instance EC2 ?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Supprimer" class="p-1.5 bg-red-600 hover:bg-red-700 text-white rounded transition shadow-sm inline-flex items-center justify-center">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                Aucune instance EC2 trouvée dans cette région.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>