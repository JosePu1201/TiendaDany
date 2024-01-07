<?php
session_start(); 
include '../library/configServer.php';
include '../library/consulSQL.php';
$nombreCliente=consultasSQL::clean_string($_POST['NomCliente']);
$telCliente=consultasSQL::clean_string($_POST['TelCliente']);
$dirCliente=consultasSQL::clean_string($_POST['DirCliente']);
$ref=consultasSQL::clean_string($_POST['RefDir']);
$tipoenvio=consultasSQL::clean_string($_POST['tipo-envio']);
$Cedclien=consultasSQL::clean_string($_POST['NitCliente']);

$verdata=  ejecutarSQL::consultar("SELECT * FROM cliente WHERE NIT='".$Cedclien."'");
if(mysqli_num_rows($verdata)>=1){
  if(!empty($_SESSION['carro'])){
    $StatusV="Pendiente";
    $suma = 0;
    foreach($_SESSION['carro'] as $codess){
        $consulta=ejecutarSQL::consultar("SELECT * FROM producto WHERE CodigoProd='".$codess['producto']."'");
        while($fila = mysqli_fetch_array($consulta, MYSQLI_ASSOC)) {
          $tp=number_format($fila['Precio']-($fila['Precio']*($fila['Descuento']/100)), 2, '.', '');
          $suma += $tp*$codess['cantidad'];
        }
        mysqli_free_result($consulta);
    }
    if(consultasSQL::InsertSQL("venta", "Fecha, NIT, TotalPagar, Estado, TipoEnvio, Telefono,Direccion,Referencia,Nombre", "'".date('d-m-Y')."','$Cedclien','$suma','$StatusV','$tipoenvio','$telCliente','$dirCliente','$ref','$nombreCliente'")){

      /*recuperando el número del pedido actual*/
      $verId=ejecutarSQL::consultar("SELECT * FROM venta WHERE NIT='$Cedclien' ORDER BY NumPedido desc limit 1");
      $fila=mysqli_fetch_array($verId, MYSQLI_ASSOC);
      $Numpedido=$fila['NumPedido'];

      /*Insertando datos en detalle de la venta*/
      foreach($_SESSION['carro'] as $carro){
        $preP=ejecutarSQL::consultar("SELECT * FROM producto WHERE CodigoProd='".$carro['producto']."'");
        $filaP=mysqli_fetch_array($preP, MYSQLI_ASSOC);
        $precioProducto= $filaP['Precio'] - ($filaP['Precio']*($filaP['Descuento']/100));
        $gananciaActual = $filaP['Ganancias']; // Obtener la ganancia actual del producto
    
        // Calcular la nueva ganancia sumando la ganancia actual más el precio por la cantidad de la nueva venta
        $nuevaGanancia = $gananciaActual + ($precioProducto * $carro['cantidad']);
    
        // Actualizar la columna de ganancia en la tabla de productos
        consultasSQL::UpdateSQL("producto", "Ganancias='$nuevaGanancia'", "CodigoProd='".$carro['producto']."'");
    
        // Insertar los detalles de la venta en la tabla detalle
        consultasSQL::InsertSQL("detalle", "NumPedido, CodigoProd, CantidadProductos, PrecioProd", "'$Numpedido', '".$carro['producto']."', '".$carro['cantidad']."', '$precioProducto'");
        
        mysqli_free_result($preP);
    
        /* Restando stock a cada producto seleccionado en el carrito */
        $prodStock=ejecutarSQL::consultar("SELECT * FROM producto WHERE CodigoProd='".$carro['producto']."'");
        while($fila = mysqli_fetch_array($prodStock, MYSQLI_ASSOC)) {
            $existencias = $fila['Stock'];
            $existenciasRest=$carro['cantidad'];
            consultasSQL::UpdateSQL("producto", "Stock=('$existencias'-'$existenciasRest')", "CodigoProd='".$carro['producto']."'");
        }
    }
    
      
      /*Vaciando el carrito*/
      unset($_SESSION['carro']);
      echo '<script>
      swal({
        title: "Pedido realizado",
        text: "El pedido se ha realizado con éxito",
        type: "success",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar",
        closeOnConfirm: false,
        closeOnCancel: false
        },
        function(isConfirm) {
        if (isConfirm) {
          location.reload();
        } else {
          location.reload();
        }
      });
      </script>';

    }else{
      echo '<script>swal("ERROR", "Ha ocurrido un error inesperado", "error");</script>';
    }
  }else{
    echo '<script>swal("ERROR", "No has seleccionado ningún producto, revisa el carrito de compras", "error");</script>';
  }
}else{
    echo '<script>swal("ERROR", "El Nit es incorrecto, no esta registrado con ningun cliente", "error");</script>';
}
mysqli_free_result($verdata);