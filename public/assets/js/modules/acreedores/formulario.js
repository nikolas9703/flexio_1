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
    };

    var suscribeEvents = function(){

    };

    var mejorasVisuales = function(){
        var colTituloBalance = dom.iTituloBalance.parent();

        //Aplicando estilos al titulo de balance del proveedor
        colTituloBalance.removeClass("col-lg-3").addClass("col-lg-6");
        colTituloBalance.css('cssText', 'clear:both;padding-top:30px;margin-bottom:100px !important');

        //limpiando chosens
        //dom.iFormaPago.find('option[value=""]').remove();
        dom.iFormaPago.trigger("chosen:updated");

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

    console.log(formulario);

    //FUNCION QUE MEJORA LA VISUALIZACION DEL FORMULARIO
    $(".table-responsive").css("padding-bottom", "100px");
    $(".table-responsive").append('<span class="tabla_dinamica_error"></span>');

    $('input[name="campo[cheque]"]').css("margin-left", "-64px");
    $('input[name="campo[cheque]"]').css("margin-top", "-7px");
    $('input[name="campo[cheque]"]').parent().parent().find("label").removeClass("pull-right");
    $('input[name="campo[cheque]"]').parent().parent().find("label").css("padding-top", "20px");

    $(".saldo_pendiente").parent().parent().append('<span class="label label-danger" style="margin-top: 10px;float: left;padding: 5px; width: 100%; font-weight:bold;font-size: 13px;">Saldo pendiente acumulado</span>');
    $(".credito").parent().parent().append('<span class="label label-success" style="margin-top: 10px;float: left;padding: 5px; width: 100%; font-weight:bold; background-color:green;font-size: 13px;">Cr&eacute;dito a favor</span>');

    $(".anterior").css("background-color", "black");
    $(".siguiente").css("background-color", "black");
    $(".siguiente").parent().addClass("pull-right");




    //ELEMENTOS DE TIPO CHOSEN
    $(".chosen").chosen({
        width: '100%',
        allow_single_deselect: true
    });

    $('.categorias').attr("data-placeholder", "Seleccione");

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

    $("form").validate({
        focusInvalid: true,
        ignore: '',
        wrapper: '',
        submitHandler: function(form) {

            //Habilitar campos ocultos
            $('input:hidden, select:hidden, textarea').removeAttr('disabled');

            //Enviar el formulario
            form.submit();
        }
    });

    $("form").find('input[name="campo[email]"]').rules( "add", {
        email: true
    });


    //Para el Modulo de Seguros se aprueba que el Telefono y Categoria no sean requeridas
    if (localStorage.getItem('ms-selected') == "seguros") {
        $(".telefono").removeAttr("data-rule-required");
        $(".categorias").removeAttr("data-rule-required");
        $(".span_requerido").remove();
        $('a[href="#tablaColaboradores"]').hide();
        $("#tablaColaboradores").hide();
    }
    


});
