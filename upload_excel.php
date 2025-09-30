<?php
// upload_excel.php
require 'vendor/autoload.php'; // Necesitas instalar phpoffice/phpspreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'consulta_cliente';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

if (isset($_FILES['excelFile']) && $_FILES['excelFile']['error'] == 0) {
    $filePath = $_FILES['excelFile']['tmp_name'];
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray(null, true, true, true);
    $columns = array_values($rows[1]);
    unset($rows[1]); // Quitar encabezado
    $count = 0;
    foreach ($rows as $row) {
        $data = array_values($row);
        // Preparar los valores para SQL
        $sql = "INSERT INTO cartera_clientes (
            Codigo, Nombre, TipoDocIdentidad, DocIdentidad, Activo, Direccion,
            CodigoZonaVenta, DescripcionZonaVenta, LineaCredito, CodigoZonaReparto,
            DescripcionZonaReparto, CategoriaCliente, TipoCliente, Distrito, PKID,
            IDCategoriaCliente, IDZonaVenta, CCC, TamañoNegocio, MixProductos,
            MaquinaExhibidora, CortadorEmbutidos, Visicooler, CajaRegistradora, TelefonoPublico
        ) VALUES (
            ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
        ) ON DUPLICATE KEY UPDATE
            Nombre=VALUES(Nombre), TipoDocIdentidad=VALUES(TipoDocIdentidad), DocIdentidad=VALUES(DocIdentidad),
            Activo=VALUES(Activo), Direccion=VALUES(Direccion), CodigoZonaVenta=VALUES(CodigoZonaVenta),
            DescripcionZonaVenta=VALUES(DescripcionZonaVenta), LineaCredito=VALUES(LineaCredito), CodigoZonaReparto=VALUES(CodigoZonaReparto),
            DescripcionZonaReparto=VALUES(DescripcionZonaReparto), CategoriaCliente=VALUES(CategoriaCliente), TipoCliente=VALUES(TipoCliente),
            Distrito=VALUES(Distrito), PKID=VALUES(PKID), IDCategoriaCliente=VALUES(IDCategoriaCliente), IDZonaVenta=VALUES(IDZonaVenta),
            CCC=VALUES(CCC), TamañoNegocio=VALUES(TamañoNegocio), MixProductos=VALUES(MixProductos), MaquinaExhibidora=VALUES(MaquinaExhibidora),
            CortadorEmbutidos=VALUES(CortadorEmbutidos), Visicooler=VALUES(Visicooler), CajaRegistradora=VALUES(CajaRegistradora), TelefonoPublico=VALUES(TelefonoPublico)
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            'ssssisssssssississsssssss',
            ...$data
        );
        if ($stmt->execute()) {
            $count++;
        }
        $stmt->close();
    }
    echo "Se cargaron/actualizaron $count registros.";
} else {
    echo 'Error al subir el archivo.';
}
$conn->close();
?>
