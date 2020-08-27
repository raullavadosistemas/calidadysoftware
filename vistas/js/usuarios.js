var tabla;

//Función que se ejecuta al inicio
function init() {

    listar();

    //cuando se da click al boton submit entonces se ejecuta la funcion guardaryeditar(e);
    $("#usuario_form").on("submit", function(e) {

        guardaryeditar(e);
    })

    //cambia el titulo de la ventana modal cuando se da click al boton
    $("#add_button").click(function() {

        $(".modal-title").text("Agregar Usuario");

    });

}

//funcion que limpia los campos del formulario

function limpiar() {

    $("#dni").val("");
    $('#nombres').val("");
    $('#apellidos').val("");
    $('#cargo').val("");
    $('#usuario').val("");
    $('#password').val("");
    $('#password2').val("");
    $('#telefono').val("");
    $('#correo').val("");
    $('#direccion').val("");
    $('#estado').val("");
    $('#id_usuario').val("");
}

//function listar 

function listar() {

    tabla = $('#usuario_data').dataTable({

        "aProcessing": true, //Activamos el procesamiento del datatables
        "aServerSide": true, //Paginación y filtrado realizados por el servidor
        dom: 'Bfrtip', //Definimos los elementos del control de tabla

        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',

            {

                extend: 'pdfHtml5',

                orientation: 'landscape',

                pageSize: 'LEGAL'

            }
        ],


        "ajax":

        {
            url: '../ajax/usuario.php?op=listar',
            type: "get",
            dataType: "json",
            error: function(e) {
                console.log(e.responseText);
            }
        },

        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10, //Por cada 10 registros hace una paginación
        "order": [
            [0, "desc"]
        ], //Ordenar (columna,orden)

        "language": {

            "sProcessing": "Procesando...",

            "sLengthMenu": "Mostrar _MENU_ registros",

            "sZeroRecords": "No se encontraron resultados",

            "sEmptyTable": "Ningún dato disponible en esta tabla",

            "sInfo": "Mostrando un total de _TOTAL_ registros",

            "sInfoEmpty": "Mostrando un total de 0 registros",

            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",

            "sInfoPostFix": "",

            "sSearch": "Buscar:",

            "sUrl": "",

            "sInfoThousands": ",",

            "sLoadingRecords": "Cargando...",

            "oPaginate": {

                "sFirst": "Primero",

                "sLast": "Último",

                "sNext": "Siguiente",

                "sPrevious": "Anterior"

            },

            "oAria": {

                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",

                "sSortDescending": ": Activar para ordenar la columna de manera descendente"

            }

        } //cerrando language




    }).DataTable();
}

//Mostrar datos del usuario en la ventana modal del formulario 

function mostrar(id_usuario) {

    $.post("../ajax/usuario.php?op=mostrar", { id_usuario: id_usuario }, function(data, status)

        {

            data = JSON.parse(data);

            $("#usuarioModal").modal("show");
            $("#dni").val(data.dni);
            $('#nombres').val(data.nombres);
            $('#apellidos').val(data.apellidos);
            $('#cargo').val(data.cargo);
            $('#usuario').val(data.usuario);
            $('#password').val(data.password);
            $('#password2').val(data.password2);
            $('#telefono').val(data.telefono);
            $('#correo').val(data.correo);
            $('#direccion').val(data.direccion);
            $('#estado').val(data.estado);
            $('.modal-title').text("Editar Usuario");
            $('#id_usuario').val(id_usuario);
            $('#action').val("Edit");


        });

}

//la funcion guardaryeditar(e); se llama cuando se da click al boton submit  

function guardaryeditar(e) {

    e.preventDefault(); //No se activará la acción predeterminada del evento
    var formData = new FormData($("#usuario_form")[0]);

    var password = $("#password").val();
    var password2 = $("#password2").val();

    //si el password conincide entonces se envia el formulario
    if (password == password2) {

        $.ajax({

            url: "../ajax/usuario.php?op=guardaryeditar",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,

            success: function(datos) {

                console.log(datos);

                $('#usuario_form')[0].reset();
                $('#usuarioModal').modal('hide');

                $('#resultados_ajax').html(datos);
                $('#usuario_data').DataTable().ajax.reload();

                limpiar();

            }
        });

    } //cierre de la validacion
    else {

        bootbox.alert("No coincide el password");
    }

}

//EDITAR ESTADO DEL USUARIO
//importante:id_usuario, est se envia por post via ajax
function cambiarEstado(id_usuario, est) {

    bootbox.confirm("¿Está Seguro de cambiar de estado?", function(result) {
        if (result) {

            $.ajax({
                url: "../ajax/usuario.php?op=activarydesactivar",
                method: "POST",

                //toma el valor del id y del estado
                data: { id_usuario: id_usuario, est: est },

                success: function(data) {

                    $('#usuario_data').DataTable().ajax.reload();

                }

            });


        }

    }); //bootbox

}

init();