var tabla;

//Función que se ejecuta al inicio
function init() {
	mostrarform(false);
	listar();

	$("#formulario").on("submit", function (e) {
		guardaryeditar(e);
	});
	$('#mCompras').addClass("treeview active");
	$('#lProveedores').addClass("active");
}

//Función limpiar
function limpiar() {
	$("#nombre").val("");
	$("#num_documento").val("");
	$("#direccion").val("");
	$("#telefono").val("");
	$("#email").val("");
	$("#idpersona").val("");
}

//Función mostrar formulario
function mostrarform(flag) {
	limpiar();
	if (flag) {
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		$("#btnGuardar").prop("disabled", false);
		$("#btnagregar").hide();
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
	tabla = $('#tbllistado').dataTable(	{
		lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]], //mostramos el menú de registros a revisar
		"aProcessing": true,//Activamos el procesamiento del datatables
		"aServerSide": true,//Paginación y filtrado realizados por el servidor
		dom: '<Bl<f>rtip>',//Definimos los elementos del control de tabla
		buttons: [
			{ text: '<i class="fa fa-fw fa-repeat fa-lg" data-toggle="tooltip" data-placement="top" title="Recargar"></i>', className: "btn bg-gradient-info", action: function ( e, dt, node, config ) { tabla.ajax.reload(null, false); } },
			{ extend: 'copyHtml5', exportOptions: { columns: [1,2,3,4,5,6], }, text: `<i class="fa fa-copy fa-lg" data-toggle="tooltip" data-placement="top" title="Copiar"></i>`, className: "btn bg-gradient-gray", footer: true,  }, 
			{ extend: 'excelHtml5', exportOptions: { columns: [1,2,3,4,5,6], }, text: `<i class="fa fa-fw fa-file-excel-o fa-lg" data-toggle="tooltip" data-placement="top" title="Excel"></i>`, className: "btn bg-gradient-success", footer: true,  }, 
			{ extend: 'pdfHtml5', exportOptions: { columns: [1,2,3,4,5,6], }, text: `<i class="fa fa-fw fa-file-pdf-o fa-lg" data-toggle="tooltip" data-placement="top" title="PDF"></i>`, className: "btn bg-gradient-danger", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
			{ extend: "colvis", text: `Columnas`, className: "btn bg-gradient-gray", exportOptions: { columns: "th:not(:last-child)", }, },
		],
		"ajax":	{
			url: '../ajax/persona.php?op=listarp',
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
		"order": [[0, "desc"]]//Ordenar (columna,orden)
	}).DataTable();
}
//Función para guardar o editar

function guardaryeditar(e) {
	e.preventDefault(); //No se activará la acción predeterminada del evento
	$("#btnGuardar").prop("disabled", true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url: "../ajax/persona.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function (datos) {
			bootbox.alert(datos);
			mostrarform(false);
			tabla.ajax.reload();
		}

	});
	limpiar();
}

function mostrar(idpersona) {
	$.post("../ajax/persona.php?op=mostrar", { idpersona: idpersona }, function (data, status) {
		data = JSON.parse(data);
		mostrarform(true);

		$("#nombre").val(data.nombre);
		$("#tipo_documento").val(data.tipo_documento);
		$("#tipo_documento").selectpicker('refresh');
		$("#num_documento").val(data.num_documento);
		$("#direccion").val(data.direccion);
		$("#telefono").val(data.telefono);
		$("#email").val(data.email);
		$("#idpersona").val(data.idpersona);


	})
}

//Función para eliminar registros
function eliminar(idpersona) {
	bootbox.confirm("¿Está Seguro de eliminar el proveedor?", function (result) {
		if (result) {
			$.post("../ajax/persona.php?op=eliminar", { idpersona: idpersona }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

function activar(idpersona) {
	bootbox.confirm("¿Está Seguro de activar el proveedor?", function (result) {
		if (result) {
			$.post("../ajax/persona.php?op=activar", { idpersona: idpersona }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

init();