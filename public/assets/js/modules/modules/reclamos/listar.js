var listarReclamos = (function() {
$(".ramo").select2({
    theme: "bootstrap",
    width:"100%"
});
"use strict";
//Init Bootstrap Calendar Plugin
$('#inicio_creacion').daterangepicker({
 format: 'DD/MM/YYYY',
 showDropdowns: true,
 defaultDate: '',
 singleDatePicker: true
}).val('');
$('#fin_creacion').daterangepicker({
 format: 'DD/MM/YYYY',
 showDropdowns: true,
 defaultDate: '',
 singleDatePicker: true
}).val('');

var opcionesModal = $('#opcionesModal');
var formularioReclamosModal = $('#ReclamosForm');
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
    opcionesModal.find('.modal-title').empty().append('<b>Reclamos</b>');
    opcionesModal.find('.modal-body').empty().append(pantalla);
    opcionesModal.find('.modal-footer').empty();
    opcionesModal.modal('show');    
    });
    
    //Formulario de Reclamos
    $(opcionesModal).on("click", botones.boton_accion, function(e){       
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();

        //Cerrar modal de opciones
        opcionesModal.modal('hide');

        var ramo_id = $(this).attr("data-formulario");     
        //Inicializar opciones del Modal
        formularioReclamosModal.modal({
        backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
        show: false
        });
        formularioReclamosModal.find('#ramo_id').val(ramo_id);
        formularioReclamosModal.submit();
    });
    
    


    }
                
return{
init: function() {
        eventos();
}
      };
})();
listarReclamos.init();

$(document).ready(function(){
    var counter = 2;
    $('#del_file_solicitud').hide();
    $('#add_file_solicitud').click(function(){
            
        $('#file_tools_solicitud').before('<div class="file_upload_solicitud row" id="fsolicitud'+counter+'"><input name="nombre_documento[]" type="text" style="width: 300px!important; float: left;" class="form-control"><input name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"><br><br></div>');
        $('#del_file_solicitud').fadeIn(0);
    counter++;
    });
    $('#del_file_solicitud').click(function(){
        if(counter == 3){
            $('#del_file_solicitud').hide();
        }   
        counter--;
        $('#fsolicitud'+counter).remove();
    });  
});