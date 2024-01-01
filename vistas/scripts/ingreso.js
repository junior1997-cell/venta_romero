var tabla_articulo;
var tabla_ingreso;

//Función que se ejecuta al inicio
function init() {
	mostrarform(false);
	listar();

	$("#formulario").on("submit", function (e) {
		guardaryeditar(e);
	});
	//Cargamos los items al select proveedor
	$.post("../ajax/ingreso.php?op=selectProveedor", function (r) {
		$("#idproveedor").html(r);
		$('#idproveedor').selectpicker('refresh');
	});
	$('#mCompras').addClass("treeview active");
	$('#lIngresos').addClass("active");
}

//Función limpiar
function limpiar() {
	$("#idproveedor").val("");
	$("#idproveedor").selectpicker('refresh');
	$("#proveedor").val("");
	$("#serie_comprobante").val("");
	$("#num_comprobante").val("");
	$("#impuesto").val("0");

	$("#total_compra").val("");
	$(".filas").remove();
	$("#total").html("0");

	//Obtenemos la fecha actual
	$('#fecha_hora').val(moment().format('YYYY-MM-DD'));

	//Marcamos el primer tipo_documento
	$("#tipo_comprobante").val("Boleta");
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
		detalles = 0;
		$("#btnAgregarArt").show();
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
	tabla_ingreso = $('#tbllistado').dataTable(	{
		lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]], //mostramos el menú de registros a revisar
		"aProcessing": true,//Activamos el procesamiento del datatables
		"aServerSide": true,//Paginación y filtrado realizados por el servidor
		dom: '<Bl<f>rtip>',//Definimos los elementos del control de tabla
		buttons: [
			{ text: '<i class="fa fa-fw fa-repeat fa-lg" data-toggle="tooltip" data-placement="top" title="Recargar"></i>', className: "btn bg-gradient-info", action: function ( e, dt, node, config ) { tabla_ingreso.ajax.reload(null, false); } },
			{ extend: 'copyHtml5', exportOptions: { columns: [1,2,3,4], }, text: `<i class="fa fa-copy fa-lg" data-toggle="tooltip" data-placement="top" title="Copiar"></i>`, className: "btn bg-gradient-gray", footer: true,  }, 
			{ extend: 'excelHtml5', exportOptions: { columns: [1,2,3,4], }, text: `<i class="fa fa-fw fa-file-excel-o fa-lg" data-toggle="tooltip" data-placement="top" title="Excel"></i>`, className: "btn bg-gradient-success", footer: true,  }, 
			{ extend: 'pdfHtml5', exportOptions: { columns: [1,2,3,4], }, text: `<i class="fa fa-fw fa-file-pdf-o fa-lg" data-toggle="tooltip" data-placement="top" title="PDF"></i>`, className: "btn bg-gradient-danger", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
			{ extend: "colvis", text: `Columnas`, className: "btn bg-gradient-gray", exportOptions: { columns: "th:not(:last-child)", }, },
		],
		"ajax":		{
			url: '../ajax/ingreso.php?op=listar',
			type: "get",
			dataType: "json",
			error: function (e) {
				console.log(e.responseText);
			}
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
      { targets: [1], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      // { targets: [4],  visible: false,  searchable: false,  },
    ],
	}).DataTable();
}


//Función ListarArticulos
function listarArticulos() {
	tabla_articulo = $('#tblarticulos').dataTable(	{
		lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]], //mostramos el menú de registros a revisar
		"aProcessing": true,//Activamos el procesamiento del datatables
		"aServerSide": true,//Paginación y filtrado realizados por el servidor
		dom: '<Bl<f>rtip>',//Definimos los elementos del control de tabla
		buttons: [{ extend: "colvis", text: `Columnas`, className: "btn bg-gradient-gray", exportOptions: { columns: "th:not(:last-child)", }, },	],
		"ajax":		{
			url: '../ajax/ingreso.php?op=listarArticulos',
			type: "get",
			dataType: "json",
			error: function (e) {
				console.log(e.responseText);
			}
		},
		language: {
			lengthMenu: "Mostrar: _MENU_ registros",
			buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
			sLoadingRecords: '<i class="fa fa-fw fa-spinner fa-pulse"></i> Cargando datos...'
		},
		"bDestroy": true,
		"iDisplayLength": 5,//Paginación
		"order": [[0, "desc"]],//Ordenar (columna,orden)
		columnDefs: [      
      { targets: [4],  visible: false,  searchable: false,  },
    ],
	}).DataTable();
}
//Función para guardar o editar

function guardaryeditar(e) {
	e.preventDefault(); //No se activará la acción predeterminada del evento
	//$("#btnGuardar").prop("disabled",true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url: "../ajax/ingreso.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,
		success: function (datos) {
			bootbox.alert(datos);
			mostrarform(false);
			tabla_ingreso.ajax.reload(null, false);
		}

	});
	limpiar();
}

function mostrar(idingreso) {
	$.post("../ajax/ingreso.php?op=mostrar", { idingreso: idingreso }, function (data, status) {
		data = JSON.parse(data);
		mostrarform(true);

		$("#idproveedor").val(data.idproveedor);
		$("#idproveedor").selectpicker('refresh');
		$("#tipo_comprobante").val(data.tipo_comprobante);
		$("#tipo_comprobante").selectpicker('refresh');
		$("#serie_comprobante").val(data.serie_comprobante);
		$("#num_comprobante").val(data.num_comprobante);
		$("#fecha_hora").val(data.fecha);
		$("#impuesto").val(data.impuesto);
		$("#idingreso").val(data.idingreso);

		//Ocultar y mostrar los botones
		$("#btnGuardar").hide();
		$("#btnCancelar").show();
		$("#btnAgregarArt").hide();
	});

	$.post("../ajax/ingreso.php?op=listarDetalle&id=" + idingreso, function (r) {
		$("#detalles").html(r);
	});
}

//Función para anular registros
function anular(idingreso) {
	bootbox.confirm("¿Está Seguro de anular el ingreso?", function (result) {
		if (result) {
			$.post("../ajax/ingreso.php?op=anular", { idingreso: idingreso }, function (e) {
				bootbox.alert(e);
				tabla_ingreso.ajax.reload(null, false);
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

function agregarDetalle(idarticulo, img) {
	$(`.btn-add-pr-${idarticulo}`).html(`<i class="fa fa-fw fa-spinner fa-pulse"></i>`);
	var cantidad = 1;
	var precio_compra = 1;
	var precio_venta = 1;

	if (idarticulo != "") {
		if ($(`#fila_${idarticulo}`).hasClass("producto_selecionado")) {
			var cant_producto = $(`.cantidad_${idarticulo}`).val(); 
			sub_total = parseInt(cant_producto, 10) + 1;
			$(`.cantidad_${idarticulo}`).val(sub_total);
			modificarSubototales();
			$(`.btn-add-pr-${idarticulo}`).html(`<span class="fa fa-plus"></span>`);
		} else {
			
			$.post("../ajax/ingreso.php?op=optener_producto_compra", { idarticulo: idarticulo }, function (e) {
				e = JSON.parse(e); console.log(e);
				var subtotal = cantidad * precio_compra;
				var fila = `<tr class="filas producto_selecionado" id="fila_${idarticulo}">
					<td>
						<button type="button" class="btn btn-danger" onclick="eliminarDetalle(${idarticulo})">X</button> 
						<button type="button" class="btn btn-info btn-show-op-${idarticulo}" onclick="ver_mas_opciones(${idarticulo}, 'show')"><i class="fa fa-fw fa-gear"></i></button>
						<button type="button" class="btn btn-info btn-hide-op-${idarticulo}" onclick="ver_mas_opciones(${idarticulo}, 'hide' )" style="display: none !important;"><i class="fa fa-fw fa-cogs"></i></button>
					</td>
					<td>
						<div class="user-block">
							<img class="img-circle" src="../files/articulos/${img}" alt="User Image">
							<span class="username"><a href="#">${e.data.producto.nombre}</a></span>				 
							<div class="description">
								<select name="unidad_medida[]" id="unidad_medida_${idarticulo}" class="form-control-sm " required="" onchange="calcular_segun_um(${idarticulo})">
									${e.data.um.um_html_option}
								</select>			
							</div>
						</div>
						<input type="hidden" name="idarticulo[]" value="${idarticulo}">
					</td>
					<td><input class="cantidad_${idarticulo}" type="number" name="cantidad[]" id="cantidad[]" value="${cantidad}" step="0.0001" min="0" onkeyup="modificarSubototales(); "></td>
					<td><input type="number" name="precio_compra[]" class="precio_compra_${idarticulo}" value="${precio_compra}" step="0.0001" min="0" onkeyup="modificarSubototales(); calcular_precio_x_unidad(${idarticulo});"></td>
					<td><input type="number" name="precio_venta[]" value="${precio_venta}" step="0.0001" min="0" onkeyup="modificarSubototales();"></td>
					<td>
						<span name="subtotal" id="subtotal${cont}">${subtotal}</span>
						<input type="hidden" name="subtotal_pr[]" id="subtotal_pr_${cont}" value="${subtotal}">
					</td>
					<td><button type="button" onclick="modificarSubototales()" class="btn btn-info"><i class="fa fa-refresh"></i></button></td>
				</tr>
				<tr class="filas fila_${idarticulo}" style="display: none;">
					<td  colspan="6" > 
						Cantidad por <span class="name-um-${idarticulo}">unidad</span>: <input type="number" name="cantidad_x_um[]" class="cantidad_x_um_${idarticulo}" step="0.0001" min="0" value="0" onkeyup="calcular_precio_x_unidad(${idarticulo});" onchange="calcular_precio_x_unidad(${idarticulo})" >  &nbsp; &nbsp;
						Precio por unidad: <input type="number" name="precio_x_um[]" class="precio_x_um_${idarticulo}" step="0.0001" min="0" value="0" readonly > 
					</td>
				</tr>`;
				cont++;
				detalles = detalles + 1;
				$('#detalles').append(fila);
				modificarSubototales();
				$(`.btn-add-pr-${idarticulo}`).html(`<span class="fa fa-plus"></span>`);
			});			
		}
	}	else {
		alert("Error al ingresar el detalle, revisar los datos del artículo");
	}
}

function modificarSubototales() {
	var cant = document.getElementsByName("cantidad[]");
	var prec = document.getElementsByName("precio_compra[]");
	var sub = document.getElementsByName("subtotal");

	for (var i = 0; i < cant.length; i++) {
		var inpC = cant[i];
		var inpP = prec[i];
		var inpS = sub[i];

		inpS.value = inpC.value * inpP.value;
		var sub_total = (inpC.value * inpP.value);
		document.getElementsByName("subtotal")[i].innerHTML = formato_miles(inpS.value);
		$(`#subtotal_pr_${i}`).val( redondearExp(sub_total, 2));
	}
	calcularTotales();
}

function calcularTotales() {
	var sub = document.getElementsByName("subtotal");
	var total = 0.0;

	for (var i = 0; i < sub.length; i++) {	total += document.getElementsByName("subtotal")[i].value;	}
	$("#total").html("S/. " + formato_miles(total));
	$("#total_compra").val(redondearExp(total, 2));
	evaluar();
}

function evaluar() {
	if (detalles > 0) {	$("#btnGuardar").show();}	else { $("#btnGuardar").hide();	cont = 0;	}
}

function eliminarDetalle(id) {
	$(`#fila_${id}`).remove();
	$(`.fila_${id}`).remove();
	calcularTotales();
	detalles = detalles - 1;
	evaluar();
}

init();

function ver_mas_opciones(id, op) {
	if (op == 'show') {
		$(`.fila_${id}`).show();
		$(`.btn-show-op-${id}`).hide();
		$(`.btn-hide-op-${id}`).show();
	} else if (op == 'hide') {
		$(`.fila_${id}`).hide();
		$(`.btn-show-op-${id}`).show();
		$(`.btn-hide-op-${id}`).hide();
	}	
}

function calcular_segun_um(id) {
	var nombre_um = $(`#unidad_medida_${id} option:selected`).text();
	$(`.name-um-${id}`).html(nombre_um);
}

function calcular_precio_x_unidad(id) {	
	var cantidad = $(`.cantidad_x_um_${id}`).val() == ''  || $(`.cantidad_x_um_${id}`).val() == null ? 0 : parseFloat($(`.cantidad_x_um_${id}`).val()) ;
	var precio_compra = $(`.precio_compra_${id}`).val() == '' || $(`.precio_compra_${id}`).val() == null ? 0 : parseFloat($(`.precio_compra_${id}`).val()) ;
	// console.log(cantidad); console.log(precio_compra);
	$(`.precio_x_um_${id}`).val(redondearExp(precio_compra/cantidad, 2));
}