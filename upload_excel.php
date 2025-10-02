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
    // Helpers para localizar columnas por encabezado y convertir booleanos
    $normalize = function($s) {
        if ($s === null) return '';
        $s = strtolower(trim((string)$s));
        $map = [
            'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n',
            'Á'=>'a','É'=>'e','Í'=>'i','Ó'=>'o','Ú'=>'u','Ñ'=>'n',
            '.'=>'',' '=>'', '-' => '', '/' => ''
        ];
        return strtr($s, $map);
    };
    $findIndex = function(array $cols, array $variants) use ($normalize) {
        $variants = array_map($normalize, $variants);
        foreach ($cols as $i => $name) {
            if (in_array($normalize($name), $variants, true)) return $i;
        }
        return -1;
    };
    $toBoolInt = function($v) use ($normalize) {
        if ($v === null) return 0;
        if (is_bool($v)) return $v ? 1 : 0;
        if (is_int($v)) return $v ? 1 : 0;
        $nv = $normalize($v);
        if ($nv === '' || $nv === '0' || $nv === 'false' || $nv === 'no' || $nv === 'off' || $nv === 'inactivo') return 0;
        if ($nv === '1' || $nv === 'true' || $nv === 'si' || $nv === 'si' || $nv === 'sí' || $nv === 'on' || $nv === 'activo' || $nv === 'x' || $nv === 'yes' || $nv === 'y') return 1;
        // fallback: cualquier otro valor distinto de vacio lo consideramos 1
        return 1;
    };
    // Índices de columnas relevantes (si existen en el Excel)
    $idxActivo = $findIndex($columns, ['Activo']);
    $idxMaquina = $findIndex($columns, ['MaquinaExhibidora','MáquinaExhibidora','Maquina','Maquina Exhibidora']);
    $idxCortador = $findIndex($columns, ['CortadorEmbutidos','Cortador Embutidos','Cortador']);
    $idxVisi = $findIndex($columns, ['Visicooler']);
    $idxCaja = $findIndex($columns, ['CajaRegistradora','Caja Registradora']);
    unset($rows[1]); // Quitar encabezado
    $count = 0;
    foreach ($rows as $row) {
        $data = array_values($row);
        // Normalizar booleanos si los encabezados están presentes
        if ($idxActivo >= 0 && array_key_exists($idxActivo, $data)) {
            $data[$idxActivo] = $toBoolInt($data[$idxActivo]);
        }
        if ($idxMaquina >= 0 && array_key_exists($idxMaquina, $data)) {
            $data[$idxMaquina] = $toBoolInt($data[$idxMaquina]);
        }
        if ($idxCortador >= 0 && array_key_exists($idxCortador, $data)) {
            $data[$idxCortador] = $toBoolInt($data[$idxCortador]);
        }
        if ($idxVisi >= 0 && array_key_exists($idxVisi, $data)) {
            $data[$idxVisi] = $toBoolInt($data[$idxVisi]);
        }
        if ($idxCaja >= 0 && array_key_exists($idxCaja, $data)) {
            $data[$idxCaja] = $toBoolInt($data[$idxCaja]);
        }
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
