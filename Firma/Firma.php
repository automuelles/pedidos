<?php
// Conexión a SQL Server
$serverName = "SERVAUTOMUELLES\SQLEXPRESS";
$connectionOptions = array(
    "Database" => "AutomuellesDiesel1",
    "Uid" => "AutomuellesDiesel",
    "PWD" => "Complex@2024Pass!"
);

try {
    $conn = new PDO("sqlsrv:server=$serverName;Database=AutomuellesDiesel1", $connectionOptions["Uid"], $connectionOptions["PWD"]);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Filtro de búsqueda (obtenemos los valores si están presentes)
$transaccion = isset($_GET['transaccion']) ? $_GET['transaccion'] : '';
$documento = isset($_GET['documento']) ? $_GET['documento'] : '';

// Verificar si se aplicaron filtros
if ($transaccion || $documento) {
    // Consultas con filtros aplicados
    $query1 = "SELECT TOP (1000) 
        [IntEmpresa],
        [IntTransaccion],
        [IntDocumento],
        [StrProducto],
        [StrLote],
        [StrTalla],
        [StrColor],
        [IntBodega],
        [StrSerie],
        [StrSerie1],
        [StrSerie2],
        [StrSerie3],
        [IntId],
        [IntCantidadDoc],
        [IntCantidad],
        [StrUnidad],
        [IntFactor],
        [IntValorUnitario],
        [IntValorTotal],
        [IntPorDescuento],
        [IntValorDescuento],
        [IntValorIva],
        [IntVrImpuesto1],
        [IntValorCosto],
        [IntValorUnitarioW],
        [IntDocRefD],
        [IntCostoAgregado],
        [IntReteFte],
        [IntSaldoI],
        [IntVUSaldoI],
        [IntSaldoF],
        [IntVUSaldoF],
        [DatFecha1],
        [DatFecha2],
        [StrSucursal],
        [StrCCosto],
        [StrSubCCosto],
        [StrDescripcion1],
        [StrTercero],
        [StrVinculado],
        [StrVendedor],
        [IntTipo],
        [IntImpresion],
        [IntGratis],
        [IntVlrBaseImpto],
        [IntBienCubierto],
        [IdSeguridad],
        [StrUbicacion],
        [StrActivoFijo],
        [IntRegistroTransporte],
        [IntValorImpPlastico],
        [IntVrImpuesto2]
    FROM [AutomuellesDiesel1].[dbo].[TblDetalleDocumentos]
    WHERE [IntTransaccion] LIKE :transaccion
    AND [IntDocumento] LIKE :documento";

    $stmt1 = $conn->prepare($query1);
    $stmt1->bindValue(':transaccion', '%' . $transaccion . '%', PDO::PARAM_STR);
    $stmt1->bindValue(':documento', '%' . $documento . '%', PDO::PARAM_STR);
    $stmt1->execute();
    $tblDetalleDocumentos = $stmt1->fetchAll(PDO::FETCH_ASSOC);

    // Consultas para la tabla TblTerceros
    $query2 = "SELECT TOP (1000) 
        [StrIdTercero],
        [StrNombre],
        [StrTipoId],
        [IntIdentificacion],
        [IntDv],
        [IntDvC],
        [StrApellido1],
        [StrApellido2],
        [StrNombre1],
        [StrNombre2],
        [StrNombreComercial],
        [StrDireccion],
        [StrDireccion2],
        [StrCodPostal],
        [StrTelefono],
        [StrCelular]
    FROM [AutomuellesDiesel1].[dbo].[TblTerceros]";
    $stmt2 = $conn->prepare($query2);
    $stmt2->execute();
    $tblTerceros = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Si no hay filtros, no se realiza la consulta
    $tblDetalleDocumentos = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Datos de Facturas y Terceros</title>
</head>
<body>

<h2>Filtrar Datos</h2>
<form method="get" action="">
    <label for="transaccion">Transacción:</label>
    <input type="text" name="transaccion" value="<?php echo htmlspecialchars($transaccion); ?>" />
    
    <label for="documento">Documento:</label>
    <input type="text" name="documento" value="<?php echo htmlspecialchars($documento); ?>" />
    
    <input type="submit" value="Filtrar" />
</form>

<h2>Tabla TblDetalleDocumentos</h2>
<table border="1">
    <tr>
        <th>IntEmpresa</th>
        <th>IntTransaccion</th>
        <th>IntDocumento</th>
        <th>StrProducto</th>
        <th>StrLote</th>
        <th>StrTalla</th>
        <th>StrColor</th>
        <th>IntBodega</th>
        <th>StrSerie</th>
        <th>StrSerie1</th>
        <th>StrSerie2</th>
        <th>StrSerie3</th>
        <th>IntId</th>
        <th>IntCantidadDoc</th>
        <th>IntCantidad</th>
        <th>StrUnidad</th>
        <th>IntFactor</th>
        <th>IntValorUnitario</th>
        <th>IntValorTotal</th>
        <th>IntPorDescuento</th>
        <th>IntValorDescuento</th>
        <th>IntValorIva</th>
        <th>IntVrImpuesto1</th>
        <th>IntValorCosto</th>
        <th>IntValorUnitarioW</th>
        <th>IntDocRefD</th>
        <th>IntCostoAgregado</th>
        <th>IntReteFte</th>
        <th>IntSaldoI</th>
        <th>IntVUSaldoI</th>
        <th>IntSaldoF</th>
        <th>IntVUSaldoF</th>
        <th>DatFecha1</th>
        <th>DatFecha2</th>
        <th>StrSucursal</th>
        <th>StrCCosto</th>
        <th>StrSubCCosto</th>
        <th>StrDescripcion1</th>
        <th>StrTercero</th>
        <th>StrVinculado</th>
        <th>StrVendedor</th>
        <th>IntTipo</th>
        <th>IntImpresion</th>
        <th>IntGratis</th>
        <th>IntVlrBaseImpto</th>
        <th>IntBienCubierto</th>
        <th>IdSeguridad</th>
        <th>StrUbicacion</th>
        <th>StrActivoFijo</th>
        <th>IntRegistroTransporte</th>
        <th>IntValorImpPlastico</th>
        <th>IntVrImpuesto2</th>
    </tr>
    <?php foreach ($tblDetalleDocumentos as $row) { ?>
        <tr>
            <td><?php echo $row['IntEmpresa']; ?></td>
            <td><?php echo $row['IntTransaccion']; ?></td>
            <td><?php echo $row['IntDocumento']; ?></td>
            <td><?php echo $row['StrProducto']; ?></td>
            <td><?php echo $row['StrLote']; ?></td>
            <td><?php echo $row['StrTalla']; ?></td>
            <td><?php echo $row['StrColor']; ?></td>
            <td><?php echo $row['IntBodega']; ?></td>
            <td><?php echo $row['StrSerie']; ?></td>
            <td><?php echo $row['StrSerie1']; ?></td>
            <td><?php echo $row['StrSerie2']; ?></td>
            <td><?php echo $row['StrSerie3']; ?></td>
            <td><?php echo $row['IntId']; ?></td>
            <td><?php echo $row['IntCantidadDoc']; ?></td>
            <td><?php echo $row['IntCantidad']; ?></td>
            <td><?php echo $row['StrUnidad']; ?></td>
            <td><?php echo $row['IntFactor']; ?></td>
            <td><?php echo $row['IntValorUnitario']; ?></td>
            <td><?php echo $row['IntValorTotal']; ?></td>
            <td><?php echo $row['IntPorDescuento']; ?></td>
            <td><?php echo $row['IntValorDescuento']; ?></td>
            <td><?php echo $row['IntValorIva']; ?></td>
            <td><?php echo $row['IntVrImpuesto1']; ?></td>
            <td><?php echo $row['IntValorCosto']; ?></td>
            <td><?php echo $row['IntValorUnitarioW']; ?></td>
            <td><?php echo $row['IntDocRefD']; ?></td>
            <td><?php echo $row['IntCostoAgregado']; ?></td>
            <td><?php echo $row['IntReteFte']; ?></td>
            <td><?php echo $row['IntSaldoI']; ?></td>
            <td><?php echo $row['IntVUSaldoI']; ?></td>
            <td><?php echo $row['IntSaldoF']; ?></td>
            <td><?php echo $row['IntVUSaldoF']; ?></td>
            <td><?php echo $row['DatFecha1']; ?></td>
            <td><?php echo $row['DatFecha2']; ?></td>
            <td><?php echo $row['StrSucursal']; ?></td>
            <td><?php echo $row['StrCCosto']; ?></td>
            <td><?php echo $row['StrSubCCosto']; ?></td>
            <td><?php echo $row['StrDescripcion1']; ?></td>
            <td><?php echo $row['StrTercero']; ?></td>
            <td><?php echo $row['StrVinculado']; ?></td>
            <td><?php echo $row['StrVendedor']; ?></td>
            <td><?php echo $row['IntTipo']; ?></td>
            <td><?php echo $row['IntImpresion']; ?></td>
            <td><?php echo $row['IntGratis']; ?></td>
            <td><?php echo $row['IntVlrBaseImpto']; ?></td>
            <td><?php echo $row['IntBienCubierto']; ?></td>
            <td><?php echo $row['IdSeguridad']; ?></td>
            <td><?php echo $row['StrUbicacion']; ?></td>
            <td><?php echo $row['StrActivoFijo']; ?></td>
            <td><?php echo $row['IntRegistroTransporte']; ?></td>
            <td><?php echo $row['IntValorImpPlastico']; ?></td>
            <td><?php echo $row['IntVrImpuesto2']; ?></td>
        </tr>
    <?php } ?>
</table>
<?php if (!empty($tblTerceros)) { ?>
    <h2>Tabla TblTerceros</h2>
    <table border="1">
        <tr>
            <th>StrIdTercero</th>
            <th>StrNombre</th>
            <th>StrTipoId</th>
            <th>IntIdentificacion</th>
            <th>IntDv</th>
            <th>IntDvC</th>
            <th>StrApellido1</th>
            <th>StrApellido2</th>
            <th>StrNombre1</th>
            <th>StrNombre2</th>
            <th>StrNombreComercial</th>
            <th>StrDireccion</th>
            <th>StrDireccion2</th>
            <th>StrCodPostal</th>
            <th>StrTelefono</th>
            <th>StrCelular</th>
        </tr>
        <?php foreach ($tblTerceros as $row) { ?>
            <tr>
                <td><?php echo $row['StrIdTercero']; ?></td>
                <td><?php echo $row['StrNombre']; ?></td>
                <td><?php echo $row['StrTipoId']; ?></td>
                <td><?php echo $row['IntIdentificacion']; ?></td>
                <td><?php echo $row['IntDv']; ?></td>
                <td><?php echo $row['IntDvC']; ?></td>
                <td><?php echo $row['StrApellido1']; ?></td>
                <td><?php echo $row['StrApellido2']; ?></td>
                <td><?php echo $row['StrNombre1']; ?></td>
                <td><?php echo $row['StrNombre2']; ?></td>
                <td><?php echo $row['StrNombreComercial']; ?></td>
                <td><?php echo $row['StrDireccion']; ?></td>
                <td><?php echo $row['StrDireccion2']; ?></td>
                <td><?php echo $row['StrCodPostal']; ?></td>
                <td><?php echo $row['StrTelefono']; ?></td>
                <td><?php echo $row['StrCelular']; ?></td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>
</body>
</html>