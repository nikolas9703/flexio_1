/**
 * Created by angel on 01/03/16.
 */
 $(function() {
    gridObj = $('#endososGrid'),
    opcionesModal = $('#opcionesModal'),
    cambioEstado = $('#cambioEstado'),
    SubirDocumento = $('#documentosModal'),
    tablaUrl = phost() + 'endosos/ajax_listar_endosos';

    botones ={
        buscar: "#searchBtn",
        clear: "#clearBtn",
        exportar:'#exportarEndososBtn',
        cambiarEstado:"#cambiarEstadosEndososLnk"
    };

    //Init Contactos Grid
    $("#endososGrid").jqGrid({
        url: tablaUrl,
        datatype: "json",
        colNames:[
        '',
        'No. endoso',
        'Cliente',
        'Aseguradora',
        'Ramo/Riesgo',
        'No. póliza',
        'Fecha de creación',
        'Tipo',
        'Estado',
        '',
        '',
        '',
        ],
        colModel:[
        {name:'id',index:'id', width: 0, align: "center", sortable: false, hidden: true},
        {name:'endoso', index:'endoso', width:10},
        {name:'cliente', index:'cliente_id', width:10 },
        {name:'aseguradora', index:'aseguradora_id', width:10 },
        {name:'ramo', index:'id_ramo', width:10 },
        {name:'poliza', index:'id_poliza', width: 10  },
        {name:'fecha', index:'fecha_creacion', width: 10  },
        {name:'tipo', index:'tipo', width: 10 },
        {name:'estado', index:'estado', width: 10 },
        {name:'link', index:'link', width:10, align:"center", sortable:false, resizable:false},
        {name:'options', index:'options', width: 10, align: "center", sortable: false, hidden: true},
        {name:'modalstate', index:'modalstate', width: 10, align: "center", sortable: false, hidden: true},
        ],
        mtype: "POST",
        postData: {
            erptkn: tkn,
            //id_empresa: id_empresa
        },
        height: "auto",
        autowidth: true,
        rowList: [10, 20,50,100],
        rowNum: 10,
        page: 1,
        pager: "#endososGridPager",
        loadtext: '<p>Cargando Remesas...',
        hoverrows: false,
        viewrecords: true,
        refresh: true,
        gridview: true,
        multiselect: true,
        sortname: '',
        sortorder: '',
        beforeProcessing: function(data, status, xhr){

            //Check Session
            if( $.isEmptyObject(data.session) === false){
                window.location = phost() + "login?expired";
            }
        },
        loadBeforeSend: function () {
            $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
            $(this).closest("div.ui-jqgrid-view").find("#jqgh_endososGrid_cb, #jqgh_endososGrid_link").css("text-align", "center");
        },
        beforeRequest: function(data, status, xhr){
            $('.jqgrid-overlay').show();
            $('#load_endososGrid').addClass('ui-corner-all');
        },
        loadComplete: function(data){

            $('.jqgrid-overlay').hide();

            //check if isset data
            if($("#endososGrid").getGridParam('records') === 0 ){
                $('#gbox_endososGrid').hide();
                $('#endososGridNoRecords').empty().append('No se encontraron endosos.').css({"color":"#868686","padding":"30px 0 0"}).show();
            }else{
                $('#gbox_endososGrid').show();
                $('#endososGridNoRecords').empty();
            }

            /*if(multiselect == true){
                //add class to headers
                $("#remesasGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");

                //floating headers
                $('#gridHeader').sticky({
                    getWidthFrom: '.ui-jqgrid-view',
                    className:'jqgridHeader'
                });
                //Arreglar tamaño de TD de los checkboxes
                $("#remesasGrid_cb").css("width","50px");
                $("#remesasGrid tbody tr").children().first("td").css("width","50px");
            }*/
        },
        onSelectRow: function(id){
            $(this).find('tr#'+ id).removeClass('ui-state-highlight');
        },
    });

    //Boton de opciones
    

    //Resize grid, on window resize end
    $(window).resizeEnd(function() {
        $(".ui-jqgrid").each(function(){
            var w = parseInt( $(this).parent().width()) - 6;
            var tmpId = $(this).attr("id");
            var gId = tmpId.replace("gbox_","");
            $("#"+gId).setGridWidth(w);
        });
    });

    $(botones.buscar).click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        endoso = $('#no_endoso').val();
        cliente = $('#cliente').val();
        aseguradora = $('#aseguradora').val();
        ramo = $('#ramo').val();
        tipo_endoso = $('#tipo_endoso').val();
        motivo_endoso = $('#motivos_endosos').val();
        fecha_inicio = $('#fecha_desde').val();
        fecha_final = $('#fecha_hasta').val();
        estado = $('#estado').val();

        if (endoso !== "" || cliente !== "" || aseguradora !== "" || ramo !== "" ||  tipo_endoso != "" || motivo_endoso != "" || fecha_inicio !== "" || fecha_final !== "" || estado != "") {
            //Reload Grid
            gridObj.setGridParam({
                url: tablaUrl,
                datatype: "json",
                postData: {
                    endoso: endoso,
                    cliente: cliente,
                    aseguradora: aseguradora,
                    ramo: ramo,
                    tipo_endoso: tipo_endoso,
                    motivo_endoso: motivo_endoso,
                    fecha_inicio: fecha_inicio,
                    fecha_final: fecha_final,
                    estado: estado,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }

    });

    $(botones.clear).click(function (e) {

        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        $('#no_endoso,#fecha_desde,#fecha_hasta').val('');
        $("#cliente,#aseguradora,#ramo,#tipo_endoso,#motivos_endosos,#estado").val('').trigger('chosen:updated');
         //Reload Grid
         reloadGrid();
    });

    function reloadGrid () {
        gridObj.setGridParam({
            url: tablaUrl,
            datatype: "json",
            postData: {
                endoso: '',
                cliente: '',
                aseguradora: '',
                ramo: '',
                tipo_endoso: '',
                motivo_endoso: '',
                fecha_inicio: '',
                fecha_final: '',
                estado: '',
                erptkn: tkn
        }}).trigger('reloadGrid');
    }


    gridObj.on("click", '.viewOptions',function(e){

        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();
        //Init boton de opciones
        
        var id = $(this).attr('data-id');
        var nombre = $(this).attr('data-nombre');
        var rowINFO = gridObj.getRowData(id);

        opcionesModal.find('.modal-title').empty().append('Opciones: ' + nombre);
        opcionesModal.find('.modal-body').empty().append(rowINFO['options']);
        opcionesModal.find('.modal-footer').empty();
        opcionesModal.modal('show');

    });

    gridObj.on('click','.estadoEndosos', function(e){

        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation;

        var id = $(this).attr('data-id');
        var nombre = $(this).attr('data-nombre');
        var estado = $(this).attr('data-estado');
        var rowINFO = gridObj.getRowData(id);

        console.log(estado);
       
        if(estado != "Cancelado" && estado  != "Aprobado" && estado  != "Rechazado"){
            opcionesModal.find('.modal-title').empty().append('Opciones: '+nombre);
            opcionesModal.find('.modal-body').empty().append(rowINFO['modalstate']);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show'); 

            opcionesModal.on('click','.cambiarEstadoBtn',function(e){
                var id = $(this).attr('data-id');
                var estado = $(this).attr('data-estado');
                var datos = {campo:{ ids: [id], estado: estado}};
                var cambio = moduloEndoso.cambiarEstadoEndoso(datos);
                cambio.done(function (response){
                    reloadGrid();
                    opcionesModal.modal('hide');
                    id = "";
                    toastr.success('Se ha efectuado Cambio de Estado correctamente.');
                });
                cambio.fail(function(response){
                    toastr.error('No se pudo efectuar el Cambio de Estado correctamente.');
                });
            });
        }
    });

    $(botones.cambiarEstado).on('click',function(e){

        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation;

        if($('#tabla').is(':visible') === true){
            
            var ids = [];

            var ids_pendiente = 0;
            var ids_tramite = 0;
            var ids_aprobado = 0;
            var ids_rechazado = 0;
            var ids_cancelado = 0;
                          
            ids = gridObj.jqGrid('getGridParam', 'selarrrow');

            if(ids.length > 0){

                ids_pendiente  = _.filter(ids, function (fila) {
                    var infoFila = $.extend({}, gridObj.getRowData(fila));
                    if ($(infoFila.estado).text() == 'Pendiente') {
                        return infoFila.id;
                    }
                });
                ids_tramite  = _.filter(ids, function (fila) {
                    var infoFila = $.extend({}, gridObj.getRowData(fila));
                    if ($(infoFila.estado).text() == 'En Trámite') {
                        return infoFila.id;
                    }
                });
                ids_aprobado  = _.filter(ids, function (fila) {
                    var infoFila = $.extend({}, gridObj.getRowData(fila));
                    if ($(infoFila.estado).text() == 'Aprobado') {
                        return infoFila.id;
                    }
                });
                ids_rechazado  = _.filter(ids, function (fila) {
                    var infoFila = $.extend({}, gridObj.getRowData(fila));
                    if ($(infoFila.estado).text() == 'Rechazado') {
                        return infoFila.id;
                    }
                });
                ids_cancelado  = _.filter(ids, function (fila) {
                    var infoFila = $.extend({}, gridObj.getRowData(fila));
                    if ($(infoFila.estado).text() == 'Cancelado') {
                        return infoFila.id;
                    }
                });

                if( (ids_pendiente.length > 0 && ids_tramite.length == 0 && ids_aprobado.length == 0 && ids_rechazado.length == 0 && ids_cancelado.length == 0) || (ids_pendiente.length == 0 && ids_tramite.length > 0 && ids_aprobado.length == 0 && ids_rechazado.length == 0 && ids_cancelado.length == 0) || (ids_pendiente.length == 0 && ids_tramite.length == 0 && ids_aprobado.length > 0 && ids_rechazado.length == 0 && ids_cancelado.length == 0) || (ids_pendiente.length == 0 && ids_tramite.length == 0 && ids_aprobado.length == 0 && ids_rechazado.length > 0 && ids_cancelado.length == 0) || (ids_pendiente.length == 0 && ids_tramite.length == 0 && ids_aprobado.length == 0 && ids_rechazado.length == 0 && ids_cancelado.length > 0) ){

                    if(ids_aprobado.length > 0 || ids_rechazado.length > 0 || ids_cancelado.length > 0){

                        opcionesModal.find('.modal-title').empty().append('Mensaje');
                        opcionesModal.find('.modal-body').empty().append("<button class='btn btn-block btn-outline btn-warning'><p>No puede realizar</p> <p>el cambio de estado</p></button>");
                        opcionesModal.find('.modal-footer').empty();
                        opcionesModal.modal('show');

                    }else{

                        if(ids_pendiente.length > 0){
                            var button = "<button id='en_tramite' data-estado='En Trámite' class='btn btn-block btn-outline massive' style='border: #F8AD46 1px solid; color: #F8AD46;'>En trámite</button>";
                        }else if(ids_tramite.length > 0){
                            var button = "<button id='pendiente' data-estado='Pendiente' class='btn btn-block btn-outline massive' style='border: #5bc0de 1px solid; color: #5bc0de;'>Pendiente</button>";
                        }

                        //button += "<button id='aprobado' data-estado='Aprobado' class='btn btn-block btn-outline massive' style='border: #5cb85c 1px solid; color: #5cb85c;' >Aprobado</button>";
                        button += "<button data-estado='Rechazado' class='btn btn-block btn-outline btn-danger massive' >Rechazado</button>";
                        button += "<button id='cancelado' data-estado='Cancelado' class='btn btn-block btn-outline massive' style='border: #000000 1px solid; color: #000000;' >Cancelado</button>";
                        
                        opcionesModal.find('.modal-title').empty().append('Cambiar estado');
                        opcionesModal.find('.modal-body').empty().append(button);
                        opcionesModal.find('.modal-footer').empty();
                        opcionesModal.modal('show');

                        $('#cancelado').mouseover(function () {
                            $('#cancelado').css('color', '#FFFFFF');
                            $('#cancelado').css('background-color', 'black');
                        });

                        $('#cancelado').mouseout(function () {
                            $('#cancelado').css('color', 'black');
                            $('#cancelado').css('background-color', '#FFFFFF');
                        });
                        /*$('#aprobado').mouseover(function () {
                            $('#aprobado').css('color', '#FFFFFF');
                            $('#aprobado').css('background-color', '#5cb85c');
                        });
                        $('#aprobado').mouseout(function () {
                            $('#aprobado').css('color', '#5cb85c');
                            $('#aprobado').css('background-color', '#FFFFFF');
                        });*/
                        $('#pendiente').mouseover(function () {
                            $('#pendiente').css('color', 'white');
                            $('#pendiente').css('background-color', '#5bc0de');
                        });
                        $('#pendiente').mouseout(function () {
                            $('#pendiente').css('color', '#5bc0de');
                            $('#pendiente').css('background-color', 'white');
                        });
                        $('#en_tramite').mouseover(function () {
                            $('#en_tramite').css('color', 'white');
                            $('#en_tramite').css('background-color', '#F8AD46');
                        });
                        $('#en_tramite').mouseout(function () {
                            $('#en_tramite').css('color', '#F8AD46');
                            $('#en_tramite').css('background-color', 'white');
                        });
                    }
                }else{
                    opcionesModal.find('.modal-title').empty().append('Mensaje');
                    opcionesModal.find('.modal-body').empty().append("<button class='btn btn-block btn-outline btn-danger'><p>Los registros</p> <p>no tienen el mismo estado <i class='fa fa-exclamation-triangle'></p></button>");
                    opcionesModal.find('.modal-footer').empty();
                    opcionesModal.modal('show');
                }

            }else{

                opcionesModal.find('.modal-title').empty().append('Mensaje');
                opcionesModal.find('.modal-body').empty().append("<button class='btn btn-block btn-outline btn-warning'>seleccione algún registro <i class='fa fa-check'></button>");
                opcionesModal.find('.modal-footer').empty();
                opcionesModal.modal('show');
            }
        }

        opcionesModal.on('click','.massive',function(e){

            var estado = $(this).attr('data-estado');
            datos = {campo: {ids: ids, estado: estado}};
            var cambio = moduloEndoso.cambiarEstadoEndoso(datos);
                cambio.done(function (response){
                    reloadGrid();
                    opcionesModal.modal('hide');
                    ids = "";
                    toastr.success('Se ha efectuado Cambio de Estado correctamente.');
                });
                cambio.fail(function(response){
                    toastr.error('No se pudo efectuar el Cambio de Estado correctamente.');
                });

        });
    });

    
    $(botones.exportar).on('click',function(e){
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation;

        if($('#tabla').is(':visible') === true){

            var ids = [];
            ids = gridObj.jqGrid('getGridParam', 'selarrrow');
            if(ids.length > 0){ 

                $("#ids").val(ids);
                $("form#exportarEndosos").submit();
                $('body').trigger('click');

            }else{
                opcionesModal.find('.modal-title').empty().append('Mensaje');
                opcionesModal.find('.modal-body').empty().append("<button class='btn btn-block btn-outline btn-warning'>seleccione algún registro <i class='fa fa-check'></button>");
                opcionesModal.find('.modal-footer').empty();
                opcionesModal.modal('show');
            }

        }

    });


    $(opcionesModal).on('click','.cambiarEstados', function(e){

        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation;
        
        var id = $(this).attr('data-id');
        var nombre = $(this).attr('data-nombre');
        rowINFO = gridObj.getRowData(id);
        
        opcionesModal.modal('hide');
        cambioEstado.find('.modal-title').empty().append('Opciones: '+nombre);
        cambioEstado.find('.modal-body').empty().append(rowINFO['modalstate']);
        cambioEstado.find('.modal-footer').empty();
        cambioEstado.modal('show'); 

        cambioEstado.on('click','.cambiarEstadoBtn',function(e){
            var id = $(this).attr('data-id');
            var estado = $(this).attr('data-estado');
            var datos = {campo:{ ids: [id], estado: estado}};
            var cambio = moduloEndoso.cambiarEstadoEndoso(datos);
            cambio.done(function (response){
                reloadGrid();
                cambioEstado.modal('hide');
                id = "";
                toastr.success('Se ha efectuado Cambio de Estado correctamente.');
            });
            cambio.fail(function(response){
                toastr.error('No se pudo efectuar el Cambio de Estado correctamente.');
            });
        });
    });

    $(opcionesModal).on('click','.subirDocumentos',function(e){

        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation;

        var id_endoso = $(this).attr('data-id');
        opcionesModal.modal('hide');
        $('#id_endoso').val(id_endoso);
        SubirDocumento.modal('show');
    });

});





    