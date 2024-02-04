<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

Class Ingreso
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	//Implementamos un método para insertar registros
	public function insertar($idproveedor, $idusuario, $tipo_comprobante, $serie_comprobante, $num_comprobante, $fecha_hora, $impuesto, 
	$subtotal_compra, $igv_compra, $total_compra, $total_utilidad,
	$unidad_medida, $idarticulo, $cantidad, $precio_caja, $precio_compra, $precio_venta, $subtotal_pr, $utilidad_xp, $utilidad_tp )	{

		$sql_1 = "SELECT i.tipo_comprobante, i.serie_comprobante, i.num_comprobante, DATE_FORMAT(i.fecha_hora, '%d/%m/%Y') as fecha, i.impuesto, i.descuento, i.total_compra, i.comprobante, i.estado, 
		DATE_FORMAT(i.created_at, '%d/%m/%Y %h:%i:%s %p') as created_at, p.nombre as proveedor, p.tipo_documento, p.num_documento
		FROM ingreso as i	INNER JOIN persona as p ON p.idpersona = i.idproveedor 
		WHERE  p.num_documento= (SELECT num_documento FROM persona WHERE idpersona = '$idproveedor') AND tipo_comprobante = '$tipo_comprobante' AND serie_comprobante ='$serie_comprobante' AND num_comprobante ='$num_comprobante';";
		$val_compra = ejecutarConsultaArray($sql_1);

		if ( empty($val_compra) ) {
			$sql="INSERT INTO ingreso (idproveedor,idusuario,tipo_comprobante,serie_comprobante,num_comprobante,fecha_hora,impuesto, subtotal, igv,  total_compra, utilidad_p)
			VALUES ('$idproveedor','$idusuario','$tipo_comprobante','$serie_comprobante','$num_comprobante','$fecha_hora','$impuesto', '$subtotal_compra', '$igv_compra', '$total_compra', '$total_utilidad')";		
			$idingresonew=ejecutarConsulta_retornarID($sql);

			$ii=0;	

			while ($ii < count($idarticulo))	{
				$sql_detalle = "INSERT INTO detalle_ingreso(idingreso, idarticulo, idunida_medida, cantidad, precio_compra, precio_venta, subtotal, cantidad_x_um, precio_x_um, utilidad_pxp, utilidad_ptp) VALUES 
				('$idingresonew', '$idarticulo[$ii]', '$unidad_medida[$ii]', '1','$precio_caja[$ii]','$precio_venta[$ii]', '$subtotal_pr[$ii]', '$cantidad[$ii]', '$precio_compra[$ii]', '$utilidad_xp[$ii]', '$utilidad_tp[$ii]')";
				ejecutarConsulta($sql_detalle);

				// Aumentamos el STOCK 
				$sql_producto = "UPDATE articulo SET stock = stock + '$cantidad[$ii]', precio_compra = '$precio_compra[$ii]', precio_venta = '$precio_venta[$ii]' WHERE idarticulo = '$idarticulo[$ii]'";
				ejecutarConsulta($sql_producto);

				$ii=$ii + 1;
			}

			return  $sw = array( 'status' => true, 'message' => 'todo ok ejecutarConsulta', 'data' => [], 'id_tabla' => '' );
		} else {
			$info_repetida = '';
	
			foreach ($val_compra as $key => $val) {
			$info_repetida .= '<li class="text-left font-size-13px">
			<span class="font-size-18px text-danger"><b >'.$val['tipo_comprobante'].': </b> '.$val['serie_comprobante'].'</span><br>
			<b>Proveedor: </b>'.$val['proveedor'].'<br>	
			<b>'.$val['tipo_documento'].'</b>: '.$val['num_documento'].'<br>		
			<b>Fecha: </b>'.($val['fecha']).'<br>	
			<b>Total compra: </b>'.number_format( floatval($val['total_compra']), 2, '.',',' ).'<br>		
			<b>Papelera: </b>'.( $val['estado']==0 ? '<i class="fa fa-check text-success"></i> SI':'<i class="fa fa-times text-danger"></i> NO') .' <br>	
			<b>Creado el: </b>'.($val['created_at']).'<br>	
			<hr class="m-t-2px m-b-2px">
			</li>';
			}
			return $sw = array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ol>'.$info_repetida.'</ol>', 'id_tabla' => '' );
		}	
	}

	//Implementamos un método para anular categorías
	public function editar( $idingreso, $idproveedor, $idusuario, $tipo_comprobante, $serie_comprobante, $num_comprobante, $fecha_hora, $impuesto, 
	$subtotal_compra, $igv_compra, $total_compra, $total_utilidad,
	$unidad_medida, $idarticulo, $cantidad, $precio_caja, $precio_compra, $precio_venta, $subtotal_pr, $utilidad_xp, $utilidad_tp ){
		
		$sql_1 = "UPDATE ingreso SET idproveedor='$idproveedor', idusuario='$idusuario', tipo_comprobante='$tipo_comprobante', serie_comprobante='$serie_comprobante', 
		num_comprobante='$num_comprobante', fecha_hora='$fecha_hora', impuesto='$impuesto', descuento='0', subtotal='$subtotal_compra', 
		igv='$igv_compra', total_compra='$total_compra', utilidad_p='$total_utilidad' WHERE  idingreso='$idingreso'";
		ejecutarConsulta($sql_1);

		// quitamos el STOCK
		$sql_0="SELECT * FROM detalle_ingreso WHERE idingreso = '$idingreso' ";
		$detalle = ejecutarConsultaArray($sql_0);

		foreach ($detalle as $key => $val) {			
			$sql_producto = "UPDATE articulo SET stock = stock - '".$val['cantidad']."' WHERE idarticulo = '".$val['idarticulo']."'";
			ejecutarConsulta($sql_producto);
		}		

		// Eliminamos el Detalle		
		$sql_0="DELETE FROM detalle_ingreso WHERE idingreso = '$idingreso' ";
		ejecutarConsulta($sql_0);

		$ii=0;	

		while ($ii < count($idarticulo))	{
			$sql_detalle = "INSERT INTO detalle_ingreso(idingreso, idarticulo, idunida_medida, cantidad, precio_compra, precio_venta, subtotal, cantidad_x_um, precio_x_um, utilidad_pxp, utilidad_ptp) VALUES 
			('$idingreso', '$idarticulo[$ii]', '$unidad_medida[$ii]', '1','$precio_caja[$ii]','$precio_venta[$ii]', '$subtotal_pr[$ii]', '$cantidad[$ii]', '$precio_compra[$ii]', '$utilidad_xp[$ii]', '$utilidad_tp[$ii]')";
			ejecutarConsulta($sql_detalle);

			// Aumentamos el STOCK 
			$sql_producto = "UPDATE articulo SET stock = stock + '$cantidad[$ii]', precio_compra = '$precio_compra[$ii]', precio_venta = '$precio_venta[$ii]' WHERE idarticulo = '$idarticulo[$ii]'";
			ejecutarConsulta($sql_producto);

			$ii=$ii + 1;
		}

		return  $sw = array( 'status' => true, 'message' => 'todo ok ejecutarConsulta', 'data' => [], 'id_tabla' => '' );
	}
	
	//Implementamos un método para anular categorías
	public function anular($idingreso){
		$sql_0="SELECT * FROM detalle_ingreso WHERE idingreso ='$idingreso';";
		$detalle = ejecutarConsultaArray($sql_0);

		foreach ($detalle as $key => $val) {
			// Reducimos el STOCK
			$sql_producto = "UPDATE articulo SET stock = stock - '".$val['cantidad_x_um']."' WHERE idarticulo = '".$val['idarticulo']."'";
			ejecutarConsulta($sql_producto);
		}
		$sql="UPDATE ingreso SET estado='Anulado' WHERE idingreso='$idingreso'";
		return ejecutarConsulta($sql);
	}

	public function compra_editar($idingreso)	{

		$sql="SELECT i.idingreso, DATE(i.fecha_hora) as fecha, i.idproveedor, UPPER( i.tipo_comprobante) as tipo_comprobante, UPPER(i.serie_comprobante) as serie_comprobante, 
		i.num_comprobante, i.total_compra, i.impuesto, i.estado,	p.nombre as proveedor, p.direccion, p.tipo_documento, p.num_documento, u.idusuario, u.nombre as usuario
		FROM ingreso i 
		INNER JOIN persona p ON i.idproveedor=p.idpersona 
		INNER JOIN usuario u ON i.idusuario=u.idusuario
		WHERE i.idingreso='$idingreso'";
		$persona = ejecutarConsultaSimpleFila($sql);

		$sql1 = "SELECT dv.idarticulo, dv.iddetalle_ingreso, dv.cantidad, dv.precio_compra, dv.precio_venta, dv.cantidad_x_um, dv.precio_x_um, dv.subtotal,
		a.nombre as articulo, um.nombre as unida_medida
		FROM detalle_ingreso as dv 
		INNER JOIN ingreso AS i ON i.idingreso = dv.idingreso
		INNER JOIN articulo AS a ON a.idarticulo = dv.idarticulo
		INNER JOIN unida_medida AS um ON um.idunida_medida = dv.idunida_medida WHERE dv.idingreso='$idingreso'";
		$detalle = ejecutarConsultaArray($sql1);

		$sql3 = "SELECT * FROM unida_medida  WHERE estado = '1';";
		$detalle_um = ejecutarConsultaArray($sql3); 

		$array_um_id = []; $array_um = []; $um_html_option = ""; $array_um_name = [];
		foreach ($detalle_um as $key => $val2) { array_push($array_um_id, $val2['idunida_medida'] ); }
		foreach ($detalle_um as $key => $val2) { $array_um[] = [ 'id' => $val2['idunida_medida'], 'nombre' => $val2['nombre'], 'abreviatura' => $val2['abreviatura'] ]; }
		foreach ($detalle_um as $key => $val2) { $um_html_option .= '<option value="'.$val2['idunida_medida'].'"  >'.$val2['nombre'].'</option>'; }
		foreach ($detalle_um as $key => $val2) { array_push($array_um_name, $val2['nombre'] ); }

		return $retorno = [
			"status" 	=> true, 
			"message" => 'todo oka', 
			"data" 		=>  [
				"persona" 	=> $persona, 
				"detalle" 	=> $detalle, 
				'um'          => [
					'id_um'        	=> $array_um_id,
					'um'          	=> $array_um_name,
					'array_ums'    	=> $array_um,
					'um_html_option'=> $um_html_option,
				]
			] 
		] ;
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idingreso)
	{
		$sql="SELECT i.idingreso,DATE(i.fecha_hora) as fecha,i.idproveedor,i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,i.total_compra,i.impuesto,i.estado,		
		p.nombre as proveedor,u.idusuario,u.nombre as usuario,
		FROM ingreso i 
		INNER JOIN persona p ON i.idproveedor=p.idpersona 
		INNER JOIN usuario u ON i.idusuario=u.idusuario 
		WHERE i.idingreso='$idingreso'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function optener_producto_compra($id)	{
		$sql="SELECT * FROM articulo WHERE idarticulo ='$id'";
		$producto = ejecutarConsultaSimpleFila($sql);

		$sql3 = "SELECT * FROM unida_medida  WHERE estado = '1';";
		$detalle_um = ejecutarConsultaArray($sql3); 

		$array_um_id = []; $array_um = []; $um_html_option = ""; $array_um_name = [];
		foreach ($detalle_um as $key => $val2) { array_push($array_um_id, $val2['idunida_medida'] ); }
		foreach ($detalle_um as $key => $val2) { $array_um[] = [ 'id' => $val2['idunida_medida'], 'nombre' => $val2['nombre'], 'abreviatura' => $val2['abreviatura'] ]; }
		foreach ($detalle_um as $key => $val2) { $um_html_option .= '<option value="'.$val2['idunida_medida'].'"  >'.$val2['nombre'].'</option>'; }
		foreach ($detalle_um as $key => $val2) { array_push($array_um_name, $val2['nombre'] ); }

		return $retorno = [
			"status" => true, "message" => 'todo oka', 
			"data" => [
				'producto'    => $producto,
        'um'          => [
					'id_um'        	=> $array_um_id,
					'um'          	=> $array_um_name,
					'array_ums'    	=> $array_um,
					'um_html_option'=> $um_html_option,
				]
			]
		] ;
	}

	public function listarDetalle($idingreso)	{
		$sql="SELECT di.*, a.nombre as articulo, a.imagen, um.nombre as um
		FROM detalle_ingreso di 
		inner join articulo a on di.idarticulo=a.idarticulo 
		inner join unida_medida um on um.idunida_medida=di.idunida_medida 
		where di.idingreso='$idingreso'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para listar los registros
	public function listar()	{
		$sql="SELECT i.idingreso,DATE(i.fecha_hora) as fecha,i.idproveedor,p.nombre as proveedor,u.idusuario,u.nombre as usuario,i.tipo_comprobante,i.serie_comprobante,
		i.num_comprobante,i.total_compra,i.impuesto,i.estado 
		FROM ingreso i 
		INNER JOIN persona p ON i.idproveedor=p.idpersona 
		INNER JOIN usuario u ON i.idusuario=u.idusuario ORDER BY i.fecha_hora desc, estado desc";
		return ejecutarConsulta($sql);		
	}
	
	public function ingresocabecera($idingreso){
		$sql="SELECT i.idingreso,i.idproveedor,p.nombre as proveedor,p.direccion,p.tipo_documento,p.num_documento,p.email,p.telefono,i.idusuario,u.nombre as usuario,i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,date(i.fecha_hora) as fecha,i.impuesto,i.total_compra FROM ingreso i INNER JOIN persona p ON i.idproveedor=p.idpersona INNER JOIN usuario u ON i.idusuario=u.idusuario WHERE i.idingreso='$idingreso'";
		return ejecutarConsulta($sql);
	}

	public function ingresodetalle($idingreso){
		$sql="SELECT a.nombre as articulo,a.codigo,d.cantidad,d.precio_compra,d.precio_venta,(d.cantidad*d.precio_compra) as subtotal FROM detalle_ingreso d INNER JOIN articulo a ON d.idarticulo=a.idarticulo WHERE d.idingreso='$idingreso'";
		return ejecutarConsulta($sql);
	}

	public function unidad_medida()	{
		$sql3 = "SELECT * FROM unida_medida  WHERE estado = '1';";
		$detalle_um = ejecutarConsultaArray($sql3); if ($detalle_um['status'] == false) { return  $detalle_um;}

		$array_marca_id = []; $array_marca = []; $marca_html_option = ""; $array_marca_name = [];

		if ( empty($detalle_um['data']) ) { 
			array_push($array_marca_id, '1' );
			$array_marca[] = [ 'id' => 1, 'nombre' => 'SIN UNIDAD MEDIDA', 'selected' => 'selected' ];
			$marca_html_option = '<option value="SIN UNIDAD MEDIDA" selected >SIN UNIDAD MEDIDA</option>';
			array_push($array_marca_name, 'SIN UNIDAD MEDIDA' );
		} else { 
			foreach ($detalle_um['data'] as $key => $val2) { array_push($array_marca_id, $val2['idunida_medida'] ); }
			foreach ($detalle_um['data'] as $key => $val2) { $array_marca[] = [ 'id' => $val2['idunida_medida'], 'nombre' => $val2['nombre'], 'abreviatura' => $val2['abreviatura'] ]; }
			foreach ($detalle_um['data'] as $key => $val2) { $marca_html_option .= '<option value="'.$val2['idunida_medida'].'"  >'.$val2['nombre_marca'].'</option>'; }
			foreach ($detalle_um['data'] as $key => $val2) { array_push($array_marca_name, $val2['nombre_marca'] ); }
		}

		return $retorno = [
			"status" => true, "message" => 'todo oka', 
			"data" => [
				'id_marca'        => $array_marca_id,
        'marcas'          => $array_marca_name,
        'array_marcas'    => $array_marca,
        'marca_html_option'=> $marca_html_option,
			]
		] ;
	}
}

?>