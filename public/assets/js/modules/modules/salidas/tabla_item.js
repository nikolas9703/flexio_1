
var tablaSalidas = (function(){
    var factura = $.parseJSON(window.factura || '{}') ;
    console.log(factura);
    var operacion_id = '';
    var operacion_type = '';
  if(factura.hasOwnProperty('id') && factura.hasOwnProperty('type')){
      operacion_id = factura.id;
      operacion_type = factura.type;
  }
    // Objeto al cual establecemos valores que vamos a usar mas adelante en este ambito.
    var st = {
        //campos generales
        jqGrid: "#salidasGrid",
        jqPager: "#pagerSalidas",
        optionsModal: "#optionsModal",
        searchBtn: "#searchBtn",
        clearBtn: "#clearBtn",
        noRecords: ".NoRecordsSalidas",
        segmento2: "salidas",
        //campos del formulario de busqueda
        iFechaDesde: "#fecha_desde",
        iFechaHasta: "#fecha_hasta",
        iDestino: "#destino",
        iEnviarDesde: "#enviar_desde",
        iEstado: "#estado",
        iNumero: "#numero",
        iTipo: "#tipo",
        inputsSearch: "#fecha_desde, #fecha_hasta, #destino, #enviar_desde, #estado, #numero, #tipo"
    };

    // Objeto vacío que guardará elementos que se manejan por HTML.
    var dom = {};

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
        dom.iDestino = $(st.iDestino);
        dom.iEnviarDesde = $(st.iEnviarDesde);
        dom.iEstado = $(st.iEstado);
        dom.iNumero = $(st.iNumero);
        dom.iTipo = $(st.iTipo);
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


            nombre = rowINFO["Nombre"];
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
            var destino = dom.iDestino.val();
            var enviar_desde = dom.iEnviarDesde.val();
            var estado = dom.iEstado.val();
            var numero = dom.iNumero.val();
            var tipo = dom.iTipo.val();

            if(fecha_desde != "" || fecha_hasta != "" || destino != "" || enviar_desde != "" || estado != "" || numero != "" || tipo != "")
            {
                dom.jqGrid.setGridParam({
                    url: phost() + st.segmento2 +'/ajax-listar',
                    datatype: "json",
                    postData: {
                        fecha_desde: fecha_desde,
                        fecha_hasta: fecha_hasta,
                        destino: destino,
                        enviar_desde: enviar_desde,
                        estado: estado,
                        numero: numero,
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
                url: phost() + st.segmento2 +'/ajax-listar',
                datatype: "json",
                postData: {
                    fecha_desde: '',
                    fecha_hasta: '',
                    destino: '',
                    enviar_desde: '',
                    estado: '',
                    numero: '',
                    tipo: '',
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
            url: phost() + st.segmento2 + '/ajax_listar_historial_item',
            datatype: "json",
            colNames:[
                'N&uacute;mero de salida',
                'Fecha',
                'Bodega',
                'Serie',
                'Cantidad',
                '',
                ''
            ],
            colModel:[
                {name:'Nombre', index:'numero', width:55},
                {name:'Fecha', index:'fecha_creacion', width:45, sortable:false},
                {name:'Bodega', index:'numero_documento', width:65, sortable:false},
                {name:'Serie', index:'tipo', width:70, sortable:false},
                {name:'Cantidad', index:'destino', width:80, sortable:false},
                {name:'link', index:'link', width:60, align:"center", sortable:false, resizable:false, hidedlg:true},
                {name:'options', index:'options', hidedlg:true, hidden: true},
            ],
            mtype: "POST",
            postData: {
                erptkn: tkn,
                operacion_id: operacion_id,
                operacion_type: operacion_type,
                item_id: typeof window.sp_item_id !== 'undefined' ? window.sp_item_id : ''
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
            multiselect: multiselect,
            sortname: 'numero',
            sortorder: "DESC",
            beforeProcessing: function(data, status, xhr){
                //Check Session
                if( $.isEmptyObject(data.session) === false){
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
                    dom.noRecords.empty().append('No se encontraron salidas de inventario.').css({"color":"#868686","padding":"30px 0 0"}).show();
                }
                else{
                    dom.noRecords.hide();
                    $('#gbox_' + st.jqGrid).show();
                }

                if(multiselect === true){
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
    };

    var ordenarDestinos = function()
    {
        // sort list
        var my_options = $(st.iDestino + " option");
        my_options.sort(function(a,b) {
            if (a.text > b.text) return 1;
            else if (a.text < b.text) return -1;
            else return 0;
        });

        dom.iDestino.empty().append(my_options).val("-1").trigger("chosen:updated");
    };

    // Función que inicializará los funciones decritas anteriormente.
    var initialize = function(){
        catchDom();
        ordenarDestinos();
        suscribeEvents();
        muestra_tabla();
    };

    /* Retorna un objeto literal con el método init haciendo referencia a la
       función initialize. */
    return{
        init:initialize
    };
})();

//verificar si la url actual es contactos
//de lo contrario no mostrar multiselect del jqgrid
var multiselect = window.location.pathname.match(/salidas/g) ? true : false;

// Ejecutando el método "init" del módulo tabs.
tablaSalidas.init();
