var tabla_x_comprobante; var tabla_x_producto;

//Función que se ejecuta al inicio
function init() {

	$('#mConsultaV').addClass("treeview active");
	$('#lConsulasV').addClass("active");
	
	$.post("../ajax/consultas.php?op=selectCliente", function (r) {	$("#c_idcliente").html(r);	$('#c_idcliente').selectpicker('refresh');	listar_por_comprobante();	});		

	$.post("../ajax/consultas.php?op=selectCategoria", function (r) {	$("#p_idcategoria").html(r);	$('#p_idcategoria').selectpicker('refresh'); });	
	$.post("../ajax/consultas.php?op=selectCliente", function (r) {	$("#p_idcliente").html(r);	$('#p_idcliente').selectpicker('refresh'); });	
	$.post("../ajax/consultas.php?op=selectNumero", function (r) {	$("#p_numero").html(r);	$('#p_numero').selectpicker('refresh');	listar_por_producto();	});	
	
}

//Función Listar
function listar_por_comprobante() {	

	tabla_x_comprobante = $('#tbla_por_comprobante').dataTable({
		lengthMenu: [[-1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200,]], //mostramos el menú de registros a revisar
		"aProcessing": true,//Activamos el procesamiento del datatables
		"aServerSide": true,//Paginación y filtrado realizados por el servidor
		dom: '<Bl<f>rtip>',//Definimos los elementos del control de tabla
		buttons: [
			{ text: '<i class="fa fa-fw fa-repeat fa-lg" data-toggle="tooltip" data-placement="top" title="Recargar"></i>', className: "btn bg-gradient-info", action: function ( e, dt, node, config ) { tabla_x_comprobante.ajax.reload(null, false); } },
			{ extend: 'copyHtml5', exportOptions: { columns: [1,2,3,4,5,6], }, text: `<i class="fa fa-copy fa-lg" data-toggle="tooltip" data-placement="top" title="Copiar"></i>`, className: "btn bg-gradient-gray", footer: true,  }, 
			{ extend: 'excelHtml5', exportOptions: { columns: [1,2,3,4,5,6], }, text: `<i class="fa fa-fw fa-file-excel-o fa-lg" data-toggle="tooltip" data-placement="top" title="Excel"></i>`, className: "btn bg-gradient-success", footer: true,  }, 
			{ extend: 'pdfHtml5', exportOptions: { columns: [1,2,3,4,5,6], }, text: `<i class="fa fa-fw fa-file-pdf-o fa-lg" data-toggle="tooltip" data-placement="top" title="PDF"></i>`, className: "btn bg-gradient-danger", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
			{ extend: "colvis", text: `Columnas`, className: "btn bg-gradient-gray", exportOptions: { columns: "th:not(:last-child)", }, },
		],
		"ajax": {
			url: '../ajax/consultas.php?op=ventas_x_comprobante',
			data: { fecha_inicio: $("#c_fecha_inicio").val(), fecha_fin: $("#c_fecha_fin").val(), idcliente:  $("#c_idcliente").val() },
			type: "get",
			dataType: "json",
			error: function (e) { console.log(e.responseText); }
		},
		language: {
			lengthMenu: "Mostrar: _MENU_ registros",
			buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
			sLoadingRecords: '<i class="fa fa-fw fa-spinner fa-pulse"></i> Cargando datos...'
		},
		footerCallback: function (tfoot, data, start, end, display) {
			var api1 = this.api(); var total1 = api1.column(4).data().reduce(function (a, b) { return parseFloat(a) + parseFloat(b); }, 0);
			$(api1.column(4).footer()).html(` <span class="float-left">S/</span> <span class="float-right">${formato_miles(total1)}</span>`);

			var api2 = this.api(); var total2 = api2.column(5).data().reduce(function (a, b) { return parseFloat(a) + parseFloat(b); }, 0);
			$(api2.column(5).footer()).html(` <span class="float-left">S/</span> <span class="float-right">${formato_miles(total2)}</span>`);
		},
		"bDestroy": true,
		"iDisplayLength": 10,//Paginación
		"order": [[0, "desc"]],//Ordenar (columna,orden)
		columnDefs: [
			{ targets: [4,5], render: function (data, type) { var number = $.fn.dataTable.render.number(',', '.', 2).display(data); if (type === 'display') { let color = 'numero_positivos'; if (data < 0) {color = 'numero_negativos'; } return `<span class="float-left">S/</span> <span class="float-right ${color} "> ${number} </span>`; } return number; }, },
			{ targets: [1], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
			// { targets: [8],  visible: false,  searchable: false,  },
		],
	}).DataTable();
}

//Función Listar
function listar_por_producto() {	

	tabla_x_producto = $('#tbla_por_producto').dataTable({
		lengthMenu: [[-1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200,]], //mostramos el menú de registros a revisar
		"aProcessing": true,//Activamos el procesamiento del datatables
		"aServerSide": true,//Paginación y filtrado realizados por el servidor
		dom: '<Bl<f>rtip>',//Definimos los elementos del control de tabla
		buttons: [
			{ text: '<i class="fa fa-fw fa-repeat fa-lg" data-toggle="tooltip" data-placement="top" title="Recargar"></i>', className: "btn bg-gradient-info", action: function ( e, dt, node, config ) { tabla_x_producto.ajax.reload(null, false); } },
			{ extend: 'copyHtml5', exportOptions: { columns: [1,2,3,4], }, text: `<i class="fa fa-copy fa-lg" data-toggle="tooltip" data-placement="top" title="Copiar"></i>`, className: "btn bg-gradient-gray", footer: true,  }, 
			{ extend: 'excelHtml5', exportOptions: { columns: [1,2,3,4], }, text: `<i class="fa fa-fw fa-file-excel-o fa-lg" data-toggle="tooltip" data-placement="top" title="Excel"></i>`, className: "btn bg-gradient-success", footer: true,  }, 
			{ extend: 'pdfHtml5', exportOptions: { columns: [1,2,3,4], }, text: `<i class="fa fa-fw fa-file-pdf-o fa-lg" data-toggle="tooltip" data-placement="top" title="PDF"></i>`, className: "btn bg-gradient-danger", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
			{ extend: "colvis", text: `Columnas`, className: "btn bg-gradient-gray", exportOptions: { columns: "th:not(:last-child)", }, },
		],
		"ajax": {
			url: '../ajax/consultas.php?op=ventas_x_producto',
			data: { fecha_inicio: $("#p_fecha_inicio").val(), fecha_fin: $("#p_fecha_fin").val(), idcliente:  $("#p_idcliente").val(), 
			idcategoria:  $("#p_idcategoria").val(), numero:  $("#p_numero").val() },
			type: "get",
			dataType: "json",
			error: function (e) { console.log(e.responseText); }
		},
		language: {
			lengthMenu: "Mostrar: _MENU_ registros",
			buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
			sLoadingRecords: '<i class="fa fa-fw fa-spinner fa-pulse"></i> Cargando datos...'
		},
		footerCallback: function (tfoot, data, start, end, display) {
			var api1 = this.api(); var total1 = api1.column(6).data().reduce(function (a, b) { return parseFloat(a) + parseFloat(b); }, 0);
			$(api1.column(6).footer()).html(` <span class="float-left">S/</span> <span class="float-right">${formato_miles(total1)}</span>`);

			var api2 = this.api(); var total2 = api2.column(7).data().reduce(function (a, b) { return parseFloat(a) + parseFloat(b); }, 0);
			$(api2.column(7).footer()).html(` <span class="float-left">S/</span> <span class="float-right">${formato_miles(total2)}</span>`);

			var api3 = this.api(); var total3 = api3.column(8).data().reduce(function (a, b) { return parseFloat(a) + parseFloat(b); }, 0);
			$(api3.column(8).footer()).html(` <span class="float-left">S/</span> <span class="float-right">${formato_miles(total3)}</span>`);
		},
		"bDestroy": true,
		"iDisplayLength": 10,//Paginación
		"order": [[0, "desc"]],//Ordenar (columna,orden)
		columnDefs: [
			{ targets: [6,7,8], render: function (data, type) { var number = $.fn.dataTable.render.number(',', '.', 2).display(data); if (type === 'display') { let color = 'numero_positivos'; if (data < 0) {color = 'numero_negativos'; } return `<span class="float-left">S/</span> <span class="float-right ${color} "> ${number} </span>`; } return number; }, },
			{ targets: [1], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
			// { targets: [8],  visible: false,  searchable: false,  },
		],
	}).DataTable();
}

function detalle_x_comprobante(id) {
	$(".detalle-x-comprobante").html(`<div class="text-center"><i class="fa fa-fw fa-spinner fa-pulse fa-2x"></i> <br> Cargando datos...</div>  `);
	$('.tooltip').remove();
	$("#modal-ver-detalle").modal('show');
	$.post("../ajax/ajax_general.php?op=detalle_x_comprobante", {id:id}, function (e) {	
		e = JSON.parse(e); console.log(e);
		$(".detalle-x-comprobante").html(e.data);	
	});	
}

function detalle_x_producto(id) {
	toastr_warning('Sin opciones!!', 'No hay nada que mostrar.' );
}


init();