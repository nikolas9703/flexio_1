var formularioProveedor = (function(){
    var st = {
        //formulario principal
        iFormaPago: "#forma_pago",
        iTituloBalance: "#campo\\[titulo2\\]"
    };

    var dom = {};

    var catchDom = function(){
        //formulario principal
        dom.iFormaPago = $(st.iFormaPago);
        dom.iTituloBalance = $(st.iTituloBalance);
        $("#retiene_impuesto").html('<option value="no">No</option><option value="si">Sí</option>');
        $("#retiene_impuesto").val(window.retiene_impuesto);
    };

    var suscribeEvents = function(){

    };

    var mostrarOcultarCamposAch = function(){
      var selected = dom.iFormaPago.find('option:selected').text();
      if(selected.match(/ach/gi)){
        $('.campos_metodo_ach').removeClass('hide');
      }else{
        $('.campos_metodo_ach').addClass('hide');
      }
    };

    var mejorasVisuales = function(){
        var colTituloBalance = dom.iTituloBalance.parent();

        //Aplicando estilos al titulo de balance del proveedor
        colTituloBalance.removeClass("col-lg-3").addClass("col-lg-6");
        colTituloBalance.css('cssText', 'clear:both;padding-top:30px;margin-bottom:100px !important');

        //limpiando chosens
        dom.iFormaPago.find('option[value=""]').remove();
        dom.iFormaPago.trigger("chosen:updated");

        //Evento:
        dom.iFormaPago.on('change', function(evt, params) {
          mostrarOcultarCamposAch();
        });

        mostrarOcultarCamposAch();
    };

    // Función que inicializará los funciones decritas anteriormente.
    var initialize = function(){
        catchDom();
        suscribeEvents();
        mejorasVisuales();
    };

    /* Retorna un objeto literal con el método init haciendo referencia a la
       función initialize. */
    return{
        init:initialize
    }
})();

formularioProveedor.init();

$(function(){
    //DEFINIENDO EL FORMULARIO
    var formulario = "";

    if($(document).find("#crearProveedoresForm").length > 0)
    {
        formulario = "#crearProveedoresForm";
    }
    else
    {
        formulario = "#editarProveedoresForm";
    }

    //console.log(formulario);

    //FUNCION QUE MEJORA LA VISUALIZACION DEL FORMULARIO
    $(".table-responsive").css("padding-bottom", "100px");
    $(".table-responsive").append('<span class="tabla_dinamica_error"></span>');

    $('input[name="campo[cheque]"]').css("margin-left", "-64px");
    $('input[name="campo[cheque]"]').css("margin-top", "-7px");
    $('input[name="campo[cheque]"]').parent().parent().find("label").removeClass("pull-right");
    $('input[name="campo[cheque]"]').parent().parent().find("label").css("padding-top", "20px");

   // $('input[name="campo[saldo]"]').parent().parent().append('<span class="label label-danger" style="margin-top: 10px;float: left;padding: 5px; width: 100%; font-weight:bold;font-size: 13px;">Saldo por pagar</span>');
   // $('input[name="campo[lcredito]"]').parent().parent().append('<span class="label label-success" style="margin-top: 10px;float: left;padding: 5px; width: 100%; font-weight:bold; background-color:green;font-size: 13px;">Cr&eacute;dito a favor</span>');

    $(".anterior").css("background-color", "black");
    $(".siguiente").css("background-color", "black");
    $(".siguiente").parent().addClass("pull-right");


    //COMPORTAMIENTOS EXCLUSIVOS DEL FORMULARIO DE EDICION
    if(formulario == "#editarProveedoresForm")
    {
        $(".anterior").click(function(){
            if(uuid_anterior.length > 0)
            {
                window.location.href = phost() + "proveedores/ver/" + uuid_anterior;
            }
            else
            {
                toastr.error("No hay registros anteriores a este.");
            }
        });

        $(".siguiente").click(function(){
            if(uuid_siguiente.length > 0)
            {
                window.location.href = phost() + "proveedores/ver/" + uuid_siguiente;
            }
            else
            {
                toastr.error("No hay registros siguientes a este.");
            }
        });


    }



    //ELEMENTOS DE TIPO CHOSEN
    $(".chosen").chosen({
        width: '100%',
        allow_single_deselect: true
    });

    $('.categorias').attr("data-placeholder", "Seleccione");
    $('.categorias').find("option").each(function(){

        if(formulario != "#editarProveedoresForm")
        {
           // $(this).removeAttr("selected", "false");
        }

        if($(this).val().length < 1)
        {
            $(this).remove();
        }

        $('.categorias').trigger("chosen:updated");
    });





    //jQuery Validate
    $.validator.setDefaults({
        errorPlacement: function(error, element){
            var aux = $(element).attr('class').split(" ");

            if($(element).hasClass("item") == true || $(element).hasClass("cuenta") == true || $(element).hasClass("cantidad") == true || $(element).hasClass("unidad") == true)
            {
                $(".tabla_dinamica_error").empty().append('<label class="error pull-left">Debe llenar todos los campos marcados con *.</label>');

            } else {

                if(aux.constructor === Array && aux.length > 0 && aux[0] == "chosen")
                {
                    element.parent().append(error);
                }
                else
                {
                    $(element).after(error);
                }
            }



        }
    });

    $(formulario).validate({
        focusInvalid: true,
        ignore: '',
        wrapper: '',
        submitHandler: function(form) {

            //Habilitar campos ocultos
            $('input:hidden, select:hidden, textarea').removeAttr('disabled');
            $("#guardarProveedor").attr('disabled', true);
            //Enviar el formulario
            form.submit();
        }
    });

    $(formulario).find('input[name="campo[email]"]').rules( "add", {
        email: true
    });





});
