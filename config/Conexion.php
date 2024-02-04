<?php 
require_once "global.php";

$conexion = new mysqli(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_NAME);

$id_usr_sesion 			=  isset($_SESSION['idusuario']) ? $_SESSION["idusuario"] : 0;
$id_empresa_sesion 	= isset($_SESSION['idempresa']) ? $_SESSION["idempresa"] : 0;

mysqli_query( $conexion, 'SET NAMES "'.DB_ENCODE.'"');						# Para el tipo de datos, ejemlo: UTF8
mysqli_query($conexion, "SET @id_usr_sesion ='$id_usr_sesion' "); # Para saber quien hizo el CRUD
mysqli_query($conexion, "SET time_zone = 'America/Lima';");       # Cambia el horario local: America/Lima
mysqli_query($conexion, "SET lc_time_names = 'es_ES';");          # Cambia el idioma a español en fechas

//Si tenemos un posible error en la conexión lo mostramos
if (mysqli_connect_errno()){
	printf("Falló conexión a la base de datos: %s\n",mysqli_connect_error());
	exit();
}

if (!function_exists('ejecutarConsulta'))
{
	function ejecutarConsulta($sql)
	{
		global $conexion;
		$query = $conexion->query($sql);
		return $query;
	}

	function ejecutarConsultaArray($sql) {
    global $conexion;
    $query = $conexion->query($sql);
    for ($data = array(); $row = $query->fetch_assoc(); $data[] = $row);
    return $data;
  }

	function ejecutarConsultaSimpleFila($sql)
	{
		global $conexion;
		$query = $conexion->query($sql);		
		$row = $query->fetch_assoc();
		return $row;
	}

	function ejecutarConsulta_retornarID($sql)
	{
		global $conexion;
		$query = $conexion->query($sql);		
		return $conexion->insert_id;			
	}

	function limpiarCadena($str)
	{
		global $conexion;
		$str = mysqli_real_escape_string($conexion,trim($str));
		return htmlspecialchars($str);
	}
}
?>