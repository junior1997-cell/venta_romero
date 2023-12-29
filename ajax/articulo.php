<?php
ob_start();
if (strlen(session_id()) < 1) {
	session_start(); //Validamos si existe o no la sesión
}
if (!isset($_SESSION["nombre"])) {
	header("Location: ../vistas/login.html"); //Validamos el acceso solo a los usuarios logueados al sistema.
} else {
	//Validamos el acceso solo al usuario logueado y autorizado.
	if ($_SESSION['almacen'] == 1) {
		require_once "../modelos/Articulo.php";

		$articulo = new Articulo();

		date_default_timezone_set('America/Lima'); $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../dist/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-toggle="tooltip"]\').tooltip(); }); </script>';

		$idarticulo 	= isset($_POST["idarticulo"]) ? limpiarCadena($_POST["idarticulo"]) : "";
		$idcategoria 	= isset($_POST["idcategoria"]) ? limpiarCadena($_POST["idcategoria"]) : "";
		$codigo 			= isset($_POST["codigo"]) ? limpiarCadena($_POST["codigo"]) : "";		
		$nombre 			= isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
		$stock 				= isset($_POST["stock"]) ? limpiarCadena($_POST["stock"]) : "";
		$descripcion 	= isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) : "";
		$imagen 			= isset($_POST["imagen"]) ? limpiarCadena($_POST["imagen"]) : "";

		$precio_compra= isset($_POST["precio_compra"]) ? limpiarCadena($_POST["precio_compra"]) : "";
		$precio_venta = isset($_POST["precio_venta"]) ? limpiarCadena($_POST["precio_venta"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':

				if (!file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name'])) {
					$imagen = $_POST["imagenactual"];
				} else {
					$ext = explode(".", $_FILES["imagen"]["name"]);					
					$imagen = $date_now .'__'. random_int(0, 20) . round(microtime(true)) . random_int(21, 41) . '.' . end($ext);
					move_uploaded_file($_FILES["imagen"]["tmp_name"], "../files/articulos/" . $imagen);					
				}
				if (empty($idarticulo)) {
					$rspta = $articulo->insertar($idcategoria, $codigo, $nombre, $stock, $descripcion, $imagen, round($precio_compra, 2), round($precio_venta, 2));
					echo $rspta ? "Artículo registrado" : "Artículo no se pudo registrar";
				} else {
					$rspta = $articulo->editar($idarticulo, $idcategoria, $codigo, $nombre, $stock, $descripcion, $imagen, $precio_compra, $precio_venta);
					echo $rspta ? "Artículo actualizado" : "Artículo no se pudo actualizar";
				}
				break;

			case 'desactivar':
				$rspta = $articulo->desactivar($idarticulo);
				echo $rspta ? "Artículo Desactivado" : "Artículo no se puede desactivar";
				break;

			case 'activar':
				$rspta = $articulo->activar($idarticulo);
				echo $rspta ? "Artículo activado" : "Artículo no se puede activar";
				break;

			case 'mostrar':
				$rspta = $articulo->mostrar($idarticulo);
				//Codificar el resultado utilizando json
				echo json_encode($rspta);
				break;

			case 'listar':
				$rspta = $articulo->listar();
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
						"0" => '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->idarticulo . ')" data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-pencil"></i></button>' .
						($reg->condicion ? ' <button class="btn btn-danger btn-sm" onclick="desactivar(' . $reg->idarticulo . ')" data-toggle="tooltip" data-placement="top" title="Desactivar"><i class="fa fa-close"></i></button>' :
							' <button class="btn btn-primary btn-sm" onclick="activar(' . $reg->idarticulo . ')" data-toggle="tooltip" data-placement="top" title="Activar"><i class="fa fa-check"></i></button>'),
						"1" =>  '<div class="user-block">
							<img class="img-circle" src="' . $img . '" alt="User Image">
							<span class="username"><a href="#">'.$reg->nombre.'</a></span>
							<span class="description"><b>Categoria: </b>'.$reg->categoria.'</span>
						</div>',
						"2" => $reg->codigo,
						"3" => $reg->stock,
						"4" => $reg->precio_compra,
						"5" => $reg->precio_venta,
						"6" => (($reg->condicion) ? '<span class="label bg-green">Activado</span>' :	'<span class="label bg-red">Desactivado</span>') . $toltip
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

			case "selectCategoria":
				require_once "../modelos/Categoria.php";
				$categoria = new Categoria();

				$rspta = $categoria->select();

				while ($reg = $rspta->fetch_object()) {
					echo '<option value=' . $reg->idcategoria . '>' . $reg->nombre . '</option>';
				}
				break;
		}
		//Fin de las validaciones de acceso
	} else {
		require 'noacceso.php';
	}
}
ob_end_flush();
