<?php
// Configuración de cabeceras para responder en formato JSON a MediaMTX
header("Content-Type: application/json");

// Capturar el cuerpo de la petición (MediaMTX envía los datos en formato JSON)
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Verificar que sea una acción de publicación (intento de transmitir)
if (isset($data['action']) && $data['action'] === 'publish') {
    
    $path     = $data['path'] ?? '';     // Nombre del canal/PC que intentan usar (ej. computadora01)
    $user     = $data['user'] ?? '';     // Usuario pasado en el RTSP
    $password = $data['password'] ?? ''; // Contraseña pasada en el RTSP
    $clientIp = $data['ip'] ?? '';       // IP de la computadora remota

    // --- Validación de credenciales fijas de seguridad ---
    $usuario_valido = "admin_ats";
    $clave_valida   = "SeguridadFuerte2026";

    if ($user === $usuario_valido && $password === $clave_valida) {
        // PERMITIDO: El servidor responde éxito
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => "Transmisión autorizada"
        ]);
        exit;
    } else {
        // DENEGADO: Credenciales inválidas o intento de hackeo
        http_response_code(401);
        echo json_encode([
            "status" => "fail",
            "message" => "Credenciales inválidas"
        ]);
        exit;
    }
}

// Bloqueo por defecto si acceden directamente por navegador
http_response_code(400);
echo json_encode([
    "status" => "error",
    "message" => "Petición no válida"
]);
