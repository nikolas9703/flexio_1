$(function () {
    var objFrom = {
        Casco_maritimoForm: $('#formCasco_maritimo'),
    };
    //jQuery Validate
    $('#formCasco_maritimo').validate({
        focusInvalid: true,
        ignore: '',
        wrapper: '',
        submitHandler: function (form) {
            //Habilitar campos ocultos
            $('input:hidden, select:hidden, textarea').removeAttr('disabled');
            $('select[name="campo[estado]"').removeAttr('disabled');
            $('select[name="campo[estado]"').attr('disabled', false);
            //Enviar el formulario
            form.submit();
        }
    });

    /*Input*/
    $('input[name="campo[serie]').rules(
            "add", {required: true
            });

    $('input[name="campo[marca]').rules(
            "add", {required: false,
                rgx: '^[a-zA-Z0-9áéíóúñÁÉÍÓÚ ]+$'
            });

    $('input[name="campo[valor]').rules(
            "add", {required: false,
                number: true,
                messages: {
                    number: 'Por favor, introduzca un número válido.'
                }
            });
    $('input[name="campo[pasajeros]').rules(
            "add", {required: false,
                number: true,
                messages: {
                    number: 'Por favor, introduzca un número válido.'
                }
            });

    /* Validadores */
    $.validator.addMethod(
            "regex",
            function (value, element, regexp) {
                var re = new RegExp(regexp);
                return this.optional(element) || re.test(value);
            },
            "Campo es alfabetico."
            );

    $.validator.addMethod(
            "rgx",
            function (value, element, regexp) {
                var re = new RegExp(regexp);
                return this.optional(element) || re.test(value);
            },
            "Campo es alfanumerico."
            );
    $('input[name="campo[porcentaje_acreedor]').rules(
            "add", {required: false,
                number: true,
                messages: {
                    number: 'Por favor, introduzca un número válido.'
                }
            });

    $("#valor_maritimo").inputmask('currency',{
        prefix: "",
        autoUnmask : true,
        removeMaskOnSubmit: true
    });  

});
$(document).ready(function () {
  // $('.estado_casco').attr('disabled', 'disabled');
    var counter = 2;
    $('#del_file_maritimo').hide();
    $('#add_file_maritimo').click(function(){
            
        $('#file_tools_maritimo').before('<div class="file_upload_maritimo row" id="fmaritimo'+counter+'"><input name="nombre_documento[]" type="text" style="width: 300px!important; float: left;" class="form-control"><input name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"><br><br></div>');
        $('#del_file_maritimo').fadeIn(0);
    counter++;
    });
    $('#del_file_maritimo').click(function(){
        if(counter==3){
            $('#del_file_maritimo').hide();
        }   
        counter--;
        $('#fmaritimo'+counter).remove();
    });  
    $("#porcentaje_acreedor").inputmask('integer', {min: 0, max: 100}).css("text-align", "left");


    if (vista === 'crear' && permiso_cambio_estado === 0) {
        $('.estado_casco').hide();
    }
    if (vista === 'editar' && permiso_editar === 0) {
        $('.guardarMaritimo').attr('disabled', true);
        $('.guardarMaritimo').hide();
        $('#serie_maritimo').attr('disabled', true);
        $('#nombre_embarcacion').attr('disabled', true);
        $('#tipo_maritimo').attr('disabled', true);
        $('#marca_maritimo').attr('disabled', true);
        $('#valor_maritimo').attr('disabled', true);
        $('#pasajeros_maritimo').attr('disabled', true);
        $('#acreedor_maritimo').attr('disabled', true);
        $('#porcentaje_acreedor').attr('disabled', true);
        $('#observaciones_maritimo').attr('disabled', true);
        $('#estado_casco').attr('disabled', true);

        //Verificar si tiene permisos para editar
        if (typeof permiso_editar !== 'undefined')
        {
            if (permiso_editar == '1') {
                setTimeout(function () {
                    $(".guardarMaritimo").prop('disabled', false);

                }, 1000);
            } else
            {
                $(".guardarMaritimo").hide();
            }
        }
    }
});
//Documentos Modal
$('#subirDocumentoLnk').click(function (e) {
    e.preventDefault();
    e.returnValue = false;
    e.stopPropagation();

    //Inicializar opciones del Modal
    $('#documentosModal').modal({
        backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
        show: false
    });

    $('#documentosModal').modal('show');
});

//imprimir formulario de casco maritimo
$('#imprimirLnk').click(function () {
    var id_martimo = $('.uuid_casco_maritimo').val();
    window.location.href = '../imprimirFormulario/' + id_martimo + '?tipo=4';
});
