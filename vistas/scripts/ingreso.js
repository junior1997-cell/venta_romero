var tabla_articulo;
var tabla_ingreso;

//Función que se ejecuta al inicio
function init() {
	mostrarform(false);
	listar();

	$("#formulario").on("submit", function (e) {	guardaryeditar(e);	});
	//Cargamos los items al select proveedor
	$.post("../ajax/ingreso.php?op=selectProveedor", function (r) {	$("#idproveedor").html(r);	$('#idproveedor').selectpicker('refresh');	});
	$('#mCompras').addClass("treeview active");
	$('#lIngresos').addClass("active");
}

//Función limpiar
function limpiar() {
	$("#idproveedor").val("");	$("#idproveedor").selectpicker('refresh');
	$("#proveedor").val("");
	$("#serie_comprobante").val("");
	$("#num_comprobante").val("");
	$("#impuesto").val("0");

	$("#subtotal_compra").val("");	$("#subtotal").html("0.00");
	$("#igv_compra").val("");	$("#igv").html("0.00");
	$("#total_compra").val("");	$("#total").html("0.00");
	$("#total_utilidad").val("");

	$(".filas").remove();
	//Obtenemos la fecha actual
	$('#fecha_hora').val(moment().format('YYYY-MM-DD'));

	//Marcamos el primer tipo_documento
	$("#tipo_comprobante").val("Boleta");	$("#tipo_comprobante").selectpicker('refresh');
}

//Función mostrar formulario
function mostrarform(flag) {
	array_class_compra = [];
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
	}	else {
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
		buttons: [
			{ text: '<i class="fa fa-fw fa-repeat fa-lg" data-toggle="tooltip" data-placement="top" title="Recargar"></i>', className: "btn bg-gradient-info", action: function ( e, dt, node, config ) { tabla_articulo.ajax.reload(null, false); } },
			{ extend: "colvis", text: `Columnas`, className: "btn bg-gradient-gray", exportOptions: { columns: "th:not(:last-child)", }, },	
		],
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
		success: function (e) {
			try {
				e = JSON.parse(e);
				if (e.status == true) {
					mostrarform(false);
					tabla_ingreso.ajax.reload(null, false);
					limpiar();
					sw_success('Exito!','Registo guardado correctamente');
				}else if (e.status = 'duplicado') {
					ver_errores(e);
				}
			} catch (err) {
				console.log('Error: ', err.message); toastr.error('<h5 class="font-size-16px">Error temporal!!</h5> puede intentalo mas tarde, o comuniquese con <i><a href="tel:+51921305769" >921-305-769</a></i> ─ <i><a href="tel:+51921487276" >921-487-276</a></i>');
			}
			
		},

	});
	
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
var array_class_compra = [];
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
	modificarSubototales();
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
				e = JSON.parse(e); //console.log(e);
				var subtotal = cantidad * precio_compra;
				var fila = `<tr class="filas producto_selecionado" id="fila_${idarticulo}">
					<td>
						<button type="button" class="btn btn-danger" onclick="eliminarDetalle(${cont}, ${idarticulo})">X</button>					
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
					<td><input type="number" name="cantidad[]" 			class="form-control cantidad_${cont}" 			value="${cantidad}" 			step="0.0001" min="0" onkeyup="modificarSubototales(); "></td>
					<td><input type="number" name="precio_caja[]" 	class="form-control precio_caja_${cont}" 	value="${cantidad}" 			step="0.0001" min="0" onkeyup="modificarSubototales(); "></td>
					<td>
						<span  class="precio_compra_${cont}">${precio_compra}</span>
						<input type="hidden" name="precio_compra[]" class="precio_compra_${cont}" value="${precio_compra}" 	step="0.0001" min="0" onkeyup="modificarSubototales();" readonly>
					</td>
					<td>
						<input type="number" name="precio_venta[]" 	class="form-control precio_venta_${cont}" 	value="${e.data.producto.precio_venta}" 	step="0.0001" min="0" onkeyup="modificarSubototales();">
						<input type="hidden" name="utilidad_xp[]" class="utilidad_xp_${cont}" value="0">
						<input type="hidden" name="utilidad_tp[]" class="utilidad_tp_${cont}" value="0">
					</td>
					<td class="text-right">
						<span name="subtotal" class="subtotal_${cont}">${subtotal}</span>
						<input type="hidden" name="subtotal_pr[]" class="subtotal_${cont}" value="${subtotal}">
					</td>
					<td><button type="button" onclick="modificarSubototales()" class="btn btn-info"><i class="fa fa-refresh"></i></button></td>
				</tr>`;
				
				detalles = detalles + 1;
				$('#detalles').append(fila);

				array_class_compra.push({ id_cont: cont });

				cont++;
				modificarSubototales();
				$(`.btn-add-pr-${idarticulo}`).html(`<span class="fa fa-plus"></span>`);
				//console.log(array_class_compra);
			});			
		}
	}	else {
		alert("Error al ingresar el detalle, revisar los datos del artículo");
	}
}

function modificarSubototales() {
	var val_igv = $('#impuesto').val(); //console.log(array_class_compra);
	if (array_class_compra.length === 0) {
	} else {
		array_class_compra.forEach((val, key) => {
			var cantidad 		= $(`.cantidad_${val.id_cont}`).val() == '' || $(`.cantidad_${val.id_cont}`).val() == null ? 0 : parseFloat($(`.cantidad_${val.id_cont}`).val());			
			var precio_caja = $(`.precio_caja_${val.id_cont}`).val() == '' || $(`.precio_caja_${val.id_cont}`).val() == null ? 0 : parseFloat($(`.precio_caja_${val.id_cont}`).val());			
			var compra 			= $(`.precio_compra_${val.id_cont}`).val() == '' || $(`.precio_compra_${val.id_cont}`).val() == null ? 0 : parseFloat($(`.precio_compra_${val.id_cont}`).val());			
			var venta 			= $(`.precio_venta_${val.id_cont}`).val() == '' || $(`.precio_venta_${val.id_cont}`).val() == null ? 0 : parseFloat($(`.precio_venta_${val.id_cont}`).val());			
			// var descuento = $(`.descuento_${val.id_cont}`).val() == '' || $(`.descuento_${val.id_cont}`).val() == null ? 0 : parseFloat($(`.descuento_${val.id_cont}`).val());
			
			// Calculamos: Subtotal de cada producto
			var subtotal_producto = precio_caja;	
			
			// Calculamos: precio unitario de cada producto
			var precio_unitario = precio_caja / cantidad;	
			var utilidad_xp				= venta - precio_unitario;
			var utilidad_tp				= utilidad_xp * cantidad;
			
			$(`.subtotal_${val.id_cont}`).html(formato_miles(subtotal_producto)).val( redondearExp(subtotal_producto) );			
			$(`.precio_compra_${val.id_cont}`).html(formato_miles(precio_unitario)).val( redondearExp(precio_unitario) );		
			$(`.precio_venta_${val.id_cont}`).attr('min',redondearExp(precio_unitario));	
			$(`.utilidad_xp_${val.id_cont}`).val(redondearExp(utilidad_xp));	
			$(`.utilidad_tp_${val.id_cont}`).val(redondearExp(utilidad_tp));				
		});
		
	}

	calcularTotales();
}

function calcularTotales() {
	var val_igv 	= $('#impuesto').val() == '' || $(`#impuesto`).val() == null ? 0 : parseFloat($(`#impuesto`).val());
	var igv 			= 0; var precio_sin_igv 			= 0;
	var total 		= 0.0;
	var utilidad 	= 0.0;

	array_class_compra.forEach((element, index) => {
    total += parseFloat($(`.subtotal_${element.id_cont}`).val()); //console.log(total);
    // descuento += parseFloat($(`.descuento_${element.id_cont}`).val());
    utilidad += parseFloat($(`.utilidad_tp_${element.id_cont}`).val());
  });

	val_igv 				= val_igv / 100;
	precio_sin_igv 	= total / (1 + val_igv);
	igv 						= total - precio_sin_igv; console.log(val_igv, precio_sin_igv, igv);

	$("#subtotal").html("S/. " + formato_miles(precio_sin_igv));	$("#subtotal_compra").val(redondearExp(precio_sin_igv));
	$("#igv").html("S/. " + formato_miles(igv));	$("#igv_compra").val(redondearExp(igv)); $(".igv_percent").html(`IGV (${val_igv * 100}%)`);
	$("#total").html("S/. " + formato_miles(total));	$("#total_compra").val(redondearExp(total));
	$("#total_utilidad").val(redondearExp(utilidad));
	evaluar();
}

function evaluar() {
	if (detalles > 0) {	$("#btnGuardar").show();}	else { $("#btnGuardar").hide();	cont = 0;	}
}

function eliminarDetalle(cont, id) {
	$(`#fila_${id}`).remove();
	array_class_compra.forEach(function (val, index, object) {
    if (val.id_cont === cont) { object.splice(index, 1);  }
  });

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

function calcular_precio_x_unidad(id, cont) {	console.log(id, cont);
	var cantidad = $(`.cantidad_x_um_${id}`).val() == ''  || $(`.cantidad_x_um_${id}`).val() == null ? 0 : parseFloat($(`.cantidad_x_um_${id}`).val()) ;
	var precio_compra = $(`.precio_compra_${cont}`).val() == '' || $(`.precio_compra_${cont}`).val() == null ? 0 : parseFloat($(`.precio_compra_${cont}`).val()) ;
	// console.log(cantidad); console.log(precio_compra);
	$(`.precio_x_um_${id}`).val(redondearExp(precio_compra/cantidad, 2));
	$(`.precio_venta_${cont}`).attr('min',redondearExp(precio_compra/cantidad, 2));

}

function detalle_x_comprobante(id) {
	$(".detalle-x-comprobante").html(`<div class="text-center"><i class="fa fa-fw fa-spinner fa-pulse fa-2x"></i> <br> Cargando datos...</div>  `);
	$('.tooltip').remove();
	$("#modal-ver-detalle").modal('show');
	$.post("../ajax/ajax_general.php?op=detalle_x_comprobante_compra", {id:id}, function (e) {	
		e = JSON.parse(e); console.log(e);
		$(".detalle-x-comprobante").html(e.data);	
	});	
}