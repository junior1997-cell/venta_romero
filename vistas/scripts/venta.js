var tabla_venta;

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
	$("#idcliente").val("");
	$("#idcliente").selectpicker('refresh');
	$("#cliente").val("");	
	$("#observacion").val("");
	$("#impuesto").val("0");

	$("#total_venta").val("");
	$(".filas").remove();
	$("#total").html("0");

	//Obtenemos la fecha actual	
	$('#fecha_hora').val(moment().format('YYYY-MM-DD'));

	//Marcamos el primer tipo_documento
	$("#tipo_comprobante").val("Ticket");
	$("#tipo_comprobante").selectpicker('refresh');
}

//Función mostrar formulario
function mostrarform(flag) {
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
	tabla_venta = $('#tbllistado').dataTable(	{
			lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]], //mostramos el menú de registros a revisar
			"aProcessing": true,//Activamos el procesamiento del datatables
			"aServerSide": true,//Paginación y filtrado realizados por el servidor
			dom: '<Bl<f>rtip>',//Definimos los elementos del control de tabla
			buttons: [
				'copyHtml5','excelHtml5',	'csvHtml5',	'pdf'
				// { text: '<i class="fa-solid fa-arrows-rotate" data-toggle="tooltip" data-original-title="Recargar"></i>', className: "btn bg-gradient-info", action: function ( e, dt, node, config ) { tabla_venta.ajax.reload(null, false); } },
				// { extend: 'copyHtml5', exportOptions: { columns: [1,2,3,4,5,6,7], }, text: `<i class="fa fa-copy" data-toggle="tooltip" data-original-title="Copiar"></i>`, className: "btn bg-gradient-gray", footer: true,  }, 
				// { extend: 'excelHtml5', exportOptions: { columns: [1,2,3,4,5,6,7], }, text: `<i class="fa fa-file-excel fa-lg" data-toggle="tooltip" data-original-title="Excel"></i>`, className: "btn bg-gradient-success", footer: true,  }, 
				// { extend: 'pdfHtml5', exportOptions: { columns: [1,2,3,4,5,6,7], }, text: `<i class="fa fa-file-pdf fa-lg" data-toggle="tooltip" data-original-title="PDF"></i>`, className: "btn bg-gradient-danger", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
				// { extend: "colvis", text: `Columnas`, className: "btn bg-gradient-gray", exportOptions: { columns: "th:not(:last-child)", }, },
			],
			"ajax":	{
				url: '../ajax/venta.php?op=listar',
				type: "get",
				dataType: "json",
				error: function (e) {
					console.log(e.responseText);
				}
			},
			language: {
				lengthMenu: "Mostrar: _MENU_ registros",
				buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
				sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
			},
			footerCallback: function( tfoot, data, start, end, display ) {
				var api1 = this.api(); var total1 = api1.column( 6 ).data().reduce( function ( a, b ) { return parseFloat(a) + parseFloat(b); }, 0 );      
				$( api1.column( 6 ).footer() ).html( ` <span class="float-left">S/</span> <span class="float-right">${formato_miles(total1)}</span>` );      
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
	tabla = $('#tblarticulos').dataTable(
		{
			"aProcessing": true,//Activamos el procesamiento del datatables
			"aServerSide": true,//Paginación y filtrado realizados por el servidor
			dom: 'Bfrtip',//Definimos los elementos del control de tabla
			buttons: [

			],
			"ajax":
			{
				url: '../ajax/venta.php?op=listarArticulosVenta',
				type: "get",
				dataType: "json",
				error: function (e) {
					console.log(e.responseText);
				}
			},
			"bDestroy": true,
			"iDisplayLength": 5,//Paginación
			"order": [[0, "desc"]]//Ordenar (columna,orden)
		}).DataTable();
}
//Función para guardar o editar

function guardaryeditar(e) {
	e.preventDefault(); //No se activará la acción predeterminada del evento
	//$("#btnGuardar").prop("disabled",true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url: "../ajax/venta.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,
		success: function (datos) {
			if (datos == 'ok') {
				bootbox.alert('Datos registrados correctamente.');
				mostrarform(false);
				listar();
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
	}
	else {
		$("#impuesto").val("0");
	}
}

function agregarDetalle(idarticulo, articulo, precio_venta, precio_compra) {
	var cantidad = 1;
	var descuento = 0;

	if (idarticulo != "") {
		var subtotal = cantidad * precio_venta;
		var fila = `<tr class="filas" id="fila${cont}">
			<td><button type="button" class="btn btn-danger" onclick="eliminarDetalle(${cont})">X</button></td>
			<td><input type="hidden" name="idarticulo[]" value="${idarticulo}">${articulo}</td>
			<td><input type="number" name="cantidad[]" id="cantidad[]" value="${cantidad}" step="0.0001" min="0" onkeyup="modificarSubototales();"></td>
			<td>
				<input type="number" name="precio_venta[]" id="precio_venta[]" value="${precio_venta}" step="0.0001" min="0" onkeyup="modificarSubototales();">
				<input type="hidden" name="precio_compra[]" id="precio_compra[]" value="${precio_compra}" step="0.0001" min="0">
			</td>
			<td><input type="number" name="descuento[]" value="${descuento}" step="0.0001" min="0" onkeyup="modificarSubototales();"></td>
			<td>
				<input type="hidden" name="subtotal_pr[]" id="subtotal_pr_${cont}" value="${subtotal}">
				<span name="subtotal" id="subtotal${cont}">${subtotal}</span>
			</td>
			<td><button type="button" onclick="modificarSubototales()" class="btn btn-info"><i class="fa fa-refresh"></i></button></td>
		</tr>`;
		cont++;
		detalles = detalles + 1;
		$('#detalles').append(fila);
		modificarSubototales();
	}
	else {
		alert("Error al ingresar el detalle, revisar los datos del artículo");
	}
}

function modificarSubototales() {
	var cant = document.getElementsByName("cantidad[]");
	var prec = document.getElementsByName("precio_venta[]");
	var desc = document.getElementsByName("descuento[]");
	var sub = document.getElementsByName("subtotal");

	for (var i = 0; i < cant.length; i++) {
		var inpC = cant[i];
		var inpP = prec[i];
		var inpD = desc[i];
		var inpS = sub[i];

		inpS.value = (inpC.value * inpP.value) - inpD.value;
		var sub_total = (inpC.value * inpP.value) - inpD.value;
		document.getElementsByName("subtotal")[i].innerHTML = inpS.value;		
		$(`#subtotal_pr_${i}`).val(sub_total);
	}
	calcularTotales();

}
function calcularTotales() {
	var sub = document.getElementsByName("subtotal");
	var total = 0.0;

	for (var i = 0; i < sub.length; i++) {
		total += document.getElementsByName("subtotal")[i].value;
	}
	$("#total").html("S/. " + total);
	$("#total_venta").val(total);
	evaluar();
}

function evaluar() {
	if (detalles > 0) {
		$("#btnGuardar").show();
	}
	else {
		$("#btnGuardar").hide();
		cont = 0;
	}
}

function eliminarDetalle(indice) {
	$("#fila" + indice).remove();
	calcularTotales();
	detalles = detalles - 1;
	evaluar()
}

init();