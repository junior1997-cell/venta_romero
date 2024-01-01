var tabla;

//Función que se ejecuta al inicio
function init() {
	mostrarform(false);
	listar();

	$("#formulario").on("submit", function (e) {guardaryeditar(e);	})

	//Cargamos los items al select categoria
	$.post("../ajax/articulo.php?op=selectCategoria", function (r) {$("#idcategoria").html(r); $('#idcategoria').selectpicker('refresh');});
	$("#imagenmuestra").hide();
	$('#mAlmacen').addClass("treeview active");
	$('#lArticulos').addClass("active");
}

//Función limpiar
function limpiar() {
	
	$("#nombre").val("");
	$("#descripcion").val("");
	$("#stock").val("");		
	$("#idarticulo").val("");
	$("#precio_compra").val("");
	$("#precio_venta").val("");

	$("#codigo").val("");
	$("#print").hide();

	$("#imagenmuestra").attr("src", "").hide();
	$("#imagenactual").val("");
	$("#imagen").val("");
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
			{ extend: 'copyHtml5', exportOptions: { columns: [1,2,3,4], }, text: `<i class="fa fa-copy fa-lg" data-toggle="tooltip" data-placement="top" title="Copiar"></i>`, className: "btn bg-gradient-gray", footer: true,  }, 
			{ extend: 'excelHtml5', exportOptions: { columns: [1,2,3,4], }, text: `<i class="fa fa-fw fa-file-excel-o fa-lg" data-toggle="tooltip" data-placement="top" title="Excel"></i>`, className: "btn bg-gradient-success", footer: true,  }, 
			{ extend: 'pdfHtml5', exportOptions: { columns: [1,2,3,4], }, text: `<i class="fa fa-fw fa-file-pdf-o fa-lg" data-toggle="tooltip" data-placement="top" title="PDF"></i>`, className: "btn bg-gradient-danger", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
			{ extend: "colvis", text: `Columnas`, className: "btn bg-gradient-gray", exportOptions: { columns: "th:not(:last-child)", }, },
		],
		"ajax":
		{
			url: '../ajax/articulo.php?op=listar',
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
		url: "../ajax/articulo.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function (datos) {
			bootbox.alert(datos);
			mostrarform(false);
			tabla.ajax.reload(null, false);
		}

	});
	limpiar();
}

function mostrar(idarticulo) {
	$.post("../ajax/articulo.php?op=mostrar", { idarticulo: idarticulo }, function (data, status) {
		data = JSON.parse(data);
		mostrarform(true);

		$("#idcategoria").val(data.idcategoria);
		$('#idcategoria').selectpicker('refresh');
		$("#codigo").val(data.codigo);
		$("#nombre").val(data.nombre);
		$("#stock").val(data.stock);
		$("#descripcion").val(data.descripcion);
		$("#imagenmuestra").show();
		$("#imagenmuestra").attr("src", "../files/articulos/" + data.imagen);
		$("#imagenactual").val(data.imagen);
		$("#idarticulo").val(data.idarticulo);

		$("#precio_compra").val(data.precio_compra);
		$("#precio_venta").val(data.precio_venta);

		generarbarcode();

	})
}

//Función para desactivar registros
function desactivar(idarticulo) {
	bootbox.confirm("¿Está Seguro de desactivar el artículo?", function (result) {
		if (result) {
			$.post("../ajax/articulo.php?op=desactivar", { idarticulo: idarticulo }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload(null, false);
			});
		}
	})
}

//Función para activar registros
function activar(idarticulo) {
	bootbox.confirm("¿Está Seguro de activar el Artículo?", function (result) {
		if (result) {
			$.post("../ajax/articulo.php?op=activar", { idarticulo: idarticulo }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload(null, false);
			});
		}
	})
}

//función para generar el código de barras
function generarbarcode() {
	codigo = $("#codigo").val();
	JsBarcode("#barcode", codigo);
	$("#print").show();
}

//Función para imprimir el Código de barras
function imprimir() {
	$("#print").printArea();
}

init();