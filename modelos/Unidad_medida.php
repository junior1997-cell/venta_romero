<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

Class UnidadMedida
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	//Implementamos un método para insertar registros
	public function insertar($nombre,$descripcion)
	{
		$sql="INSERT INTO unida_medida (nombre,descripcion)
		VALUES ('$nombre','$descripcion')";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para editar registros
	public function editar($idunida_medida,$nombre,$descripcion)
	{
		$sql="UPDATE unida_medida SET nombre='$nombre',descripcion='$descripcion' WHERE idunida_medida='$idunida_medida'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para desactivar categorías
	public function desactivar($idunida_medida)
	{
		$sql="UPDATE unida_medida SET estado='0' WHERE idunida_medida='$idunida_medida'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para activar categorías
	public function activar($idunida_medida)
	{
		$sql="UPDATE unida_medida SET estado='1' WHERE idunida_medida='$idunida_medida'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idunida_medida)
	{
		$sql="SELECT * FROM unida_medida WHERE idunida_medida='$idunida_medida'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function listar()
	{
		$sql="SELECT * FROM unida_medida";
		return ejecutarConsulta($sql);		
	}
	//Implementar un método para listar los registros y mostrar en el select
	public function select()
	{
		$sql="SELECT * FROM unida_medida where estado=1";
		return ejecutarConsulta($sql);		
	}
}

?>