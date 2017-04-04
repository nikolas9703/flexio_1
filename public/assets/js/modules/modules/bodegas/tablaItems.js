// Definimos la variable tabs la cual contendrá todo nuestro modulo.
var tablaItems = (function(){
    
    // Objeto al cual establecemos valores que vamos a usar mas adelante en este ambito.
    var st = {
        jqGrid: "#itemsGrid",
        jqPager: "#itemsPager",
        segmento2: "bodegas",
        optionsModal: "#optionsModal",
        noRecords: ".NoRecordsItems",
    };
   
    // Objeto vacío que guardará elementos que se manejan por HTML.
    var dom = {}

    // Función que llenará al objeto dom con los objetos HTML a través de jQuery ($).
    var catchDom = function(){
        dom.jqGrid = $(st.jqGrid);
        dom.jqPager = $(st.jqPager);
        dom.optionsModal = $(st.optionsModal);
        dom.noRecords = $(st.noRecords);
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
            var uuid = self.attr("data-id");
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
            url: phost() + st.segmento2 + '/ajax-listar-items',
            datatype: "json",
            colNames:[
                'Categor&iacute;a',
                'Item',
                'Nombre',
                'Costo',
                'Unidad',
                'Total',
                'Estado',
                '',
                '',
                ''//seriales
            ],
            colModel:[
                {name:'Categoria', index:'categoria', width:80,  sortable:false},
                {name:'Item', index:'codigo', width:80},
                {name:'Nombre', index:'nombre', width:80,  sortable:false, align:'left'},
                {name:'Costo', index:'costo', width:80,  sortable:false},
                {name:'Unidad', index:'unidad', width: 70, sortable:false, align:'left'},
                {name:'Total', index:'total', width: 80, sortable:false, align:'right'},
                {name:'Estado', index:'estado', width: 70, sortable:false},
                {name:'link', index:'link', width:60, align:"center", sortable:false, resizable:false, hidedlg:true},
                {name:'options', index:'options', hidedlg:true, hidden: true},
                {name:'seriales_html', index:'seriales_html', hidedlg:true, hidden: true},
            ],
            mtype: "POST",
            postData: {
                erptkn: tkn,
                uuid_bodega: uuid_bodega
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
            multiselect: false,
            sortname: 'id',
            sortorder: "DESC",
            //propiedades y metodos del subgrid
            subGrid:true,
            subGridOptions:{
                plusicon : "ui-icon-triangle-1-e",
                minusicon : "ui-icon-triangle-1-s",
                openicon: "ui-icon-blank",
                expandOnLoad: false,
                selectOnExpand : false,
                reloadOnExpand : true 
            },
            subGridRowExpanded: function(subgrid_id, row_id) {
                var rowINFO = dom.jqGrid.getRowData(row_id);
                var seriales_html = rowINFO["seriales_html"];
                $("#" + subgrid_id).append(seriales_html);
            },
            //fin de propiedades y metodos de subgrid
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
                    dom.noRecords.hide();
                    $('#gbox_' + st.jqGrid).show();
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
var multiselect = window.location.pathname.match(/bodegas/g) ? true : false;


// Ejecutando el método "init" del módulo tabs.
tablaItems.init();

 

