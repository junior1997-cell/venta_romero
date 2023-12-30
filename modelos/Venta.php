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
	public function insertar($idcliente,$idusuario,$tipo_comprobante, $observacion, $fecha_hora,$impuesto,$total_venta,
	$idarticulo,$cantidad,$precio_venta, $precio_compra, $descuento, $subtotal)	{

		$sql="INSERT INTO venta (idcliente,idusuario,tipo_comprobante,serie_comprobante,num_comprobante,fecha_hora,impuesto,total_venta, observacion, estado)
		VALUES ('$idcliente','$idusuario','$tipo_comprobante',(SELECT serie FROM catalogo1 WHERE idcatalogo1 = 1), (SELECT (numero + 1) as numero FROM catalogo1 WHERE idcatalogo1 = 1),
		'$fecha_hora','$impuesto','$total_venta', '$observacion', 'Aceptado')";		
		$idventanew=ejecutarConsulta_retornarID($sql);

		// actualizamos el correlatvo
		$sql="UPDATE catalogo1 SET numero=numero+1 WHERE idcatalogo1='1'";
		ejecutarConsulta($sql);

		$ii=0;
		$sw=true;

		while ($ii < count($idarticulo))		{
			$sql_detalle = "INSERT INTO detalle_venta(idventa, idarticulo, cantidad, precio_venta, precio_compra, descuento, subtotal) VALUES 
			('$idventanew', '$idarticulo[$ii]', '$cantidad[$ii]', '$precio_venta[$ii]', '$precio_compra[$ii]', '$descuento[$ii]', '$subtotal[$ii]')";
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
		$sql_0="SELECT * FROM detalle_venta WHERE idventa ";
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
		$sql="SELECT v.idventa,DATE(v.fecha_hora) as fecha,v.idcliente,p.nombre as cliente,u.idusuario,u.nombre as usuario,v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,v.total_venta,v.impuesto,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN usuario u ON v.idusuario=u.idusuario WHERE v.idventa='$idventa'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listarDetalle($idventa)
	{
		$sql="SELECT dv.idventa,dv.idarticulo,a.nombre,dv.cantidad,dv.precio_venta,dv.descuento,(dv.cantidad*dv.precio_venta-dv.descuento) as subtotal FROM detalle_venta dv inner join articulo a on dv.idarticulo=a.idarticulo where dv.idventa='$idventa'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para listar los registros
	public function listar()
	{
		$sql="SELECT v.idventa,DATE(v.fecha_hora) as fecha,v.idcliente,p.nombre as cliente,u.idusuario,u.nombre as usuario,v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,v.total_venta,v.impuesto,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN usuario u ON v.idusuario=u.idusuario ORDER by v.idventa desc";
		return ejecutarConsulta($sql);		
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