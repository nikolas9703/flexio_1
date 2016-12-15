/**
 * Created by Ivan Cubilla on 2/11/16.
 */
// Definimos la variable tabs la cual contendrá todo nuestro modulo.
var tablaNotificaciones = (function(){
    // Objeto al cual establecemos valores que vamos a usar mas adelante en este ambito.
    var st = {
        jqGrid: "#notificacionesGrid",
        optionsModal: "#optionsModal",
    };
    // Objeto vacío que guardará elementos que se manejan por HTML.
    var dom = {}

    // Función que llenará al objeto dom con los objetos HTML a través de jQuery ($).
    var catchDom = function(){
        dom.jqGrid = $(st.jqGrid);
        dom.optionsModal = $(st.optionsModal);
    };
    // Función donde establecemos los eventos que tendrán cada elemento.
    var suscribeEvents = function(){
        dom.jqGrid.on("click", ".viewOptions", events.eMostrarModal);
    };
    /* Objeto que guarda métodos que se van a usar en cada evento definido
     en la función suscribeEvents. */
    var events = {
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
        }
    };
    var muestra_tabla = function(){
        dom.jqGrid.jqGrid({
            url: phost() + 'notificaciones/ajax-listar',
            datatype: "json",
            colNames:[
                'M&oacute;dulo',
                'Transacci&oacute;n',
                'Rol(es)',
                'Usuario',
                'Categor&iacute;a(s)',
                'Monto',
                'D&iacute;as',
                'Tipo',
                'Estado',
                '',
                ''
            ],
            colModel:[
                {name:'Modulo', index:'modulo', width:60},
                {name:'Transaccion', index:'transaccion', width:60,  sortable:false},
                {name:'Rol', index:'rol', width:80,  sortable:false},
                {name:'Usuario', index:'usuario', width:70,  sortable:false},
                {name:'Categoria', index:'categoria', width: 50, sortable:false, align:'left'},
                {name:'Monto', index:'monto', width: 50,sortable:false, align:'center'},
                {name:'Dias', index:'dias', width: 30,sortable:false, align:'left'},
                {name:'Tipo', index:'tipo', width: 70,sortable:false, align:'left'},
                {name:'Estado', index:'estado', width:50,  sortable:false, align:'center'},
                {name:'link', index:'link', width:60, align:"center", sortable:false, resizable:false, hidedlg:true},
                {name:'options', index:'options', hidedlg:true, hidden: true}
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
            pager: "#pager",
            loadtext: '<p>Cargando...',
            pgtext : "Página {0} de {1}",
            hoverrows: false,
            viewrecords: true,
            refresh: true,
            gridview: true,
            multiselect: multiselect,
            sortname: 'modulo',
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
                    dom.noRecords.empty().append('No se encontraron Items.').css({"color":"#868686","padding":"30px 0 0"}).show();
                }
                else{
                    //dom.noRecords.hide();
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
var multiselect = window.location.pathname.match(/notificaciones/g) ? true : false;


// Ejecutando el método "init" del módulo tabs.
tablaNotificaciones.init();