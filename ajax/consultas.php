<?php
ob_start();

if (strlen(session_id()) < 1) {	session_start(); } //Validamos si existe o no la sesión

if (!isset($_SESSION["nombre"])) {
	header("Location: ../vistas/login.html"); //Validamos el acceso solo a los usuarios logueados al sistema.
} else {
	//Validamos el acceso solo al usuario logueado y autorizado.
	if ($_SESSION['consultac'] == 1 || $_SESSION['consultav'] == 1) {

		require_once "../modelos/Consultas.php";

		$consulta = new Consultas();

		date_default_timezone_set('America/Lima'); $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../dist/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-toggle="tooltip"]\').tooltip(); }); </script>';

		switch ($_GET["op"]) {

			case 'comprasfecha':
				$fecha_inicio = $_REQUEST["fecha_inicio"];
				$fecha_fin = $_REQUEST["fecha_fin"];

				$rspta = $consulta->comprasfecha($fecha_inicio, $fecha_fin);
				//Vamos a declarar un array
				$data = array();

				while ($reg = $rspta->fetch_object()) {
					$data[] = array(
						"0" => $reg->fecha,
						"1" => $reg->usuario,
						"2" => $reg->proveedor,
						"3" => $reg->tipo_comprobante,
						"4" => $reg->serie_comprobante . ' ' . $reg->num_comprobante,
						"5" => $reg->total_compra,
						"6" => $reg->impuesto,
						"7" => ($reg->estado == 'Aceptado') ? '<span class="label bg-green">Aceptado</span>' :
							'<span class="label bg-red">Anulado</span>'
					);
				}
				$results = array(
					"sEcho" => 1, //Información para el datatables
					"iTotalRecords" => count($data), //enviamos el total registros al datatable
					"iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
					"aaData" => $data
				);
				echo json_encode($results, true);

			break;

			case 'ventas_x_comprobante':
				$fecha_inicio = $_REQUEST["fecha_inicio"];
				$fecha_fin = $_REQUEST["fecha_fin"];
				$idcliente = $_REQUEST["idcliente"];

				$rspta = $consulta->ventasfechacliente($fecha_inicio, $fecha_fin, $idcliente);
				//Vamos a declarar un array
				$data = array();

				while ($reg = $rspta->fetch_object()) {
					$data[] = array(
						"0" => '<button class="btn btn-info btn-sm" onclick="detalle_x_comprobante(' . $reg->idventa . ')" data-toggle="tooltip" data-placement="top" title="Ver detalle"><i class="fa fa-eye"></i></button>',
						"1" => $reg->fecha,
						"2" => $reg->cliente,
						"3" => $reg->serie_comprobante . '-' . $reg->num_comprobante,
						"4" => $reg->total_venta,
						"5" => $reg->utilidad ,
						"6" => ($reg->estado == 'Aceptado') ? '<span class="label bg-green">Aceptado</span>' :'<span class="label bg-red">Anulado</span>'
					);
				}
				$results = array(
					"sEcho" => 1, //Información para el datatables
					"iTotalRecords" => count($data), //enviamos el total registros al datatable
					"iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
					"aaData" => $data
				);
				echo json_encode($results, true);

			break;

			case 'ventas_x_producto':
				$fecha_inicio = $_REQUEST["fecha_inicio"];
				$fecha_fin 		= $_REQUEST["fecha_fin"];
				$idcliente 		= $_REQUEST["idcliente"];
				$idcategoria 	= $_REQUEST["idcategoria"];
				$numero 			= $_REQUEST["numero"];

				$rspta = $consulta->ventas_x_producto($fecha_inicio, $fecha_fin, $idcliente, $idcategoria, $numero);
				//Vamos a declarar un array
				$data = array();

				while ($reg = $rspta->fetch_object()) {
					$data[] = array(
						"0" => '<button class="btn btn-info btn-sm" onclick="detalle_x_producto(' . $reg->idventa . ')" data-toggle="tooltip" data-placement="top" title="Ver detalle"><i class="fa fa-eye"></i></button>',
						"1" => $reg->fecha,
						"2" => $reg->cliente,
						"3" => $reg->articulo,
						"4" => $reg->serie_comprobante . '-' . $reg->num_comprobante,
						"5" => $reg->cantidad,
						"6" => $reg->precio_compra,
						"7" => $reg->subtotal ,
						"8" => $reg->utilidad ,
						"9" => $reg->categoria,
						"10" => ($reg->estado == 'Aceptado') ? '<span class="label bg-green">Aceptado</span>' :'<span class="label bg-red">Anulado</span>'
					);
				}
				$results = array(
					"sEcho" => 1, //Información para el datatables
					"iTotalRecords" => count($data), //enviamos el total registros al datatable
					"iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
					"aaData" => $data
				);
				echo json_encode($results, true);

			break;			

			case 'selectCliente':
				$rspta = $consulta->listar_clientes();
				echo '<option value="TODOS">Todos</option>';	
				while ($reg = $rspta->fetch_object()) {		echo '<option value=' . $reg->idpersona . '>' . $reg->nombre . '</option>';	}
			break;

			case 'selectCategoria':
				$rspta = $consulta->listar_categoria();
				echo '<option value="TODOS">Todos</option>';	
				while ($reg = $rspta->fetch_object()) {		echo '<option value=' . $reg->idcategoria . '>' . $reg->nombre . '</option>';	}
			break;

			case 'selectNumero':
				$rspta = $consulta->listar_numero();
				echo '<option value="TODOS">Todos</option>';	
				while ($reg = $rspta->fetch_object()) {		echo '<option value=' . $reg->num_comprobante . '>' . $reg->serie_comprobante .'-'. $reg->num_comprobante . '</option>';	}
			break;
		}
		//Fin de las validaciones de acceso
	} else {
		require 'noacceso.php';
	}
}
ob_end_flush();
