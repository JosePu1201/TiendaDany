<?php
session_start();
require './fpdf/fpdf.php';
include '../library/configServer.php';
include '../library/consulSQL.php';
$id=$_GET['id'];
$sVenta=ejecutarSQL::consultar("SELECT * FROM venta WHERE NumPedido='$id'");
if (!$sVenta) {
    die('Error en la consulta: ' . mysqli_error());
}

$dVenta = mysqli_fetch_array($sVenta, MYSQLI_ASSOC);
$sCliente=ejecutarSQL::consultar("SELECT * FROM cliente WHERE NIT='".$dVenta['NIT']."'");
$dCliente=mysqli_fetch_array($sCliente, MYSQLI_ASSOC);

$sDet=ejecutarSQL::consultar("SELECT * FROM detalle WHERE NumPedido='".$id."'");
$suma = 0;

while($fila1 = mysqli_fetch_array($sDet, MYSQLI_ASSOC)){
    $consulta=ejecutarSQL::consultar("SELECT * FROM producto WHERE CodigoProd='".$fila1['CodigoProd']."'");
    $fila=mysqli_fetch_array($consulta, MYSQLI_ASSOC);
    $suma += $fila1['PrecioProd']*$fila1['CantidadProductos'];
    mysqli_free_result($consulta);
}

mysqli_free_result($sVenta);
mysqli_free_result($sCliente);
mysqli_free_result($sDet);

// Genera una cadena de texto con la información de la venta

$infoVenta = "Fecha del Pedido: {$dVenta['Fecha']}\n";
//$infoVenta .= "Nombre del Cliente: {$dCliente['NombreCompleto']} {$dCliente['Apellido']}\n";
//$infoVenta .= "Detalles de los Productos:\n";

$sDet=ejecutarSQL::consultar("SELECT * FROM detalle WHERE NumPedido='".$id."'");
while($fila1 = mysqli_fetch_array($sDet, MYSQLI_ASSOC)){
    $consulta=ejecutarSQL::consultar("SELECT * FROM producto WHERE CodigoProd='".$fila1['CodigoProd']."'");
    $fila=mysqli_fetch_array($consulta, MYSQLI_ASSOC);
    //$infoVenta .= "{$fila['NombreProd']}: {$fila1['CantidadProductos']} unidades\n";
    mysqli_free_result($consulta);
}

//$infoVenta .= "Total: $".number_format($suma, 2);
//
// Liberación de resultados y cierre de conexiones
mysqli_free_result($sDet);

// Muestra la información en un cuadro de alerta mediante JavaScript


echo '<script>';
echo '  console.log("'.$infoVenta.'");';
echo '</script>';
?>
