/**
 * Created by angel on 01/03/16.
 */
 $(function() {

    if(typeof id_cliente === 'undefined'){
        id_cliente="";
    }

    var multiselect = window.location.pathname.match(/remesas/g) ? true : false,
    gridObj = $('#remesasGrid'),
    opcionesModal = $('#opcionesModal'),


    botones ={
        buscar: "#searchBtn",
        opciones :'.viewOptions',
        clear: "#clearBtn",
        exportar:'#exportarRemesasBtn',
        modalState: "button.updateState",
        cambiarEstado:"#cambiarEstadosBtn"
    };


    //Init Contactos Grid
    $("#remesasGrid").jqGrid({
        url: tablaUrl,
        datatype: "json",
        colNames:[
        '',
        'Nº de Remesa',
        'Recibos remesados',
        //'Nº de Póliza',
        'Aseguradora',
        'Monto',
        'Fecha',
        'Usuario',
        'Estado',
        '',
        '',
        ''
        ],
        colModel:[
        {name:'id',index:'id', width: 0, align: "center", sortable: false, hidden: true},
        {name:'remesa', index:'seg_remesas.remesa', width:10},
        {name:'recibos_remesados', index:'cantidadRecibos', width:10 },
        //{name:'poliza', index:'pol.numero', width:10 },
        {name:'aseguradora_id', index:'aseguradora_id', width:10 },
        {name:'monto', index:'seg_remesas.monto', width: 10  },
        {name:'fecha', index:'seg_remesas.fecha', width: 10  },
        {name:'usuario', index:'fullname', width: 10 },
        {name:'estado', index:'seg_remesas.estado', width: 10 },
        {name:'link', index:'link', width:10, align:"center", sortable:false, resizable:false},
        {name:'options', index:'options', width: 10, align: "center", sortable: false, hidden: true},
        {name:'updatedStated', index:'updatedStated', width: 10, align: "center", sortable: false, hidden: true}

        ],
        mtype: "POST",
        postData: {
            erptkn: tkn,
            id_cliente: id_cliente
        },
        height: "auto",
        autowidth: true,
        rowList: [10, 20,50,100],
        rowNum: 10,
        page: 1,
        pager: "#remesasGridPager",
        loadtext: '<p>Cargando Remesas...',
        hoverrows: false,
        viewrecords: true,
        refresh: true,
        gridview: true,
        multiselect: true,
        sortname: 'nombre',
        sortorder: "ASC",
        beforeProcessing: function(data, status, xhr){

            //Check Session
            if( $.isEmptyObject(data.session) === false){
                window.location = phost() + "login?expired";
            }
        },
        loadBeforeSend: function () {
            $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
            $(this).closest("div.ui-jqgrid-view").find("#jqgh_remesasGrid_cb, #jqgh_remesasGrid_link").css("text-align", "center");
        },
        beforeRequest: function(data, status, xhr){
            $('.jqgrid-overlay').show();
            $('#load_remesasGrid').addClass('ui-corner-all');
        },
        loadComplete: function(data){

            $('.jqgrid-overlay').hide();

            //check if isset data
            if($("#remesasGrid").getGridParam('records') === 0 ){
                $('#gbox_remesasGrid').hide();
                $('#remesasGridNoRecords').empty().append('No se encontraron remesas.').css({"color":"#868686","padding":"30px 0 0"}).show();
            }
            else{
                $('#gbox_remesasGrid').show();
                $('#remesasGridNoRecords').empty();
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
    $("#remesasGrid").on("click", ".viewOptions", function(e){
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();
        var id_plan = $(this).attr("data-plan");

        var rowINFO = $("#remesasGrid").getRowData(id_plan);
        var nombre = $(this).attr("data-nombre");
        var options = rowINFO["options"];
        //Init boton de opciones
        $('#optionsModal').find('.modal-title').empty().append('Opciones: '+ nombre);
        $('#optionsModal').find('.modal-body').empty().append(options);
        $('#optionsModal').find('.modal-footer').empty();
        $('#optionsModal').modal('show');
    });

    //Resize grid, on window resize end
    $(window).resizeEnd(function() {
        $(".ui-jqgrid").each(function(){
            var w = parseInt( $(this).parent().width()) - 6;
            var tmpId = $(this).attr("id");
            var gId = tmpId.replace("gbox_","");
            $("#"+gId).setGridWidth(w);
        });
    });

    $("#iconGrid").on("click", ".viewOptionsGrid", function(e){

        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();
        //Init boton de opciones
        $('#optionsModal').find('.modal-title').empty().append('Opciones: '+ $(this).closest(".chat-element").find("input[type='checkbox']").data("nombre"));
    });

    $(botones.buscar).click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        var remesa = $('#no_remesa').val();
        var aseguradora = $('#aseguradora').val();
        var usuario = $("#usuario").val();
        var estado = $("#estado").val();
        var recibo = $("#no_recibo").val();
        var poliza = $("#no_poliza").val();

        if (remesa !== "" || aseguradora !== "" || usuario !== "" || estado !=="" || recibo!==""||poliza!=="") {
            //Reload Grid
            gridObj.setGridParam({
                url: tablaUrl,
                datatype: "json",
                postData: {
                    remesa: remesa,
                    aseguradora: aseguradora,
                    usuario: usuario,
                    estado: estado,
                    recibo: recibo,
                    poliza: poliza,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }


    });
    $(botones.clear).click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        $('#no_remesa').val("");
        $("#no_recibo").val("");
        $("#no_poliza").val("");
        $("#estado,#aseguradora,#usuario").val('').trigger('chosen:updated');
         //Reload Grid
         reloadGrid();


     });



    gridObj.on("click", botones.opciones, function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        var id = $(this).attr("data-id");
        var rowINFO = $.extend({}, gridObj.getRowData(id));
        var options = rowINFO.options;
            //Init Modal
            opcionesModal.find('.modal-title').empty().append('Opciones: ' + $(rowINFO.remesa).text());
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
        });

    gridObj.on("click", botones.modalState, function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        var id = $(this).attr("data-id");
        var rowINFO = $.extend({}, gridObj.getRowData(id));
        var options = rowINFO.updatedStated;
            //Init Modal
            opcionesModal.find('.modal-title').empty().append('Opciones: ' + rowINFO.remesa);
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
            var count = 0;
            opcionesModal.on("click", ".modal-std", function (e) {
                if(count===0){
                    var previusState = $(this).attr("data-estado");
                    var estado_anterior = $(this).attr("data-estado-anterior");
                    var remesaId = [];
                    remesaId.push(id);
                    var datos = {campo: {"estado": previusState, "campo_anterior":estado_anterior ,"ids": remesaId}};
                    var cambio = moduloRemesa.cambiarEstadoRemesa(datos);
                    cambio.done(function (response) {
                        
                        if(response == 0){
                            console.log(response);
                            opcionesModal.modal('hide');
                            $('#opcionesModalAnulado').find('.modal-title').empty().append('Opciones: ' + rowINFO.remesa);
                            $('#opcionesModalAnulado').find('.modal-body').empty().append("<button class='btn btn-block btn-outline btn-danger'><p>No se puede anular la remesa</p> <p>el pago ya esta aplicado <i class='fa fa-exclamation-triangle'></p></button>");
                            $('#opcionesModalAnulado').find('.modal-footer').empty();
                            $('#opcionesModalAnulado').modal('show');
                        }else{
                            opcionesModal.modal('hide');
                            toastr.success('Se ha efectuado Cambio de Estado correctamente.');
                            $("#mensaje").hide();
                            reloadGrid();
                        }
                        
                    });
                }
                count =1;
            });

        });


    $(botones.exportar).on("click", function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        if ($('#tabla').is(':visible') === true) {
            //Exportar Seleccionados del jQgrid
            var ids = [];
            ids = gridObj.jqGrid('getGridParam', 'selarrrow');
            //Verificar si hay seleccionados
            if (ids.length > 0) {

                $('#ids').val(ids);
                $('form#exportarRemesas').submit();
                $('body').trigger('click');
            }
        }
    });

    $(opcionesModal).on("click", '.descargarRemesa', function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        //if ($('#tabla').is(':visible') === true) {
            var id = $(this).attr('data-id');
            console.log(id);
            $('#id').val(id);
            $('form#descargarRemesas').submit();
            $('body').trigger('click'); 
            $(opcionesModal).modal('hide');
        //}
    });

    $(botones.cambiarEstado).on("click", function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        if ($('#tabla').is(':visible') === true) {
            //Exportar Seleccionados del jQgrid

            var ids = [];
            
            var ids_Proceso = 0,   
            ids_pagada = 0,
            ids_anulado = 0;

            ids = gridObj.jqGrid('getGridParam', 'selarrrow');
            if (ids.length > 0) {

                ids_Proceso = _.filter(ids, function (fila) {
                    var infoFila = $.extend({}, gridObj.getRowData(fila));
                    if ($(infoFila.estado).text() == 'En Proceso') {
                        return infoFila.id;
                    }
                });
                ids_pagada = _.filter(ids, function (fila) {
                    var infoFila = $.extend({}, gridObj.getRowData(fila));
                    if ($(infoFila.estado).text() == 'Pagada') {
                        return infoFila.id;
                    }
                });
                ids_anulado = _.filter(ids, function (fila) {
                    var infoFila = $.extend({}, gridObj.getRowData(fila));
                    if ($(infoFila.estado).text() == 'Anulado') {
                        return infoFila.id;
                    }
                });
            };
            var arrayStates =[
            {name:'En Proceso',ids:ids_Proceso},
            {name:'Pagada',ids:ids_pagada},
            {name:'Anulado',ids:ids_anulado}
            ],
            sameValue=0; 
            for (var i = arrayStates.length - 1; i >= 0; i--) {
                if(arrayStates[i].ids.length>0){
                    sameValue ++;
                }
            }   
            //Verificar si hay seleccionados
            if (ids.length > 0) {
               if (sameValue===1) {
                var politicas_general = moduloRemesa.ajaxcambiarObtenerPoliticasGeneral();
                var permisos_generales = politicas_general.success(function (data) {
                    var politicas = moduloRemesa.ajaxcambiarObtenerPoliticas();
                    var permisos1 = politicas.success(function (data) {
                        var permisos = [];
                        $.each(data, function (i, filename) {
                            permisos.push(filename);
                        });

                        if (permisos.indexOf(19, 0) != -1 || permisos.indexOf(20, 0) != -1){
                            if (ids_Proceso.length > 0){
                                if (permisos.indexOf(19, 0) != -1){
                                       // updateState(ids_inactivos,ids_activos);

                                } else {
                                    opcionesModal.modal('hide');
                                    $("#mensaje").show();
                                    $("#mensaje").html('<div id="mensaje" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Usted no tiene permisos para cambiar a este estado</div>');
                                }
                            } else if (ids_pagada.length > 0){

                                if (permisos.indexOf(20, 0) != -1){
                                       // updateState(ids_inactivos,ids_activos);

                                }else {
                                    opcionesModal.modal('hide');
                                    $("#mensaje").show();
                                    $("#mensaje").html('<div id="mensaje" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Usted no tiene permisos para cambiar a este estado</div>');
                                }

                            }else if (ids_anulado.length>0){

                            }
                        } else {
                           updateState(arrayStates);
                       }
                       return permisos;
                   });
                });


            } else {
                opcionesModal.find('.modal-title').empty().append('Mensaje');
                opcionesModal.find('.modal-body').empty().append("<button class='btn btn-block btn-outline btn-danger'><p>Los registros</p> <p>no tienen el mismo estado <i class='fa fa-exclamation-triangle'></p></button>");
                opcionesModal.find('.modal-footer').empty();
                opcionesModal.modal('show');

            }

        } else {
            opcionesModal.find('.modal-title').empty().append('Mensaje');
            opcionesModal.find('.modal-body').empty().append("<button class='btn btn-block btn-outline btn-warning'>seleccione algún registro <i class='fa fa-check'></button>");
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
        }

    }

});



});

opcionesModal = $('#opcionesModal');
gridObj = $('#remesasGrid');
tablaUrl = phost() + 'remesas/ajax-listar-remesas';

function updateState(arrayStates){

   var style,
   estado,
   cambiar,
   estados =[
   {name:"En Proceso",style:"#f8ac59"},
   {name:"Anulado",style:"black"},
   {name:"Pagada",style:"#5cb85c"}];
   button=""; 
   for (var i = arrayStates.length - 1; i >= 0; i--) {
       value = arrayStates[i];
       if(value.ids.length>0){
           cambiar = value.ids;
           for (var j = estados.length - 1; j >= 0; j--) {   
             options = estados[j];
             if(options.name!=value.name && value.name!=="Pagada"){
                button += "<button class='btn btn-block manyremesa' data-estado='"+options.name+"' style='color:white;background-color:"+options.style+"'>"+options.name+"</button>";
            }else{
               button =""; 
               button += "<button class='btn btn-block btn-outline btn-warning'><p>No se puede realizar</p><p>cambio de estado</p></button>"; 
            }
        }
    }

}

opcionesModal.find('.modal-title').empty().append('Cambiar estado(s)');
opcionesModal.find('.modal-body').empty().append(button);
opcionesModal.find('.modal-footer').empty();
opcionesModal.modal('show');


var cont = 0;
opcionesModal.on("click", ".manyremesa", function (e) {
    var datos = {campo: {"estado": $(this).attr("data-estado"), "ids": cambiar}};
    if (cont == 0) {
        var cambio = moduloRemesa.cambiarEstadoRemesa(datos);
        cambio.done(function (response) {
            opcionesModal.modal('hide');
            toastr.success('Se ha efectuado Cambio de Estado correctamente.');
            $("#mensaje").hide();
            reloadGrid();

        });

    }
    cont = 1;
});

}


function reloadGrid () {
 gridObj.setGridParam({
    url: tablaUrl,
    datatype: "json",
    postData: {
     remesa: '',
     aseguradora: '',
     usuario: '',
     estado: '',
     recibo: '',
     poliza: '',
     erptkn: tkn
 }
}).trigger('reloadGrid');

}