<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class S3DriveController extends Controller
{
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

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:100480',
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

    public function list(Request $request)
    {
        try {
            $items = Storage::disk('s3')->allFiles();
            $formattedItems = [];

            foreach ($items as $itemPath) {
                $fileName = basename($itemPath);
                if ($fileName === '.keep') {
                    continue;
                }

                $ext = pathinfo($itemPath, PATHINFO_EXTENSION);
                
                try {
                    $sizeBytes = Storage::disk('s3')->size($itemPath);
                    $lastModified = Storage::disk('s3')->lastModified($itemPath);
                } catch (\Exception $e) {
                    $sizeBytes = 0;
                    $lastModified = time();
                }
                
                $size = $this->formatBytes($sizeBytes);
                $updated = date('Y-m-d H:i', $lastModified);
                
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

    public function download(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        return Storage::disk('s3')->download($request->path);
    }

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
