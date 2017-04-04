
var tablaDocumentos = (function () {

    var id_ramo = $("#id_ramo_catalogo").val();
    if(id_ramo == ''){
        id_ramo = 0;
    }

    var tablaUrl = phost() + 'catalogos/ajax_listar_documentos';
    
    var gridId = "tablaDocumentos";
    var gridObj = $("#tablaDocumentos");
    var opcionesModal = $('#opcionesModal');
    var grid_obj = $("#tablaDocumentos");

    var botones = {
        opciones: ".opcionesDocumentos",
        subir_archivo: ".subir_documento_solicitudes_intereses",
        ver_interes: ".linkCargaInfo"
    };

    var tabla = function () {
        gridObj.jqGrid({
            url: tablaUrl,
            mtype: "POST",
            datatype: "json",
            colNames: [
                '',
                'Nombre', 
                'Categoria', 
                'Modulo', 
                'Estado',
                '',
                ''
            ],
            colModel:
            [
                {name:'', index:'id', hidden:true, width:30,align:"center"},
                {name:'nombre', index:'nombre', width:40,align:"center"},
                {name:'categoria', index:'categoria', width:40,align:"center"},
                {name:'modulo', index:'modulo', width: 40,align:"center"},
                {name:'estado', index:'estado', width: 40,align:"center"},
                {name:'options', index:'options', width:50, sortable:false, resizable:false, hidedlg:true, align:"center"},
                {name:'modal', index:'modal', width:50, sortable:false, resizable:false, hidedlg:true, align:"center", hidden:true,},
            ],
            postData: {
                id_ramo: id_ramo,
                erptkn: tkn,
            },
            height: "auto",
            autowidth: true,
            rowList: [10, 20, 50, 100],
            rowNum: 10,
            page: 1,
            pager: "#" + gridId + "Pager",
            loadtext: '<p>Cargando...</p>',
            hoverrows: false,
            viewrecords: true,
            refresh: true,
            gridview: true,
            sortname: "nombre",
            sortorder: "ASC",

            beforeProcessing: function (data, status, xhr) {
                //Check Session
                if ($.isEmptyObject(data.session) == false) {
                    window.location = phost() + "login?expired";
                }
            },
            loadBeforeSend: function () {//propiedadesGrid_cb
                $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                $(this).closest("div.ui-jqgrid-view").find("#tablaSolicitudesVehiculoGrid_cb, #jqgh_tablaSolicitudesVehiculoGrid_link").css("text-align", "center");
            },
            beforeRequest: function (data, status, xhr) {},
            loadComplete: function (data, status, xhr) {

                /*if (gridObj.getGridParam('records') === 0) {
                    $('#gbox_' + gridId).hide();
                    $('#' + gridId + 'NoRecords').empty().append('No se han agregado intereses asegurados.').css({"color": "#868686", "padding": "30px 0 0"}).show();
                } else {
                    $('#gbox_' + gridId).show();
                    $('#' + gridId + 'NoRecords').empty();
                }*/
                var DataGrid = gridObj;
                gridObj.jqGrid('setGridWidth', $('#accordion').width()); 
                console.log($('#accordion').width());
                //sets the grid size initially
                 
                

                //---------
                // Cargar plugin jquery Sticky Objects
                //----------
                //add class to headers

                //gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeader");

                //floating headers
                $('#gridHeader').sticky({
                    getWidthFrom: '.ui-jqgrid-view',
                    className: 'jqgridHeader'
                });

                //Arreglar tamaño de TD de los checkboxes
                //FALTA ADAPTAR EL CODIGO PARA QUE LOS CHECKBOX SE VEAN BIEN
                $('#jqgh_' + gridId + "_cb").css("text-align", "center");
                $('.s-ico').removeAttr('style');
            },
            
            /*onSelectRow: function (id) {
                $(this).find('tr#' + id).removeClass('ui-state-highlight');
            }*/
        });

        //Al redimensionar ventana
        $(window).resizeEnd(function () {
            tablaDocumentos.redimencionar_tabla();
        });
    };


    var eventos = function () {
        //Bnoton de Opciones
        
        $(".opcionesDocumentos").click(function (e) {
            console.log("hola");
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            var id = $(this).attr("data-id");
            var rowINFO = $.extend({}, gridObj.getRowData(id));
            var options = rowINFO.modal;
            //Init Modal
            var nombre = rowINFO.nombre;
            opcionesModal.find('.modal-title').empty().append('Opciones: ' + nombre + '');
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
        });

        $(opcionesModal).on("click", ".linkCargaInfo", function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            opcionesModal.modal("hide");
            var id = $(this).attr("data-id");
            var parametros = {id:id};
            var verDocumento = moduloAseguradora.verDocumento(parametros);
            verDocumento.done(function (data) {
                var datos = $.parseJSON(data);
                $("#id_documento").val(datos.id);
                $("#nombre_documentacion").val(datos.nombre);
                $("#categoria_documentos").val(datos.categoria);
                $("#modulo_documentos").val(datos.modulo);
            }); 
        });

        $(opcionesModal).on("click", ".cambiar_estado", function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            opcionesModal.modal("hide");
            var id = $(this).attr("data-id");
            var rowINFO = $.extend({}, gridObj.getRowData(id));
            var options = rowINFO.modal;
            var nombre = rowINFO.nombre;
            //Init Modal
            $('#modalCambioEstado').find('.modal-title').empty().append('Cambiar estado: ' + nombre + '');
            $('#modalCambioEstado').find('.modal-body').empty().append("<a style='color:white; background-color: #5cb85c' class='btn btn-block btn-outline cambiarestadoDocumento' data-id='"+id+"' data-estado='Activo' >Activo</a><a style='color:white; background-color: red;' class='btn btn-block btn-outline cambiarestadoDocumento' data-id='"+id+"' data-estado='Inactivo'>Inactivo</a>");
            $('#modalCambioEstado').find('.modal-footer').empty();
            $('#modalCambioEstado').modal('show');

                $('#modalCambioEstado').on("click",".cambiarestadoDocumento",function (e) {
                    var estado = $(this).attr("data-estado");
                    console.log(id,estado);
                    var parametros = {id:id,estado:estado};
                    var cambiarEstado = moduloAseguradora.cambiarEstado(parametros);
                    cambiarEstado.done(function (data){
                        $('#modalCambioEstado').modal('hide');
                        var respuesta = $.parseJSON(data);
                        if (respuesta.estado == 200) {
                            $("#mensaje_info_documentos").empty().html('<div id="success-alert" class="alert alert-' + respuesta.clase + '"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + respuesta.mensaje + '</div>');
                            $('html, body').animate({
                                scrollTop: $("#mensaje_info_documentos").offset().top
                            }, 500);
                        }else{
                            $("#mensaje_info_documentos").empty().html('<div id="success-alert" class="alert alert-' + respuesta.clase + '"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + respuesta.mensaje + '</div>');
                            $('html, body').animate({
                                scrollTop: $("#mensaje_info_documentos").offset().top
                            }, 500);
                        }
                        recargar();
                    });
                });
        });

    };

    var recargar = function (id_ramo) {
        //Reload Grid
        gridObj.setGridParam({
            url: tablaUrl,
            datatype: "json",
            postData: {
                numero: '',
                categoria: '',
                modulo: '',
                estado: '',
                id_ramo: id_ramo,
            }
        }).trigger('reloadGrid');

    };
    var redimencionar_tabla = function () {
        $(window).resizeEnd(function () {
            $(".ui-jqgrid").each(function () {
                var w = parseInt($(this).parent().width()) - 6;
                var tmpId = $(this).attr("id");
                var gId = tmpId.replace("gbox_", "");
                $("#" + gId).setGridWidth(w);
            });
        });
    };
    return{
        init: function () {
            tabla();
            eventos();

            //redimencionar_tabla();
        },
        recargar: function (id_ramo) {
            //Reload Grid
            gridObj.setGridParam({
                url: tablaUrl,
                datatype: "json",
                postData: {
                    numero: '',
                    categoria: '',
                    modulo: '',
                    estado: '',
                    id_ramo: id_ramo,
                }
            }).trigger('reloadGrid');
        }
    };

})();



$(function () {
    //$("#jqgh_tablaSolicitudesArticuloGrid_cb span").removeClass("s-ico");
    //$('#jqgh_tablaSolicitudesArticuloGrid_options span').removeClass("s-ico");
});

$(document).ready(function(){
    //();
    //tablaDocumentos.recargar();

    $("#guardar_documentos").click(function(){

        var id_ramo = $("#id_ramo_catalogo").val();
        var id_documento = $("#id_documento").val();
        var nombre = $("#nombre_documentacion").val();
        var categoria = $("#categoria_documentos").val();
        var modulo = $("#modulo_documentos").val();

        if(nombre == '' || id_ramo == ''){
            
            if(nombre == ''){
                $("#mensaje_info_documentos").empty().html('<div id="success-alert" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><b>¡&Error!</b> debe escribir un nombre</div>');
                $('html, body').animate({
                    scrollTop: $("#mensaje_info_documentos").offset().top
                }, 500);
            }else if(id_ramo == ''){
                $("#mensaje_info_documentos").empty().html('<div id="success-alert" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><b>¡&Error!</b> debe seleccionar un ramo</div>');
                $('html, body').animate({
                    scrollTop: $("#mensaje_info_documentos").offset().top
                }, 500);
            }

        }else if(id_documento == ''){
            var parametros = {id_ramo:id_ramo,nombre:nombre,categoria:categoria,modulo:modulo};
            var guardarDocumentos = moduloAseguradora.guardarDocumentos(parametros);
            guardarDocumentos.done(function (data) {
                var respuesta = $.parseJSON(data);
                if (respuesta.estado == 200) {
                    $("#mensaje_info_documentos").empty().html('<div id="success-alert" class="alert alert-' + respuesta.clase + '"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + respuesta.mensaje + '</div>');
                    $('html, body').animate({
                        scrollTop: $("#mensaje_info_documentos").offset().top
                    }, 500);
                    tablaDocumentos.recargar(id_ramo);
                    $("#nombre_documentacion").val('');
                    $("#categoria_documentos").val('Opcional');
                    $("#modulo_documentos").val('Cliente');
                }
            });
        }else{
            var parametros = {id_ramo:id_ramo,nombre:nombre,categoria:categoria,modulo:modulo,id:id_documento};
            var editarDocumeto = moduloAseguradora.editarDocumeto(parametros);
            editarDocumeto.done(function (data) {
                var respuesta = $.parseJSON(data);
                if (respuesta.estado == 200) {
                    $("#mensaje_info_documentos").empty().html('<div id="success-alert" class="alert alert-' + respuesta.clase + '"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + respuesta.mensaje + '</div>');
                    $('html, body').animate({
                        scrollTop: $("#mensaje_info_documentos").offset().top
                    }, 500);
                    tablaDocumentos.recargar(id_ramo);
                    $("#id_documento").val('');
                    $("#nombre_documentacion").val('');
                    $("#categoria_documentos").val('Opcional')
                    $("#modulo_documentos").val('Cliente')
                }else{
                    $("#mensaje_info_documentos").empty().html('<div id="success-alert" class="alert alert-' + respuesta.clase + '"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + respuesta.mensaje + '</div>');
                    $('html, body').animate({
                        scrollTop: $("#mensaje_info_documentos").offset().top
                    }, 500);
                    tablaDocumentos.recargar(id_ramo);
                }
            });

        }
    })

    $("#cancelar_documenos").click(function(){
        $("#nombre_documentacion").val('');
    })

});

$(document).ajaxStop(function(){
    tablaDocumentos.init();
    //tablaDocumentos.redimencionar_tabla();
})