@extends('layouts.app')

@section('title', 'AWS S3 - Nuage Drive')

@section('header-search')
    <div class="flex max-w-md flex-1 items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-4 py-1.5 transition-all focus-within:border-orange-500 focus-within:ring-2 focus-within:ring-orange-500/10 focus-within:bg-white w-full">
        <svg class="h-5 w-5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input type="text" id="searchInput" oninput="filterItems()" placeholder="Rechercher des fichiers..." class="w-full bg-transparent text-sm placeholder:text-slate-400 focus:outline-none text-slate-700">
    </div>
@endsection

@section('sidebar-actions')
    <button onclick="openModal('uploadModal')" class="flex w-full items-center justify-center gap-2 rounded-xl bg-orange-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-600/15 hover:bg-orange-500 active:scale-[0.98] transition-all">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
        </svg>
        Uploader un fichier
    </button>
@endsection

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200/60 pb-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Tous les fichiers</h2>
            <p class="text-xs text-slate-500">Liste des objets stockés à la racine de votre bucket S3.</p>
        </div>

        <div class="flex items-center gap-2">
            <div class="md:hidden flex items-center gap-1.5">
                <button onclick="openModal('uploadModal')" class="p-2 rounded-lg bg-orange-600 text-white shadow-md shadow-orange-500/10 hover:bg-orange-500">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                </button>
            </div>

            <div class="h-6 w-px bg-slate-200 max-md:hidden"></div>

            <div class="flex items-center gap-0.5 rounded-lg bg-slate-100 p-0.5 border border-slate-200">
                <button onclick="setViewMode('grid')" id="btn-grid" class="p-1.5 rounded-md transition-all text-slate-400 hover:text-slate-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                </button>
                <button onclick="setViewMode('list')" id="btn-list" class="p-1.5 rounded-md transition-all text-slate-400 hover:text-slate-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div id="empty-state" class="hidden flex-1 flex-col items-center justify-center py-20 text-center">
        <div class="h-16 w-16 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center mb-4">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
        </div>
        <h3 class="text-sm font-semibold text-slate-800">Votre bucket S3 est vide</h3>
        <p class="mt-1 text-xs text-slate-500 max-w-xs">Uploadez des fichiers directement pour commencer à les stocker sur S3.</p>
    </div>

    <div id="files-section" class="mt-6">
        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Fichiers</h2>
        
        <div id="files-grid" class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
        </div>

        <div id="files-table-container" class="hidden overflow-hidden border border-slate-200/80 bg-white rounded-xl shadow-sm">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase">
                    <tr>
                        <th scope="col" class="px-6 py-3.5">Nom</th>
                        <th scope="col" class="px-6 py-3.5">Dernière modification</th>
                        <th scope="col" class="px-6 py-3.5">Taille</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="files-table-body" class="divide-y divide-slate-100 bg-white text-slate-700">
                </tbody>
            </table>
        </div>
    </div>

    <div id="toast" class="fixed bottom-5 right-5 z-50 flex transform translate-y-10 scale-95 opacity-0 items-center gap-3 rounded-2xl bg-slate-900 px-4 py-3 text-sm text-white shadow-xl transition-all duration-300 pointer-events-none">
        <span id="toast-message">Action effectuée</span>
    </div>

    <div id="uploadModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-200">
        <form action="{{ route('drive.upload') }}" method="POST" enctype="multipart/form-data" class="w-full max-w-md scale-95 rounded-2xl bg-white p-6 shadow-2xl transition-transform duration-200 border border-slate-100">
            @csrf
            <h3 class="text-base font-bold text-slate-800">Uploader un fichier sur S3</h3>
            <p class="mt-1 text-xs text-slate-500">Sélectionnez un fichier pour l'envoyer directement dans votre bucket S3.</p>
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Fichier</label>
                    <div class="relative flex flex-col items-center justify-center border-2 border-dashed border-slate-200 rounded-xl p-6 bg-slate-50 hover:bg-slate-100/50 transition-colors cursor-pointer">
                        <input type="file" name="file" id="fileInput" required class="absolute inset-0 opacity-0 cursor-pointer" onchange="updateFileInfo()">
                        <svg class="h-8 w-8 text-slate-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <span id="upload-file-label" class="text-xs text-slate-600 font-medium">Sélectionner un fichier</span>
                        <span id="upload-file-details" class="text-[10px] text-slate-400 mt-1">Fichier max : 100 Mo</span>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2.5">
                <button type="button" onclick="closeModal('uploadModal')" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-500 hover:bg-slate-50">Annuler</button>
                <button type="submit" class="rounded-xl bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-500">Uploader</button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        let items = {!! $itemsJson !!};
        let viewMode = localStorage.getItem('s3_view_mode') || 'grid';

        function setViewMode(mode) {
            viewMode = mode;
            localStorage.setItem('s3_view_mode', mode);
            render();
        }

        function openModal(id) {
            const modal = document.getElementById(id);
            modal.classList.remove('pointer-events-none', 'opacity-0');
            modal.querySelector('form').classList.remove('scale-95');
            modal.querySelector('form').classList.add('scale-100');
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            modal.classList.add('pointer-events-none', 'opacity-0');
            modal.querySelector('form').classList.add('scale-95');
            modal.querySelector('form').classList.remove('scale-100');
            if (id === 'uploadModal') {
                document.getElementById('fileInput').value = '';
                document.getElementById('upload-file-label').innerText = 'Sélectionner un fichier';
                document.getElementById('upload-file-details').innerText = 'Fichier max : 100 Mo';
            }
        }

        let selectedFile = null;
        function updateFileInfo() {
            const input = document.getElementById('fileInput');
            if (input.files && input.files[0]) {
                const file = input.files[0];
                selectedFile = file;
                document.getElementById('upload-file-label').innerText = file.name;
                const formattedSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                document.getElementById('upload-file-details').innerText = formattedSize;
            }
        }

        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toast-message').innerText = msg;
            toast.classList.remove('opacity-0', 'translate-y-10', 'scale-95');
            toast.classList.add('opacity-100', 'translate-y-0', 'scale-100');
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-10', 'scale-95');
                toast.classList.remove('opacity-100', 'translate-y-0', 'scale-100');
            }, 3000);
        }

        function deleteAction(path, isDir) {
            if (confirm(`Voulez-vous supprimer définitivement ce fichier de S3 ?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('drive.delete') }}";
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = "{{ csrf_token() }}";
                form.appendChild(csrfInput);
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                const pathInput = document.createElement('input');
                pathInput.type = 'hidden';
                pathInput.name = 'path';
                pathInput.value = path;
                form.appendChild(pathInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }

        function getFileIcon(ext) {
            const cleanExt = ext ? ext.toLowerCase() : '';
            
            if (['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'].includes(cleanExt)) {
                return `<div class="h-10 w-10 shrink-0 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>`;
            } else if (cleanExt === 'pdf') {
                return `<div class="h-10 w-10 shrink-0 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>`;
            } else if (['zip', 'rar', 'tar', 'gz', '7z'].includes(cleanExt)) {
                return `<div class="h-10 w-10 shrink-0 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                </div>`;
            } else if (['xls', 'xlsx', 'csv'].includes(cleanExt)) {
                return `<div class="h-10 w-10 shrink-0 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>`;
            } else if (['doc', 'docx', 'txt', 'md'].includes(cleanExt)) {
                return `<div class="h-10 w-10 shrink-0 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>`;
            } else {
                return `<div class="h-10 w-10 shrink-0 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>`;
            }
        }

        let currentSearchQuery = '';
        function filterItems() {
            const query = document.getElementById('searchInput').value.toLowerCase().trim();
            currentSearchQuery = query;
            render();
        }

        function render() {
            let visibleItems = [...items];

            if (currentSearchQuery) {
                visibleItems = visibleItems.filter(item => item.name.toLowerCase().includes(currentSearchQuery));
            }

            const files = visibleItems.filter(i => i.type === 'file');

            const filesGrid = document.getElementById('files-grid');
            const filesTableBody = document.getElementById('files-table-body');
            const filesTableContainer = document.getElementById('files-table-container');
            const emptyState = document.getElementById('empty-state');
            const filesSection = document.getElementById('files-section');

            const btnGrid = document.getElementById('btn-grid');
            const btnList = document.getElementById('btn-list');
            if (viewMode === 'grid') {
                btnGrid.className = "p-1.5 rounded-md bg-white text-slate-800 shadow-sm border border-slate-200/50 transition-all";
                btnList.className = "p-1.5 rounded-md text-slate-400 hover:text-slate-600 transition-all";
                
                filesGrid.classList.remove('hidden');
                filesTableContainer.classList.add('hidden');
            } else {
                btnList.className = "p-1.5 rounded-md bg-white text-slate-800 shadow-sm border border-slate-200/50 transition-all";
                btnGrid.className = "p-1.5 rounded-md text-slate-400 hover:text-slate-600 transition-all";
                
                filesGrid.classList.add('hidden');
                filesTableContainer.classList.remove('hidden');
            }

            if (visibleItems.length === 0) {
                emptyState.classList.remove('hidden');
                filesSection.classList.add('hidden');
                return;
            } else {
                emptyState.classList.add('hidden');
            }

            if (files.length === 0) {
                filesSection.classList.add('hidden');
            } else {
                filesSection.classList.remove('hidden');
                
                filesGrid.innerHTML = '';
                filesTableBody.innerHTML = '';

                files.forEach(file => {
                    const simulatedSignedUrl = file.signed_url;

                    const gridCard = document.createElement('div');
                    gridCard.className = "group relative flex flex-col justify-between rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm hover:shadow-md hover:border-orange-500/40 transition-all";
                    gridCard.innerHTML = `
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                ${getFileIcon(file.ext)}
                                <div class="overflow-hidden">
                                    <h3 class="text-sm font-semibold text-slate-700 truncate max-w-[130px]" title="${file.name}">${file.name}</h3>
                                    <p class="text-[10px] text-slate-400">${file.size} • ${file.ext.toUpperCase()}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="${simulatedSignedUrl}" target="_blank" title="Télécharger via URL S3 Signée" class="h-7 w-7 rounded-lg hover:bg-orange-50 text-slate-400 hover:text-orange-600 flex items-center justify-center">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </a>
                                <button onclick="deleteAction('${file.path}', false)" title="Supprimer" class="h-7 w-7 rounded-lg hover:bg-red-50 text-slate-400 hover:text-red-500 flex items-center justify-center">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[9px] text-slate-400">
                            <span>Modifié: ${file.updated}</span>
                            <span class="font-mono bg-slate-50 text-slate-500 rounded px-1 text-[8px] border border-slate-100/50">Private</span>
                        </div>
                    `;
                    filesGrid.appendChild(gridCard);

                    const tableRow = document.createElement('tr');
                    tableRow.className = "hover:bg-slate-50/50 transition-colors group";
                    tableRow.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                ${getFileIcon(file.ext)}
                                <span class="font-medium text-slate-800 truncate max-w-xs" title="${file.name}">${file.name}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-slate-400 text-xs">${file.updated}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-slate-500 text-xs font-mono">${file.size}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="${simulatedSignedUrl}" target="_blank" class="h-8 px-2.5 rounded-lg border border-slate-200 bg-white hover:bg-orange-50 hover:text-orange-600 hover:border-orange-200 inline-flex items-center gap-1.5 text-xs text-slate-600 transition-all shadow-sm">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    URL signée S3
                                </a>
                                <button onclick="deleteAction('${file.path}', false)" class="h-8 w-8 rounded-lg border border-slate-200 bg-white text-slate-400 hover:text-red-500 hover:bg-red-50 hover:border-red-200 flex items-center justify-center transition-all shadow-sm">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    `;
                    filesTableBody.appendChild(tableRow);
                });
            }
        }

        render();

        @if(session('success'))
            showToast("{{ session('success') }}");
        @endif
        @if(session('error'))
            showToast("{{ session('error') }}");
        @endif
        @if($errors->any())
            showToast("{{ $errors->first() }}");
        @endif
    </script>
@endsection
