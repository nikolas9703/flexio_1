/**
 * Created by Ivan Cubilla on 27/7/16.
 */
var tablaTipos = (function(){

    var st = {
        iOptionsModal:"#optionsModal",
        jqGrid: "#tiposGrid",
        jqPager: "#tiposPager",
        optionsModal: "#optionsModal",
        guardarBtn: "#guardarTipoBtn",
        cancelarBtn: "#cancelarTipoBtn",
        noRecords: ".NoRecordsTipos",
        segmento2: "configuracion_ventas",
        editar: ".editarTipos",
        desactivar: ".desactivarTipos",
        activar: ".activarTipos",
        //campos del formulario de creacion/edicion
        iTipo: "#tipo",
        iDescripcion: "#descripcion",
        iModo: "#modoTipo"
    };
    var config = {
        chosen: {
            width: '100%',
            allow_single_deselect: true
        }
    };
    // Objeto vacío que guardará elementos que se manejan por HTML.
    var dom = {};
    // Función que llenará al objeto dom con los objetos HTML a través de jQuery ($).
    var catchDom = function(){
        dom.iOptionsModal = $(st.optionsModal);
        dom.jqGrid = $(st.jqGrid);
        dom.jqPager = $(st.jqPager);
        dom.optionsModal = $(st.optionsModal);
        dom.guardarBtn = $(st.guardarBtn);
        dom.cancelarBtn = $(st.cancelarBtn);
        dom.noRecords = $(st.noRecords);
        //campos del formulario de creacion/edicion
        dom.iTipo = $(st.iTipo);
        dom.iDescripcion = $(st.iDescripcion);
    };
    // Función donde establecemos los eventos que tendrán cada elemento.
    var suscribeEvents = function(){
        dom.jqGrid.on("click", ".viewOptions", events.eMostrarModal);
        dom.iOptionsModal.on("click", st.editar, events.eEditar);
        dom.iOptionsModal.on("click", st.desactivar, events.eDesactivar);
        dom.iOptionsModal.on("click", st.activar, events.eActivar);

        dom.guardarBtn.on("click", events.eGuardar);
        dom.cancelarBtn.on("click", limpiarFormulario);
        dom.iModo = $(st.iModo);

        //dom.cChosen.chosen(config.chosen);;
    };
    var events = {

        eGuardar: function(e){
        console.log('Guardar...');
            if(!validarFormulario())
            {
                toastr.error("Error! Complete los valores que son requeridos.");
                return;
            }
            var uuid = dom.iModo.data("uuid");
            $.ajax({
                url: phost() + st.segmento2 + "/ajax-guardar-tipos",
                type:"POST",
                data:{
                    erptkn:tkn,
                    uuid: uuid,
                    tipo: dom.iTipo.val(),
                    descripcion: dom.iDescripcion.val()
                },
                dataType:"json",
                success: function(data){
                    if(data.success === true)
                    {
                        dom.jqGrid.trigger("reloadGrid");
                        toastr.success("¡&Eacute;xito! Se ha guardado correctamente.");
                        limpiarFormulario();
                    }
                }

            });
        },
        eDesactivar: function(e){
            var self = $(this);
            var uuid = self.data("uuid");

            cambiarEstado(uuid, "inactivo");
        },
        eActivar: function(e){
            var self = $(this);
            var uuid = self.data("uuid");

            cambiarEstado(uuid, "activo");
        },
        eEditar: function(e){

            var self = $(this);
            var uuid = self.data("uuid");

            dom.iModo.data("uuid", uuid);

            $.ajax({
                url: phost() + st.segmento2 + "/ajax-get-tipos",
                type:"POST",
                data:{
                    erptkn:tkn,
                    uuid: uuid
                },
                dataType:"json",
                success: function(data){
                    if(data.success === true)
                    {
                        dom.iOptionsModal.modal("hide");
                        //llenarFormulario(data.registro);
                        llenarFormulario(data.nombre, data.descripcion, data.uuid);
                    }
                }

            });
        },
        eMostrarModal: function(e)
        {
            var self = $(this);

            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();

            var nombre = '';
            var uuid = self.attr("data-uuid");
            var rowINFO = dom.jqGrid.getRowData(uuid);
            var options = rowINFO["options"];


            nombre = rowINFO["Tipo"];
            //Init boton de opciones
            dom.optionsModal.find('.modal-title').empty().append('Opciones: '+ nombre);
            dom.optionsModal.find('.modal-body').empty().append(options);
            dom.optionsModal.find('.modal-footer').empty();
            dom.optionsModal.modal('show');
        }
    };
    var muestra_tabla = function(){
        var multiselect = '';
        dom.jqGrid.jqGrid({
            url: phost() + st.segmento2 + '/ajax-listar-tipos',
            datatype: "json",
            colNames:[
                'Tipo',
                'Descripci&oacute;n',
                'Estatus',
                '',
                ''
            ],
            colModel:[
                {name:'Tipo', index:'nombre', width:60},
                {name:'Descripcion', index:'descripcion', width:180,  sortable:false},
                {name:'Estatus', index:'estatus', width:60,  sortable:false},
                {name:'link', index:'link', width:60, align:"center", sortable:false, resizable:false, hidedlg:true},
                {name:'options', index:'options', hidedlg:true, hidden: true},
            ],
            mtype: "POST",
            postData: {
                erptkn: tkn,
            },
            height: "auto",
            autowidth: true,
            rowList: [10,20,50,100],
            rowNum: 10,
            page: 1,
            pager: st.jqPager,
            loadtext: '<p>Cargando...',
            pgtext : "Página {0} de {1}",
            hoverrows: false,
            viewrecords: true,
            refresh: true,
            gridview: true,
            multiselect: true,
            sortname: 'nombre',
            sortorder: "ASC",
            beforeProcessing: function(data, status, xhr){
                //Check Session
                if( $.isEmptyObject(data.session) == false){
                    window.location = phost() + "login?expired";
                }
            },
            loadBeforeSend: function () {//propiedadesGrid_cb
                $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                $(this).closest("div.ui-jqgrid-view").find(st.jqGrid + "_cb, #jqgh_" + st.jqGrid + "_link").css("text-align", "center");
            },
            beforeRequest: function(data, status, xhr){},
            loadComplete: function(data){

                //check if isset data
                if( data['total'] == 0 ){
                    $('#gbox_' + st.jqGrid).hide();
                    dom.noRecords.empty().append('No se encontraron tipos de clientes.').css({"color":"#868686","padding":"30px 0 0"}).show();
                }
                else{
                    dom.noRecords.hide();
                    $('#gbox_' + st.jqGrid).show();
                }

                if(multiselect == true){
                    //---------
                    // Cargar plugin jquery Sticky Objects
                    //----------
                    //add class to headers
                    dom.jqGrid.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");

                    //floating headers
                    $('#gridHeader').sticky({
                        getWidthFrom: '.ui-jqgrid-view',
                        className:'jqgridHeader'
                    });

                    //Arreglar tamaño de TD de los checkboxes
                    $(st.jqGrid + "_cb").css("width","50px");
                    $(st.jqGrid + " tbody tr").children().first("td").css("width","50px");
                }

            },
            onSelectRow: function(id){
                $(this).find('tr#'+ id).removeClass('ui-state-highlight');
            },
        });
        dom.jqGrid.jqGrid('columnToggle');

        //-------------------------
        // Redimensioanr Grid al cambiar tamaño de la ventanas.
        //-------------------------
        $(window).resizeEnd(function() {
            $(".ui-jqgrid").each(function(){
                var w = parseInt( $(this).parent().width()) - 6;
                var tmpId = $(this).attr("id");
                var gId = tmpId.replace("gbox_","");
                $("#"+gId).setGridWidth(w);
            });
        });

    }
    var limpiarFormulario = function(){

        dom.iTipo.val("");
        dom.iDescripcion.val("");

    }
    var cambiarEstado = function(uuid, estado){
        $.ajax({
            url: phost() + st.segmento2 + "/ajax-cambiar-estado-tipos",
            type:"POST",
            data:{
                erptkn:tkn,
                uuid: uuid,
                estado: estado
            },
            dataType:"json",
            success: function(data){
                if(data.success === true)
                {
                    dom.iOptionsModal.modal("hide");
                    dom.jqGrid.trigger("reloadGrid");
                    toastr.success("¡&Eacute;xito! Se ha cambiado de estado correctamente.");
                }
            }

        });
    }
    var validarFormulario = function(){
        var camposConDatos = 0;
        var camposRequeridos = 2;

        if(dom.iTipo.val().length > 0)
        {
            camposConDatos += 1;
        }

        if(dom.iDescripcion.val().length > 0)
        {
            camposConDatos += 1;
        }

        if(camposConDatos == camposRequeridos)
        {
            return true;
        }
        return false;
    }
    var llenarFormulario = function(nombre, descripcion, uuid){
      //  var llenarFormulario = function(registro){
        dom.iTipo.val(nombre);
        dom.iDescripcion.val(descripcion);
        dom.iModo.data("uuid", uuid);
        //dom.iTipo.val(registro.nombre);
        //dom.iDescripcion.val(registro.descripcion);
       // dom.iModo.data("uuid", registro.uuid_tipo);
    }
    // Función que inicializará los funciones decritas anteriormente.
    var initialize = function(){
        catchDom();
        suscribeEvents();
        muestra_tabla();
    };

    /* Retorna un objeto literal con el método init haciendo referencia a la
     función initialize. */
    return{
        init:initialize
    }
})();
// Ejecutando el método "init" del módulo tabs.
tablaTipos.init();