<?php
// buscar_direccion.php
require_once __DIR__ . '/config.php';
$conn = db_connect();
$DEBUG = isset($_GET['debug']) && $_GET['debug'] == '1';

// Utilidad para comprobar si existe una columna en la tabla
function column_exists(mysqli $conn, string $table, string $column): bool {
    $tableEsc = $conn->real_escape_string($table);
    $columnEsc = $conn->real_escape_string($column);
    $sql = "SHOW COLUMNS FROM `$tableEsc` LIKE '$columnEsc'";
    $res = $conn->query($sql);
    return $res && $res->num_rows > 0;
}
// Detectar si existe la columna Activo en el hosting
$HAS_ACTIVO = column_exists($conn, 'cartera_clientes', 'Activo');

// Obtener parámetros del formulario
$calle = isset($_GET['calle']) ? trim($_GET['calle']) : '';
$codigoZona = isset($_GET['codigoZonaVenta']) ? trim($_GET['codigoZonaVenta']) : '';
$mz = isset($_GET['mz']) ? trim($_GET['mz']) : '';
$lt = isset($_GET['lt']) ? trim($_GET['lt']) : '';
// Fallback desde la sección "Sector y grupo"
if ($mz === '' && isset($_GET['mz_sg'])) { $mz = trim($_GET['mz_sg']); }
if ($lt === '' && isset($_GET['lt_sg'])) { $lt = trim($_GET['lt_sg']); }
$nro = isset($_GET['nro']) ? trim($_GET['nro']) : '';
$numeral = isset($_GET['numeral']) ? trim($_GET['numeral']) : '';
$sector = isset($_GET['sector']) ? trim($_GET['sector']) : '';
$grupo = isset($_GET['grupo']) ? trim($_GET['grupo']) : '';
$puesto = isset($_GET['puesto']) ? trim($_GET['puesto']) : '';

// Sinónimos para etiquetas de dirección (insensible a mayúsculas)
$mzSynonyms = ['%mz%', '%manzana%'];
$ltSynonyms = ['%lt%', '%lote%'];
$sectorSynonyms = ['%sector%', '%sec%'];
$grupoSynonyms = ['%grp%', '%grupo%', '%grup%', '%grpo%'];

if ($calle !== '') {
    // Construir WHERE dinámico con partes de dirección
    $wheres = [];
    $params = [];
    $types = '';

    // Dirección por partes: cada parte que llegue se usa con LIKE
    $wheres[] = 'Direccion LIKE ?';
    $params[] = "%" . $calle . "%";
    $types .= 's';
    // Complementos: AND entre todos los provistos, con sinónimos
    if ($mz !== '') {
        $wheres[] = '(Direccion LIKE ? AND (' . implode(' OR ', array_fill(0, count($mzSynonyms), 'LOWER(Direccion) LIKE ?')) . '))';
        $params[] = "%" . $mz . "%";
        foreach ($mzSynonyms as $syn) { $params[] = $syn; }
        $types .= str_repeat('s', 1 + count($mzSynonyms));
    }
    if ($lt !== '') {
        $wheres[] = '(Direccion LIKE ? AND (' . implode(' OR ', array_fill(0, count($ltSynonyms), 'LOWER(Direccion) LIKE ?')) . '))';
        $params[] = "%" . $lt . "%";
        foreach ($ltSynonyms as $syn) { $params[] = $syn; }
        $types .= str_repeat('s', 1 + count($ltSynonyms));
    }
    // Preferir 'numeral' pero mantener compatibilidad con 'nro'
    $numeroRef = $numeral !== '' ? $numeral : $nro;
    if ($numeroRef !== '') { $wheres[] = 'Direccion LIKE ?'; $params[] = "%" . $numeroRef . "%"; $types .= 's'; }
    if ($sector !== '') {
        $wheres[] = '(Direccion LIKE ? AND (' . implode(' OR ', array_fill(0, count($sectorSynonyms), 'LOWER(Direccion) LIKE ?')) . '))';
        $params[] = "%" . $sector . "%";
        foreach ($sectorSynonyms as $syn) { $params[] = $syn; }
        $types .= str_repeat('s', 1 + count($sectorSynonyms));
    }
    if ($grupo !== '') {
        $wheres[] = '(Direccion LIKE ? AND (' . implode(' OR ', array_fill(0, count($grupoSynonyms), 'LOWER(Direccion) LIKE ?')) . '))';
        $params[] = "%" . $grupo . "%";
        foreach ($grupoSynonyms as $syn) { $params[] = $syn; }
        $types .= str_repeat('s', 1 + count($grupoSynonyms));
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
    // Mostrar solo activos si la columna existe
    if ($HAS_ACTIVO) { $wheres[] = 'Activo = 1'; }

    $whereSQL = count($wheres) ? ('WHERE ' . implode(' AND ', $wheres)) : '';
    $selectCols = ($HAS_ACTIVO ? 'Activo, ' : '') . 'Codigo, Nombre, TipoDocIdentidad, DocIdentidad, Direccion, CodigoZonaVenta';
    $sql = "SELECT $selectCols FROM cartera_clientes $whereSQL LIMIT 50";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        if ($DEBUG) { echo '<pre>ERROR PREPARE: ' . htmlspecialchars($conn->error) . "\nSQL: " . htmlspecialchars($sql) . '</pre>'; }
        http_response_code(500); die('Error preparando la consulta.');
    }
    mysqli_bind_params($stmt, $types, $params);
    $stmt->execute();
    if ($DEBUG && $stmt->error) { echo '<pre>ERROR EXECUTE: ' . htmlspecialchars($stmt->error) . '</pre>'; }
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='6'><tr>" . ($HAS_ACTIVO ? "<th>Activo</th>" : "") . "<th>Código</th><th>Nombre</th><th>TipoDocIdentidad</th><th>DocIdentidad</th><th>Dirección</th><th>CodigoZonaVenta</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>" . ($HAS_ACTIVO ? ("<td>" . htmlspecialchars((string)($row['Activo'] ?? '')) . "</td>") : "")
                . "<td>" . htmlspecialchars($row['Codigo']) . "</td><td>" . htmlspecialchars($row['Nombre']) . "</td><td>" . htmlspecialchars($row['TipoDocIdentidad']) . "</td><td>" . htmlspecialchars($row['DocIdentidad']) . "</td><td>" . htmlspecialchars($row['Direccion']) . "</td><td>" . htmlspecialchars($row['CodigoZonaVenta']) . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No se encontraron coincidencias.</p>";
    }
    $stmt->close();
} else if ($mz !== '' || $lt !== '' || $numeral !== '' || $sector !== '' || $grupo !== '' || $puesto !== '' || $codigoZona !== '') {
    // Permitir búsqueda solo por complementos
    $wheres = [];
    $params = [];
    $types = '';
    // Complementos: AND entre todos los provistos, con sinónimos
    if ($mz !== '') {
        $wheres[] = '(Direccion LIKE ? AND (' . implode(' OR ', array_fill(0, count($mzSynonyms), 'LOWER(Direccion) LIKE ?')) . '))';
        $params[] = "%" . $mz . "%";
        foreach ($mzSynonyms as $syn) { $params[] = $syn; }
        $types .= str_repeat('s', 1 + count($mzSynonyms));
    }
    if ($lt !== '') {
        $wheres[] = '(Direccion LIKE ? AND (' . implode(' OR ', array_fill(0, count($ltSynonyms), 'LOWER(Direccion) LIKE ?')) . '))';
        $params[] = "%" . $lt . "%";
        foreach ($ltSynonyms as $syn) { $params[] = $syn; }
        $types .= str_repeat('s', 1 + count($ltSynonyms));
    }
    $numeroRef2 = $numeral !== '' ? $numeral : $nro;
    if ($numeroRef2 !== '') { $wheres[] = 'Direccion LIKE ?'; $params[] = "%" . $numeroRef2 . "%"; $types .= 's'; }
    if ($sector !== '') {
        $wheres[] = '(Direccion LIKE ? AND (' . implode(' OR ', array_fill(0, count($sectorSynonyms), 'LOWER(Direccion) LIKE ?')) . '))';
        $params[] = "%" . $sector . "%";
        foreach ($sectorSynonyms as $syn) { $params[] = $syn; }
        $types .= str_repeat('s', 1 + count($sectorSynonyms));
    }
    if ($grupo !== '') {
        $wheres[] = '(Direccion LIKE ? AND (' . implode(' OR ', array_fill(0, count($grupoSynonyms), 'LOWER(Direccion) LIKE ?')) . '))';
        $params[] = "%" . $grupo . "%";
        foreach ($grupoSynonyms as $syn) { $params[] = $syn; }
        $types .= str_repeat('s', 1 + count($grupoSynonyms));
    }
    // Filtros adicionales si se proporcionan
    if ($puesto !== '') {
        $wheres[] = 'Direccion LIKE ?';
        $params[] = "%" . $puesto . "%";
        $types .= 's';
        $wheres[] = '(LOWER(Direccion) LIKE ? OR LOWER(Direccion) LIKE ? OR LOWER(Direccion) LIKE ?)';
        array_push($params, '%mcdo%', '%mdo%', '%mercado%');
        $types .= 'sss';
    }
    if ($codigoZona !== '') { $wheres[] = 'CodigoZonaVenta = ?'; $params[] = $codigoZona; $types .= 's'; }
    // Mostrar solo activos si existe la columna
    if ($HAS_ACTIVO) { $wheres[] = 'Activo = 1'; }
    $whereSQL = count($wheres) ? ('WHERE ' . implode(' AND ', $wheres)) : '';
    $selectCols = ($HAS_ACTIVO ? 'Activo, ' : '') . 'Codigo, Nombre, TipoDocIdentidad, DocIdentidad, Direccion, CodigoZonaVenta';
    $sql = "SELECT $selectCols FROM cartera_clientes $whereSQL LIMIT 50";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        if ($DEBUG) { echo '<pre>ERROR PREPARE: ' . htmlspecialchars($conn->error) . "\nSQL: " . htmlspecialchars($sql) . '</pre>'; }
        http_response_code(500); die('Error preparando la consulta.');
    }
    mysqli_bind_params($stmt, $types, $params);
    $stmt->execute();
    if ($DEBUG && $stmt->error) { echo '<pre>ERROR EXECUTE: ' . htmlspecialchars($stmt->error) . '</pre>'; }
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='6'><tr>" . ($HAS_ACTIVO ? "<th>Activo</th>" : "") . "<th>Código</th><th>Nombre</th><th>TipoDocIdentidad</th><th>DocIdentidad</th><th>Dirección</th><th>CodigoZonaVenta</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>" . ($HAS_ACTIVO ? ("<td>" . htmlspecialchars((string)($row['Activo'] ?? '')) . "</td>") : "")
                . "<td>" . htmlspecialchars($row['Codigo']) . "</td><td>" . htmlspecialchars($row['Nombre']) . "</td><td>" . htmlspecialchars($row['TipoDocIdentidad']) . "</td><td>" . htmlspecialchars($row['DocIdentidad']) . "</td><td>" . htmlspecialchars($row['Direccion']) . "</td><td>" . htmlspecialchars($row['CodigoZonaVenta']) . "</td></tr>";
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
