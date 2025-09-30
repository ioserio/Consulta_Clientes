<?php
// buscar_direccion.php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "consulta_cliente";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener la dirección del formulario
$direccion = isset($_GET['direccion']) ? trim($_GET['direccion']) : '';
$codigoZona = isset($_GET['codigoZonaVenta']) ? trim($_GET['codigoZonaVenta']) : '';

if ($direccion !== '') {
    if ($codigoZona !== '') {
        $sql = "SELECT Codigo, Nombre, Direccion FROM cartera_clientes WHERE Direccion LIKE ? AND CodigoZonaVenta = ? LIMIT 50";
        $stmt = $conn->prepare($sql);
        $like = "%" . $direccion . "%";
        $stmt->bind_param('ss', $like, $codigoZona);
    } else {
        $sql = "SELECT Codigo, Nombre, Direccion FROM cartera_clientes WHERE Direccion LIKE ? LIMIT 50";
        $stmt = $conn->prepare($sql);
        $like = "%" . $direccion . "%";
        $stmt->bind_param('s', $like);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='6'><tr><th>Código</th><th>Nombre</th><th>Dirección</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . htmlspecialchars($row['Codigo']) . "</td><td>" . htmlspecialchars($row['Nombre']) . "</td><td>" . htmlspecialchars($row['Direccion']) . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No se encontraron coincidencias.</p>";
    }
    $stmt->close();
} else {
    echo "<p>Ingrese una dirección para buscar.</p>";
}
$conn->close();
?>
