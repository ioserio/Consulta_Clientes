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

// Obtener parámetros del formulario
$calle = isset($_GET['calle']) ? trim($_GET['calle']) : '';
$codigoZona = isset($_GET['codigoZonaVenta']) ? trim($_GET['codigoZonaVenta']) : '';
$mz = isset($_GET['mz']) ? trim($_GET['mz']) : '';
$lt = isset($_GET['lt']) ? trim($_GET['lt']) : '';
$nro = isset($_GET['nro']) ? trim($_GET['nro']) : '';
$numeral = isset($_GET['numeral']) ? trim($_GET['numeral']) : '';
$sector = isset($_GET['sector']) ? trim($_GET['sector']) : '';
$grupo = isset($_GET['grupo']) ? trim($_GET['grupo']) : '';
$puesto = isset($_GET['puesto']) ? trim($_GET['puesto']) : '';

if ($calle !== '') {
    // Construir WHERE dinámico con partes de dirección
    $wheres = [];
    $params = [];
    $types = '';

    // Dirección por partes: cada parte que llegue se usa con LIKE
    $wheres[] = 'Direccion LIKE ?';
    $params[] = "%" . $calle . "%";
    $types .= 's';
    if ($mz !== '' && $lt !== '' && $sector !== '' && $grupo !== '') {
        $wheres[] = '(
            Direccion LIKE ? AND LOWER(Direccion) LIKE ?
            AND Direccion LIKE ? AND LOWER(Direccion) LIKE ?
            AND Direccion LIKE ? AND (LOWER(Direccion) LIKE ? OR LOWER(Direccion) LIKE ?)
            AND Direccion LIKE ? AND (LOWER(Direccion) LIKE ? OR LOWER(Direccion) LIKE ? OR LOWER(Direccion) LIKE ? OR LOWER(Direccion) LIKE ?)
        )';
        $params[] = "%" . $mz . "%";
        $params[] = "%mz%";
        $params[] = "%" . $lt . "%";
        $params[] = "%lt%";
        $params[] = "%" . $sector . "%";
        $params[] = "%sec%";
        $params[] = "%sector%";
        $params[] = "%" . $grupo . "%";
        $params[] = "%grupo%";
        $params[] = "%grup%";
        $params[] = "%grp%";
        $params[] = "%grpo%";
        $types .= 'ssssssssssss';
    } else {
        if ($mz !== '') {
            $wheres[] = '(Direccion LIKE ? AND LOWER(Direccion) LIKE ?)';
            $params[] = "%" . $mz . "%";
            $params[] = "%mz%";
            $types .= 'ss';
        }
        if ($lt !== '') {
            $wheres[] = '(Direccion LIKE ? AND LOWER(Direccion) LIKE ?)';
            $params[] = "%" . $lt . "%";
            $params[] = "%lt%";
            $types .= 'ss';
        }
        // Preferir 'numeral' pero mantener compatibilidad con 'nro'
        $numeroRef = $numeral !== '' ? $numeral : $nro;
        if ($numeroRef !== '') { $wheres[] = 'Direccion LIKE ?'; $params[] = "%" . $numeroRef . "%"; $types .= 's'; }
        if ($sector !== '') {
            $wheres[] = '(Direccion LIKE ? AND (LOWER(Direccion) LIKE ? OR LOWER(Direccion) LIKE ?))';
            $params[] = "%" . $sector . "%";
            $params[] = "%sec%";
            $params[] = "%sector%";
            $types .= 'sss';
        }
        if ($grupo !== '') {
            $wheres[] = '(Direccion LIKE ? AND (LOWER(Direccion) LIKE ? OR LOWER(Direccion) LIKE ? OR LOWER(Direccion) LIKE ? OR LOWER(Direccion) LIKE ?))';
            $params[] = "%" . $grupo . "%";
            $params[] = "%grupo%";
            $params[] = "%grup%";
            $params[] = "%grp%";
            $params[] = "%grpo%";
            $types .= 'sssss';
        }
    }

    // Si el campo puesto está lleno, aplicar filtro especial
    if ($puesto !== '') {
        $wheres[] = 'Direccion LIKE ?';
        $params[] = "%" . $puesto . "%";
        $types .= 's';
        // Solo mostrar direcciones que contengan mcdo, mdo o mercado (insensible a mayúsculas)
        $wheres[] = "(LOWER(Direccion) LIKE ? OR LOWER(Direccion) LIKE ? OR LOWER(Direccion) LIKE ?)";
        $params[] = "%mcdo%";
        $params[] = "%mdo%";
        $params[] = "%mercado%";
        $types .= 'sss';
    }

    if ($codigoZona !== '') { $wheres[] = 'CodigoZonaVenta = ?'; $params[] = $codigoZona; $types .= 's'; }

    $whereSQL = count($wheres) ? ('WHERE ' . implode(' AND ', $wheres)) : '';
    $sql = "SELECT Codigo, Nombre, Direccion FROM cartera_clientes $whereSQL LIMIT 50";
    $stmt = $conn->prepare($sql);
    // bind_param requiere referencias; usar spread con referencias dinámicamente
    $stmt->bind_param($types, ...$params);
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
} else if ((isset($_GET['calle']) && trim($_GET['calle']) !== '') || (isset($_GET['sector']) && trim($_GET['sector']) !== '') || (isset($_GET['grupo']) && trim($_GET['grupo']) !== '')) {
    // Permitir búsqueda solo por complementos
    $wheres = [];
    $params = [];
    $types = '';
    $mz = isset($_GET['mz']) ? trim($_GET['mz']) : '';
    $lt = isset($_GET['lt']) ? trim($_GET['lt']) : '';
    $sector = isset($_GET['sector']) ? trim($_GET['sector']) : '';
    $grupo = isset($_GET['grupo']) ? trim($_GET['grupo']) : '';
    if ($mz !== '' && $lt !== '' && $sector !== '' && $grupo !== '') {
        $wheres[] = '(
            Direccion LIKE ? AND LOWER(Direccion) LIKE ?
            AND Direccion LIKE ? AND LOWER(Direccion) LIKE ?
            AND Direccion LIKE ? AND (LOWER(Direccion) LIKE ? OR LOWER(Direccion) LIKE ?)
            AND Direccion LIKE ? AND (LOWER(Direccion) LIKE ? OR LOWER(Direccion) LIKE ? OR LOWER(Direccion) LIKE ? OR LOWER(Direccion) LIKE ?)
        )';
        $params[] = "%" . $mz . "%";
        $params[] = "%mz%";
        $params[] = "%" . $lt . "%";
        $params[] = "%lt%";
        $params[] = "%" . $sector . "%";
        $params[] = "%sec%";
        $params[] = "%sector%";
        $params[] = "%" . $grupo . "%";
        $params[] = "%grupo%";
        $params[] = "%grup%";
        $params[] = "%grp%";
        $params[] = "%grpo%";
        $types .= 'ssssssssssss';
    } else {
        if ($mz !== '') {
            $wheres[] = '(Direccion LIKE ? AND LOWER(Direccion) LIKE ?)';
            $params[] = "%" . $mz . "%";
            $params[] = "%mz%";
            $types .= 'ss';
        }
        if ($lt !== '') {
            $wheres[] = '(Direccion LIKE ? AND LOWER(Direccion) LIKE ?)';
            $params[] = "%" . $lt . "%";
            $params[] = "%lt%";
            $types .= 'ss';
        }
        if ($sector !== '') {
            $wheres[] = '(Direccion LIKE ? AND (LOWER(Direccion) LIKE ? OR LOWER(Direccion) LIKE ?))';
            $params[] = "%" . $sector . "%";
            $params[] = "%sec%";
            $params[] = "%sector%";
            $types .= 'sss';
        }
        if ($grupo !== '') {
            $wheres[] = '(Direccion LIKE ? AND (LOWER(Direccion) LIKE ? OR LOWER(Direccion) LIKE ? OR LOWER(Direccion) LIKE ? OR LOWER(Direccion) LIKE ?))';
            $params[] = "%" . $grupo . "%";
            $params[] = "%grupo%";
            $params[] = "%grup%";
            $params[] = "%grp%";
            $params[] = "%grpo%";
            $types .= 'sssss';
        }
    }
    $whereSQL = count($wheres) ? ('WHERE ' . implode(' AND ', $wheres)) : '';
    $sql = "SELECT Codigo, Nombre, Direccion FROM cartera_clientes $whereSQL LIMIT 50";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
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
    echo "<p>Ingrese Calle/Jr/pasaje/Mcdo, Sector o Grupo para buscar.</p>";
}
$conn->close();
?>
