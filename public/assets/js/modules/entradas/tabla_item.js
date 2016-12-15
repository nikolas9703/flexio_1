
var tablaEntradas = (function(){

    // Objeto al cual establecemos valores que vamos a usar mas adelante en este ambito.
    var st = {
        //campos generales
        jqGridName: "entradasGrid",
        jqGrid: "#entradasGrid",
        optionsModal: "#optionsModal, #opcionesModal",
        searchBtn: "#searchBtn",
        clearBtn: "#clearBtn",
        noRecords: ".NoRecordsEntradas",
        segmento2: "entradas",
        //campos del formulario de busqueda
        iFechaDesde: "#fecha_desde",
        iFechaHasta: "#fecha_hasta",
        recibir_en: "#recibir_en",
        iOrigen: "#origen",
        iTipo: "#tipo",
        estado: "#estado",
        referencia: "#referencia",
        numero: "#numero",
        inputsSearch: "#fecha_desde, #fecha_hasta, #recibir_en, #origen, #tipo, #estado, #referencia, #numero"
    };

    // Objeto vacío que guardará elementos que se manejan por HTML.
    var dom = {}

    // Función que llenará al objeto dom con los objetos HTML a través de jQuery ($).
    var catchDom = function(){
        dom.jqGrid = $(st.jqGrid);
        dom.optionsModal = $(st.optionsModal);
        dom.searchBtn = $(st.searchBtn);
        dom.clearBtn = $(st.clearBtn);
        dom.noRecords = $(st.noRecords);
        //campos del formulario de busqueda
        dom.iFechaDesde = $(st.iFechaDesde);
        dom.iFechaHasta = $(st.iFechaHasta);
        dom.recibir_en = $(st.recibir_en);
        dom.iOrigen = $(st.iOrigen);
        dom.iTipo = $(st.iTipo);
        dom.estado = $(st.estado);
        dom.referencia = $(st.referencia);
        dom.numero = $(st.numero);
        dom.inputsSearch = $(st.inputsSearch);
    };

    // Función donde establecemos los eventos que tendrán cada elemento.
    var suscribeEvents = function(){
        dom.jqGrid.on("click", ".viewOptions", events.eMostrarModal);
        dom.searchBtn.bind('click', events.eSearchBtnHlr);
        dom.clearBtn.on('click', events.eClearBtn);
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


            nombre = rowINFO["No. Entrada"];
	    //Init boton de opciones
            dom.optionsModal.find('.modal-title').empty().append('Opciones: '+ nombre);
            dom.optionsModal.find('.modal-body').empty().append(options);
            dom.optionsModal.find('.modal-footer').empty();
            dom.optionsModal.modal('show');
        },
        eSearchBtnHlr: function (e) {

            e.preventDefault();
            //actualizar chosens...
            //console.log(listarOrdenes.dom.chosens);
            dom.searchBtn.unbind('click', events.eSearchBtnHlr);

            var fecha_desde = dom.iFechaDesde.val();
            var fecha_hasta = dom.iFechaHasta.val();
            var recibir_en = dom.recibir_en.val();
            var estado = dom.estado.val();
            var referencia = dom.referencia.val();
            var numero = dom.numero.val();
            var origen = dom.iOrigen.val();
            var tipo = dom.iTipo.val();

            if(fecha_desde != "" || fecha_hasta != "" || recibir_en != "" || estado != ""  || referencia != "" || numero != "" || origen != "" || tipo != "")
            {
                dom.jqGrid.setGridParam({
                    url: phost() + 'entradas/ajax-listar',
                    datatype: "json",
                    postData: {
                        fecha_desde: fecha_desde,
                        fecha_hasta: fecha_hasta,
                        recibir_en: recibir_en,
                        estado: estado,
                        referencia: referencia,
                        numero: numero,
                        origen: origen,
                        tipo: tipo,
                        erptkn: tkn
                    }
                }).trigger('reloadGrid');

                dom.searchBtn.bind('click', events.eSearchBtnHlr);
            }else{
                dom.searchBtn.bind('click', events.eSearchBtnHlr);
            }
        },
        eClearBtn: function(e){
            e.preventDefault();
            dom.jqGrid.setGridParam({
                url: phost() + 'entradas/ajax-listar',
                datatype: "json",
                postData: {
                    fecha_desde: '',
                    fecha_hasta: '',
                    recibir_en: '',
                    estado: '',
                    referencia: '',
                    numero: '',
                    origen: '',
                    tipo:'',
                    erptkn: tkn
                }
            }).trigger('reloadGrid');

            //Reset Fields
            dom.inputsSearch.val('');

            //Reset Chosens
            dom.inputsSearch.trigger("chosen:updated");
	}
    };

    var muestra_tabla = function(){
        dom.jqGrid.jqGrid({
            url: phost() + st.segmento2 + '/ajax-listar-historial-item',
            datatype: "json",
            colNames:[
                'N&uacute;mero de entrada',
                'Fecha',
                'Bodega',
                'Serie',
                'Cantidad',
                '',
                ''
            ],
            colModel:[
                {name:'No. Entrada', index:'codigo', width:55},
                {name:'fecha', index:'fecha_creacion', width:45, sortable:false},
                {name:'bodega', index:'numero_documento', width:65, sortable:false},
                {name:'serie', index:'origen', width:80, sortable:false, hidedlg:window.modulo_series, hidden:window.modulo_series},
                {name:'cantidad', index:'referencia', width:80, sortable:false, hidedlg:window.modulo_series, hidden:window.modulo_series},
                {name:'link', index:'link', width:60, align:"center", sortable:false, resizable:false, hidedlg:true},
                {name:'options', index:'options', hidedlg:true, hidden: true},
            ],
            mtype: "POST",
            postData: {
                erptkn: tkn,
                factura_compra_id: (typeof factura_compra_id !== 'undefined') ? factura_compra_id : '',
                item_id: (typeof window.sp_item_id !== 'undefined') ? window.sp_item_id : '',
                serie_id: (typeof window.sp_serie_id !== 'undefined') ? window.sp_serie_id : ''
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
            sortname: 'codigo',
            sortorder: "DESC",
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
                    $('#gbox_' + st.jqGridName).hide();
                    dom.noRecords.empty().append('No se encontraron entradas de inventario.').css({"color":"#868686","padding":"30px 0 0"}).show();
                }
                else{
                    dom.noRecords.hide();
                    $('#gbox_' + st.jqGridName).show();
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

        //dom.jqGrid.jqGrid('columnToggle');

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

    var ordenarOrigenes = function()
    {
        // sort list
        var my_options = $(st.iOrigen + " option");
        my_options.sort(function(a,b) {
            if (a.text > b.text) return 1;
            else if (a.text < b.text) return -1;
            else return 0
        })

        dom.iOrigen.empty().append(my_options).val("-1").trigger("chosen:updated");
    }

    // Función que inicializará los funciones decritas anteriormente.
    var initialize = function(){
        catchDom();
        ordenarOrigenes();
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
var multiselect = window.location.pathname.match(/entradas/g) ? true : false;
var modulo_series = window.location.pathname.match(/series/g) ? true : false;

// Ejecutando el método "init" del módulo tabs.
tablaEntradas.init();
