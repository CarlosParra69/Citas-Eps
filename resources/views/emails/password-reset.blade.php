<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de Contraseña - MediApp</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #1C1C1E;
            max-width: 600px;
            margin: 0 auto;
            background-color: #F9F9F9;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border: 1px solid #C6C6C8;
        }
        .header {
            text-align: center;
            margin-bottom: 32px;
            border-bottom: 3px solid #FF6B35;
            padding-bottom: 24px;
        }
        .logo {
            color: #FF6B35;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 12px;
            letter-spacing: 1px;
        }
        .logo img {
            width: 80px;
            height: 80px;
            margin-bottom: 16px;
        }
        .subtitle {
            color: #8E8E93;
            font-size: 18px;
            font-weight: 500;
        }
        .content {
            margin: 20px 0;
            text-align: center;
        }
        .button {
            display: inline-block;
            background-color: #FF6B35;
            color: white !important;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 24px 0;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(255, 107, 53, 0.2);
        }
        .button:hover {
            background-color: #FF8C42 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(255, 107, 53, 0.3);
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .warning {
            background-color: #FFF5F5;
            border: 1px solid #FEB2B2;
            border-left: 4px solid #E53E3E;
            border-radius: 8px;
            padding: 16px;
            margin: 24px 0;
            color: #C53030;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img class="header">
            <div class="subtitle">Gestión de Citas Médicas</div>
        </div>
        <div class="content">
            <h2 style="color: #FF6B35; margin-bottom: 24px;">Recuperación de Contraseña</h2>
            <p>Hola <strong style="color: #FF6B35;">{{ $user->nombre }} {{ $user->apellido }}</strong>,</p>
            <p>Has solicitado recuperar tu contraseña en MediApp. Para continuar con el proceso, haz clic en el siguiente botón:</p>

            <a href="{{ $resetUrl }}" class="button">Recuperar Contraseña</a>

            <div class="warning">
                <strong>Importante:</strong> Este enlace expirará en 1 hora por razones de seguridad.
                Si no solicitaste este cambio de contraseña, puedes ignorar este correo.
            </div>

            <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
        </div>

        <div class="footer">
            <p>Este es un correo automático, por favor no respondas directamente a este mensaje.</p>
            <p>&copy; {{ date('Y') }} MediApp - Gestión de Citas Médicas</p>
        </div>
    </div>
</body>
</html>