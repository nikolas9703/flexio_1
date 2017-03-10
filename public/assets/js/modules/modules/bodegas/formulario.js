// Definimos la variable tabs la cual contendrá todo nuestro modulo.
var formularioBodega = (function(){
    // Objeto la cual establecemos valores que vamos a usar mas adelante en este ámbito.
    var st = {
        input: "input",
        cChosen: ".chosen",
        iTreeBodegas : "#jstree_bodegas",
        iPadre: "#padre",
        gCamposSubBodega: "#campo\\[contacto_principal\\], #campo\\[telefono\\], #campo\\[direccion\\], #entrada, #estado_items_bodega",
        iEditarForm: "#editarBodegasForm",
        iExportarBtn: "#exportarBtn"
    };
   
    // Objeto vacío que guardará elementos que se manejan por HTML.
    var dom = {};

    // Función que llenará al objeto dom con los objetos HTML a través de jQuery ($).
    var catchDom = function(){
        dom.formulario = $(formulario);
        dom.input = $(st.input);
        dom.cChosen = $(st.cChosen);
        dom.iTreeBodegas = $(st.iTreeBodegas);
        dom.iPadre = $(st.iPadre);
        dom.gCamposSubBodega = $(st.gCamposSubBodega);
        dom.iEditarForm = $(st.iEditarForm);
        dom.iExportarBtn = $(st.iExportarBtn);
    };

    // Función donde establecemos los eventos que tendrán cada elemento.
    var suscribeEvents = function(){
        
        dom.cChosen.chosen({
            width: '100%',
            allow_single_deselect: true 
        });
        
        $("#moduloOpciones").on("click", st.iExportarBtn, events.exportarExistencia);
        
    };

    /* Objeto que guarda métodos que se van a usar en cada evento definido 
      en la función suscribeEvents. */
    var events = {
        exportarExistencia: function(){
            console.log("me ejecute");
            location.href = phost() + "bodegas/exportar_existencia/" + uuid_bodega;
        }
    };
    
    var iniciaTree = function(){
        $.ajax({
            url: phost() + "bodegas/ajax-listar-bodegas",
            type:"POST",
            data:{
                erptkn:tkn,
                nodo_id: dom.iPadre.val() != "0" ? dom.iPadre.val() : ""
            },
            dataType:"json",
            success: function(data){
                populateTree(data);
            }

        });
    };
    
    var populateTree = function(data){
        var arbol = data;
        
        dom.iTreeBodegas.jstree(arbol)
            .bind("select_node.jstree", function(e, data) {
                var nodo = data.node;
                var nodo_id = nodo.id;
                
                dom.iPadre.val(nodo_id);
                deshabilitaCamposSubBodega();
            });
        
        dom.iTreeBodegas.jstree(true).redraw(true);
        
        if(dom.iEditarForm.length > 0)
        {
            if(dom.iPadre.val() != "0")
            {
                deshabilitaCamposSubBodega();
                //deshabilitar jstree en la edicion
            }
        }
    };
    
    
    
    var deshabilitaCamposSubBodega = function(){
        dom.gCamposSubBodega.val("").prop("disabled", true).trigger("chosen:updated");
    };
    
    var habilitaCamposSubBodega = function(){
        dom.gCamposSubBodega.prop("disabled", false).trigger("chosen:updated");
    };
    
    
    
    // Función que inicializará los funciones decritas anteriormente.
    var initialize = function(){
        catchDom();
        suscribeEvents();
        iniciaTree();
        
        //jQuery Validate
        $.validator.setDefaults({
            errorPlacement: function(error, element){
                
                if($(element).hasClass("chosen") == true)
                {
                    element.parent().append(error);
                }
                else
                {
                    $(element).after(error);
                }
                
            }
        });


        dom.formulario.validate({
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
    };

    /* Retorna un objeto literal con el método init haciendo referencia a la 
       función initialize. */
    return{
        init:initialize
    };
})();


var formulario = "";
var decimales = 2;
    
if($(document).find("#crearBodegasForm").length > 0)
{
    formulario = "#crearBodegasForm";
}
else
{
    formulario = "#editarBodegasForm";
}
formularioBodega.init();


