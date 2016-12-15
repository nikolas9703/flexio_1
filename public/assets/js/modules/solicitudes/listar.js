var listarSolicitudes = (function() {
$(".ramo").select2({
    theme: "bootstrap",
    width:"100%"
});
"use strict";
//Init Bootstrap Calendar Plugin
$('#fecha_creacion').daterangepicker({
 format: 'DD/MM/YYYY',
 showDropdowns: true,
 defaultDate: '',
 singleDatePicker: true
}).val('');
var opcionesModal = $('#opcionesModal');
var formularioSolicitudesModal = $('#SolicitudesForm');
var botones = {
    modalOpcionesCrear: ".modalOpcionesCrear",
    boton_accion : "#botonAccionar"
};
        
//var botones_crear = $('body').text();
        
var eventos = function(){
   //Boton de Opciones
    $('#moduloOpciones').on("click", botones.modalOpcionesCrear, function(e){
        var pantalla = $('#menu_crear');
        pantalla.css('display', 'block');
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();        
       
        //Inicializar opciones del Modal
        opcionesModal.modal({
                backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
                show: false
        });
    opcionesModal.find('.modal-title').empty().append('<b>Solicitudes</b>');
    opcionesModal.find('.modal-body').empty().append(pantalla);
    opcionesModal.find('.modal-footer').empty();
    opcionesModal.modal('show');    
    });
    
    //Formulario de Solicitudes
    $(opcionesModal).on("click", botones.boton_accion, function(e){       
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();

        //Cerrar modal de opciones
        opcionesModal.modal('hide');

        var solicitud_id = $(this).attr("data-formulario");     
        //Inicializar opciones del Modal
        formularioSolicitudesModal.modal({
        backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
        show: false
        });
        formularioSolicitudesModal.find('#solicitudes_id').val(solicitud_id);
        formularioSolicitudesModal.submit();
    });
    
    }
                
return{
init: function() {
        eventos();
}
      };
})();
listarSolicitudes.init();