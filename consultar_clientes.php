<?php
// consultar_clientes.php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'consulta_cliente';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('<span style="color:red;">Error de conexión a la base de datos.</span>');
}

$codigo = isset($_GET['codigo']) ? $conn->real_escape_string($_GET['codigo']) : '';
$dni = isset($_GET['dni']) ? $conn->real_escape_string($_GET['dni']) : '';
$nombres = isset($_GET['nombres']) ? $conn->real_escape_string($_GET['nombres']) : '';

$where = [];
if ($codigo !== '') {
    $where[] = "Codigo LIKE '%$codigo%'";
}
if ($dni !== '') {
    $where[] = "DocIdentidad LIKE '%$dni%'";
}
if ($nombres !== '') {
    $where[] = "Nombre LIKE '%$nombres%'";
}
$whereSQL = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';

$sql = "SELECT Codigo, DocIdentidad, Nombre, Direccion, TelefonoPublico FROM cartera_clientes $whereSQL LIMIT 500";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo '<div style="overflow-x:auto;">';
    echo '<table style="width:100%;border-collapse:collapse;font-size:16px;box-shadow:0 2px 8px rgba(0,0,0,0.07);background:#fff;">';
    echo '<thead><tr style="background:#007bff;color:#fff;">'
        .'<th style="border:1px solid #e0e0e0;padding:10px 8px;">Código</th>'
        .'<th style="border:1px solid #e0e0e0;padding:10px 8px;">DNI</th>'
        .'<th style="border:1px solid #e0e0e0;padding:10px 8px;">Nombres</th>'
        .'<th style="border:1px solid #e0e0e0;padding:10px 8px;">Dirección</th>'
        .'<th style="border:1px solid #e0e0e0;padding:10px 8px;">Teléfono</th>'
        .'</tr></thead><tbody>';
    $rowIndex = 0;
    while ($row = $result->fetch_assoc()) {
        $rowColor = ($rowIndex % 2 == 0) ? '#f9f9f9' : '#f4f8ff';
        echo '<tr style="background:'.$rowColor.';">'
            .'<td style="border:1px solid #e0e0e0;padding:8px;">'.htmlspecialchars($row['Codigo']).'</td>'
            .'<td style="border:1px solid #e0e0e0;padding:8px;">'.htmlspecialchars($row['DocIdentidad']).'</td>'
            .'<td style="border:1px solid #e0e0e0;padding:8px;">'.htmlspecialchars($row['Nombre']).'</td>'
            .'<td style="border:1px solid #e0e0e0;padding:8px;">'.htmlspecialchars($row['Direccion']).'</td>'
            .'<td style="border:1px solid #e0e0e0;padding:8px;">'.htmlspecialchars($row['TelefonoPublico']).'</td>'
            .'</tr>';
        $rowIndex++;
    }
    echo '</tbody></table></div>';
} else {
    echo '<div style="color:#888;font-size:17px;padding:20px;text-align:center;">No se encontraron resultados.</div>';
}
$conn->close();
?>
