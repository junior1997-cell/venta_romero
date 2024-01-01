<?php
ob_start();
if (strlen(session_id()) < 1) {
	session_start(); //Validamos si existe o no la sesión
}
if (!isset($_SESSION["nombre"])) {
	header("Location: ../vistas/login.html"); //Validamos el acceso solo a los usuarios logueados al sistema.
} else {
	//Validamos el acceso solo al usuario logueado y autorizado.
	if ($_SESSION['ventas'] == 1) {

		require_once "../modelos/Venta.php";
		require_once "../modelos/Persona.php";
		require_once "../modelos/Articulo.php";
		
		$venta 		= new Venta();
		$persona 	= new Persona();
		$articulo = new Articulo();

		date_default_timezone_set('America/Lima'); $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../dist/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-toggle="tooltip"]\').tooltip(); }); </script>';

		$idventa 						= isset($_POST["idventa"]) ? limpiarCadena($_POST["idventa"]) : "";
		$idcliente 					= isset($_POST["idcliente"]) ? limpiarCadena($_POST["idcliente"]) : "";
		$idusuario 					= $_SESSION["idusuario"];
		$tipo_comprobante 	= isset($_POST["tipo_comprobante"]) ? limpiarCadena($_POST["tipo_comprobante"]) : "";		
		$observacion 				= isset($_POST["observacion"]) ? limpiarCadena($_POST["observacion"]) : "";
		$fecha_hora 				= isset($_POST["fecha_hora"]) ? limpiarCadena($_POST["fecha_hora"]) : "";

		$subtotal_venta 		= isset($_POST["total_venta"]) ? limpiarCadena($_POST["total_venta"]) : "";
		$descuento_venta 		= isset($_POST["total_descuento"]) ? limpiarCadena($_POST["total_descuento"]) : "";
		$impuesto 					= isset($_POST["impuesto"]) ? limpiarCadena($_POST["impuesto"]) : "";
		$total_venta 				= isset($_POST["total_venta"]) ? limpiarCadena($_POST["total_venta"]) : "";
		$utilidad_venta 		= isset($_POST["total_utilidad"]) ? limpiarCadena($_POST["total_utilidad"]) : "";

		switch ($_GET["op"]) {
			case 'guardar_y_editar_venta':
				if (empty($idventa)) {
					$rspta = $venta->insertar_venta($idcliente, $idusuario, $tipo_comprobante,  $observacion, $fecha_hora, $subtotal_venta, $descuento_venta, $impuesto, $total_venta, $utilidad_venta,
					$_POST["idarticulo"], $_POST["cantidad"], $_POST["precio_venta"], $_POST["precio_compra"], $_POST["descuento"], $_POST["subtotal_pr"], $_POST["utilidad"]); 
					echo $rspta ? "ok" : "No se pudieron registrar todos los datos de la venta";
				} else {
					$rspta = $venta->editar_venta($idventa, $idcliente, $idusuario, $tipo_comprobante,  $observacion, $fecha_hora, $subtotal_venta, $descuento_venta, $impuesto, $total_venta, $utilidad_venta,
					$_POST["idarticulo"], $_POST["cantidad"], $_POST["precio_venta"], $_POST["precio_compra"], $_POST["descuento"], $_POST["subtotal_pr"], $_POST["utilidad"]); 
					echo $rspta ? "ok" : "No se pudieron registrar todos los datos de la venta";
				}
			break;

			case 'anular':
				$rspta = $venta->anular($idventa);
				echo $rspta ? "Venta anulada" : "Venta no se puede anular";
			break;

			case 'mostrar':
				$rspta = $venta->mostrar($idventa);
				//Codificar el resultado utilizando json
				echo json_encode($rspta);
			break;

			case 'ver_venta_editar':
				$rspta = $venta->ver_venta_editar($_POST["idventa"]);
				//Codificar el resultado utilizando json
				echo json_encode($rspta);
			break;

			case 'optener_producto_venta':
				$rspta = $venta->optener_producto_compra($_POST["idarticulo"]);
				//Codificar el resultado utilizando json
				echo json_encode($rspta);
			break;

			case 'listarDetalle':
				//Recibimos el idingreso
				$id = $_GET['id'];

				$rspta = $venta->listarDetalle($id);
				$total = 0;
				echo '<thead style="background-color:#A9D0F5">
						<th>Opciones</th><th>Artículo</th><th>Cantidad</th><th>Precio Venta</th><th>Descuento</th><th>Subtotal</th>
				</thead>';

				while ($reg = $rspta->fetch_object()) {
					echo '<tr class="filas">
						<td></td>
						<td>' . $reg->nombre . '</td>
						<td>' . $reg->cantidad . '</td>
						<td>' . $reg->precio_venta . '</td>
						<td>' . $reg->descuento . '</td>
						<td>' . $reg->subtotal . '</td>
					</tr>';
					$total = $total + (($reg->precio_venta * $reg->cantidad) - $reg->descuento);
				}
				echo '<tfoot>						
					<th></th><th></th><th></th><th></th><th>TOTAL</th><th><h4 id="total">S/.' . $total . '</h4></th> 
				</tfoot>';
			break;

			case 'listar':
				$rspta = $venta->listar($_GET['fecha_filtro']);
				//Vamos a declarar un array
				$data = array();

				while ($reg = $rspta->fetch_object()) {

					if ($reg->tipo_comprobante == 'Ticket') {	$url = '../reportes/exTicket_v2.php?idventa='; } else { $url = '../reportes/exFactura.php?id='; }

					$data[] = array(
						"0" => '<button class="btn btn-warning btn-sm" onclick="ver_editar(' . $reg->idventa . ')" data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-fw fa-pencil"></i></button>'.
						' <button class="btn btn-info btn-sm" onclick="detalle_x_comprobante(' . $reg->idventa . ')" data-toggle="tooltip" data-placement="top" title="ver"><i class="fa fa-eye"></i></button>'.
						(($reg->estado == 'Aceptado') ?  ' <button class="btn btn-danger btn-sm" onclick="anular(' . $reg->idventa . ')" data-toggle="tooltip" data-placement="top" title="Anular"><i class="fa fa-close"></i></button>' :	'') .
							'<a target="_blank" href="' . $url . $reg->idventa . '"> <button class="btn bg-purple btn-sm" data-toggle="tooltip" data-placement="top" title="Imprimir"><i class="fa fa-file"></i></button></a>',
						"1" => $reg->fecha,
						"2" => $reg->cliente,
						"3" => $reg->tipo_comprobante,
						"4" => $reg->serie_comprobante . '-' . $reg->num_comprobante,
						"5" => $reg->total_venta,
						"6" => $reg->utilidad,
						"7" => ($reg->estado == 'Aceptado') ? '<span class="label bg-green">Aceptado</span>' : '<span class="label bg-red">Anulado</span>',
						"8" => $toltip
					);
				}
				$results = array(
					"sEcho" => 1, //Información para el datatables
					"iTotalRecords" => count($data), //enviamos el total registros al datatable
					"iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
					"aaData" => $data
				);
				echo json_encode($results);

			break;

			case 'selectCliente':
				$rspta = $persona->listarC();
				while ($reg = $rspta->fetch_object()) {		echo '<option value=' . $reg->idpersona . '>' . $reg->nombre . '</option>';	}
			break;

			case 'listarArticulosVenta':				

				$rspta = $articulo->listarActivosVenta();
				//Vamos a declarar un array
				$data = array();

				while ($reg = $rspta->fetch_object()) {
					
					$img_parametro = "producto-sin-foto.svg"; $img = "";  $clas_stok = "";
  
					if (empty($reg->imagen)) { $img = '../files/articulos/producto-sin-foto.svg'; } else {	$img = '../files/articulos/' . $reg->imagen;	$img_parametro = $reg->imagen; }

					$data[] = array(
						"0" => '<button class="btn btn-warning" onclick="agregarDetalle(' . $reg->idarticulo .  ',' . $reg->precio_venta .  ',' . $reg->precio_compra . ',\'' . $img_parametro . '\')"><span class="fa fa-plus"></span></button>',
						"1" => '<div class="user-block">
						<img class="img-circle" src="' . $img . '" alt="User Image">
						<span class="username"><a href="#">'.$reg->nombre.'</a></span>
						<!-- <span class="description"><b>Categoria: </b>'.$reg->categoria.'</span> -->
						</div>',
						"2" => $reg->categoria,
						"3" => $reg->codigo,
						"4" => $reg->stock,
						"5" => $reg->precio_venta,
					);
				}
				$results = array(
					"sEcho" => 1, //Información para el datatables
					"iTotalRecords" => count($data), //enviamos el total registros al datatable
					"iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
					"aaData" => $data
				);
				echo json_encode($results);
			break;
		}
		//Fin de las validaciones de acceso
	} else {
		require 'noacceso.php';
	}
}
ob_end_flush();
