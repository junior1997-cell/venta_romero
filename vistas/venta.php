<?php
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

date_default_timezone_set('America/Lima');

if (!isset($_SESSION["nombre"])) {
  header("Location: login.html");
} else {
  require 'header.php';

  if ($_SESSION['ventas'] == 1) {
?>
    <!--Contenido-->
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Main content -->
      <section class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="box">
              <div class="box-header with-border">
                <h1 class="box-title">Venta 
                  <button class="btn btn-success" id="btnagregar" onclick="mostrarform(true); limpiar();"><i class="fa fa-plus-circle"></i> Agregar</button> 
                  <a href="#" class="btn-reporte-pdf btn btn-info" target="_blank"><i class="fa fa-clipboard"></i> Reporte</a>                  
                                    
                </h1>
                <div class="box-tools pull-right">
                  <input type="date" class="form-control" name="fecha_filtro" id="fecha_filtro" value="<?php echo date("Y-m-d"); ?>" onchange=" listar();">
                </div>
              </div>
              <!-- /.box-header -->
              <!-- centro -->
              <div class="panel-body table-responsive" id="listadoregistros">
                <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
                  <thead>
                    <th>Opciones</th>
                    <th>Fecha</th>                    
                    <th>Usuario</th>
                    <th>Documento</th>
                    <th>Número</th>
                    <th>Total Venta</th>
                    <th>Utilidad</th>
                    <th>Estado</th>
                  </thead>
                  <tbody>
                  </tbody>
                  <tfoot>
                    <th>Opciones</th>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Documento</th>
                    <th>Número</th>
                    <th class="text-right px-1">Total Venta</th>
                    <th class="text-right px-1">Utilidad</th>
                    <th>Estado</th>
                  </tfoot>
                </table>
              </div>
              <div class="panel-body" style="height: 100%; display: none !important;" id="formularioregistros">
              
                <form name="formulario" id="formulario" method="POST">

                  <div class="row" id="cargando-1-fomulario">        
                    
                    <input type="hidden" name="idventa" id="idventa">

                    <div class="form-group col-lg-8 col-md-8 col-sm-8 col-xs-12">
                      <label>Cliente(*):</label>                      
                      <select id="idcliente" name="idcliente" class="form-control selectpicker" data-live-search="true" required>  </select>
                    </div>

                    <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                      <label>Fecha(*):</label>
                      <input type="date" class="form-control" name="fecha_hora" id="fecha_hora" required="" >
                    </div>

                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                      <label>Tipo Comprobante(*):</label>
                      <select name="tipo_comprobante" id="tipo_comprobante" class="form-control selectpicker" required="">
                        <!-- <option value="Boleta">Boleta</option>
                        <option value="Factura">Factura</option> -->
                        <option value="Ticket">Ticket</option>
                      </select>
                    </div>                  
                    
                    <div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-12">
                      <label>Impuesto:</label>
                      <input type="text" class="form-control" name="impuesto" id="impuesto" required="" value="0" readonly>
                    </div>

                    <div class="form-group col-lg-4 col-md-2 col-sm-6 col-xs-12">
                      <label>Observacion:</label>                    
                      <textarea name="observacion" class="form-control" id="observacion" rows="1"></textarea>
                    </div>

                    <div class="form-group col-lg-12 col-md-3 col-sm-6 col-xs-12">
                      <a data-toggle="modal" href="#myModal">
                        <button id="btnAgregarArt" type="button" class="btn btn-primary"> <span class="fa fa-plus"></span> Agregar Artículos</button>
                      </a>
                    </div>

                    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 table-responsive">
                      <table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
                        <thead style="background-color:#A9D0F5">
                          <th>Opciones</th>
                          <th>Artículo</th>
                          <th>Cantidad</th>
                          <th>Precio Venta</th>
                          <th>Descuento</th>
                          <th>Subtotal</th>
                        </thead>
                        <tfoot>                        
                          <th colspan="4"></th>                        
                          <th class="text-right"> <h5>Descuento</h5> <h5>IGV <span class="html_percent_igv">(0%)</span></h5> <h4><b>TOTAL</b></h4>  </th>
                          <th class="text-right">
                            <h5 id="descuento_html"><span class="pull-left">S/.</span> 0.00</h5> <input type="hidden" name="total_descuento" id="total_descuento">
                            <h5 id="impuesto_html"><span class="pull-left"> S/.</span> 0.00</h5> <input type="hidden" name="total_igv" id="total_igv">
                            <h4 class="text-bold" id="total"><span class="pull-left">S/.</span> 0.00</h4> <input type="hidden" name="total_venta" id="total_venta">
                            <input type="hidden" name="total_utilidad" id="total_utilidad">
                          </th>
                        </tfoot>
                        <tbody>

                        </tbody>
                      </table>
                    </div>
                  </div>

                  <div class="row" id="cargando-2-fomulario" style="display: none;">
                    <div class="col-lg-12 text-center">
                      <i class="fa fa-fw fa-spinner fa-pulse fa-2x"></i><br /><h4>Cargando...</h4>
                    </div>
                  </div>

                  <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <button class="btn btn-primary" type="submit" id="btnGuardar"><i class="fa fa-save"></i> Guardar</button>
                    <button id="btnCancelar" class="btn btn-danger" onclick="cancelarform()" type="button"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                  </div>
                </form>
              </div>
              <!--Fin centro -->
            </div><!-- /.box -->
          </div><!-- /.col -->
        </div><!-- /.row -->
      </section><!-- /.content -->

    </div><!-- /.content-wrapper -->
    <!--Fin-Contenido-->

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog" style="width: 65% !important;">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Seleccione un Artículo</h4>
          </div>
          <div class="modal-body table-responsive">
            <table id="tblarticulos" class="table table-striped table-bordered table-condensed table-hover" style="width: 100% !important;">
              <thead>
                <th>Opciones</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Código</th>
                <th>Stock</th>
                <th>Precio Venta</th>                
              </thead>
              <tbody>   </tbody>
              <tfoot>
                <th>Opciones</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Código</th>
                <th>Stock</th>
                <th>Precio Venta</th>                
              </tfoot>
            </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Fin modal -->

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
  <?php
  } else {
    require 'noacceso.php';
  }

  require 'footer.php';
  ?>
  <script type="text/javascript" src="scripts/venta.js"></script>
<?php
}
ob_end_flush();
?>