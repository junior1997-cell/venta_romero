<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

Class Consultas
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	public function comprasfecha($fecha_inicio,$fecha_fin)
	{
		$sql="SELECT DATE(i.fecha_hora) as fecha,u.nombre as usuario, p.nombre as proveedor,i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,i.total_compra,i.impuesto,i.estado 
		FROM ingreso i 
		INNER JOIN persona p ON i.idproveedor=p.idpersona 
		INNER JOIN usuario u ON i.idusuario=u.idusuario 
		WHERE DATE(i.fecha_hora)>='$fecha_inicio' AND DATE(i.fecha_hora)<='$fecha_fin' and i.estado = 'Aceptado'";
		return ejecutarConsulta($sql);		
	}

	public function ventasfechacliente($fecha_inicio,$fecha_fin,$idcliente)	{		

		$filtro_fecha 	=  (!empty($fecha_inicio) && !empty($fecha_fin) ? "AND v.fecha_hora BETWEEN '$fecha_inicio' AND '$fecha_fin'" : (!empty($fecha_inicio) ? "AND v.fecha_hora = '$fecha_inicio'" : (!empty($fecha_fin) ? "AND v.fecha_hora = '$fecha_fin'" : '') ) );
		$filtro_cliente =  (empty($idcliente) 	|| $idcliente 	 == '' || $idcliente 	 	== 'TODOS' ? '' : "AND v.idcliente='$idcliente'" );

		$sql="SELECT v.idventa, DATE(v.fecha_hora) as fecha, u.nombre as usuario, p.nombre as cliente,v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,
		v.total_venta,v.impuesto, v.estado, v.utilidad,
		(select sum(IFNULL(precio_compra, 0) * IFNULL(cantidad, 0)) from detalle_venta where idventa = v.idventa) as precio_compra
		FROM venta v 
		INNER JOIN persona p ON v.idcliente=p.idpersona 
		INNER JOIN usuario u ON v.idusuario=u.idusuario 
		WHERE v.estado = 'Aceptado' $filtro_fecha $filtro_cliente ";
		return ejecutarConsulta($sql);		
	}

	public function ventas_x_producto($fecha_inicio, $fecha_fin, $idcliente, $idcategoria, $numero)	{

		$filtro_f_i 			=  (empty($fecha_inicio)|| $fecha_inicio == '' || $fecha_inicio == 'TODOS' ? '' : "AND DATE(v.fecha_hora)>='$fecha_inicio'" );
		$filtro_f_f 			=  (empty($fecha_fin) 	|| $fecha_fin 	 == '' || $fecha_fin 	 	== 'TODOS' ? '' : "AND DATE(v.fecha_hora)<='$fecha_fin'" );
		$filtro_cliente 	=  (empty($idcliente) 	|| $idcliente 	 == '' || $idcliente 	 	== 'TODOS' ? '' : "AND v.idcliente='$idcliente'" );
		$filtro_categoria =  (empty($idcategoria) || $idcategoria  == '' || $idcategoria  == 'TODOS' ? '' : "AND a.idcategoria='$idcategoria'" );
		$filtro_numero =  (empty($numero) || $numero  == '' || $numero  == 'TODOS' ? '' : "AND v.serie_comprobante ='TK001' AND v.num_comprobante='$numero'" );

		$sql="SELECT v.idventa, DATE(v.fecha_hora) as fecha, v.tipo_comprobante, v.serie_comprobante, v.num_comprobante, v.total_venta, v.estado,
		u.nombre as usuario, p.nombre as cliente, dv.cantidad, dv.precio_compra, dv.precio_venta, dv.descuento, dv.subtotal, dv.utilidad,
		a.nombre as articulo, a.imagen, c.nombre as categoria 
		FROM detalle_venta dv 
		INNER JOIN venta v ON v.idventa=dv.idventa 
		INNER JOIN articulo a ON a.idarticulo=dv.idarticulo 
		INNER JOIN categoria c ON c.idcategoria=a.idcategoria
		INNER JOIN persona p ON v.idcliente=p.idpersona 
		INNER JOIN usuario u ON v.idusuario=u.idusuario
		WHERE v.estado = 'Aceptado' $filtro_f_i $filtro_f_f $filtro_cliente $filtro_categoria $filtro_numero ";
		return ejecutarConsulta($sql);		
	}

	public function ver_detalle_venta($idventa)	{
		$sql_0="SELECT v.*, u.nombre as usuario, p.nombre as cliente
		FROM venta AS v
		INNER JOIN persona p ON v.idcliente=p.idpersona 
		INNER JOIN usuario u ON v.idusuario=u.idusuario 
		WHERE idventa=$idventa;";
		$venta=ejecutarConsultaSimpleFila($sql_0);

		$sql_1="SELECT dv.idventa, dv.idarticulo, dv.cantidad, dv.precio_compra, dv.precio_venta, dv.descuento, dv.subtotal, dv.utilidad,
		a.nombre as producto, a.imagen, c.nombre as categoria
		FROM detalle_venta dv 
		inner join articulo a on dv.idarticulo=a.idarticulo 
		inner join categoria c on c.idcategoria=a.idcategoria 
		where dv.idventa='$idventa'";
		$detalle = ejecutarConsultaArray($sql_1);

		return $retorno = [
			"status" => true, "message" => 'todo oka', 
			"data" => [
				'venta'    => $venta,
        'detalle'  => $detalle
			]
		];
	}

	// ::::::::::::::::::::::::::::::::: E S C R I T O R I O :::::::::::::::::::::::::::::::::

	public function totalcomprahoy()
	{
		$sql="SELECT IFNULL(SUM(total_compra),0) as total_compra FROM ingreso WHERE DATE(fecha_hora)=curdate()";
		return ejecutarConsulta($sql);
	}

	public function totalventahoy()
	{
		$sql="SELECT IFNULL(SUM(total_venta),0) as total_venta FROM venta WHERE DATE(fecha_hora)=curdate()";
		return ejecutarConsulta($sql);
	}

	public function comprasultimos_10dias()
	{
		$sql="SELECT CONCAT(DAY(fecha_hora),'-',MONTH(fecha_hora)) as fecha,SUM(total_compra) as total FROM ingreso GROUP by fecha_hora ORDER BY fecha_hora DESC limit 0,10";
		return ejecutarConsulta($sql);
	}

	public function ventasultimos_12meses()
	{
		$sql="SELECT DATE_FORMAT(fecha_hora,'%M') as fecha,SUM(total_venta) as total FROM venta GROUP by MONTH(fecha_hora) ORDER BY fecha_hora DESC limit 0,10";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para listar los registros 
	public function listar_clientes()	{
		$sql="SELECT * FROM persona WHERE tipo_persona='Cliente'";
		return ejecutarConsulta($sql);		
	}

	public function listar_categoria()	{
		$sql="SELECT * FROM categoria WHERE condicion='1'";
		return ejecutarConsulta($sql);		
	}

	public function listar_numero()	{
		$sql="SELECT * FROM venta WHERE estado='Aceptado'";
		return ejecutarConsulta($sql);		
	}
}

?>