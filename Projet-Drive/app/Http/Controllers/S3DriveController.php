<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class S3DriveController extends Controller
{
    // Accueil - Liste des fichiers pour test.blade.php
    public function index()
    {
        try {
            $files = Storage::disk('s3')->allFiles();
            $error = null;
        } catch (\Exception $e) {
            $files = [];
            $error = $e->getMessage();
        }

        return view('test', compact('files', 'error'));
    }

    // Envoi d'un fichier vers S3 directement à la racine
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:100480', // 100 Mo max
        ]);

        try {
            if (!$request->hasFile('file')) {
                return back()->with('error', 'Aucun fichier reçu.');
            }

            $file = $request->file('file');
            if (!$file->isValid()) {
                return back()->with('error', 'Le fichier téléchargé n\'est pas valide.');
            }

            $fileName = $file->getClientOriginalName();
            
            // Upload flat directly to S3 root using stream (highly robust)
            $stream = fopen($file->getRealPath(), 'r');
            Storage::disk('s3')->put($fileName, $stream);
            if (is_resource($stream)) {
                fclose($stream);
            }

            return back()->with('success', 'Fichier "' . $fileName . '" uploadé avec succès sur S3 !');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur d\'upload : ' . $e->getMessage());
        }
    }

    // Suppression d'un fichier sur S3
    public function delete(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        try {
            Storage::disk('s3')->delete($request->path);
            return back()->with('success', 'Fichier supprimé avec succès de S3 !');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur de suppression : ' . $e->getMessage());
        }
    }

    // Liste plate de tous les fichiers réels stockés sur S3
    public function list(Request $request)
    {
        try {
            // Retrieve all objects flatly from S3
            $items = Storage::disk('s3')->allFiles();
            $formattedItems = [];

            foreach ($items as $itemPath) {
                // Skip placeholder folder markers if any
                $fileName = basename($itemPath);
                if ($fileName === '.keep') {
                    continue;
                }

                $ext = pathinfo($itemPath, PATHINFO_EXTENSION);
                
                // Get size and modification time
                try {
                    $sizeBytes = Storage::disk('s3')->size($itemPath);
                    $lastModified = Storage::disk('s3')->lastModified($itemPath);
                } catch (\Exception $e) {
                    $sizeBytes = 0;
                    $lastModified = time();
                }
                
                $size = $this->formatBytes($sizeBytes);
                $updated = date('Y-m-d H:i', $lastModified);
                
                // Generate a real pre-signed download URL valid for 15 minutes
                try {
                    $signedUrl = Storage::disk('s3')->temporaryUrl($itemPath, now()->addMinutes(15));
                } catch (\Exception $e) {
                    $signedUrl = route('drive.download', ['path' => $itemPath]);
                }

                $formattedItems[] = [
                    'name' => $fileName,
                    'type' => 'file',
                    'ext' => $ext ?: 'txt',
                    'path' => $itemPath,
                    'size' => $size,
                    'updated' => $updated,
                    'signed_url' => $signedUrl
                ];
            }

            $itemsJson = json_encode($formattedItems);
            $error = null;
        } catch (\Exception $e) {
            $itemsJson = json_encode([]);
            $error = $e->getMessage();
        }

        return view('drive', compact('itemsJson', 'error'));
    }

    // Téléchargement sécurisé d'un fichier depuis S3 (fallback)
    public function download(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        return Storage::disk('s3')->download($request->path);
    }

    // Convertisseur de taille en format lisible
    private function formatBytes($bytes, $precision = 2)
    {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

}
