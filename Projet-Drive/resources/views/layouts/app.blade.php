<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50 font-sans antialiased text-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AWS Control Panel')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
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
    @endif

    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif;
        }
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @yield('head')
</head>
<body class="h-full flex flex-col overflow-hidden bg-slate-50/50">

    <header class="flex h-16 shrink-0 items-center justify-between border-b border-slate-200/80 bg-white/80 px-6 backdrop-blur-md z-30">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-tr from-amber-500 to-orange-600 text-white shadow-md shadow-orange-500/20">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-orange-600">Amazon Web Services</span>
                <h1 class="text-lg font-bold text-slate-800 leading-none">Console AWS</h1>
            </div>
        </div>

        <div class="flex-1 flex justify-center max-w-md mx-8 max-md:hidden">
            @yield('header-search')
        </div>

        <div class="flex items-center gap-3">
            <div class="hidden sm:flex flex-col items-end">
                <div class="flex items-center gap-1.5">
                    <span class="inline-block h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-xs font-medium text-slate-600">AWS Connecté</span>
                </div>
                <span class="text-[10px] text-slate-400 font-mono">{{ config('filesystems.disks.s3.region', 'eu-west-3') }}</span>
            </div>
            <div class="h-9 w-9 rounded-full bg-slate-100 flex items-center justify-center text-sm font-semibold text-slate-700 border border-slate-200">
                LG
            </div>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
        
        <aside class="w-64 border-r border-slate-200/80 bg-white/50 px-4 py-6 flex flex-col justify-between shrink-0 max-md:hidden">
            <div class="space-y-6">
                <div class="space-y-2.5">
                    @yield('sidebar-actions')
                </div>

                <nav class="space-y-1.5">
                    <a href="{{ route('drive.list') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all {{ Route::is('drive.*') ? 'bg-orange-50 text-orange-600 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 shrink-0 {{ Route::is('drive.*') ? 'text-orange-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Mon S3 Drive
                    </a>
                    <a href="{{ route('ec2.list') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all {{ Route::is('ec2.*') ? 'bg-orange-50 text-orange-600 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 shrink-0 {{ Route::is('ec2.*') ? 'text-orange-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                        </svg>
                        Instances EC2
                    </a>
                    <a href="{{ route('vpc.list') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all {{ Route::is('vpc.*') ? 'bg-orange-50 text-orange-600 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 shrink-0 {{ Route::is('vpc.*') ? 'text-orange-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                        Réseaux VPC AWS
                    </a>
                </nav>
            </div>
        </aside>

        <main class="flex-1 flex flex-col overflow-y-auto px-6 py-6 lg:px-8">
            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>
