<?php
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

date_default_timezone_set('America/Lima');

if (!isset($_SESSION["nombre"])) {
  header("Location: login.html");
} else {
  require 'header.php';

  if ($_SESSION['consultav'] == 1) {
?>
    <!--Contenido-->
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>  Reportes  <small>de Ventas por fecha y cliente</small>  </h1>
        <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
          <li><a href="#">Reportes</a></li>
          <li class="active">ventas</li>
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="nav-tabs-custom">
              <ul class="nav nav-tabs">
                <li class="active"><a href="#por-documento" data-toggle="tab">General</a></li>
                <li><a href="#por-producto" data-toggle="tab">Detallado</a></li>
              </ul>
              <div class="tab-content">
                <!-- :::::::::::::: POR COMPROBANTE :::::::::::::: -->
                <div class="tab-pane active" id="por-documento">
                  <div class="row">
                    <div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
                      <label>Fecha Inicio</label>
                      <input type="date" class="form-control" name="c_fecha_inicio" id="c_fecha_inicio" value="<?php echo date("Y-m-d"); ?>" onchange="listar_por_comprobante();">
                    </div>
                    <div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
                      <label>Fecha Fin</label>
                      <input type="date" class="form-control" name="c_fecha_fin" id="c_fecha_fin" value="<?php echo date("Y-m-d"); ?>"onchange="listar_por_comprobante();">
                    </div>
                    <div class="form-inline col-lg-6 col-md-6 col-sm-6 col-xs-12">
                      <label>Cliente</label>
                      <select name="c_idcliente" id="c_idcliente" class="form-control selectpicker" data-live-search="true" onchange="listar_por_comprobante();">
                      </select>                    
                    </div>                    
                    <!-- <div class="form-inline col-lg-1 col-md-6 col-sm-6 col-xs-12">
                      <br>
                      <button class="btn btn-success" onclick="listar_por_comprobante()">Mostrar</button>
                    </div> -->
                  </div>
                    
                  <table id="tbla_por_comprobante" class="table table-striped table-bordered table-condensed table-hover" style="width: 100% !important;">
                    <thead>
                      <th>OP</th>
                      <th>Fecha</th>
                      <th>Cliente</th>
                      <th>Número</th>
                      <th>Total Venta</th>
                      <th>Utilidad</th>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                      <th>OP</th>
                      <th>Fecha</th>
                      <th>Cliente</th>
                      <th>Número</th>
                      <th>Total Venta</th>
                      <th>Utilidad</th>
                    </tfoot>
                  </table>
                </div>    <!-- /#ion-icons -->

                <!-- :::::::::::::: POR PRODUCTO :::::::::::::: -->
                <div class="tab-pane" id="por-producto">
                  <div class="row">
                    <div class="form-group col-lg-2 col-md-3 col-sm-6 col-xs-12">
                      <label>Fecha Inicio</label>
                      <input type="date" class="form-control" name="p_fecha_inicio" id="p_fecha_inicio" value="<?php echo date("Y-m-d"); ?>" onchange="listar_por_producto();">
                    </div>
                    <div class="form-group col-lg-2 col-md-3 col-sm-6 col-xs-12">
                      <label>Fecha Fin</label>
                      <input type="date" class="form-control" name="p_fecha_fin" id="p_fecha_fin" value="<?php echo date("Y-m-d"); ?>" onchange="listar_por_producto();">
                    </div>
                    <div class="form-inline col-lg-4 col-md-6 col-sm-6 col-xs-12">
                      <label>Cliente</label>
                      <select name="p_idcliente" id="p_idcliente" class="form-control selectpicker" data-live-search="true" onchange="listar_por_producto();">
                      </select>                    
                    </div>
                    <div class="form-inline col-lg-2 col-md-6 col-sm-6 col-xs-12">
                      <label>Categoria</label>
                      <select name="p_idcategoria" id="p_idcategoria" class="form-control selectpicker" data-live-search="true" onchange="listar_por_producto();">
                      </select>                    
                    </div>
                    <div class="form-inline col-lg-2 col-md-6 col-sm-6 col-xs-12">
                      <label>Número</label>
                      <select name="p_numero" id="p_numero" class="form-control selectpicker" data-live-search="true" onchange="listar_por_producto();">
                      </select>   
                    </div>
                  </div>

                  <table id="tbla_por_producto" class="table table-striped table-bordered table-condensed table-hover" style="width: 100% !important;">
                    <thead>
                      <th>OP</th>
                      <th>Fecha</th>
                      <th>Cliente</th>
                      <th>Articulo</th>
                      <th>Número</th>
                      <th>Cant</th>
                      <th>Compra</th>                      
                      <th>Venta</th>                      
                      <th>Utilidad</th>
                      <th>Categoria</th>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                      <th>OP</th>
                      <th>Fecha</th>
                      <th>Cliente</th>
                      <th>Articulo</th>
                      <th>Número</th>
                      <th>Cant</th>
                      <th>Compra</th>                      
                      <th>Venta</th>                      
                      <th>Utilidad</th>
                      <th>Categoria</th>
                    </tfoot>
                  </table>
                </div>  <!-- /#ion-icons -->

              </div>  <!-- /.tab-content -->
            </div> <!-- /.nav-tabs-custom -->            
          </div><!-- /.col -->
        </div><!-- /.row -->
      </section><!-- /.content -->

      <div class="modal fade" id="modal-ver-detalle">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Detalle Venta</h4>
            </div>
            <div class="modal-body detalle-x-comprobante">
              <div class="text-center">
                <i class="fa fa-fw fa-spinner fa-pulse fa-2x"></i> <br> Cargando datos...
              </div>              
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
              <!-- <button type="button" class="btn btn-success">Save changes</button> -->
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->

    </div><!-- /.content-wrapper -->
    <!--Fin-Contenido-->
  <?php
  } else {
    require 'noacceso.php';
  }

  require 'footer.php';
  ?>
  <script type="text/javascript" src="scripts/ventasfechacliente.js"></script>
  <script> $(function () { $('[data-toggle="tooltip"]').tooltip(); }); </script>
<?php
}
ob_end_flush();
?>