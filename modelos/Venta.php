<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

Class Venta
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	//Implementamos un método para insertar registros
	public function insertar_venta($idcliente, $idusuario, $tipo_comprobante, $observacion, $fecha_hora, $subtotal_venta, $descuento_venta, $impuesto, $total_venta, $utilidad_venta,
	$idarticulo,$cantidad,$precio_venta, $precio_compra, $descuento, $subtotal, $utilidad)	{

		$sql="INSERT INTO venta (idcliente,idusuario,tipo_comprobante,serie_comprobante,num_comprobante,fecha_hora, subtotal, descuento, impuesto, total_venta, utilidad, observacion, estado)
		VALUES ('$idcliente','$idusuario','$tipo_comprobante',(SELECT serie FROM catalogo1 WHERE idcatalogo1 = 1), (SELECT (numero + 1) as numero FROM catalogo1 WHERE idcatalogo1 = 1),
		'$fecha_hora', '$subtotal_venta', '$descuento_venta', '$impuesto','$total_venta', '$utilidad_venta', '$observacion', 'Aceptado')";		
		$idventanew=ejecutarConsulta_retornarID($sql);

		// actualizamos el correlatvo
		$sql="UPDATE catalogo1 SET numero=numero+1 WHERE idcatalogo1='1'";
		ejecutarConsulta($sql);

		$ii=0;
		$sw=true;

		while ($ii < count($idarticulo))		{
			$sql_detalle = "INSERT INTO detalle_venta(idventa, idarticulo, cantidad, precio_venta, precio_compra, descuento, subtotal, utilidad) VALUES 
			('$idventanew', '$idarticulo[$ii]', '$cantidad[$ii]', '$precio_venta[$ii]', '$precio_compra[$ii]', '$descuento[$ii]', '$subtotal[$ii]', '$utilidad[$ii]')";
			ejecutarConsulta($sql_detalle) or $sw = false;			

			// Reducimos el STOCK
			$sql_producto = "UPDATE articulo SET stock = stock - '$cantidad[$ii]' WHERE idarticulo = '$idarticulo[$ii]'";
      ejecutarConsulta($sql_producto);

			$ii=$ii + 1;
		}

		return $sw;
	}

	public function editar_venta($idventa, $idcliente, $idusuario, $tipo_comprobante, $observacion, $fecha_hora, $subtotal_venta, $descuento_venta, $impuesto, $total_venta, $utilidad_venta,
	$idarticulo,$cantidad,$precio_venta, $precio_compra, $descuento, $subtotal, $utilidad)	{
		
		$sql="UPDATE venta SET idcliente='$idcliente', idusuario='$idusuario', tipo_comprobante='$tipo_comprobante', fecha_hora='$fecha_hora',
		subtotal='$subtotal_venta',impuesto='$impuesto',descuento='$descuento_venta',total_venta='$total_venta',utilidad='$utilidad_venta',observacion='$observacion' 
		WHERE idventa='$idventa' ";		
		ejecutarConsulta($sql);

		// devolvemos el STOK
		$sql_0="SELECT * FROM detalle_venta WHERE idventa = '$idventa' ";
		$detalle = ejecutarConsultaArray($sql_0);

		foreach ($detalle as $key => $val) {			
			$sql_producto = "UPDATE articulo SET stock = stock + '".$val['cantidad']."' WHERE idarticulo = '".$val['idarticulo']."'";
			ejecutarConsulta($sql_producto);
		}

		// Eliminamos el Detalle		
		$sql_0="DELETE FROM `detalle_venta` WHERE idventa = '$idventa' ";
		ejecutarConsulta($sql_0);

		$ii=0;
		$sw=true;

		while ($ii < count($idarticulo))		{
			$sql_detalle = "INSERT INTO detalle_venta(idventa, idarticulo, cantidad, precio_venta, precio_compra, descuento, subtotal, utilidad) VALUES 
			('$idventa', '$idarticulo[$ii]', '$cantidad[$ii]', '$precio_venta[$ii]', '$precio_compra[$ii]', '$descuento[$ii]', '$subtotal[$ii]', '$utilidad[$ii]')";
			ejecutarConsulta($sql_detalle) or $sw = false;			

			// Reducimos el STOCK
			$sql_producto = "UPDATE articulo SET stock = stock - '$cantidad[$ii]' WHERE idarticulo = '$idarticulo[$ii]'";
      ejecutarConsulta($sql_producto);

			$ii=$ii + 1;
		}

		return $sw;
	}
	
	//Implementamos un método para anular la venta
	public function anular($idventa)	{
		$sql_0="SELECT * FROM detalle_venta WHERE idventa ='$idventa';";
		$detalle = ejecutarConsultaArray($sql_0);

		foreach ($detalle as $key => $val) {
			// Reducimos el STOCK
			$sql_producto = "UPDATE articulo SET stock = stock + '".$val['cantidad']."' WHERE idarticulo = '".$val['idarticulo']."'";
			ejecutarConsulta($sql_producto);
		}
		
		$sql="UPDATE venta SET estado='Anulado' WHERE idventa='$idventa'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idventa)
	{
		$sql="SELECT v.idventa,DATE(v.fecha_hora) as fecha,v.idcliente,p.nombre as cliente,u.idusuario,u.nombre as usuario,v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,v.total_venta,v.impuesto,v.estado 
		FROM venta v 
		INNER JOIN persona p ON v.idcliente=p.idpersona 
		INNER JOIN usuario u ON v.idusuario=u.idusuario WHERE v.idventa='$idventa'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function ver_venta_editar($idventa)
	{
		$sql="SELECT v.idventa, DATE(v.fecha_hora) as fecha, v.idcliente, v.tipo_comprobante, v.serie_comprobante, v.num_comprobante, v.subtotal, v.descuento, v.total_venta, 
		v.impuesto, v.estado, v.observacion,
		p.nombre as cliente, p.tipo_documento, p.num_documento, p.direccion, u.idusuario, u.nombre as usuario
		FROM venta v 
		INNER JOIN persona p ON v.idcliente=p.idpersona 
		INNER JOIN usuario u ON v.idusuario=u.idusuario WHERE v.idventa='$idventa'";
		$persona = ejecutarConsultaSimpleFila($sql);

		$sql="SELECT dv.idventa, dv.idarticulo,  dv.cantidad, dv.precio_compra, dv.precio_venta, dv.descuento, dv.subtotal,
		a.nombre, a.imagen, c.nombre as categoria 
		FROM detalle_venta AS dv 
		INNER JOIN articulo a ON dv.idarticulo=a.idarticulo
		INNER JOIN categoria c ON c.idcategoria=a.idcategoria WHERE dv.idventa='$idventa';";
		$detalle = ejecutarConsultaArray($sql);

		return $retorno = [
			"status" 	=> true, 
			"message" => 'todo oka', 
			"data" 		=>  [
				"persona" 	=> $persona, 
				"detalle" 	=> $detalle, 
			] 
		] ;
	}

	public function listarDetalle($idventa)
	{
		$sql="SELECT dv.idventa,dv.idarticulo,a.nombre,dv.cantidad,dv.precio_venta,dv.descuento,(dv.cantidad*dv.precio_venta-dv.descuento) as subtotal 
		FROM detalle_venta dv 
		inner join articulo a on dv.idarticulo=a.idarticulo where dv.idventa='$idventa'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para listar los registros
	public function listar($fecha_filtro)	{
		$filtro = (empty($fecha_filtro) ? '' :" AND v.fecha_hora ='$fecha_filtro'" );
		$sql="SELECT v.idventa,DATE(v.fecha_hora) as fecha, v.idcliente, v.tipo_comprobante, v.serie_comprobante, v.num_comprobante,v.total_venta,v.impuesto,v.estado, v.utilidad,
		p.nombre as cliente, u.idusuario,u.nombre as usuario
		FROM venta v 
		INNER JOIN persona p ON v.idcliente=p.idpersona 
		INNER JOIN usuario u ON v.idusuario=u.idusuario 
		WHERE v.estado IN ('Anulado', 'Aceptado') $filtro
		ORDER by v.idventa desc";
		return ejecutarConsulta($sql);		
	}


	//Implementar un método para mostrar los datos de un registro a modificar
	public function optener_producto_compra($id)	{
		$sql="SELECT a.*, c.nombre as categoria
		FROM articulo as a
		INNER JOIN categoria as c ON c.idcategoria = a.idcategoria
		WHERE a.idarticulo ='$id'";
		$producto = ejecutarConsultaSimpleFila($sql);
		
		return $retorno = [
			"status" 	=> true, 
			"message" => 'todo oka', 
			"data" 		=>  $producto, 
		] ;
	}

	public function ventacabecera($idventa){
		$sql="SELECT v.idventa,v.idcliente,p.nombre as cliente,p.direccion,p.tipo_documento,p.num_documento,p.email,p.telefono,v.idusuario,u.nombre as usuario,v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,date(v.fecha_hora) as fecha,v.impuesto,v.total_venta FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN usuario u ON v.idusuario=u.idusuario WHERE v.idventa='$idventa'";
		return ejecutarConsulta($sql);
	}

	public function ventadetalle($idventa){
		$sql="SELECT a.nombre as articulo,a.codigo,d.cantidad,d.precio_venta,d.descuento,(d.cantidad*d.precio_venta-d.descuento) as subtotal FROM detalle_venta d INNER JOIN articulo a ON d.idarticulo=a.idarticulo WHERE d.idventa='$idventa'";
		return ejecutarConsulta($sql);
	}
	
}
?>