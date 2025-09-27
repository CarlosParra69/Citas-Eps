<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AvatarController extends Controller
{
    /**
     * Subir avatar de usuario
     */
    public function upload(Request $request)
    {
        try {
            // El usuario ya está autenticado por el middleware JWT
            $currentUser = Auth::user();

            if (!$currentUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            // Si se especifica un user_id en la petición, verificar que sea superadmin
            $targetUserId = $request->input('user_id');
            $user = null;

            if ($targetUserId) {
                // Si es superadmin, puede subir avatar para otros usuarios
                if (!$currentUser->isSuperAdmin()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No tienes permisos para subir avatar para otros usuarios'
                    ], 403);
                }
                $user = \App\Models\User::find($targetUserId);
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Usuario objetivo no encontrado'
                    ], 404);
                }
            } else {
                // Subir avatar para el usuario autenticado
                $user = $currentUser;
            }

            if (!$request->hasFile('avatar')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró el archivo avatar'
                ], 400);
            }

            $file = $request->file('avatar');

            // Validar la imagen
            $validator = Validator::make($request->all(), [
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB máximo
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Imagen inválida',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verificar si hay una imagen anterior y eliminarla
            if ($user->foto && file_exists(public_path('avatars/' . $user->foto))) {
                unlink(public_path('avatars/' . $user->foto));
            }

            // Generar nombre único para el archivo
            $fileName = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();

            // Asegurarse de que el directorio existe
            $avatarDir = storage_path('app/public/avatars');
            if (!file_exists($avatarDir)) {
                mkdir($avatarDir, 0755, true);
            }

            // Guardar en public/avatars para evitar problemas con enlaces simbólicos
            $avatarDir = public_path('avatars');
            if (!file_exists($avatarDir)) {
                mkdir($avatarDir, 0755, true);
            }

            $file->move($avatarDir, $fileName);
            $path = 'avatars/' . $fileName;

            // Actualizar la ruta en la base de datos
            $user->foto = $fileName;
            $saved = $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Avatar subido exitosamente',
                'data' => [
                    'avatar_url' => asset('avatars/' . $fileName),
                    'avatar_path' => $fileName
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir el avatar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar avatar de usuario
     */
    public function delete(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            // Verificar si tiene una foto
            if ($user->foto && file_exists(public_path('avatars/' . $user->foto))) {
                // Eliminar la imagen del directorio público
                unlink(public_path('avatars/' . $user->foto));

                // Actualizar la base de datos
                $user->foto = null;
                $user->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Avatar eliminado exitosamente'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se encontró avatar para eliminar'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el avatar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener avatar del usuario actual
     */
    public function get(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $avatarUrl = $user->foto ? asset('avatars/' . $user->foto) : null;

            return response()->json([
                 'success' => true,
                 'data' => [
                     'avatar_url' => $avatarUrl,
                     'avatar_path' => $user->foto
                 ]
             ], 200);

         } catch (\Exception $e) {
             return response()->json([
                 'success' => false,
                 'message' => 'Error al obtener el avatar',
                 'error' => $e->getMessage()
             ], 500);
         }
     }

    /**
     * Obtener avatar de un usuario específico por ID
     */
    public function getByUserId($userId)
    {
        try {
            $user = \App\Models\User::find($userId);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            $avatarUrl = $user->foto ? asset('avatars/' . $user->foto) : null;

            return response()->json([
                'success' => true,
                'data' => [
                    'avatar_url' => $avatarUrl,
                    'avatar_path' => $user->foto
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el avatar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Servir imagen de avatar directamente (método alternativo)
     */
    public function serveImage($filename)
    {
        try {
            $filePath = public_path('avatars/' . $filename);

            if (!file_exists($filePath)) {
                // Devolver una imagen por defecto o una respuesta vacía
                return response('', 404);
            }

            $file = file_get_contents($filePath);
            $mimeType = mime_content_type($filePath);

            return response($file, 200)->header('Content-Type', $mimeType);

        } catch (\Exception $e) {
            // Devolver una respuesta vacía en lugar de JSON
            return response('', 500);
        }
    }




}