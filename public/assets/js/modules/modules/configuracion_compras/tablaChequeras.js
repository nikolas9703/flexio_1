// Definimos la variable tabs la cual contendrá todo nuestro modulo.
var tablaChequeras = (function(){

    // Objeto al cual establecemos valores que vamos a usar mas adelante en este ambito.
    var st = {
        iOptionsModal:"#optionsModal",
        jqGrid: "#chequerasGrid",
        jqPager: "#chequerasPager",
        optionsModal: "#optionsModal",
        guardarBtn: "#guardarChequeraBtn",
        cancelarBtn: "#cancelarChequeraBtn",
        noRecords: ".NoRecordsChequeras",
        segmento2: "configuracion_compras",
        editarChequera: ".editarChequera",
        desactivarChequera: ".desactivarChequera",
        activarChequera: ".activarChequera",
        //campos del formulario de creacion/edicion
        iChequera: "#nombre",
        iCuentaBanco: "#cuenta_banco",
        iChequeInicial: "#cheque_inicial",
        iChequeFinal: "#cheque_final",
        iProximoCheque: "#proximo_cheque",
        iAncho: "#ancho",
        iAlto: "#alto",
        iIzquierda: "#izquierda",
        iDerecha: "#derecha",
        iArriba: "#arriba",
        iAbajo: "#abajo",
        iPosicion: "#posicion",
        iModo: "#modoChequera"

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
        dom.iChequera = $(st.iChequera);
        dom.iCuentaBanco = $(st.iCuentaBanco);
        dom.iChequeInicial = $(st.iChequeInicial);
        dom.iChequeFinal = $(st.iChequeFinal);
        dom.iProximoCheque = $(st.iProximoCheque);
        dom.iAncho = $(st.iAncho);
        dom.iAlto = $(st.iAlto);
        dom.iIzquierda = $(st.iIzquierda);
        dom.iDerecha = $(st.iDerecha);
        dom.iArriba = $(st.iArriba);
        dom.iAbajo = $(st.iAbajo);
        dom.iPosicion = $(st.iPosicion);
        dom.iModo = $(st.iModo);
    };

    // Función donde establecemos los eventos que tendrán cada elemento.
    var suscribeEvents = function(){
        dom.jqGrid.on("click", ".viewOptions", events.eMostrarModal);
        dom.iOptionsModal.on("click", st.editarChequera, events.eEditarChequera);
        dom.iOptionsModal.on("click", st.desactivarChequera, events.eDesactivarChequera);
        dom.iOptionsModal.on("click", st.activarChequera, events.eActivarChequera);

        dom.guardarBtn.on("click", events.eGuardar);
        dom.cancelarBtn.on("click", limpiarFormulario);
    };

    /* Objeto que guarda métodos que se van a usar en cada evento definido
      en la función suscribeEvents. */
    var events = {
        eGuardar: function(e){

            var uuid = dom.iModo.data("uuid");

            $.ajax({
                url: phost() + st.segmento2 + "/ajax-guardar",
                type:"POST",
                data:{
                    erptkn:tkn,
                    uuid: uuid,
                    nombre: $('#nombre_chequera').val(),
                    chequera: dom.iChequera.val(),
                    cuenta_banco: dom.iCuentaBanco.val(),
                    cheque_inicial: dom.iChequeInicial.val(),
                    cheque_final: dom.iChequeFinal.val(),
                    proximo_cheque: dom.iProximoCheque.val(),
                    ancho: dom.iAncho.val(),
                    alto: dom.iAlto.val(),
                    izquierda: dom.iIzquierda.val(),
                    derecha: dom.iDerecha.val(),
                    arriba: dom.iArriba.val(),
                    abajo: dom.iAbajo.val(),
                    posicion: dom.iPosicion.val(),
                    modo: dom.iModo.val()
                },
                dataType:"json",
                success: function(data){
                    if(data.success === true)
                    {
                        dom.jqGrid.trigger("reloadGrid");
                        toastr.success("¡&Eacute;xito! Se ha guardado correctamente la << Chequera >>.");

                        limpiarFormulario();
                    }
                }

            });
        },
        eEditarChequera: function(e){

            var self = $(this);
            var uuid = self.data("uuid");

            dom.iModo.data("uuid", uuid);

            $.ajax({
                url: phost() + st.segmento2 + "/ajax-get-chequera",
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
        eDesactivarChequera: function(e){
            var self = $(this);
            var uuid = self.data("uuid");

            cambiarEstadoChequera(uuid, "2");
        },
        eActivarChequera: function(e){
            var self = $(this);
            var uuid = self.data("uuid");

            cambiarEstadoChequera(uuid, "1");
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

            nombre = rowINFO["Nombre Chequera"];
	    //Init boton de opciones
            dom.optionsModal.find('.modal-title').empty().append('Opciones: '+ nombre);
            dom.optionsModal.find('.modal-body').empty().append(options);
            dom.optionsModal.find('.modal-footer').empty();
            dom.optionsModal.modal('show');
        }
    };

    var muestra_tabla = function(){
        dom.jqGrid.jqGrid({
            url: phost() + st.segmento2 + '/ajax-listar-chequeras',
            datatype: "json",
            colNames:[
                'Nombre Chequera',
                'N&uacute;mero de cheque inicial',
                'N&uacute;mero de cheque final',
                'Estatus',
                'Acci&oacute;n',
                ''
            ],
            colModel:[
                {name:'Nombre Chequera', index:'nombre', width:60},
                {name:'N&uacute;mero de cheque inicial', index:'cheque_inicial', width:60,  sortable:false},
                {name:'N&uacute;mero de cheque final', index:'cheque_final', width:60,  sortable:false},
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
            multiselect: multiselect,
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
                    dom.noRecords.empty().append('No se encontraron Chequeras.').css({"color":"#868686","padding":"30px 0 0"}).show();
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

    var cambiarEstadoChequera = function(uuid, estado){
        $.ajax({
            url: phost() + st.segmento2 + "/ajax-cambiar-estado-chequera",
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
                    toastr.success("¡&Eacute;xito! Se ha guardado correctamente la << Chequera >>.");
                }
            }

        });
    }

    var limpiarFormulario = function(){

        dom.iChequera.val("");
        dom.iCuentaBanco.val("");
        dom.iChequeInicial.val("");
        dom.iChequeFinal.val("");
        dom.iProximoCheque.val("");
        dom.iAncho.val("");
        dom.iAlto.val("");
        dom.iIzquierda.val("");
        dom.iDerecha.val("");
        dom.iArriba.val("");
        dom.iAbajo.val("");
        dom.iPosicion.val("");
        dom.iModo.data("uuid", "");
        $('#nombre_chequera').val("");

    }

    var llenarFormulario = function(registro){

        $('#nombre_chequera').val(registro.nombre);
        dom.iCuentaBanco.val(registro.cuenta_banco_id);
        dom.iChequeInicial.val(registro.cheque_inicial);
        dom.iChequeFinal.val(registro.cheque_final);
        dom.iProximoCheque.val(registro.proximo_cheque);
        dom.iAncho.val(registro.ancho);
        dom.iAlto.val(registro.alto);
        dom.iIzquierda.val(registro.izquierda);
        dom.iDerecha.val(registro.derecha);
        dom.iArriba.val(registro.arriba);
        dom.iAbajo.val(registro.abajo);
        dom.iPosicion.val(registro.posicion);
        dom.iModo.data("uuid", registro.uuid_chequera);


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
var multiselect = window.location.pathname.match(/configuracion_compras/g) ? true : false;


// Ejecutando el método "init" del módulo tabs.
tablaChequeras.init();
