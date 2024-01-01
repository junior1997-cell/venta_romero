<?php 
  //Incluímos inicialmente la conexión a la base de datos
  require "../config/Conexion.php";

  Class Ajax_general
  {
    //Implementamos nuestro variable global
    public $id_usr_sesion;

    //Implementamos nuestro constructor
    public function __construct($id_usr_sesion = 0)
    {
      $this->id_usr_sesion = $id_usr_sesion;
    } 

    //CAPTURAR PERSONA  DE RENIEC 
    public function datos_reniec($dni) { 

      $url = "https://dniruc.apisperu.com/api/v1/dni/".$dni."?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6Imp1bmlvcmNlcmNhZG9AdXBldS5lZHUucGUifQ.bzpY1fZ7YvpHU5T83b9PoDxHPaoDYxPuuqMqvCwYqsM";
      //  Iniciamos curl
      $curl = curl_init();
      // Desactivamos verificación SSL
      curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 );
      // Devuelve respuesta aunque sea falsa
      curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
      // Especificamo los MIME-Type que son aceptables para la respuesta.
      curl_setopt( $curl, CURLOPT_HTTPHEADER, [ 'Accept: application/json' ] );
      // Establecemos la URL
      curl_setopt( $curl, CURLOPT_URL, $url );
      // Ejecutmos curl
      $json = curl_exec( $curl );
      // Cerramos curl
      curl_close( $curl );

      return json_decode( $json, true );
    }

    public function consultaDniReniec($ruc)	{ 
      $token = 'apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N';
      $nndnii = $_GET['nrodni'];

      // Iniciar llamada a API
      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.apis.net.pe/v1/dni?numero=' . $nndnii,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 2,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
          'Referer: https://apis.net.pe/consulta-dni-api',
          'Authorization: Bearer' . $token
        ),
      ));
      $response = curl_exec($curl);
      curl_close($curl);
      // Datos listos para usar
      return json_decode($response);
    }

    //CAPTURAR PERSONA  DE SUNAT
    public function datos_sunat($ruc)	{ 
      $url = "https://dniruc.apisperu.com/api/v1/ruc/".$ruc."?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6Imp1bmlvcmNlcmNhZG9AdXBldS5lZHUucGUifQ.bzpY1fZ7YvpHU5T83b9PoDxHPaoDYxPuuqMqvCwYqsM";
      //  Iniciamos curl
      $curl = curl_init();
      // Desactivamos verificación SSL
      curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 );
      // Devuelve respuesta aunque sea falsa
      curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
      // Especificamo los MIME-Type que son aceptables para la respuesta.
      curl_setopt( $curl, CURLOPT_HTTPHEADER, [ 'Accept: application/json' ] );
      // Establecemos la URL
      curl_setopt( $curl, CURLOPT_URL, $url );
      // Ejecutmos curl
      $json = curl_exec( $curl );
      // Cerramos curl
      curl_close( $curl );

      return json_decode( $json, true );

    }  

    public function consultaRucSunat($ruc)	{ 
      $token = 'apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N';  

      // Iniciar llamada a API
      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.apis.net.pe/v1/ruc?numero=' . $ruc,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
          'Referer: https://apis.net.pe/api-ruc',
          'Authorization: Bearer' . $token
        ),
      ));
      $response = curl_exec($curl);
      curl_close($curl);
      // Datos listos para usar
      return json_decode($response);
    }
    /* ══════════════════════════════════════ C O M P R O B A N T E  ══════════════════════════════════════ */ 

    /* ══════════════════════════════════════ T R A B A J A D O R ══════════════════════════════════════ */   
    
    /* ══════════════════════════════════════ C L I E N T E  ══════════════════════════════════════ */    

    /* ══════════════════════════════════════ TIPO PERSONA  ══════════════════════════════════════ */   

    /* ══════════════════════════════════════ P R O V E E D O R -- C L I E N T E S  ══════════════════════════════════════ */   

    /* ══════════════════════════════════════ S U C U R S A L  ══════════════════════════════════════ */

    /* ══════════════════════════════════════ B A N C O ══════════════════════════════════════ */
    
    /* ══════════════════════════════════════ C O L O R ══════════════════════════════════════ */
    
    /* ══════════════════════════════════════ M A R C A ════════════════════════════ */

    /* ══════════════════════════════════════ U N I D A D   D E   M E D I D A ══════════════════════════════════════ */

    /* ══════════════════════════════════════ C A T E G O R I A ══════════════════════════════════════ */

    /* ══════════════════════════════════════ P R O D U C T O ══════════════════════════════════════ */
    public function mostrar_producto($idproducto)  {
      $data = []; $array_marca = []; $array_marca_name = [];

      $sql = "SELECT p.idproducto, p.idcategoria_producto, p.idunidad_medida, p.idmarca, p.idcolor, p.nombre, p.contenido_neto, p.precio_venta, 
      p.precio_compra, p.stock, p.descripcion, p.imagen,
      um.nombre as unidad_medida, um.abreviatura, cp.nombre as nombre_categoria, c.nombre_color, m.nombre_marca
      FROM producto AS p, unidad_medida AS um, categoria_producto as cp, color AS c, marca AS m
      WHERE p.idunidad_medida = um.idunidad_medida AND p.idcategoria_producto = cp.idcategoria_producto AND p.idcolor = c.idcolor 
      AND p.idmarca = m.idmarca AND p.idproducto = '$idproducto'";
      $activos = ejecutarConsultaSimpleFila($sql); if ($activos['status'] == false) { return  $activos;}

      if ( empty($activos['data'])  ) {
        return $retorno = ['status'=> true, 'message' => 'Salió todo ok,', 'data' => null ];
      }else{
       
        
        $data = [
          'idproducto'          => $activos['data']['idproducto'],
          'idcategoria_producto'=> $activos['data']['idcategoria_producto'],
          'idunidad_medida'     => $activos['data']['idunidad_medida'],
          'idmarca'             => $activos['data']['idmarca'],
          'idcolor'             => $activos['data']['idcolor'],
          'nombre_producto'     => $activos['data']['nombre'],
          'contenido_neto'      => $activos['data']['contenido_neto'],
          'precio_venta'        => (empty($activos['data']['precio_venta']) ? 0 : floatval($activos['data']['precio_venta']) ),
          'precio_compra'       => (empty($activos['data']['precio_compra']) ? 0 : floatval($activos['data']['precio_compra']) ),
          'stock'               => (empty($activos['data']['stock']) ? 0 : floatval($activos['data']['stock']) ),
          'descripcion'         => $activos['data']['descripcion'],
          'imagen'              => $activos['data']['imagen'],
          'unidad_medida'       => $activos['data']['unidad_medida'],
          'abreviatura'         => $activos['data']['abreviatura'],   
          'nombre_categoria'    => $activos['data']['nombre_categoria'],
          'nombre_color'        => $activos['data']['nombre_color'],
          'nombre_marca'        => $activos['data']['nombre_marca'],          
                 
        ];

        return $retorno = ['status'=> true, 'message' => 'Salió todo ok,', 'data' => $data ];  
      }       
    }
    //funcion para mostrar registros de prosuctos
    public function tblaProductos() {
      $sql = "SELECT p.idproducto, p.idcategoria_producto, p.idunidad_medida, p.nombre, p.contenido_neto, p.precio_venta, p.precio_compra,
      p.stock, p.descripcion, p.imagen, p.estado,  
      um.nombre as nombre_medida, cp.nombre AS categoria, m.nombre_marca, c.nombre_color
      FROM producto as p, unidad_medida AS um, categoria_producto AS cp, marca as m, color as c
      WHERE p.idcategoria_producto = cp.idcategoria_producto and p.idunidad_medida = um.idunidad_medida and p.idmarca = m.idmarca and p.idcolor = c.idcolor
      and p.estado='1' AND p.estado_delete='1' ORDER BY p.nombre ASC";
      return ejecutarConsulta($sql);
    }  

  }

?>