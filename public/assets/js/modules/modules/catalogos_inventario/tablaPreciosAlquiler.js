// Definimos la variable tabs la cual contendrá todo nuestro modulo.
var tablaPreciosAlquiler = (function(){

    // Objeto al cual establecemos valores que vamos a usar mas adelante en este ambito.
    var st = {
        iOptionsModal:"#optionsModal",
        jqGrid: "#preciosAlquilerGrid",
        jqPager: "#preciosAlquilerPager",
        optionsModal: "#optionsModal",
        guardarBtn: "#guardarPrecioAlquilerBtn",
        cancelarBtn: "#cancelarPrecioBtn",
        noRecords: ".NoRecordsPrecios",
        segmento2: "catalogos_inventario",
        editar: ".editarPrecioAlquiler",
        desactivar: ".desactivarPrecio",
        activar: ".activarPrecio",
        //campos del formulario de creacion/edicion
        iNombre: "#precio_nombre_alquiler",
        iDescripcion: "#precio_descripcion_alquiler",
        iEstado: "#precio_estado_alquiler",
        iModo: "#modoPrecio_alquiler",
        cChosen: "#precio_estado_alquiler",
        principal:".principal"
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
        dom.iNombre = $(st.iNombre);
        dom.iDescripcion = $(st.iDescripcion);
        dom.iEstado = $(st.iEstado);
        dom.iModo = $(st.iModo);
        dom.cChosen = $(st.cChosen);
    };

    // Función donde establecemos los eventos que tendrán cada elemento.
    var suscribeEvents = function(){
        dom.jqGrid.on("click", ".viewOptions", events.eMostrarModal);
        dom.jqGrid.on("click", st.principal, events.eSelccionarPrincipal);
        dom.iOptionsModal.on("click", st.editar, events.eEditar);
        dom.iOptionsModal.on("click", st.desactivar, events.eDesactivar);
        dom.iOptionsModal.on("click", st.activar, events.eActivar);

        dom.guardarBtn.on("click", events.eGuardar);
        dom.cancelarBtn.on("click", limpiarFormulario);

        dom.cChosen.chosen(config.chosen);;
    };

    /* Objeto que guarda métodos que se van a usar en cada evento definido
      en la función suscribeEvents. */
    var events = {
        eGuardar: function(e){

            if(!validarFormulario())
            {
                toastr.error("Error! Complete los valores que son requeridos.");
                return;
            }

            var uuid = dom.iModo.data("uuid");

            $.ajax({
                url: phost() + st.segmento2 + "/ajax-guardar-precio",
                type:"POST",
                data:{
                    erptkn:tkn,
                    uuid: uuid,
                    nombre: dom.iNombre.val(),
                    descripcion: dom.iDescripcion.val(),
                    estado: dom.iEstado.val(),
                    tipo:'alquiler'
                },
                dataType:"json",
                success: function(data){
                    if(data.success === true)
                    {
                        dom.jqGrid.trigger("reloadGrid");
                        toastr.success("¡&Eacute;xito! Se ha guardado correctamente el << Precio Alquiler/Inventario >>.");

                        limpiarFormulario();
                    }
                }

            });
        },
        eEditar: function(e){

            var self = $(this);
            var uuid = self.data("uuid");

            dom.iModo.data("uuid", uuid);

            $.ajax({
                url: phost() + st.segmento2 + "/ajax-get-precio",
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
                        llenarFormulario(data.registro);
                    }
                }

            });
        },
        eDesactivar: function(e){
            var self = $(this);
            var uuid = self.data("uuid");

            cambiarEstado(uuid, "2");
        },
        eActivar: function(e){
            var self = $(this);
            var uuid = self.data("uuid");

            cambiarEstado(uuid, "1");
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


            nombre = rowINFO["Nombre"];
	    //Init boton de opciones
            dom.optionsModal.find('.modal-title').empty().append('Opciones: '+ nombre);
            dom.optionsModal.find('.modal-body').empty().append(options);
            dom.optionsModal.find('.modal-footer').empty();
            dom.optionsModal.modal('show');
        },
        eSelccionarPrincipal: function (e) {
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();
            var uuid_precio = $(this).data('rowid');
            //console.log(uuid_precio);
            var parametros = {uuid_precio:uuid_precio, tipo:'alquiler'};
            var principal = moduloPrecio.asignarPrincipal(parametros);
            principal.done(function (data) {
                $("#preciosAlquilerGrid").trigger('reloadGrid');
            });
        }
    };

    var muestra_tabla = function(){
        dom.jqGrid.jqGrid({
            url: phost() + st.segmento2 + '/ajax-listar-precios',
            datatype: "json",
            colNames:[
                'Principal',
                'Nombre',
                'Descripci&oacute;n',
                'Estatus',
                'Acci&oacute;n',
                ''
            ],
            colModel:[
                {name:'principal', index:'principal', width:25,align: "center",editable: true, edittype: 'checkbox', editoptions: { value: "True:False" }, formatter:cboxFormatter, formatoptions: { disabled: false}, classes:'check'  },
                {name:'Nombre', index:'nombre', width:60},
                {name:'Descripcion', index:'descripcion', width:180,  sortable:false},
                {name:'Estatus', index:'estatus', width:60,  sortable:false},
                {name:'link', index:'link', width:60, align:"center", sortable:false, resizable:false, hidedlg:true},
                {name:'options', index:'options', hidedlg:true, hidden: true},
            ],
            mtype: "POST",
            postData: {
                erptkn: tkn,
                'tipo': 'alquiler',
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
           // multiselect: multiselect,
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
                    dom.noRecords.empty().append('No se encontraron Categor&iacute;as.').css({"color":"#868686","padding":"30px 0 0"}).show();
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
    var cboxFormatter = function (cellvalue, options, rowObject) {
        return '<input type="checkbox"' + (cellvalue == "1" ? ' checked="checked" disabled ' : '') +
            'data-rowId="' + options.rowId + '" value="' + cellvalue + '" class="principal"/>';
    }
    var cambiarEstado = function(uuid, estado){
        $.ajax({
            url: phost() + st.segmento2 + "/ajax-cambiar-estado-precio",
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
                    toastr.success("¡&Eacute;xito! Se ha guardado correctamente el << Precio/Inventario >>.");
                }
            }

        });
    }

    var validarFormulario = function(){
        var camposConDatos = 0;
        var camposRequeridos = 3;

        if(dom.iNombre.val().length > 0)
        {
            camposConDatos += 1;
        }

        if(dom.iDescripcion.val().length > 0)
        {
            camposConDatos += 1;
        }

        if(dom.iEstado.val().length > 0)
        {
            camposConDatos += 1;
        }

        if(camposConDatos == camposRequeridos)
        {
            return true;
        }
        return false;
    }

    var limpiarFormulario = function(){

        dom.iNombre.val("");
        dom.iDescripcion.val("");
        dom.iEstado.val("").trigger("chosen:updated");
        dom.iModo.data("uuid", "");

    }

    var llenarFormulario = function(registro){

        dom.iNombre.val(registro.nombre);
        dom.iDescripcion.val(registro.descripcion);
        dom.iEstado.val(registro.estado).trigger("chosen:updated");
        dom.iModo.data("uuid", registro.uuid_precio);

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

//verificar si la url actual es contactos
//de lo contrario no mostrar multiselect del jqgrid
var multiselect = window.location.pathname.match(/catalogos_inventarios/g) ? true : false;


// Ejecutando el método "init" del módulo tabs.
tablaPreciosAlquiler.init();
