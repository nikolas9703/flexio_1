var listarAjustadores = (function () {
$("#mensaje").hide();
    "use strict";
//Init Bootstrap Calendar Plugin
    $('#fecha_creacion').daterangepicker({
        format: 'DD/MM/YYYY',
        showDropdowns: true,
        defaultDate: '',
        singleDatePicker: true
    }).val('');
    var opcionesModal = $('#opcionesModal');
    var formularioAjustadoresModal = $('#AjustadoresForm');
    var botones = {
        modalOpcionesCrear: ".modalOpcionesCrear",
        boton_accion: "#botonAccionar"
    };

//var botones_crear = $('body').text();

    var eventos = function () {
        //Boton de Opciones
        $('#moduloOpciones').on("click", botones.modalOpcionesCrear, function (e) {
            var pantalla = $('#menu_crear');
            pantalla.css('display', 'block');
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            //Inicializar opciones del Modal
            opcionesModal.modal({
                backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
                show: false
            });
            opcionesModal.find('.modal-title').empty().append('<b>Ajustadores</b>');
            opcionesModal.find('.modal-body').empty().append(pantalla);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
        });

        //Formulario de Ajustadores
        $(opcionesModal).on("click", botones.boton_accion, function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            //Cerrar modal de opciones
            opcionesModal.modal('hide');

            var ajustadores_id = $(this).attr("data-formulario");
            //Inicializar opciones del Modal
            formularioAjustadoresModal.modal({
                backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
                show: false
            });
            formularioAjustadoresModal.find('#ajustadores_id').val(ajustadores_id);
            formularioAjustadoresModal.submit();
        });

    }

    $('#moduloOpciones').click(function () {
        localStorage.removeItem('tomo_n');
        localStorage.removeItem('asiento_n');
        localStorage.removeItem('pasaporte');
        localStorage.removeItem('provincia');
        localStorage.removeItem('letras');
        localStorage.removeItem('folio');
        localStorage.removeItem('tomo_j');
        localStorage.removeItem('asiento_j');
        localStorage.removeItem('digverificador');
        localStorage.removeItem('nombre');
        localStorage.removeItem('telefono');
        localStorage.removeItem('email');
        localStorage.removeItem('direccion');
        localStorage.removeItem('estado');
        localStorage.removeItem('identificacion');
        
    });

    return{
        init: function () {
            eventos();
        }
    };
})();
listarAjustadores.init();