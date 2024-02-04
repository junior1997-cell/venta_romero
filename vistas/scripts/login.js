function login_data(e) {
  // e.preventDefault();
  var logina = $("#logina").val();
  var clavea = $("#clavea").val();

  $.post("../ajax/usuario.php?op=verificar",  { "logina": logina, "clavea": clavea }, function (data) {
    if (data != "null") {
      $(location).attr("href", "escritorio.php");
    }  else {
      toastr_error('Error', 'Usuario o clave no son correctos, porfavor vuelva a intentar.');
    }
  });
}

$('.btn-red-social').on('click', function () {
  toastr_warning('VACIO!', 'No existe una <b>📶 URL</b> valida para navegar, porfavor comuniquese con el administrador o programador de este sitio web.');
});


// .....::::::::::::::::::::::::::::::::::::: V A L I D A T E   F O R M  :::::::::::::::::::::::::::::::::::::::..
$(function () {   

  // Aplicando la validacion del select cada vez que cambie

  $("#form-login").validate({
    ignore: '.select2-input, .select2-focusser, .note-editor *',
    rules: {
      logina: { required: true, minlength:2, maxlength:50 },
      clavea: { required: true, minlength:2, maxlength:50},     
    },
    messages: {
      logina: { required: "Campo requerido", minlength: "Minimo {0} caracteres", maxlength: "Maximo {0} Caracteres" },
      clavea: { required: "Campo requerido", minlength: "Minimo {0} caracteres", maxlength: "Maximo {0} Caracteres" },      
    },

    errorElement: "span",

    errorPlacement: function (error, element) {
      error.addClass("invalid-feedback");
      element.closest(".form-group").append(error);
    },

    highlight: function (element, errorClass, validClass) {
      $(element).addClass("is-invalid").removeClass("is-valid");
    },

    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass("is-invalid").addClass("is-valid");
    },

    submitHandler: function (e) {
      login_data(e);
    },

  });

});