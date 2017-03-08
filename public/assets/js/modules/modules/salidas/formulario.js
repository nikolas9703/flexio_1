// Definimos la variable tabs la cual contendrá todo nuestro modulo.
var formularioSalida = (function(){
    // Objeto la cual establecemos valores que vamos a usar mas adelante en este ámbito.
    var st = {
        //datos generales
        segmento2: "salidas",
        input: "input",
        form: "form",
        cChosen: ".chosen",
        cTableResponsive: ".table-responsive",
        iFormularioEditar: "#editarSalidaForm",
        cBtnGuardar: ".btnGuardar",
        
        //datos del formulario
        iDestino:"#destino",
        iBodegaSalida: "#bodega_salida",
        iEstado: "#estado",
        iNumeroDocumento: "#campo\\[numero_documento\\]",
        iComentarios: "#campo\\[comentarios\\]",
        
        //tabla dinamica de items
        iItemsTable: "#itemsTable",
        cItem: ".item",
        cDescripcion: ".descripcion",
        cObservacion: ".observacion",
        cCuentaGasto: ".cuenta",
        cCantidadEnviada: ".cantidad_enviada",
        cUnidad: ".unidad",
        
        //Generales personalidades
        inputsDisabled: "#destino, #bodega_salida, #campo\\[numero_documento\\], .item, .descripcion, .observacion, .cuenta, .cantidad_enviada, .unidad",
        inputsEnabled: "#estado, #campo\\[comentarios\\]"
    };
   
    var config = {
        chosen : {
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
        dom.iFormularioEditar = $(st.iFormularioEditar);
        
        //datos del formulario
        dom.iDestino = $(st.iDestino);
        dom.iBodegaSalida = $(st.iBodegaSalida);
        dom.iEstado = $(st.iEstado);
        dom.iNumeroDocumento = $(st.iNumeroDocumento);
        dom.iComentarios = $(st.iComentarios);
        
        //tabla dinamica de items
        dom.iItemsTable = $(st.iItemsTable);
        dom.cItem = $(st.cItem);
        dom.cDescripcion = $(st.cDescripcion);
        dom.cObservacion = $(st.cObservacion);
        dom.cCuentaGasto = $(st.cCuentaGasto);
        dom.cCantidadEnviada = $(st.cCantidadEnviada);
        dom.cUnidad = $(st.cUnidad);
        
        //Generales personalidades
        dom.inputsDisabled = $(st.inputsDisabled);
        dom.inputsEnabled = $(st.inputsEnabled);
    };

    // Función donde establecemos los eventos que tendrán cada elemento.
    var suscribeEvents = function(){
        
        dom.cChosen.chosen(config.chosen);
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
        dom.inputsDisabled.prop("disabled", true).trigger("chosen:updated");
        dom.inputsEnabled.prop("disabled", false).trigger("chosen:updated");
        
        //enviado
        if(dom.iEstado.val() == "2"){
            dom.iEstado.prop("disabled", true).trigger("chosen:updated");;
            dom.iComentarios.prop("disabled", true);
            
            dom.cBtnGuardar.prop("disabled", true);
        }
        
        //popular y seleccionar Origen
        populateAndSelectDestino();
    }
    
    var populateAndSelectDestino = function(){
        $.ajax({
            url: phost() + st.segmento2 + "/ajax-get-destinos",
            type:"POST",
            data:{
                erptkn:tkn
            },
            dataType:"json",
            success: function(data){
                if(data.success === true)
                {
                    dom.iDestino.empty();
                    
                    $.each(data.registros, function(i, r){
                        dom.iDestino.append('<option value="'+ r.uuid +'">'+ r.nombre +'</option>');
                    });
                    
                    setTimeout(function(){
                        dom.iDestino.val(uuid_destino).trigger("chosen:updated")
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
formularioSalida.init();
