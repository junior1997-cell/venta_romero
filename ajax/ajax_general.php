<?php
  ob_start();

  if (strlen(session_id()) < 1) {

    session_start(); //Validamos si existe o no la sesión
  }

  if (!isset($_SESSION["nombre"])) {

    $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [] ];
    echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.

  } else {     
    
    require_once "../modelos/Ajax_general.php";
    require_once "../modelos/Consultas.php";

    $ajax_general   = new Ajax_general($_SESSION['idusuario']);
		$consulta = new Consultas();

    $scheme_host  =  ($_SERVER['HTTP_HOST'] == 'localhost' ? 'http://localhost/front_jdl/admin/' :  $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'].'/');
    $imagen_error = "this.src='../dist/svg/404-v2.svg'";
    $toltip       = '<script> $(function () { $(\'[data-toggle="tooltip"]\').tooltip(); }); </script>';

    switch ($_GET["op"]) {       

      // buscar datos de RENIEC
      case 'reniec':
        $dni = $_POST["dni"];
        $rspta = $ajax_general->datos_reniec($dni);
        echo json_encode($rspta);
      break;

      case 'consultaDniReniec':
        $dni = $_POST["dni"];
        $rspta = $ajax_general->consultaDniReniec($dni);
        echo json_encode($datosDniCli);
      break;
      
      // buscar datos de SUNAT
      case 'sunat':
        $ruc = $_POST["ruc"];
        $rspta = $ajax_general->datos_sunat($ruc);
        echo json_encode($rspta, true);
      break;

      case 'consultaRucSunat':
        $ruc = $_POST["ruc"];
        $rspta = $ajax_general->consultaRucSunat($ruc);
        echo json_encode($datosRucCli);
      break;      

      /* ══════════════════════════════════════ C O M P R O B A N T E  ══════════════════════════════════════ */      
      
      /* ══════════════════════════════════════ TIPO PERSONA  ══════════════════════════════════════ */     

      /* ══════════════════════════════════════ T R A B A J A D O R  ══════════════════════════════════════ */        
   
      /* ══════════════════════════════════════ P E R S O N A   P O R   T I P O  ══════════════════════════════════════ */
     

      /* ══════════════════════════════════════ S U C U R S A L  ══════════════════════════════════════ */
       
      
      /* ══════════════════════════════════════ B A N C O  ══════════════════════════════════════ */
     
      
      /* ══════════════════════════════════════ C O L O R ══════════════════════════════════════ */
     

      /* ══════════════════════════════════════ M A R C A ══════════════════════════════════════ */
      
      
      /* ══════════════════════════════════════ U N I D A D   D E   M E D I D A  ══════════════════════════════════════ */
      
      
      /* ══════════════════════════════════════ C A T E G O R I A ══════════════════════════════════════ */     


      /* ══════════════════════════════════════ P R O D U C T O ══════════════════════════════════════ */
      case 'mostrar_producto':
        $rspta = $ajax_general->mostrar_producto($_POST["idproducto"]); 
        echo json_encode($rspta, true);
      break;
      
      case 'tblaProductos':
          
        $rspta = $ajax_general->tblaProductos(); 

        $datas = [];         

        if ($rspta['status'] == true) {

          while ($reg = $rspta['data']->fetch_object()) {

            $img_parametro = ""; $img = "";  $clas_stok = "";
  
            if (empty($reg->imagen)) {
              $img = '../dist/docs/producto/img_perfil/producto-sin-foto.svg';
            } else {
              $img = '../dist/docs/producto/img_perfil/' . $reg->imagen;
              $img_parametro = $reg->imagen;
            }

            if ( $reg->stock <= 0) { $clas_stok = 'badge-danger'; }else if ($reg->stock > 0 && $reg->stock <= 10) { $clas_stok = 'badge-warning'; }else if ($reg->stock > 10) { $clas_stok = 'badge-success'; }
            $data_btn = 'btn-add-producto-'.$reg->idproducto;
            $datas[] = [
              "0" => '<button class="btn btn-warning '.$data_btn.'" onclick="agregarDetalleComprobante(' . $reg->idproducto .')" data-toggle="tooltip" data-original-title="Agregar Activo"><span class="fa fa-plus"></span></button>',
              "1" => '<div class="user-block w-250px">'.
                '<img class="profile-user-img img-responsive img-circle cursor-pointer" src="' . $img . '" alt="user image" onerror="' . $imagen_error . '" onclick="ver_img_producto(\'' . $img . '\', \''.($reg->nombre).'\');">'.
                '<span class="username"><p class="mb-0" >' . $reg->nombre . '</p></span>
                <span class="description"><b>Categoria: </b>' . $reg->categoria . '</span>'.
              '</div>',
              "2" =>'<span class="badge '.$clas_stok.' font-size-14px" stock="'.$reg->stock.'" id="table_stock_'.$reg->idproducto.'">'.$reg->stock.'</span>',
              "3" => number_format($reg->precio_venta, 2, '.', ','),
              "4" => '<textarea class="form-control textarea_datatable" cols="30" rows="1">' . $reg->descripcion . '</textarea>'. $toltip,
            ];
          }
  
          $results = [
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($datas), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($datas), //enviamos el total registros a visualizar
            "aaData" => $datas,
          ];

          echo json_encode($results, true);
        } else {

          echo $rspta['code_error'] .' - '. $rspta['message'] .' '. $rspta['data'];
        }
    
      break;
      
      /* ══════════════════════════════════════ C O M P R A   D E   P R O D U C T O ════════════════════════════ */
     

      /* ══════════════════════════════════════ V E N T A   D E   P R O D U C T O ════════════════════════════ */
      case 'detalle_x_comprobante':       

        $rspta = $consulta->ver_detalle_venta($_POST['id']);
        $subtotal = 0;    $html_input = ''; $html_data = '';

        $html_input = '<div class="row">
          <!-- Tipo de Empresa -->        
          <div class="col-lg-8">
            <div class="form-group">
              <label class="font-size-15px" for="idproveedor">Proveedor</label>
              <span class="form-control-mejorado input-sm" >'.$rspta['data']['venta']['cliente'].'</span>
            </div>
          </div>
          <!-- fecha -->
          <div class="col-lg-4">
            <div class="form-group">
              <label class="font-size-15px" for="fecha_compra">Fecha </label>
              <span class="form-control-mejorado input-sm"><i class="far fa-calendar-alt"></i>&nbsp;&nbsp;&nbsp;'.date("d/m/Y", strtotime($rspta['data']['venta']['fecha_hora'])).' </span>
            </div>
          </div>
          <!-- Tipo de comprobante -->
          <div class="col-lg-4">
            <div class="form-group">
              <label class="font-size-15px" for="tipo_comprovante">Tipo Comprobante</label>
              <span  class="form-control-mejorado input-sm"> '. ((empty($rspta['data']['venta']['tipo_comprobante'])) ? '- - -' :  $rspta['data']['venta']['tipo_comprobante'])  .' </span>
            </div>
          </div>
          <!-- serie_comprovante-->
          <div class="col-lg-4">
            <div class="form-group">
              <label class="font-size-15px" for="serie_comprovante">N° de Comprobante</label>
              <span  class="form-control-mejorado input-sm"> '. $rspta['data']['venta']['serie_comprobante']  . '-' . $rspta['data']['venta']['num_comprobante'].' </span>
            </div>
          </div>
          <!-- IGV-->
          <div class="col-lg-4 " >
            <div class="form-group">
              <label class="font-size-15px" for="igv">IGV</label>
              <span class="form-control-mejorado input-sm"> '.$rspta['data']['venta']['impuesto'].' </span>                                 
            </div>
          </div>
          <!-- Descripcion-->
          <div class="col-lg-12">
            <div class="form-group">
              <label class="font-size-15px" for="descripcion">Descripción </label> <br />
              <textarea class="form-control-mejorado form-control-sm" readonly rows="1">'.((empty($rspta['data']['venta']['observacion'])) ? '- - -' :$rspta['data']['venta']['observacion']).'</textarea>
            </div>
          </div>
        </div> ';
        $html_detalle = '';
        foreach ($rspta['data']['detalle'] as $key => $val) {			
					$img = (empty( $val['imagen']) ? $img = '../files/articulos/producto-sin-foto.svg' : $img = '../files/articulos/' .  $val['imagen'] );	
					$html_detalle .= '<tr class="filas">						
						<td class="">
							<div class="user-block">
								<img class="img-circle" src="' . $img . '" alt="User Image">
								<span class="username"><a href="#">' . $val['producto'] . '</a></span>				 
								<div class="description">	' .$val['categoria'] . '	</div>
							</div>
						</td>
						<td>' . $val['cantidad'] . '</td>
						<td class="text-right">' . $val['precio_compra'] . '</td>
						<td class="text-right">' . $val['precio_venta'] . '</td>
						<td class="text-right">' . $val['descuento'] . '</td>
						<td class="text-right">' . $val['subtotal'] . '</td>
						<td class="text-right">' . $val['utilidad'] . '</td>
					</tr>';					
				}				

				$html_data = '<table class="table table-striped table-bordered table-condensed table-hover" style="width: 100% !important;"> 
					<thead style="background-color:#A9D0F5">
						<th>Artículo</th> <th>Cant</th> <th>Compra</th> <th>Venta</th> <th>Descto</th> <th>Subtotal</th> <th>Utilidad</th>
					</thead>
					'.$html_detalle.'
					<tfoot>			
						<th>
						</th><th></th><th></th><th></th>
            <th><h5>Descuento</h5> <h5>IGV <span>(0%)</span></h5> <h4><b>TOTAL</b></h4></th>
            <th>
              <h5 class="text-nowrap text-right"><span class="pull-left">S/.</span> <span >' . $rspta['data']['venta']['descuento'] . '</span> </h5>
              <h5 class="text-nowrap text-right"><span class="pull-left"> S/.</span> <span >' . $rspta['data']['venta']['impuesto'] . '</span></h5>
              <h4 class="text-nowrap text-right text-bold"><span class="pull-left">S/.</span> <span >' . $rspta['data']['venta']['total_venta'] . '</span></h4>
						</th> 
            <th class="text-center">' . $rspta['data']['venta']['utilidad'] . '</th>
					</tfoot>
				</table>';

        $retorno = ['status' => true, 'message' => 'todo oka', 'data' => $html_input. $html_data ,];
        echo json_encode( $retorno, true );

      break;
   

      default: 
        $rspta = ['status'=>'error_code', 'message'=>'Te has confundido en escribir en el <b>swich.</b>', 'data'=>[]]; echo json_encode($rspta, true); 
      break;
    }
      
  }

  ob_end_flush();
?>
