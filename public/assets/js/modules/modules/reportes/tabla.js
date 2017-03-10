/**
 * Created by victor on 01/03/16.
 */
$(function() {

    if(typeof id_cliente === 'undefined'){
        id_cliente="";
    }

    //verificar si la url actual es contactos
    //de lo contrario no mostrar multiselect del jqgrid
    var multiselect = window.location.pathname.match(/reportes/g) ? true : false;

    //Init Contactos Grid
    $("#reportesGrid").jqGrid({
        url: phost() + 'reportes/ajax-listar-reportes',
        datatype: "json",
        colNames:[
            '',
            'Nombre',
            '&Aacute;rea',
            'Ramo',
            'Clase',
            'Acci&oacute;n',
            ''
        ],
        colModel:[
            {name:'id', index:'id', width:30,  hidedlg:true, hidden: true},
            {name:'reporte', index:'reporte', width:70 },
            {name:'area', index:'area', width:70 },
            {name:'ramo', index:'ramo', width: 50 , sortable:false},
            {name:'clase', index:'clase', width: 50 , sortable:false},
            {name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false},
            {name:'options', index:'options', hidden: true}
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
        pager: "#reportesGridPager",
        loadtext: '<p>Cargando Reportes...',
        hoverrows: false,
        viewrecords: true,
        refresh: true,
        gridview: true,
        //multiselect: multiselect,
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
            $(this).closest("div.ui-jqgrid-view").find("#jqgh_reportesGrid_cb, #jqgh_reportesGrid_link").css("text-align", "center");
        },
        beforeRequest: function(data, status, xhr){
            $('.jqgrid-overlay').show();
            $('#load_reportesGrid').addClass('ui-corner-all');
        },
        loadComplete: function(data){

            $('.jqgrid-overlay').hide();

            //check if isset data
            if($("#reportesGrid").getGridParam('records') === 0 ){
                $('#gbox_reportesGrid').hide();
                $('#reportesGridNoRecords').empty().append('No se encontraron Reportes.').css({"color":"#868686","padding":"30px 0 0"}).show();
            }
            else{
                $('#gbox_reportesGrid').show();
                $('#reportesGridNoRecords').empty();
            }

            if(multiselect == true){
                //add class to headers
                $("#reportesGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");

                //floating headers
                $('#gridHeader').sticky({
                    getWidthFrom: '.ui-jqgrid-view',
                    className:'jqgridHeader'
                });
                //Arreglar tamaño de TD de los checkboxes
                $("#reportesGrid_cb").css("width","50px");
                $("#reportesGrid tbody tr").children().first("td").css("width","50px");
            }
        },
        onSelectRow: function(id){
            $(this).find('tr#'+ id).removeClass('ui-state-highlight');
        },
    });

    //Boton de opciones
    $("#reportesGrid").on("click", ".viewOptions", function(e){
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();
        var id_plan = $(this).attr("data-reporte");

        var rowINFO = $("#reportesGrid").getRowData(id_plan);
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

    //Estas funciones aplican cuando se carga la tabla de contactos
    //desde modulo de clientes.
    /*if(multiselect == false){

        //-----------------------------
        // Accciones para modo: Subpanel
        //-----------------------------
        //Abrir ventana de Crear contacto
        /*$("#optionsModal").on("click", "#verPlan", function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();

            if($(this).attr('href') == "#"){

                var id_contacto = $(this).attr('data-reporte');

                //ocultar vista de cliente
                $('.editarFormularioClientes').addClass('hide');

                //mostrar formulario de editar contacto
                $('#sub-panel-grid-modulos').find('.panel-heading').find('.sub-panel-dropdown-contenido').find('a[href*="editarContactos"]').trigger('click');

                //ocultar modal
                $('#optionsModal').modal('hide');

                popular_detalle_contacto(id_contacto);
            }
        });

        //Asignar un contacto como principal
        $("#optionsModal").on("click", "#asignarContactoPrincipalBtn", function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();
            var uuid_contacto = $(this).attr('data-contacto');
            $.ajax({
                url: phost() + 'contactos/ajax-asignar-contacto-principal',
                data: {
                    uuid_contacto: uuid_contacto,
                    uuid_cliente: id_cliente,
                    erptkn: tkn
                },
                type: "POST",
                dataType: "json",
                cache: false,
            }).done(function(json) {
                //Check Session
                if( $.isEmptyObject(json.session) == false){
                    window.location = phost() + "login?expired";
                }

                //If json object is empty.
                if($.isEmptyObject(json.results) == true){
                    return false;
                }

                //Mostrar Mensaje
                $class_mensaje = json.results[0]['respuesta'] == true ? 'alert-success' : 'alert-danger';
                mensaje_alerta(json.results[0]['mensaje'], $class_mensaje);

                //Recargar tabla de contactos
                $("#reportesGrid").setGridParam({
                    url: phost() + 'contactos/ajax-listar-contactos',
                    datatype: "json",
                    postData: {
                        nombre: '',
                        cliente: '',
                        telefono: '',
                        email: '',
                        erptkn: tkn
                    }
                }).trigger('reloadGrid');

            });

            $('#optionsModal').modal('hide');
        });
    }

    function cboxFormatter(cellvalue, options, rowObject) {
        return '<input type="checkbox"' + (cellvalue == "1" ? ' checked="checked" disabled ' : '') +
            'data-rowId="' + options.rowId + '" value="' + cellvalue + '" class="principal"/>';
    }
    $('table#reportesGrid').on('click','.principal', function(e){
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();
        var uuid_contacto = $(this).data('rowid');
        var parametros = {uuid_contacto:uuid_contacto,uuid_cliente:id_cliente};
        var principal = moduloContacto.asignarPrincipal(parametros);
        principal.done(function(data){
            $("#reportesGrid").trigger('reloadGrid');
        });
        //verificar si s
    });*/

});
