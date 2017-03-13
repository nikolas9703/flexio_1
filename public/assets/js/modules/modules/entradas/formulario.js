// Definimos la variable tabs la cual contendrá todo nuestro modulo.
var formularioEntrada = (function(){
    // Objeto la cual establecemos valores que vamos a usar mas adelante en este ámbito.
    var st = {
        //datos generales
        segmento2: "entradas",
        input: "input",
        form: "form",
        cChosen: ".chosen",
        cTableResponsive: ".table-responsive",
        iFormularioCrear: "#crearEntradasForm",
        iFormularioEditar: "#editarEntradasForm",
        cBtnGuardar: ".btnGuardar",
        
        //datos del formulario
        iOrigen:"#origen",
        iRecibidoEn: "#recibido_en",
        iItemsTable: "#itemsTable",
        cEstado: ".estado",
        cCantidadRecibida: ".cantidad_recibida",
        cItem: ".item",
        cUnidad: ".unidad",
        cComentarios: ".comentarios",
        
        //configuraciones
        configChosen : {
            width: '100%',
            allow_single_deselect: true
        }
    };
   
    // Objeto vacío que guardará elementos que se manejan por HTML.
    var dom = {};

    // Función que llenará al objeto dom con los objetos HTML a través de jQuery ($).
    var catchDom = function(){
        dom.input = $(st.input);
        dom.form = $(st.form);
        dom.cChosen = $(st.cChosen);
        dom.cTableResponsive = $(st.cTableResponsive);
        dom.iItemsTable = $(st.iItemsTable);
        dom.cBtnGuardar = $(st.cBtnGuardar);
        dom.iFormularioCrear = $(st.iFormularioCrear);
        dom.iFormularioEditar = $(st.iFormularioEditar);
        
        //datos del formulario
        dom.iOrigen = $(st.iOrigen);
        dom.iRecibidoEn = $(st.iRecibidoEn);
        dom.cItem = $(st.cItem);
        dom.cCantidadRecibida = $(st.cCantidadRecibida);
        dom.cUnidad = $(st.cUnidad);
        dom.cEstado = $(st.cEstado);
        dom.cComentarios = $(st.cComentarios);
    };

    // Función donde establecemos los eventos que tendrán cada elemento.
    var suscribeEvents = function(){
        
        dom.cChosen.chosen(st.configChosen);
        dom.iItemsTable.on("click", "tr", events.eShowHideSeriales);
        
    };

    /* Objeto que guarda métodos que se van a usar en cada evento definido 
      en la función suscribeEvents. */
    var events = {
        eShowHideSeriales: function(){
            var self = $(this);
            var seriales = $("#"+ self.prop("id") +"Seriales");
            var fila = $("#"+ self.prop("id"));
            
            if(seriales.length > 0 && seriales.hasClass("hide"))
            {
                seriales.removeClass("hide");
                fila.find(".fa-caret-right").addClass("hide");
                fila.find(".fa-caret-down").removeClass("hide");
            }
            else if(seriales.length > 0)
            {
                seriales.addClass("hide");
                fila.find(".fa-caret-right").removeClass("hide");
                fila.find(".fa-caret-down").addClass("hide");
            }
        }
    };
    
    var preparaInputs = function(){
        dom.cItem.prop("disabled", true);
        dom.cUnidad.prop("disabled", true);
        dom.iRecibidoEn.prop("disabled", true).trigger("chosen:updated");
        
        //completo
        if(dom.cEstado.val() == "3"){
            dom.cCantidadRecibida.prop("disabled", true);
            dom.cBtnGuardar.prop("disabled", true);
            dom.cComentarios.prop("disabled", true);
        }
        
        //popular y seleccionar Origen
        populateAndSelectOrigen();
    }
    
    var populateAndSelectOrigen = function(){
        $.ajax({
            url: phost() + st.segmento2 + "/ajax-get-origenes",
            type:"POST",
            data:{
                erptkn:tkn
            },
            dataType:"json",
            success: function(data){
                if(data.success === true)
                {
                    dom.iOrigen.empty();
                    
                    $.each(data.registros, function(i, r){
                        dom.iOrigen.append('<option value="'+ r.uuid +'">'+ r.nombre +'</option>');
                    });
                    
                    setTimeout(function(){
                        dom.iOrigen.val(uuid_origen).trigger("chosen:updated")
                    },500);
                }
            }

        });
    };
    
    
    // Función que inicializará los funciones decritas anteriormente.
    var initialize = function(){
        catchDom();
        preparaInputs();
        suscribeEvents();
        
        //FUNCION QUE MEJORA LA VISUALIZACION DEL FORMULARIO
        dom.iItemsTable.css("display", "block");
        //dom.cTableResponsive.css("padding-bottom", "180px");
        dom.cTableResponsive.append('<span class="tabla_dinamica_error"></span>');
        
        //jQuery Validate
        $.validator.setDefaults({
            errorPlacement: function(error, element){
                
                if($(element).hasClass("cantidad_recibida") == true)
                {
                    $(".tabla_dinamica_error").empty().append('<label class="error pull-left">Debe llenar todos los campos marcados con *.</label>');

                } else {

                    if($(element).hasClass("chosen"))
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

        dom.form.validate({
            focusInvalid: true,
            ignore: '',
            wrapper: '',
            submitHandler: function(form) {

                //Habilitar campos ocultos
                $('input:hidden, select:hidden, textarea, input').removeAttr('disabled');

                //Enviar el formulario
                form.submit();
            }
        });
        
    };

    /* Retorna un objeto literal con el método init haciendo referencia a la 
       función initialize. */
    return{
        init:initialize
    };
})();


var decimales = 2;
formularioEntrada.init();
