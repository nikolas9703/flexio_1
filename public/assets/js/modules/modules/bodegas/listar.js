// Definimos la variable tabs la cual contendrá todo nuestro modulo.
var listarBodegas = (function(){
    // Objeto la cual establecemos valores que vamos a usar mas adelante en este ámbito.
    var st = {
        modulosOpciones: "#moduloOpciones ul",
        exportarBtn: "#exportarBtn",
        tabla: "#tabla",
        jqGrid: "#bodegasGrid",
        filename: "bodegas",
        segmento2: "bodegas",
        input: "input",
        chosens: "#categorias, #estado",
        cBtnCambiarEstado: ".btn-cambiar-estado",
        form: "form",
        optionsModal: "#optionsModal"
    };
   
    // Objeto vacío que guardará elementos que se manejan por HTML.
    var dom = {}

    // Función que llenará al objeto dom con los objetos HTML a través de jQuery ($).
    var catchDom = function(){
        dom.modulosOpciones = $(st.modulosOpciones);
        dom.exportarBtn = $(st.exportarBtn);
        dom.tabla = $(st.tabla);
        dom.jqGrid = $(st.jqGrid);
        dom.input = $(st.input);
        dom.chosens = $(st.chosens);
        dom.cBtnCambiarEstado = $(st.cBtnCambiarEstado);
        dom.form = $(st.form);
        dom.optionsModal = $(st.optionsModal);
    };

    // Función donde establecemos los eventos que tendrán cada elemento.
    var suscribeEvents = function(){
        dom.modulosOpciones.on("click", st.exportarBtn, events.eExportar);
        dom.optionsModal.on("click", st.cBtnCambiarEstado, events.eCambiarEstado);
        
        dom.chosens.chosen({
            width: '100%',
            allow_single_deselect: true 
        });
    };

    /* Objeto que guarda métodos que se van a usar en cada evento definido 
      en la función suscribeEvents. */
    var events = {
        eCambiarEstado: function(e){
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            
            var self = $(this);
            var id = self.data("id");
            var estado = self.data("estado_id");
            
            cambiarEstado(id, estado);
        },
        eExportar: function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();

            if(dom.tabla.is(':visible') == true){
                
                //Desde la Tabla
                events.eExportarjQgrid();

            }else{
                
                //Desde el Grid
                events.eExportarGrid();
                
            }
        },
        eExportarjQgrid: function(e) {
            //Exportar Seleccionados del jQgrid
            var registros_jqgrid = [];

            registros_jqgrid = dom.jqGrid.jqGrid('getGridParam','selarrrow');

            var obj = new Object();
            obj.count = registros_jqgrid.length;

            if(obj.count) {

                obj.items = new Array();

                for(elem in registros_jqgrid) {
                    //console.log(proyectos[elem]);
                    var registro_jqgrid = dom.jqGrid.getRowData(registros_jqgrid[elem]);

                    //Remove objects from associative array
                    delete registro_jqgrid['link'];
                    delete registro_jqgrid['options'];

                    //Push to array
                    obj.items.push(registro_jqgrid);
                }


                var json = JSON.stringify(obj);
                var csvUrl = JSONToCSVConvertor(json);
                var filename = st.filename +'_'+ Date.now() +'.csv';

                //Ejecutar funcion para descargar archivo
                downloadURL(csvUrl, filename);

                $('body').trigger('click');
            } 
        },
        eExportarGrid: function(e){
            var registros_grid = [];

            $("#iconGrid").find('input[type="checkbox"]:checked').filter(function(){
                registros_grid.push(this.value);
            });

            //Verificar si ha seleccionado algun proyecto
            if(registros_grid.length==0){
                return false;
            }
            //Convertir array a srting separado por guion
            var registros_grid_string = registros_grid.join('-');
            var obj;

            $.ajax({
                url: phost() + st.segmento2 + "/ajax-exportar",
                type:"POST",
                data:{
                    erptkn:tkn,
                    id_registros: registros_grid_string
                },
                dataType:"json",
                success: function(data){
                    if(!data)
                    {
                        return;
                    }

                    var json = JSON.stringify(data);
                    var csvUrl = JSONToCSVConvertor(json);
                    var filename = st.filename +'_'+ Date.now() +'.csv';

                    //Ejecutar funcion para descargar archivo
                    downloadURL(csvUrl, filename);

                    $('body').trigger('click');
                }

            });

        }
    };
    
    var cambiarEstado = function(id, estado_actual){
        $.ajax({
            url: phost() + "bodegas/ajax-cambiar-estado",
            type:"POST",
            data:{
                erptkn:tkn,
                id:id,
                estado_actual:estado_actual
            },
            dataType:"json",
            success: function(data){
                
                dom.optionsModal.modal("toggle");
                if(data.success.estado === 500)
                {
                    toastr.error(data.success.mensaje);
                }
                else
                {
                    toastr.success(data.success.mensaje);
                }
                
                setTimeout(function(){
                    dom.jqGrid.trigger("reloadGrid");
                },700);
            }

        });
    };
    
    var mostrar_mensaje = function(){
        //mensaje clase viene desde el controlador...
        if(mensaje_clase != 0)
        {
            if(mensaje_clase == "alert-success")
            {
                toastr.success("¡&Eacute;xito! Se ha guardado correctamente la << Bodega/Bodegas >>.");
            }
            else
            {
                toastr.error("¡Error! Su solicitud no fue procesada en la << Bodega/Bodegas >>.");
            }
        }
    }
 
    // Función que inicializará los funciones decritas anteriormente.
    var initialize = function(){
        catchDom();
        suscribeEvents();
        mostrar_mensaje();
    };

    /* Retorna un objeto literal con el método init haciendo referencia a la 
       función initialize. */
    return{
        init:initialize,
        dom:dom
    }
})();

// Ejecutando el método "init" del módulo tabs.
listarBodegas.init();

 