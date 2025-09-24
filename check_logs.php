<?php

$logFile = __DIR__ . '/storage/logs/laravel.log';

if (!file_exists($logFile)) {
    echo "Archivo de logs no encontrado: $logFile\n";
    exit(1);
}

$lines = file($logFile);
$lines = array_reverse($lines); // Invertir para obtener las últimas líneas primero

$foundErrors = [];
$avatarErrors = [];
$recentErrors = [];

foreach ($lines as $line) {
    // Buscar errores relacionados con avatar
    if (stripos($line, 'avatar') !== false || stripos($line, 'Avatar') !== false) {
        $avatarErrors[] = $line;
    }

    // Buscar errores 500
    if (stripos($line, '500') !== false) {
        $recentErrors[] = $line;
    }

    // Buscar errores generales
    if (stripos($line, 'ERROR') !== false || stripos($line, 'Exception') !== false) {
        $foundErrors[] = $line;
    }
}

echo "=== ERRORES DE AVATAR ===\n";
if (empty($avatarErrors)) {
    echo "No se encontraron errores específicos de avatar.\n";
} else {
    foreach ($avatarErrors as $error) {
        echo trim($error) . "\n";
    }
}

echo "\n=== ERRORES 500 RECIENTES ===\n";
if (empty($recentErrors)) {
    echo "No se encontraron errores 500 recientes.\n";
} else {
    foreach ($recentErrors as $error) {
        echo trim($error) . "\n";
    }
}

echo "\n=== ÚLTIMOS 20 ERRORES GENERALES ===\n";
$generalErrors = array_slice($foundErrors, 0, 20);
if (empty($generalErrors)) {
    echo "No se encontraron errores generales.\n";
} else {
    foreach ($generalErrors as $error) {
        echo trim($error) . "\n";
    }
}