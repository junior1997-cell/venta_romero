var tabla_venta;
var tabla_articulos;

//Función que se ejecuta al inicio
function init() {
	mostrarform(false);
	listar();

	$("#formulario").on("submit", function (e) {guardaryeditar(e);});
	//Cargamos los items al select cliente
	$.post("../ajax/venta.php?op=selectCliente", function (r) {	$("#idcliente").html(r);	$('#idcliente').selectpicker('refresh');});

	$('#mVentas').addClass("treeview active");
	$('#lVentas').addClass("active");
}

//Función limpiar
function limpiar() {
	$("#idventa").val("");	
	$('#fecha_hora').val(moment().format('YYYY-MM-DD'));
	$("#observacion").val("");
	$("#impuesto").val("0");

	$("#idcliente").val("");
	$("#idcliente").selectpicker('refresh');

	$("#tipo_comprobante").val("Ticket");
	$("#tipo_comprobante").selectpicker('refresh');

	$("#total_descuento").val("");	$("#descuento_html").html(`<span class="pull-left"> S/.</span> 0.00`);
	$("#total_igv").val("");	$("#impuesto_html").html(`<span class="pull-left"> S/.</span> 0.00`);
	$("#total_venta").val("");	$("#total").html(`<span class="pull-left"> S/.</span> 0.00`);
	$("#total_utilidad").val("");

	$(".filas").remove();

}

//Función mostrar formulario
function mostrarform(flag) {
	array_class_venta = [];
	//limpiar();
	if (flag) {
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		//$("#btnGuardar").prop("disabled",false);
		$("#btnagregar").hide();
		listarArticulos();

		$("#btnGuardar").hide();
		$("#btnCancelar").show();
		$("#btnAgregarArt").show();
		detalles = 0;
	}
	else {
		$("#listadoregistros").show();
		$("#formularioregistros").hide();
		$("#btnagregar").show();
	}
}

//Función cancelarform
function cancelarform() {
	limpiar();
	mostrarform(false);
}

//Función Listar
function listar() {
	$('.btn-reporte-pdf').attr('href',`../reportes/rptventas.php?fecha_filtro=${$("#fecha_filtro").val()}`);
	tabla_venta = $('#tbllistado').dataTable(	{
		lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]], //mostramos el menú de registros a revisar
		"aProcessing": true,//Activamos el procesamiento del datatables
		"aServerSide": true,//Paginación y filtrado realizados por el servidor
		dom: '<Bl<f>rtip>',//Definimos los elementos del control de tabla
		buttons: [
			{ text: '<i class="fa fa-fw fa-repeat fa-lg" data-toggle="tooltip" data-placement="top" title="Recargar"></i>', className: "btn bg-gradient-info", action: function ( e, dt, node, config ) { tabla_venta.ajax.reload(null, false); } },
			{ extend: 'copyHtml5', exportOptions: { columns: [1,2,3,4,5,6,7], }, text: `<i class="fa fa-copy fa-lg" data-toggle="tooltip" data-placement="top" title="Copiar"></i>`, className: "btn bg-gradient-gray", footer: true,  }, 
			{ extend: 'excelHtml5', exportOptions: { columns: [1,2,3,4,5,6,7], }, text: `<i class="fa fa-fw fa-file-excel-o fa-lg" data-toggle="tooltip" data-placement="top" title="Excel"></i>`, className: "btn bg-gradient-success", footer: true,  }, 
			{ extend: 'pdfHtml5', exportOptions: { columns: [1,2,3,4,5,6,7], }, text: `<i class="fa fa-fw fa-file-pdf-o fa-lg" data-toggle="tooltip" data-placement="top" title="PDF"></i>`, className: "btn bg-gradient-danger", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
			{ extend: "colvis", text: `Columnas`, className: "btn bg-gradient-gray", exportOptions: { columns: "th:not(:last-child)", }, },
		],
		"ajax":	{
			url: `../ajax/venta.php?op=listar&fecha_filtro=${$("#fecha_filtro").val()}`,
			type: "get",
			dataType: "json",
			error: function (e) {
				console.log(e.responseText);
			}
		},
		createdRow: function (row, data, ixdex) {
      //console.log(data);
      if (data[5] != '') { $("td", row).eq(5).addClass('text-right'); }
      if (data[6] != '') { $("td", row).eq(6).addClass('text-right'); }
    },
		language: {
			lengthMenu: "Mostrar: _MENU_ registros",
			buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
			sLoadingRecords: '<i class="fa fa-fw fa-spinner fa-pulse"></i> Cargando datos...'
		},
		footerCallback: function( tfoot, data, start, end, display ) {
			var api1 = this.api(); var total1 = api1.column( 5 ).data().reduce( function ( a, b ) { return parseFloat(a) + parseFloat(b); }, 0 );      
			$( api1.column( 5 ).footer() ).html( ` <span class="pull-left">S/</span> <span class="text-right">${formato_miles(total1)}</span>` );   
			var api2 = this.api(); var total2 = api2.column( 6 ).data().reduce( function ( a, b ) { return parseFloat(a) + parseFloat(b); }, 0 );      
			$( api2.column( 6 ).footer() ).html( ` <span class="pull-left">S/</span> <span class="text-right">${formato_miles(total2)}</span>` );      
		},
		"bDestroy": true,
		"iDisplayLength": 10,//Paginación
		"order": [[0, "desc"]],//Ordenar (columna,orden)
		columnDefs: [
			// { targets: [6], render: function (data, type) { var number = $.fn.dataTable.render.number(',', '.', 2).display(data); if (type === 'display') { let color = 'numero_positivos'; if (data < 0) {color = 'numero_negativos'; } return `<span class="float-left">S/</span> <span class="float-right ${color} "> ${number} </span>`; } return number; }, },
			{ targets: [1], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
			// { targets: [8],  visible: false,  searchable: false,  },
		],
	}).DataTable();
}


//Función ListarArticulos
function listarArticulos() {
	tabla_articulos = $('#tblarticulos').dataTable(	{
		lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]], //mostramos el menú de registros a revisar
		"aProcessing": true,//Activamos el procesamiento del datatables
		"aServerSide": true,//Paginación y filtrado realizados por el servidor
		dom: '<Bl<f>rtip>',//Definimos los elementos del control de tabla
		buttons: [
			{ text: '<i class="fa fa-fw fa-repeat fa-lg" data-toggle="tooltip" data-placement="top" title="Recargar"></i>', className: "btn bg-gradient-info", action: function ( e, dt, node, config ) { tabla_articulos.ajax.reload(null, false); } },
			{ extend: "colvis", text: `Columnas`, className: "btn bg-gradient-gray", exportOptions: { columns: "th:not(:last-child)", }, },	
		],
		"ajax":		{
			url: '../ajax/venta.php?op=listarArticulosVenta',
			type: "get",
			dataType: "json",
			error: function (e) { console.log(e.responseText);	}
		},
		language: {
			lengthMenu: "Mostrar: _MENU_ registros",
			buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
			sLoadingRecords: '<i class="fa fa-fw fa-spinner fa-pulse"></i> Cargando datos...'
		},
		"bDestroy": true,
		"iDisplayLength": 10,//Paginación
		"order": [[0, "desc"]],//Ordenar (columna,orden)
		columnDefs: [    
			{ targets: [5], render: function (data, type) { var number = $.fn.dataTable.render.number(',', '.', 2).display(data); if (type === 'display') { let color = 'numero_positivos'; if (data < 0) {color = 'numero_negativos'; } return `<span class="float-left">S/</span> <span class="float-right ${color} "> ${number} </span>`; } return number; }, },  
      // { targets: [4],  visible: false,  searchable: false,  },
    ],
	}).DataTable();
}
//Función para guardar o editar

function guardaryeditar(e) {
	e.preventDefault(); //No se activará la acción predeterminada del evento
	//$("#btnGuardar").prop("disabled",true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url: "../ajax/venta.php?op=guardar_y_editar_venta",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,
		success: function (datos) {
			if (datos == 'ok') {
				bootbox.alert('Datos registrados correctamente.');
				mostrarform(false);
				tabla_venta.ajax.reload(null, false);
				limpiar();
			} else {
				bootbox.alert(datos);
			}			
		}
	});	
}

function mostrar(idventa) {
	$.post("../ajax/venta.php?op=mostrar", { idventa: idventa }, function (data, status) {
		data = JSON.parse(data);
		mostrarform(true);

		$("#idcliente").val(data.idcliente);
		$("#idcliente").selectpicker('refresh');
		$("#tipo_comprobante").val(data.tipo_comprobante);
		$("#tipo_comprobante").selectpicker('refresh');
		$("#observacion").val(data.observacion);
		$("#fecha_hora").val(data.fecha);
		$("#impuesto").val(data.impuesto);
		$("#idventa").val(data.idventa);

		//Ocultar y mostrar los botones
		$("#btnGuardar").hide();
		$("#btnCancelar").show();
		$("#btnAgregarArt").hide();
	});

	$.post("../ajax/venta.php?op=listarDetalle&id=" + idventa, function (r) {
		$("#detalles").html(r);
	});
}

//Función para anular registros
function anular(idventa) {
	bootbox.confirm("¿Está Seguro de anular la venta?", function (result) {
		if (result) {
			$.post("../ajax/venta.php?op=anular", { idventa: idventa }, function (e) {
				bootbox.alert(e);
				tabla_venta.ajax.reload(null, false);
			});
		}
	})
}

//Declaración de variables necesarias para trabajar con las compras y
//sus detalles
var array_class_venta = [];
var impuesto = 18;
var cont = 0;
var detalles = 0;
//$("#guardar").hide();
$("#btnGuardar").hide();
$("#tipo_comprobante").change(marcarImpuesto);

function marcarImpuesto() {
	var tipo_comprobante = $("#tipo_comprobante option:selected").text();
	if (tipo_comprobante == 'Factura') {
		$("#impuesto").val(impuesto);
	}	else {
		$("#impuesto").val("0");
	}
}

function agregarDetalle(idarticulo, precio_venta, precio_compra, img) {
	var cantidad = 1;
	var descuento = 0;

	if (idarticulo != "") {
		$.post("../ajax/venta.php?op=optener_producto_venta", { idarticulo: idarticulo }, function (e) {
			e = JSON.parse(e); console.log(e);
			var subtotal = cantidad * precio_venta;
			var fila = `<tr class="filas" id="fila${cont}">
				<td><button type="button" class="btn btn-danger" onclick="eliminarDetalle(${cont})">X</button></td>
				<td>
					<div class="user-block">
						<img class="img-circle" src="../files/articulos/${img}" alt="User Image">
						<span class="username"><a href="#">${e.data.nombre}</a></span>				 
						<div class="description">	${e.data.categoria}	</div>
					</div>
					<input type="hidden" name="idarticulo[]" value="${idarticulo}">
				</td>
				<td><input type="number" name="cantidad[]" class="form-control cantidad_${cont}" value="${cantidad}" step="0.0001" min="0" onkeyup="modificarSubototales();" onchange="modificarSubototales();"></td>
				<td>
					<input type="number" name="precio_venta[]" class="form-control precio_venta_${cont}" value="${precio_venta}" step="0.0001" min="0" onkeyup="modificarSubototales();" onchange="modificarSubototales();">
					<input type="hidden" name="precio_compra[]" class="precio_compra_${cont}" value="${precio_compra}" step="0.0001" min="0">
				</td>
				<td><input type="number" name="descuento[]" class="form-control descuento_${cont}" value="${descuento}" step="0.0001" min="0" onkeyup="modificarSubototales();" onchange="modificarSubototales();"></td>
				<td class="text-right">
					<span class="subtotal_${cont}">${subtotal}</span> 
					<input type="hidden" name="subtotal_pr[]" class="subtotal_${cont}" value="${subtotal}">		
					<input type="hidden" name="utilidad[]" class="utilidad_${cont}" value="0">				
				</td>
				<td><button type="button" onclick="modificarSubototales()" class="btn btn-info"><i class="fa fa-refresh"></i></button></td>
			</tr>`;
			
			detalles = detalles + 1;
			$('#detalles').append(fila);

			array_class_venta.push({ id_cont: cont });

			cont++;
			modificarSubototales();
		});			
	}	else {
		alert("Error al ingresar el detalle, revisar los datos del artículo");
	}
}

function modificarSubototales() {
	var val_igv = $('#impuesto').val(); //console.log(array_class_venta);
	if (array_class_venta.length === 0) {
	} else {
		array_class_venta.forEach((val, key) => {
			var cantidad = $(`.cantidad_${val.id_cont}`).val() == '' || $(`.cantidad_${val.id_cont}`).val() == null ? 0 : parseFloat($(`.cantidad_${val.id_cont}`).val());			
			var compra = $(`.precio_compra_${val.id_cont}`).val() == '' || $(`.precio_compra_${val.id_cont}`).val() == null ? 0 : parseFloat($(`.precio_compra_${val.id_cont}`).val());			
			var venta = $(`.precio_venta_${val.id_cont}`).val() == '' || $(`.precio_venta_${val.id_cont}`).val() == null ? 0 : parseFloat($(`.precio_venta_${val.id_cont}`).val());			
			var descuento = $(`.descuento_${val.id_cont}`).val() == '' || $(`.descuento_${val.id_cont}`).val() == null ? 0 : parseFloat($(`.descuento_${val.id_cont}`).val());
			
			// Calculamos: Subtotal de cada producto
			var subtotal_producto = (cantidad * venta) - descuento;	
			
			// Calculamos: utilidad de cada producto
			var utilidad = subtotal_producto - (compra * cantidad);	
			
			$(`.subtotal_${val.id_cont}`).html(formato_miles(subtotal_producto)).val( redondearExp(subtotal_producto) );
			$(`.utilidad_${val.id_cont}`).val( redondearExp(utilidad) );
		});
		calcularTotales();
	}	
}

function calcularTotales() {
	var val_igv = $('#impuesto').val() == '' || $(`#impuesto`).val() == null ? 0 : parseFloat($(`#impuesto`).val());
  var igv = 0;
  var total = 0.0;
  var descuento = 0.0;
  var utilidad = 0.0;

  array_class_venta.forEach((element, index) => {
    total += parseFloat($(`.subtotal_${element.id_cont}`).val());
    descuento += parseFloat($(`.descuento_${element.id_cont}`).val());
    utilidad += parseFloat($(`.utilidad_${element.id_cont}`).val());
  });	
	
	$("#descuento_html").html(`<span class="pull-left">S/.</span> ${formato_miles(descuento)} `); $("#total_descuento").val( redondearExp(descuento) );
	$("#total").html(`<span class="pull-left">S/.</span> ${formato_miles(total)}` ); $("#total_venta").val( redondearExp(total) );
	$("#total_utilidad").val( redondearExp(utilidad) );
	
	evaluar();
}

function evaluar() {
	if (detalles > 0) {	$("#btnGuardar").show(); }	else { $("#btnGuardar").hide(); cont = 0;	}
}

function eliminarDetalle(indice) {
	$("#fila" + indice).remove();
	array_class_venta.forEach(function (car, index, object) {
    if (car.id_cont === indice) { object.splice(index, 1);  }
  });

	calcularTotales();
	detalles = detalles - 1;
	evaluar()
}

function ver_editar(id) {
	$("#cargando-1-fomulario").hide();
  $("#cargando-2-fomulario").show();
	mostrarform(true)
	var cantidad = 1;
	var descuento = 0;

	if (id != "") {
		$.post("../ajax/venta.php?op=ver_venta_editar", { idventa: id }, function (e) {
			e = JSON.parse(e); console.log(e);

			$("#idventa").val(e.data.persona.idventa);
			$("#idcliente").val(e.data.persona.idcliente);
			$("#idcliente").selectpicker('refresh');

			$("#tipo_comprobante").val(e.data.persona.tipo_comprobante);
			$("#tipo_comprobante").selectpicker('refresh');
			
			$("#fecha_hora").val(e.data.persona.fecha);
			$("#impuesto").val(e.data.persona.impuesto);
			$("#observacion").val(e.data.persona.observacion);			

			e.data.detalle.forEach((val, key) => {			
				var img = val.imagen == '' || val.imagen == null ? 'producto-sin-foto.svg' : val.imagen ;
				var fila = `<tr class="filas" id="fila${cont}">
					<td><button type="button" class="btn btn-danger" onclick="eliminarDetalle(${cont})">X</button></td>
					<td>
						<div class="user-block">
							<img class="img-circle" src="../files/articulos/${img}" alt="User Image">
							<span class="username"><a href="#">${val.nombre}</a></span>				 
							<div class="description">	${val.categoria}	</div>
						</div>
						<input type="hidden" name="idarticulo[]" value="${val.idarticulo}">						
					</td>
					<td><input type="number" name="cantidad[]" class="form-control cantidad_${cont}" value="${val.cantidad}" step="0.0001" min="0" onkeyup="modificarSubototales();" onchange="modificarSubototales();"></td>
					<td>
						<input type="number" name="precio_venta[]" class="form-control precio_venta_${cont}" value="${val.precio_venta}" step="0.0001" min="0" onkeyup="modificarSubototales();" onchange="modificarSubototales();">
						<input type="hidden" name="precio_compra[]" class="precio_compra_${cont}" value="${val.precio_compra}" step="0.0001" min="0">
					</td>
					<td><input type="number" name="descuento[]" class="form-control descuento_${cont}" value="${val.descuento}" step="0.0001" min="0" onkeyup="modificarSubototales();" onchange="modificarSubototales();"></td>
					<td class="text-right">
						<span class="subtotal_${cont}">${val.subtotal}</span> 
						<input type="hidden" name="subtotal_pr[]" class="subtotal_${cont}" value="${val.subtotal}">		
						<input type="hidden" name="utilidad[]" class="utilidad_${cont}" value="${val.utilidad}">			
					</td>
					<td><button type="button" onclick="modificarSubototales()" class="btn btn-info"><i class="fa fa-refresh"></i></button></td>
				</tr>`;
				
				detalles = detalles + 1;
				$('#detalles').append(fila);

				array_class_venta.push({ id_cont: cont });

				cont++;
			});

			modificarSubototales();
			$("#cargando-1-fomulario").show();
      $("#cargando-2-fomulario").hide();
		});			
	}	else {
		alert("Error al ingresar el detalle, revisar los datos del artículo");
	}
}

function detalle_x_comprobante(id) {
	$(".detalle-x-comprobante").html(`<div class="text-center"><i class="fa fa-fw fa-spinner fa-pulse fa-2x"></i> <br> Cargando datos...</div>  `);
	$('.tooltip').remove();
	$("#modal-ver-detalle").modal('show');
	$.post("../ajax/ajax_general.php?op=detalle_x_comprobante", {id:id}, function (e) {	
		e = JSON.parse(e); //console.log(e);
		$(".detalle-x-comprobante").html(e.data);	
	});	
}

init();

function reload_cliente() { 
	$('.btn-reload-cliente').html('<i class="fa fa-fw fa-spinner fa-pulse fa-lg text-white"></i>');	
	$.post("../ajax/venta.php?op=selectCliente", function (r) {	
		$("#idcliente").html(r); $('#idcliente').selectpicker('refresh'); $('.btn-reload-cliente').html('<i class="fa fa-rotate-right"></i>');	
	}); 
}