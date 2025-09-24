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
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

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
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            // Generar nombre único para el archivo
            $fileName = 'avatars/' . time() . '_' . $user->id . '.' . $request->file('avatar')->getClientOriginalExtension();

            // Guardar la imagen
            $path = $request->file('avatar')->storeAs('public', $fileName);

            // Actualizar la ruta en la base de datos
            $user->foto = $fileName;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Avatar subido exitosamente',
                'data' => [
                    'avatar_url' => asset('storage/' . $fileName),
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
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                // Eliminar la imagen del almacenamiento
                Storage::disk('public')->delete($user->foto);

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

            $avatarUrl = $user->foto ? asset('storage/' . $user->foto) : null;

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
}