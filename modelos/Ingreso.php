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
	public function insertar($idproveedor,$idusuario,$tipo_comprobante,$serie_comprobante,$num_comprobante,$fecha_hora,$impuesto,$total_compra,
	$unidad_medida, $idarticulo,$cantidad,$precio_compra,$precio_venta, $subtotal_pr, $cantidad_x_um, $precio_x_um )
	{
		$sql="INSERT INTO ingreso (idproveedor,idusuario,tipo_comprobante,serie_comprobante,num_comprobante,fecha_hora,impuesto,total_compra,estado)
		VALUES ('$idproveedor','$idusuario','$tipo_comprobante','$serie_comprobante','$num_comprobante','$fecha_hora','$impuesto','$total_compra','Aceptado')";		
		$idingresonew=ejecutarConsulta_retornarID($sql);

		$ii=0;
		$sw=true;

		while ($ii < count($idarticulo))	{
			$sql_detalle = "INSERT INTO detalle_ingreso(idingreso, idarticulo, idunida_medida, cantidad,precio_compra,precio_venta, subtotal, cantidad_x_um, precio_x_um) VALUES 
			('$idingresonew', '$idarticulo[$ii]', '$unidad_medida[$ii]', '$cantidad[$ii]','$precio_compra[$ii]','$precio_venta[$ii]', '$subtotal_pr[$ii]', '$cantidad_x_um[$ii]', '$precio_x_um[$ii]')";
			ejecutarConsulta($sql_detalle) or $sw = false;

			// Aumentamos el STOCK -- no se usa a pedido de cliente
			// $sql_producto = "UPDATE articulo SET stock = stock + '$cantidad[$ii]', precio_compra = '$precio_compra[$ii]', precio_venta = '$precio_venta[$ii]' WHERE idarticulo = '$idarticulo[$ii]'";
      // ejecutarConsulta($sql_producto);

			$ii=$ii + 1;
		}

		return $sw;
	}

	
	//Implementamos un método para anular categorías
	public function anular($idingreso)
	{
		$sql="UPDATE ingreso SET estado='Anulado' WHERE idingreso='$idingreso'";
		return ejecutarConsulta($sql);
	}


	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idingreso)
	{
		$sql="SELECT i.idingreso,DATE(i.fecha_hora) as fecha,i.idproveedor,p.nombre as proveedor,u.idusuario,u.nombre as usuario,i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,i.total_compra,i.impuesto,i.estado FROM ingreso i INNER JOIN persona p ON i.idproveedor=p.idpersona INNER JOIN usuario u ON i.idusuario=u.idusuario WHERE i.idingreso='$idingreso'";
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
	public function listar()
	{
		$sql="SELECT i.idingreso,DATE(i.fecha_hora) as fecha,i.idproveedor,p.nombre as proveedor,u.idusuario,u.nombre as usuario,i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,i.total_compra,i.impuesto,i.estado FROM ingreso i INNER JOIN persona p ON i.idproveedor=p.idpersona INNER JOIN usuario u ON i.idusuario=u.idusuario ORDER BY i.idingreso desc";
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