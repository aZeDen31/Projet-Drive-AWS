<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\S3DriveController;
use App\Http\Controllers\EC2Controller;

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

// Page pour lister les instances EC2
Route::get('/ec2', [EC2Controller::class, 'listInstances'])->name('ec2.list');

// Route pour redémarrer une instance EC2
Route::post('/ec2/restart/{instanceId}', [EC2Controller::class, 'restartInstance'])->name('ec2.restart');

// Route pour supprimer/résilier une instance EC2
Route::delete('/ec2/delete/{instanceId}', [EC2Controller::class, 'deleteInstance'])->name('ec2.delete');

// Route pour arrêter une instance EC2
Route::post('/ec2/stop/{instanceId}', [EC2Controller::class, 'stopInstance'])->name('ec2.stop');

// Route pour démarrer une instance EC2
Route::post('/ec2/start/{instanceId}', [EC2Controller::class, 'startInstance'])->name('ec2.start');

// Route pour créer une instance EC2
Route::post('/ec2/create', [EC2Controller::class, 'createInstance'])->name('ec2.create');

