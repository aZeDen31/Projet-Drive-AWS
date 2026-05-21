<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50 font-sans antialiased text-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AWS S3 - Page de Test de Connexion</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-full flex flex-col bg-slate-50/50 py-10 px-4 sm:px-6 lg:px-8">

    <div class="max-w-4xl mx-auto w-full">
        <!-- Header -->
        <header class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-600 text-white shadow-lg shadow-orange-500/20">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold uppercase tracking-wider text-orange-600">Environnement AWS S3</span>
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-800 border border-slate-200">Mode Test Direct</span>
                    </div>
                    <h1 class="text-2xl font-extrabold text-slate-900 leading-tight">Page de test de connexion</h1>
                </div>
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('drive.list') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-all shadow-sm">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Interface Drive
                </a>
                <a href="{{ route('drive.test') }}" class="inline-flex items-center gap-2 rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-orange-500 transition-all shadow-md shadow-orange-600/15">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3-3m0 0l3 3m-3-3v12" />
                    </svg>
                    Actualiser
                </a>
            </div>
        </header>

        <!-- S3 Bucket Configuration Info Card -->
        <section class="mb-6 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3">Informations de configuration S3</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="block text-xs font-medium text-slate-400">Nom du Bucket</span>
                    <span class="block text-sm font-semibold text-slate-800 font-mono truncate" title="{{ env('AWS_BUCKET') }}">{{ env('AWS_BUCKET', 'Non défini') }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="block text-xs font-medium text-slate-400">Région AWS</span>
                    <span class="block text-sm font-semibold text-slate-800 font-mono">{{ env('AWS_DEFAULT_REGION', 'Non défini') }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="block text-xs font-medium text-slate-400">Statut de la Connexion</span>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        @if($error)
                            <span class="inline-block h-2 w-2 rounded-full bg-red-500 animate-pulse"></span>
                            <span class="text-sm font-bold text-red-600">Erreur de Connexion</span>
                        @else
                            <span class="inline-block h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-sm font-bold text-emerald-600">S3 Connecté</span>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <!-- Error Card if AWS connection fails -->
        @if($error)
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50/50 p-6 text-red-800 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="h-10 w-10 shrink-0 rounded-xl bg-red-100 text-red-600 flex items-center justify-center">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-red-900 mb-1">Impossible de communiquer avec Amazon S3</h3>
                        <p class="text-sm text-red-700 mb-3">Une erreur est survenue lors de la tentative de récupération de la liste des fichiers. Veuillez vérifier vos clés AWS d'accès et votre configuration réseau.</p>
                        <div class="p-3.5 bg-red-950/5 text-red-900 rounded-xl border border-red-200/50 font-mono text-xs break-all max-h-60 overflow-y-auto">
                            <strong>Détails du message d'erreur :</strong><br>
                            {{ $error }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- S3 Files Section -->
        <main class="rounded-2xl border border-slate-200/80 bg-white overflow-hidden shadow-sm">
            <div class="border-b border-slate-200/60 bg-slate-50 px-6 py-4 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-800">Fichiers réels stockés sur S3</h2>
                    <p class="text-xs text-slate-500">Tous les fichiers trouvés dans le bucket (via <code>Storage::disk('s3')->allFiles()</code>)</p>
                </div>
                <span class="inline-flex items-center rounded-full bg-orange-50 px-2.5 py-0.5 text-xs font-semibold text-orange-700 border border-orange-200/60">
                    {{ count($files) }} {{ count($files) <= 1 ? 'fichier' : 'fichiers' }}
                </span>
            </div>

            @if(count($files) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <tr>
                                <th scope="col" class="px-6 py-3.5">Fichier / Chemin S3</th>
                                <th scope="col" class="px-6 py-3.5">Dossier parent</th>
                                <th scope="col" class="px-6 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                            @foreach($files as $file)
                                @php
                                    $parts = explode('/', $file);
                                    $fileName = end($parts);
                                    $dirName = count($parts) > 1 ? implode('/', array_slice($parts, 0, -1)) : 'Racine';
                                    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <!-- File Icon Badge based on ext -->
                                            <div class="h-9 w-9 shrink-0 rounded-lg flex items-center justify-center {{ $dirName === 'Racine' ? 'bg-orange-50 text-orange-600' : 'bg-slate-100 text-slate-600' }}">
                                                @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'svg']))
                                                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                @elseif(strtolower($ext) === 'pdf')
                                                    <svg class="h-4.5 w-4.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                    </svg>
                                                @elseif(in_array(strtolower($ext), ['zip', 'rar', 'tar', 'gz']))
                                                    <svg class="h-4.5 w-4.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                                    </svg>
                                                @else
                                                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <div>
                                                <span class="block font-semibold text-slate-800 break-all" title="{{ $file }}">{{ $fileName }}</span>
                                                <span class="block text-[10px] text-slate-400 font-mono font-medium truncate max-w-xs sm:max-w-md">{{ $file }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500 font-medium">
                                        @if($dirName === 'Racine')
                                            <span class="inline-flex items-center rounded-full bg-slate-50 px-2 py-1 text-[10px] font-semibold text-slate-500 border border-slate-200/50">Racine (/)</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-1 text-[10px] font-semibold text-amber-700 border border-amber-200/50 font-mono">{{ $dirName }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('drive.download', ['path' => $file]) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-orange-50 hover:text-orange-600 hover:border-orange-200 text-xs font-semibold text-slate-700 transition-all shadow-sm" title="Télécharger le fichier">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                Télécharger
                                            </a>
                                            
                                            <form action="{{ route('drive.delete') }}" method="POST" onsubmit="return confirm('Confirmer la suppression définitive de ce fichier sur S3 ?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="path" value="{{ $file }}">
                                                <button type="submit" class="h-8 w-8 rounded-lg border border-slate-200 bg-white text-slate-400 hover:text-red-500 hover:bg-red-50 hover:border-red-200 flex items-center justify-center transition-all shadow-sm" title="Supprimer">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
                    <div class="h-14 w-14 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center mb-4">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800">Aucun fichier trouvé</h3>
                    <p class="mt-1 text-xs text-slate-500 max-w-xs">Votre bucket S3 est vide ou aucun fichier n'a été indexé.</p>
                </div>
            @endif
        </main>
    </div>

</body>
</html>