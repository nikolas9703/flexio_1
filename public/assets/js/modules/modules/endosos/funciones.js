function getPoliza(estado){

    if(estado == "Aprobado"){

        $('#cancelar_endoso').addClass('hidden');
        $('#guardar_endoso').addClass('hidden');
        $('#subPanels').addClass('hidden');
        $('#subPanelPoliza').removeClass('hidden');
        $('#fecha_afectacion').attr('data-rule-required','true');
        formularioCrear.getIntereses();
        formularioCrearEndosos.habilitarPoliza();
    }else{

        $('#cancelar_endoso').removeClass('hidden');
        $('#guardar_endoso').removeClass('hidden');    
        $('#subPanels').removeClass('hidden');
        $('#subPanelPoliza').addClass('hidden');
    }
}

$(document).ready(function(){

    $('#endoso_guardar').on('click', function(){
        $('#formPolizasCrear').submit(function(){
            return false;
        });
        formularioCrearEndosos.guardarPoliza();
    });
    

    $('#CrearEndososForm').validate({
        submitHandler: function (form) {
            $('#id_ramo').attr('disabled',false);
            $('#cliente_id').attr('disabled',false);
            $('#id_poliza').attr('disabled',false);
            $('#tipo_endoso').attr('disabled',false);
            $('#motivo_endoso').attr('disabled',false);
            if($('#CrearEndososForm').validate().form() == true){
                form.submit();
            }else{
                return false;
            }
        }
    });
	

    var counter = 2;
    $('#add_file_endoso').click(function(){
            
        $('#file_tools_endoso').before('<div class="file_upload_endoso row" id="fendoso'+counter+'"><input name="nombre_documento[]" type="text" style="width: 300px!important; float: left;" class="form-control"><input name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"><br><br></div>');
        $('#del_file_endoso').fadeIn(0);
    counter++;
    });
    $('#del_file_endoso').click(function(){
        if(counter==3){
            $('#del_file_endoso').hide();
        }   
        counter--;
        $('#fendoso'+counter).remove();
    });

    $('#id_ramo,#cliente_id,#id_poliza,#motivo_endoso').select2();

    $('#imprimirEndososBtn').on('click', function(){
    	var id_endoso = $('#id_endosos').val();
    	window.open('../imprimirEndoso/'+id_endoso); 
    });

    $('#documentosEndososBtn').on('click', function(){

    	$('#guardarDocumento').append('<input type="hidden" name="vista" id="vista" value="editar">')
    	$('#id_endoso').val($('#id_endosos').val())
    	$('#documentosModal').modal('show');
    });

    if(vista == "crear"){

        $('#id_tab_documentos').hide();
        $('#id_tab_documentos').removeClass('active');
        $('#id_tab_polizas').hide();

        if(id_poliza != "" && tipo_endoso != "" && id_motivo != ""){

            $('#id_ramo').attr('disabled',true);
            $('#cliente_id').attr('disabled',true);
            $('#id_poliza').attr('disabled',true);
            $('#tipo_endoso').attr('disabled',true);
            $('#motivo_endoso').attr('disabled',true);
            verModificaPrima();

        }else if(id_poliza != "" && tipo_endoso == "" && id_motivo == ""){

            $('#id_ramo').attr('disabled',true);
            $('#cliente_id').attr('disabled',true);
            $('#id_poliza').attr('disabled',true);
        }

        
    }
    
    if(vista == "editar"){

        $('#documentos_endosos').empty();
        $('#id_ramo').attr('disabled',true);
        $('#cliente_id').attr('disabled',true);
        $('#id_poliza').attr('disabled',true);
        formularioCrearEndosos.getPolizaEndoso();
    }

}); 




