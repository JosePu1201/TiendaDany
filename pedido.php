<!DOCTYPE html>
<html lang="es">
<head>
    <title>Pedido</title>
    <?php include './inc/link.php'; ?>
</head>
<body id="container-page-index">
    <?php include './inc/navbar.php'; ?>
    <section id="container-pedido">
        <div class="container">
            <div class="page-header">
              <h1>PEDIDOS <small class="tittles-pages-logo">NICE ACCESORIOS</small></h1>
            </div>
            <br><br><br>
            <div class="row">
              <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                <?php
                  require_once "library/configServer.php";
                  require_once "library/consulSQL.php";
                  if($_SESSION['UserType']=="Admin" || $_SESSION['UserType']=="User"){
                    if(isset($_SESSION['carro'])){
                ?>
                      <br><br><br>
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-xs-10 col-xs-offset-1">
                            <p class="text-center lead">Selecciona un metodo de pago</p>
                            <p class="text-center">
                              <button class="btn btn-lg btn-raised btn-success btn-block" data-toggle="modal" data-target="#PagoModalTran">Pago contra entrega</button>
                              <button class="btn btn-lg btn-raised btn-success btn-block" data-toggle="modal" >Transaccion Bancaria</button>
                            </p>
                          </div>
                        </div>
                      </div>
                <?php
                    }else{
                      echo '<p class="text-center lead">No tienes pedidos pendientes de pago</p>';
                    }
                  }else{
                    echo '<p class="text-center lead">Inicia sesión para realizar pedidos</p>';
                  }
                ?>
              </div>
            </div>
        </div>
        <?php
            if($_SESSION['UserType']=="User"){
                $consultaC=ejecutarSQL::consultar("SELECT * FROM venta WHERE NIT='".$_SESSION['UserNIT']."'");
        ?>
            <div class="container" style="margin-top: 70px;">
              <div class="page-header">
                <h1>Mis pedidos</h1>
              </div>
            </div>
        <?php
            if(mysqli_num_rows($consultaC)>=1){
        ?> 
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12">
                            <table class="table table-hover table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Envío</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while($rw=mysqli_fetch_array($consultaC, MYSQLI_ASSOC)){
                                    ?> 
                                        <tr>
                                            <td><?php echo $rw['Fecha']; ?></td>
                                            <td>Q<?php echo $rw['TotalPagar']; ?></td>
                                            <td>
                                            <?php
                                              switch ($rw['Estado']) {
                                                case 'Enviado':
                                                  echo "En camino";
                                                  break;
                                                case 'Pendiente':
                                                  echo "En espera";
                                                  break;
                                                case 'Entregado':
                                                  echo "Entregado";
                                                  break;
                                                default:
                                                  echo "Sin informacion";
                                                  break;
                                              }
                                            ?>
                                            </td>
                                            <td><?php echo $rw['TipoEnvio']; ?></td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                        
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        <?php
            }else{
              echo '<p class="text-center lead">No tienes ningun pedido realizado</p>';
            }
            mysqli_free_result($consultaC);
        }
        ?>
    </section>
    <div class="modal fade" id="PagoModalTran" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <form class="modal-content FormCatElec" action="process/confirmcompra.php" method="POST" role="form" data-form="save">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Pago contra entrega</h4>
          </div>
          <div class="modal-body">
            <?php
              $consult1=ejecutarSQL::consultar("SELECT * FROM cuentabanco");
              if(mysqli_num_rows($consult1)>=1){
                $datBank=mysqli_fetch_array($consult1, MYSQLI_ASSOC);
            ?>
                <?php if($_SESSION['UserType']=="Admin"): ?>
                <div class="form-group">
                    <label>Nombre</label>
                    <input class="form-control" type="text" name="NomCliente" placeholder="Nombre del cliente" maxlength="50" required="">
                </div>
                <div class="form-group">
                  <span>Tipo De Envio</span>
                  <select class="form-control" name="tipo-envio" data-toggle="tooltip" data-placement="top" title="Elige El Tipo De Envio">
                      <option value="" disabled="" selected="">Selecciona una opción</option>
                      <option value="Recoger Por Tienda">Recoger Por Tienda</option>
                      <option value="Envio Por Currier">Envio Gratis</option> 
                  </select>
               </div>
                <div class="form-group">
                    <label>NIT</label>
                    <input class="form-control" type="text" name="NitCliente" placeholder="NIT del cliente" maxlength="15" required="">
                </div>
                <div class="form-group">
                    <label>Telefono</label>
                    <input class="form-control" type="text" name="TelCliente" placeholder="Numero de telefono del cliente" maxlength="15" required="">
                </div>
                <div class="form-group">
                    <label>Direccion</label>
                    <input class="form-control" type="text" name="DirCliente" placeholder="Direccion del cliente" maxlength="150" required="">
                </div>
                <div class="form-group">
                    <label>Referencia</label>
                    <input class="form-control" type="text" name="RefDir" placeholder="Referencia de la direccion" maxlength="150" required="">
                </div>
                <?php else: ?>
                  <div class="form-group">
                    <label>Nombre</label>
                    <input class="form-control" type="text" name="NomCliente" placeholder="Ingresa tu nombre sin tildes" maxlength="100" required="">
                  </div>
                    <div class="form-group">
                      <span>Tipo De Envio</span>
                      <select class="form-control" name="tipo-envio" data-toggle="tooltip" data-placement="top" title="Elige El Tipo De Envio">
                          <option value="" disabled="" selected="">Selecciona una opción</option>
                          <option value="Recoger Por Tienda">Recoger Por Tienda</option>
                          <option value="Envio Por Currier">Envio Gratis</option> 
                      </select>
                   </div>
                   <input type="hidden" name="NitCliente" value="<?php echo $_SESSION['UserNIT']; ?>">
                <div class="form-group">
                    <label>Telefono</label>
                    <input class="form-control" type="text" name="TelCliente" placeholder="Tu numero de telefono" maxlength="15" required="">
                </div>
                <div class="form-group">
                    <label>Direccion</label>
                    <input class="form-control" type="text" name="DirCliente" placeholder="Tu direccion para entrega" maxlength="150" required="">
                </div>
                <div class="form-group">
                    <label>Referencia</label>
                    <input class="form-control" type="text" name="RefDir" placeholder="Referencia de la direccion" maxlength="150" required="">
                </div>
                <?php 
                endif;
              }else{
                echo "Ocurrio un error: Parese ser que no se ha configurado las cuentas de banco";
              }
              mysqli_free_result($consult1);
            ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-sm btn-raised" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary btn-sm btn-raised">Confirmar</button>
          </div>
        </form>
      </div>
    </div>
    <div class="ResForm"></div>
    <?php include './inc/footer.php'; ?>
</body>
</html>
