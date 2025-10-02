<?php
// Configuración de base de datos y utilidades comunes

$DB_HOST = 'sql306.infinityfree.com';
$DB_USER = 'if0_39093659';
$DB_PASS = '923486317';
$DB_NAME = 'if0_39093659_consulta_cliente';

function db_connect() {
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    if ($conn->connect_error) {
        http_response_code(500);
        die('Error de conexión a la base de datos.');
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}

// Helper para bind_param con referencias dinámicas
function mysqli_bind_params(mysqli_stmt $stmt, string $types, array &$params) {
    if ($types === '' || empty($params)) {
        return; // Nada que bindear
    }
    $refs = [];
    $refs[] = &$types;
    foreach ($params as $k => &$v) {
        $refs[] = &$v; // referenciar cada parámetro
    }
    call_user_func_array([$stmt, 'bind_param'], $refs);
}

?>