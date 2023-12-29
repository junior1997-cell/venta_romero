<?php
ob_start();

if (strlen(session_id()) < 1) {	session_start(); }//Validamos si existe o no la sesión

if (!isset($_SESSION["nombre"])) {
	header("Location: ../vistas/login.html"); //Validamos el acceso solo a los usuarios logueados al sistema.
} else {
	//Validamos el acceso solo al usuario logueado y autorizado.
	if ($_SESSION['compras'] == 1) {

		require_once "../modelos/Ingreso.php";
		require_once "../modelos/Persona.php";				

		$ingreso = new Ingreso();
		$persona = new Persona();

		date_default_timezone_set('America/Lima'); $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../dist/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-toggle="tooltip"]\').tooltip(); }); </script>';

		$idingreso 					= isset($_POST["idingreso"]) ? limpiarCadena($_POST["idingreso"]) : "";
		$idproveedor 				= isset($_POST["idproveedor"]) ? limpiarCadena($_POST["idproveedor"]) : "";
		$idusuario 					= $_SESSION["idusuario"];
		$tipo_comprobante 	= isset($_POST["tipo_comprobante"]) ? limpiarCadena($_POST["tipo_comprobante"]) : "";
		$serie_comprobante 	= isset($_POST["serie_comprobante"]) ? limpiarCadena($_POST["serie_comprobante"]) : "";
		$num_comprobante 		= isset($_POST["num_comprobante"]) ? limpiarCadena($_POST["num_comprobante"]) : "";
		$fecha_hora 				= isset($_POST["fecha_hora"]) ? limpiarCadena($_POST["fecha_hora"]) : "";
		$impuesto 					= isset($_POST["impuesto"]) ? limpiarCadena($_POST["impuesto"]) : "";
		$total_compra 			= isset($_POST["total_compra"]) ? limpiarCadena($_POST["total_compra"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':
				if (empty($idingreso)) {
					$rspta = $ingreso->insertar($idproveedor, $idusuario, $tipo_comprobante, $serie_comprobante, $num_comprobante, $fecha_hora, $impuesto, $total_compra, 
					$_POST["unidad_medida"],$_POST["idarticulo"], $_POST["cantidad"], $_POST["precio_compra"], $_POST["precio_venta"], $_POST["subtotal_pr"], 
					$_POST["cantidad_x_um"], $_POST["precio_x_um"]);
					echo $rspta ? "Ingreso registrado" : "No se pudieron registrar todos los datos del ingreso";
				} else {
				}
			break;

			case 'anular':
				$rspta = $ingreso->anular($idingreso);
				echo $rspta ? "Ingreso anulado" : "Ingreso no se puede anular";
			break;

			case 'mostrar':
				$rspta = $ingreso->mostrar($idingreso);
				//Codificar el resultado utilizando json
				echo json_encode($rspta);
			break;

			case 'optener_producto_compra':
				$rspta = $ingreso->optener_producto_compra($_POST["idarticulo"]);
				//Codificar el resultado utilizando json
				echo json_encode($rspta);
			break;

			case 'mostrar_unidad_medida':
				$rspta = $ingreso->unidad_medida();
				//Codificar el resultado utilizando json
				echo json_encode($rspta);
			break;

			case 'listarDetalle':
				//Recibimos el idingreso
				$id = $_GET['id'];

				$rspta = $ingreso->listarDetalle($id);
				$total = 0;
				echo '<thead style="background-color:#A9D0F5">
						<th>Opciones</th>
						<th>Artículo</th>
						<th>Cantidad</th>
						<th>Precio Compra</th>
						<th>Precio Venta</th>
						<th>Subtotal</th>
				</thead>';

				while ($reg = $rspta->fetch_object()) {
					echo '<tr class="filas"><td></td><td>' . $reg->nombre . '</td><td>' . $reg->cantidad . '</td><td>' . $reg->precio_compra . '</td><td>' . $reg->precio_venta . '</td><td>' . $reg->precio_compra * $reg->cantidad . '</td></tr>';
					$total = $total + ($reg->precio_compra * $reg->cantidad);
				}
				echo '<tfoot>
						<th>TOTAL</th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th><h4 id="total">S/.' . $total . '</h4><input type="hidden" name="total_compra" id="total_compra"></th> 
				</tfoot>';
			break;

			case 'listar':
				$rspta = $ingreso->listar();
				//Vamos a declarar un array
				$data = array();

				while ($reg = $rspta->fetch_object()) {
					$data[] = array(
						"0" => '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->idingreso . ')"><i class="fa fa-eye"></i></button>' .
						(($reg->estado == 'Aceptado') ?  ' <button class="btn btn-danger btn-sm" onclick="anular(' . $reg->idingreso . ')"><i class="fa fa-close"></i></button>' :'') .
						'<a target="_blank" href="../reportes/exIngreso.php?id=' . $reg->idingreso . '"> <button class="btn btn-info btn-sm"><i class="fa fa-file"></i></button></a>',
						"1" => $reg->fecha,
						"2" => $reg->proveedor,
						"3" => $reg->usuario,
						"4" => $reg->tipo_comprobante,
						"5" => $reg->serie_comprobante . '-' . $reg->num_comprobante,
						"6" => $reg->total_compra,
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
				echo json_encode($results);

			break;

			case 'selectProveedor':
				$rspta = $persona->listarP();
				while ($reg = $rspta->fetch_object()) {
					echo '<option value=' . $reg->idpersona . '>' . $reg->nombre . '</option>';
				}
			break;

			case 'listarArticulos':
				require_once "../modelos/Articulo.php";
				$articulo = new Articulo();

				$rspta = $articulo->listarActivos();
				//Vamos a declarar un array
				$data = array();

				while ($reg = $rspta->fetch_object()) {
					$img_parametro = "producto-sin-foto.svg"; $img = "";  $clas_stok = "";
  
					if (empty($reg->imagen)) {
						$img = '../files/articulos/producto-sin-foto.svg';
					} else {
						$img = '../files/articulos/' . $reg->imagen;
						$img_parametro = $reg->imagen;
					}
					$data[] = array(
						"0" => '<button class="btn btn-warning btn-add-pr-' . $reg->idarticulo . '" onclick="agregarDetalle(' . $reg->idarticulo . ',\'' . $img_parametro .  '\')"><span class="fa fa-plus"></span></button>',
						"1" => '<div class="user-block">
						<img class="img-circle" src="' . $img . '" alt="User Image">
						<span class="username"><a href="#">'.$reg->nombre.'</a></span>
						<!-- <span class="description"><b>Categoria: </b>'.$reg->categoria.'</span> -->
						</div>',
						"2" => $reg->categoria,						
						"3" => $reg->stock,		
						"4" => $reg->codigo,				
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
