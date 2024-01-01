<?php

require '../vendor/autoload.php';
use Luecano\NumeroALetras\NumeroALetras;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;

ob_start(); //Activamos el almacenamiento en el buffer

if (strlen(session_id()) < 1) { session_start(); }   

if (!isset($_SESSION["nombre"])) {
  echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
  if ($_SESSION['ventas'] == 1) {
    date_default_timezone_set('America/Lima'); $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../dist/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-toggle="tooltip"]\').tooltip(); }); </script>';    
    $scheme_host =  ($_SERVER['HTTP_HOST'] == 'localhost' ? 'http://localhost/venta_romero/' :  $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'].'/');
  ?>

  <html>

  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link href="../public/css/ticket.css" rel="stylesheet" type="text/css">
  </head>

  <body onload="window.print();">
    <?php

    //Incluímos la clase Venta
    require_once "../modelos/Venta.php";
    $venta = new Venta();

    $numero_a_letra = new NumeroALetras();
    

    $datos = $venta->ver_venta_editar($_GET['idventa']);

    //Establecemos los datos de la empresa
    $empresa = "Comercial Mister SAC";
    $documento = "20477157772";
    $direccion = "Chongoyape, José Gálvez 1368";
    $telefono = "931742904";
    $email = "romero@gmail.com";
    $web = "https://romero.com";

    ?>    

    <br>
    <!-- Detalle de empresa -->
    <table border="0" align="center" width="230px">
      <tbody>
        <tr>
          <td align="center"><img src="../public/img/logo-romero.jpg" width="100"></td>
        </tr>
        <tr align="center">
          <td style="font-size: 14px">.::<strong> <?php echo mb_convert_encoding(htmlspecialchars_decode($empresa),"UTF-8", mb_detect_encoding($empresa)) ?> </strong>::.</td>
        </tr>        
        <tr align="center">
          <td style="font-size: 14px"> <strong> R.U.C. <?php echo $documento; ?> </strong> </td>
        </tr>
        <tr align="center">
          <td style="font-size: 10px"> <?php echo mb_convert_encoding(htmlspecialchars_decode($direccion),"UTF-8", mb_detect_encoding($direccion)) . ' <br> ' . $telefono; ?> </td>
        </tr>
        <tr align="center">
          <td style="font-size: 10px"> <?php echo mb_convert_encoding(strtolower($email),"UTF-8", mb_detect_encoding($email)); ?> </td>
        </tr>
        <tr align="center">
          <td style="font-size: 10px"> <?php echo mb_convert_encoding(strtolower($web),"UTF-8", mb_detect_encoding($web)); ?> </td>
        </tr>
        <tr>
          <td style="text-align: center;">--------------------------------------------------------</td>
        </tr>
        <tr>
          <td align="center"> <strong> TIKET ELECTRÓNICO </strong> <br> 
          <b style="font-size: 14px"><?php echo $datos['data']['persona']['serie_comprobante'] .'-'.$datos['data']['persona']['num_comprobante']; ?> </b></td>
        </tr>
        <tr>
          <td style="text-align: center;">--------------------------------------------------------</td>
        </tr>
      </tbody>
    </table>

    <!-- Datos cliente -->
    <table border="0" align="center" width="230px">
      <tbody>
        <tr align="left">
          <td><strong>Cliente:</strong> <?php echo $datos['data']['persona']['cliente']; ?> </td>
        </tr>
        <tr align="left">
          <td><strong><?php echo $datos['data']['persona']['tipo_documento']; ?>:</strong> <?php echo $datos['data']['persona']['num_documento']; ?></td>
        </tr>
        <tr align="left">
          <td><strong>Dirección:</strong> <?php echo $datos['data']['persona']['direccion']; ?></td>
        </tr>
        <tr align="left">
          <td><strong>Fecha de emisión:</strong> <?php echo  $datos['data']['persona']['fecha']; ?> </td>
        </tr>
        <tr align="left">
          <td><strong>Moneda:</strong> SOLES</td>
        </tr>
        <tr align="left">
          <td><strong>Atención:</strong> <?php echo $datos['data']['persona']['usuario']; ?> </td>
        </tr>
        <tr>
          <td><strong>Tipo de pago:</strong> Efectivo </td>
        </tr>        
        <tr>
          <td><strong>Observación:</strong> <?php echo $datos['data']['persona']['observacion']; ?> </td>
        </tr>
      </tbody>
    </table>

    <br>    

    <!-- Mostramos los detalles de la venta en el documento HTML -->
    <table border="0" align="center" width="230px" style="font-size: 12px">
      <tr>
        <td colspan="5">--------------------------------------------------------</td>
      </tr>
      <tr>
        <td>Cant.</td>
        <td>Producto</td>
        <td>P.u.</td>
        <td>Importe</td>
      </tr>
      <tr>
        <td colspan="5">--------------------------------------------------------</td>
      </tr>

      <?php     

      //processing form input
      $dataTxt = $datos['data']['persona']['num_documento'] . "|" . 
      $datos['data']['persona']['tipo_documento'] . "|" . 
      $datos['data']['persona']['serie_comprobante'] . "|" . $datos['data']['persona']['num_comprobante'] . "|0.00|" . 
      $datos['data']['persona']['total_venta'] . "|" . $datos['data']['persona']['fecha'] . "|" . 
      $datos['data']['persona']['tipo_documento'] . "|" . $datos['data']['persona']['num_documento']. "|";
      
      // user data
      $errorCorrectionLevel = 'H';
      $matrixPointSize = '2';
      $filename = $datos['data']['persona']['serie_comprobante'] .'-'.$datos['data']['persona']['num_comprobante'].'__'
       . $date_now .'__'. random_int(0, 20) . round(microtime(true)) . random_int(21, 41) . '.'  . '.png';

      $qr_code = QrCode::create($dataTxt)
      ->setEncoding(new Encoding('UTF-8'))
      ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
      ->setSize(600)
      ->setMargin(10)
      ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
      ->setForegroundColor(new Color(0, 0, 0))
      ->setBackgroundColor(new Color(255, 255, 255));

      // Create generic label
      $label = Label::create( $datos['data']['persona']['serie_comprobante'] .'-'.$datos['data']['persona']['num_comprobante'])->setTextColor(new Color(255, 0, 0));

      $writer = new PngWriter();

      $result = $writer->write($qr_code, label: $label);

      // Save it to a file
      $result->saveToFile(__DIR__.'/venta/qr-ticket/'.$filename);

      // Generate a data URI to include image data inline (i.e. inside an <img> tag)
      $dataUri = $result->getDataUri();
      

      //===============SEGUNDA COPIA DE BOLETA=========================      
      $cantidad = 0;
      foreach ($datos['data']['detalle'] as $key => $val) {
        
        echo "<tr>";
        echo "<td>" . $val['cantidad'] . "</td>";
        echo "<td>" . strtolower($val['nombre']) . "</td>";
        echo '<td style="text-align: right;">' . number_format($val['precio_venta'], 2) . "</td>";
        echo '<td style="text-align: right;">' . $val['subtotal'] . "</td>";
        echo "</tr>";
        
      }
      ?>
    </table>

    <!-- Division -->
    <table border='0' align='center' width='230px' style='font-size: 12px'>
      <tr>
        <td>--------------------------------------------------------</td>
      </tr>
      <tr></tr>
    </table>

    <!-- Detalles de totales sunat -->
    <table border='0' align="center" width='230px' style='font-size: 12px'>
      <tr>
        <td colspan='5'><strong>Descuento </strong></td>
        <td>:</td>
        <td style="text-align: right;"> <?php echo $datos['data']['persona']['descuento']; ?> </td>
      </tr>
      <tr>
        <td colspan='5'><strong>Op. Gravada </strong></td>
        <td>:</td>
        <td style="text-align: right;"> 0 </td>
      </tr>
      <tr>
        <td colspan='5'><strong>Op. Exonerado </strong></td>
        <td>:</td>
        <td style="text-align: right;"> 0 </td>
      </tr>      
      <tr>
        <td colspan='5'><strong>ICBPER</strong></td>
        <td>:</td>
        <td style="text-align: right;"> 0 </td>
      </tr>
      <tr>
        <td colspan='5'><strong>I.G.V.</strong></td>
        <td>:</td>
        <td style="text-align: right;"> <?php echo $datos['data']['persona']['impuesto']; ?> </td>
      </tr>
      <tr>
        <td colspan='5'><strong>Imp. Pagado</strong></td>
        <td>:</td>
        <td style="text-align: right;"> <?php echo $datos['data']['persona']['total_venta']; ?> </td>
      </tr>
      <tr>
        <td colspan='5'><strong>Vuelto</strong></td>
        <td>:</td>
        <td style="text-align: right;"> 0 </td>
      </tr>
      <!--<tr><td colspan='5'><strong>I.G.V. 18.00 </strong></td><td >:</td><td><?php echo $reg->sumatoria_igv_18_1; ?></td></tr>-->
    </table>

    <?php $num_total = $numero_a_letra->toInvoice($datos['data']['persona']['total_venta'], 2, " SOLES"); ?>

    <!-- Mostramos los totales de la venta en el documento HTML -->
    <table border='0' align="center" width='230px' style='font-size: 12px'>
      <tr>
        <td><strong>Importe a pagar </strong></td>
        <td>:</td>
        <td style="text-align: right;"><strong> <?php echo$datos['data']['persona']['total_venta']; ?> </strong></td>
      </tr>
      <tr>
        <td colspan="3">--------------------------------------------------------</td>
      </tr>
      <tr>
        <td colspan="3"><strong>Son: </strong> <?php echo $num_total; ?> </td>
      </tr>
      <tr>
        <td colspan="3">--------------------------------------------------------</td>
      </tr>
    </table>

    <br>

    <div style="text-align: center;">
      <img src=<?php echo $dataUri; ?> width="130" height="auto"><br>
      <!-- <label> <?php echo $dataUri; ?> </label> -->
      <br>
      <br>
      <label>Representación impresa de la Boleta de<br>Venta Electrónica puede ser consultada<br>en
        <?php echo mb_convert_encoding(htmlspecialchars_decode($web),"UTF-8", mb_detect_encoding($web)) ?>
      </label>
      <br>
      <br>
      <label><strong>::.GRACIAS POR SU COMPRA.::</strong></label>
    </div>
    <p>&nbsp;</p>
  </body>

  </html>
  <?php
  } else {
    echo 'No tiene permiso para visualizar el reporte';
  }
}
ob_end_flush();
?>