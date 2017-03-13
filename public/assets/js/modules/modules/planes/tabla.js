/**
 * Created by victor on 01/03/16.
 */
 
$(function() {
     
     var uuid_asegura=$('input[name="campo[uuid]').val();

    //verificar si la url actual es contactos
    //de lo contrario no mostrar multiselect del jqgrid
    var multiselect = window.location.pathname.match(/planes/g) ? true : false;

    //Init Contactos Grid
    $("#PlanesGrid").jqGrid({
        url: phost() + 'planes/ajax-listar-planes',
        datatype: "json",
        colNames:[
            '',
            'Nombre',
            'Producto',
            'Ramo',
            'Comisi&oacute;n',
            'Sobre comisi&oacute;n',
            'Desc. Comisi&oacute;n',
            '',
            ''
        ],
        colModel:[
            {name:'id', index:'id', width:30,  hidedlg:true, hidden: true},
            {name:'seg_planes.nombre', index:'seg_planes.nombre', width:70 },
            {name:'producto.nombre', index:'producto.nombre', width:70 },
            {name:'seg_ramos.nombre', index:'seg_ramos.nombre', width: 50 },
            {name:'seg_planes.comision', index:'seg_planes.comision', width: 50 },
            {name:'seg_planes.sobre_comision', index:'seg_planes.sobre_comision', width: 50 },
            {name:'seg_planes.desc_comision', index:'seg_planes.desc_comision', width: 50 },
            {name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false},
            {name:'options', index:'options', hidden: true}
        ],
        mtype: "POST",
        postData: {
            erptkn: tkn,
            uuid: uuid_asegura
        },
        height: "auto",
        autowidth: true,
        rowList: [10, 20,50,100],
        rowNum: 10,
        page: 1,
        pager: "#planesGridPager",
        loadtext: '<p>Cargando Planes...',
        hoverrows: false,
        viewrecords: true,
        refresh: true,
        gridview: true,
        multiselect: true,
        sortname: 'seg_planes.nombre',
        sortorder: "ASC",
        beforeProcessing: function(data, status, xhr){
            //Check Session
            if( $.isEmptyObject(data.session) === false){
                window.location = phost() + "login?expired";
            }
        },
        loadBeforeSend: function () {
            $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
            $(this).closest("div.ui-jqgrid-view").find("#jqgh_planesGrid_cb, #jqgh_planesGrid_link").css("text-align", "center");
        },
        beforeRequest: function(data, status, xhr){
            $('.jqgrid-overlay').show();
            $('#load_planesGrid').addClass('ui-corner-all');
        },
        loadComplete: function(data){
/*
            $('.jqgrid-overlay').hide();

            //check if isset data
            if($("#planesGrid").getGridParam('records') === 0 ){
                $('#gbox_planesGrid').hide();
                $('#planesGridNoRecords').empty().append('No se encontraron Planes.').css({"color":"#868686","padding":"30px 0 0"}).show();
            }
            else{
                $('#gbox_planesGrid').show();
                $('#planesGridNoRecords').empty();
            }

            if(multiselect == true){
                //add class to headers
                $("#planesGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");

                //floating headers
                $('#gridHeader').sticky({
                    getWidthFrom: '.ui-jqgrid-view',
                    className:'jqgridHeader'
                });
                //Arreglar tamaño de TD de los checkboxes
                $("#planesGrid_cb").css("width","50px");
                $("#planesGrid tbody tr").children().first("td").css("width","50px");
            } */
        },
        onSelectRow: function(id){
            $(this).find('tr#'+ id).removeClass('ui-state-highlight');
        },
    });

    //Boton de opciones
    $("#planesGrid").on("click", ".viewOptions", function(e){
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();
        var id_plan = $(this).attr("data-id");

        var rowINFO = $("#planesGrid").getRowData(id_plan);
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

                var id_contacto = $(this).attr('data-plan');

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
                $("#planesGrid").setGridParam({
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
    $('table#planesGrid').on('click','.principal', function(e){
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();
        var uuid_contacto = $(this).data('rowid');
        var parametros = {uuid_contacto:uuid_contacto,uuid_cliente:id_cliente};
        var principal = moduloContacto.asignarPrincipal(parametros);
        principal.done(function(data){
            $("#planesGrid").trigger('reloadGrid');
        });
        //verificar si s
    });*/

});
