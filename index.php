<?php
// Configuración de cabeceras para responder en formato JSON a MediaMTX
header("Content-Type: application/json");

// Capturar el cuerpo de la petición (MediaMTX envía los datos en formato JSON)
$input = file_get_contents("php://input");
$data = json_json_decode($input, true);

/* MediaMTX envía un JSON con esta estructura cuando alguien intenta publicar:
  {
    "action": "publish",
    "path": "nombre_del_flujo",  (ej. computadora01)
    "user": "usuario_enviado",
    "password": "password_enviado",
    "ip": "ip_de_la_pc"
  }
*/

// Verificar que sea una acción de publicación (intento de transmitir)
if (isset($data['action']) && $data['action'] === 'publish') {
    
    $path     = $data['path'] ?? '';     // Nombre del canal/PC que intentan usar
    $user     = $data['user'] ?? '';     // Usuario pasado en el RTSP
    $password = $data['password'] ?? ''; // Contraseña pasada en el RTSP
    $clientIp = $data['ip'] ?? '';       // IP de la computadora remota

    // --- AQUÍ CONECTAS CON TU BASE DE DATOS EN EL FUTURO ---
    // Por ahora, hacemos una validación fuerte directamente en código:
    
    $usuario_valido = "admin_ats";
    $clave_valida   = "SeguridadFuerte2026";

    if ($user === $usuario_valido && $password === $clave_valida) {
        // PERMITIDO: Retornamos un estado de éxito (HTTP 200)
        // Puedes guardar un registro en tu bitácora si lo deseas
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => "Transmisión autorizada para el flujo: " . $path
        ]);
        exit;
    } else {
        // DENEGADO: Credenciales incorrectas
        http_response_code(401); // No autorizado
        echo json_encode([
            "status" => "fail",
            "message" => "Credenciales inválidas para transmitir en " . $path
        ]);
        exit;
    }
}

// Bloqueo por defecto si acceden de forma incorrecta
http_response_code(400);
echo json_encode([
    "status" => "error",
    "message" => "Petición no válida para el sistema de autenticación."
]);
