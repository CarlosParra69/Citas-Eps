<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\AvatarController;
use App\Models\User;

// Configurar entorno básico de Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PRUEBA DEL ENDPOINT DE AVATAR ===\n\n";

// Crear instancia del controlador
$controller = new AvatarController();

// Crear un usuario de prueba con email único
$uniqueId = time();
$user = User::factory()->create([
    'name' => 'Usuario Prueba',
    'email' => "test{$uniqueId}@example.com",
    'password' => bcrypt('password'),
    'role_id' => 1
]);

echo "Usuario de prueba creado:\n";
echo "- ID: {$user->id}\n";
echo "- Nombre: {$user->name}\n";
echo "- Email: {$user->email}\n\n";

// Simular autenticación
Auth::login($user);
echo "Usuario autenticado correctamente.\n\n";

// Crear un archivo de imagen de prueba
$testImagePath = __DIR__ . '/test_image.jpg';
if (!file_exists($testImagePath)) {
    // Crear una imagen de prueba usando base64 (1x1 pixel rojo)
    $imageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
    file_put_contents($testImagePath, $imageData);
    echo "Imagen de prueba creada: {$testImagePath}\n\n";
}

// Crear request con la imagen
$request = new Request();
$request->files->set('avatar', new \Symfony\Component\HttpFoundation\File\UploadedFile(
    $testImagePath,
    'test_image.jpg',
    'image/jpeg',
    null,
    true
));

echo "=== PROBANDO MÉTODO UPLOAD ===\n";
try {
    $response = $controller->upload($request);

    echo "Respuesta del método upload:\n";
    echo json_encode($response, JSON_PRETTY_PRINT) . "\n";

    if ($response->getStatusCode() === 200) {
        echo "\n✅ Upload exitoso!\n";
    } else {
        echo "\n❌ Upload falló con código: " . $response->getStatusCode() . "\n";
    }
} catch (\Exception $e) {
    echo "❌ Error en upload: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== PROBANDO MÉTODO GET ===\n";
try {
    $response = $controller->get($request);

    echo "Respuesta del método get:\n";
    echo json_encode($response, JSON_PRETTY_PRINT) . "\n";

    if ($response->getStatusCode() === 200) {
        echo "\n✅ Get exitoso!\n";
    } else {
        echo "\n❌ Get falló con código: " . $response->getStatusCode() . "\n";
    }
} catch (\Exception $e) {
    echo "❌ Error en get: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== PROBANDO MÉTODO DELETE ===\n";
try {
    $response = $controller->delete($request);

    echo "Respuesta del método delete:\n";
    echo json_encode($response, JSON_PRETTY_PRINT) . "\n";

    if ($response->getStatusCode() === 200) {
        echo "\n✅ Delete exitoso!\n";
    } else {
        echo "\n❌ Delete falló con código: " . $response->getStatusCode() . "\n";
    }
} catch (\Exception $e) {
    echo "❌ Error en delete: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

// Limpiar archivos de prueba
if (file_exists($testImagePath)) {
    unlink($testImagePath);
    echo "\nArchivo de prueba eliminado.\n";
}

// Eliminar usuario de prueba
$user->delete();
echo "Usuario de prueba eliminado.\n";

echo "\n=== FIN DE LA PRUEBA ===\n";