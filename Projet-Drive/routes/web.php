<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\S3DriveController;

Route::get('/', [S3DriveController::class, 'list'])->name('drive.list');

// Envoi d'un fichier vers S3
Route::post('/upload', [S3DriveController::class, 'upload'])->name('drive.upload');

// Suppression d'un fichier ou d'un dossier virtuel sur S3
Route::delete('/delete', [S3DriveController::class, 'delete'])->name('drive.delete');

// Liste des fichiers et dossiers contenus dans un chemin S3
Route::get('/list', [S3DriveController::class, 'list'])->name('drive.list');

// Téléchargement sécurisé d'un fichier depuis S3
Route::get('/download', [S3DriveController::class, 'download'])->name('drive.download');

// Page de test simpliste pour lister les fichiers du S3
Route::get('/test', [S3DriveController::class, 'index'])->name('drive.test');

