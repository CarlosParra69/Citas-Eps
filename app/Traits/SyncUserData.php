<?php

namespace App\Traits;

use App\Models\User;

trait SyncUserData
{
    /**
     * Sincronizar datos entre médicos/pacientes y la tabla users
     * 
     * @param \Illuminate\Http\Request $request
     * @param User|null $user
     * @return void
     */
    protected function syncUserData($request, $user = null)
    {
        if (!$user) {
            return;
        }

        $userData = [];
        
        // Sincronizar campos comunes
        if ($request->has('nombre')) {
            $userData['nombre'] = $request->nombre;
            $userData['name'] = $request->nombre . ' ' . ($request->apellido ?? $user->apellido ?? '');
        }
        
        if ($request->has('apellido')) {
            $userData['apellido'] = $request->apellido;
            $userData['name'] = ($request->nombre ?? $user->nombre ?? '') . ' ' . $request->apellido;
        }
        
        if ($request->has('cedula')) {
            $userData['cedula'] = $request->cedula;
        }
        
        if ($request->has('email')) {
            $userData['email'] = $request->email;
        }
        
        if ($request->has('telefono')) {
            $userData['telefono'] = $request->telefono;
        }
        
        if ($request->has('activo')) {
            $userData['activo'] = $request->activo;
        }
        
        // Actualizar el usuario solo si hay datos para sincronizar
        if (!empty($userData)) {
            $user->update($userData);
        }
    }
}