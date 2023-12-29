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
		require_once "../modelos/Unidad_medida.php";

		$categoria = new UnidadMedida();

		date_default_timezone_set('America/Lima'); $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../dist/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-toggle="tooltip"]\').tooltip(); }); </script>';

		$idunida_medida = isset($_POST["idunida_medida"]) ? limpiarCadena($_POST["idunida_medida"]) : "";
		$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
		$descripcion = isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':
				if (empty($idunida_medida)) {
					$rspta = $categoria->insertar($nombre, $descripcion);
					echo $rspta ? "Unidad de medida registrada" : "Unidad de medida no se pudo registrar";
				} else {
					$rspta = $categoria->editar($idunida_medida, $nombre, $descripcion);
					echo $rspta ? "Unidad de medida actualizada" : "Unidad de medida no se pudo actualizar";
				}
				break;

			case 'desactivar':
				$rspta = $categoria->desactivar($idunida_medida);
				echo $rspta ? "Unidad de medida Desactivada" : "Unidad de medida no se puede desactivar";
				break;

			case 'activar':
				$rspta = $categoria->activar($idunida_medida);
				echo $rspta ? "Unidad de medida activada" : "Unidad de medida no se puede activar";
				break;

			case 'mostrar':
				$rspta = $categoria->mostrar($idunida_medida);
				//Codificar el resultado utilizando json
				echo json_encode($rspta);
				break;

			case 'listar':
				$rspta = $categoria->listar();
				//Vamos a declarar un array
				$data = array();

				while ($reg = $rspta->fetch_object()) {
					$data[] = array(
						"0" => '<button class="btn btn-warning btn-sm" disabled data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-pencil"></i></button>' .
						(($reg->estado) ? ' <button class="btn btn-danger btn-sm" onclick="desactivar(' . $reg->idunida_medida . ')" data-toggle="tooltip" data-placement="top" title="Desactivar"><i class="fa fa-close"></i></button>' :							 
							' <button class="btn btn-primary btn-sm" onclick="activar(' . $reg->idunida_medida . ')" data-toggle="tooltip" data-placement="top" title="Activar"><i class="fa fa-check"></i></button>'),
						"1" => $reg->nombre,
						"2" => $reg->abreviatura,
						"3" => $reg->equivalencia,
						"4" => (($reg->estado) ? '<span class="label bg-green">Activado</span>' :	'<span class="label bg-red">Desactivado</span>') . $toltip
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
