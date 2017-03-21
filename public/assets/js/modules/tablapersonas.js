if(desde=="solicitudes"){

    var tablaSolicitudesPersonas = (function () {

        var unico = $("#detalleunico").val();

        var tablaUrl = phost() + 'intereses_asegurados/ajax_listar_personas';
        var gridId = "tablaSolicitudesPersonas";
        var gridObj = $("#tablaSolicitudesPersonas");
        var opcionesModal = $('#opcionesModalIntereses');
        var formularioBuscar = '';
        var documentosModal = $('#documentosModal');
        var grid_obj = $("#tablaSolicitudesPersonas");



        var botones = {
            opciones: ".viewOptions",
            subir_archivo: ".subir_documento_solicitudes_intereses",
        };

        var tabla = function () {
            gridObj.jqGrid({
                url: tablaUrl,
                mtype: "POST",
                datatype: "json",
                colNames: ['N° interés','N° Certificado', 'Nombre', 'Cédula', 'Fecha nacimiento','Edad','Sexo','Estatura','Peso','Telefono','Fecha inclusión','Fecha exclusión', 'Prima','Estado','',''],
                colModel: [
                {name:'numero', index:'int_intereses_asegurados.numero',width:45,sortable:true,sorttype:"text"},
                {name:'certificado', index:'int_intereses_asegurados_detalles.detalle_int_asociado',width:45,sortable:true,sorttype:"text"},
                {name:'nombrePersona', index:'int_personas.nombrePersona', width:60,sortable:true,sorttype:"text"},
                {name:'identificacion', index:'int_personas.identificacion', width:45,sortable:true,sorttype:"text"},
                {name:'fecha_nacimiento', index:'int_personas.fecha_nacimiento', width: 40,sortable:true,sorttype:"text"},
                {name:'edad', index:'int_personas.edad', width:20,sortable:true,sorttype:"text"},
                {name:'sexo', index:'int_personas.sexo', width:20,sortable:true,sorttype:"text"},
                {name:'estatura', index:'int_personas.estatura', width: 20,sortable:true,sorttype:"text"},
                {name:'peso', index:'int_personas.peso', width:20,sortable:true,sorttype:"text"},
                {name:'telefono', index:'int_personas.telefono_residencial', width: 40,sortable:true,sorttype:"text"},
                {name:'fecha_inclusion', index:'int_personas.created_at', width: 40,sortable:true,sorttype:"text"},
                {name:'fecha_exclusion', index:'int_personas.created_at', width: 40,sortable:true,sorttype:"text"},
                {name:'prima', index:'int_intereses_asegurados_detalles.detalle_prima', width: 30,sortable:true,sorttype:"text"},
                {name:'estado', index:'int_intereses_asegurados.estado', width: 40,sortable:true,sorttype:"text"},
                
                {name:'options', index:'options', width:65, sortable:false, resizable:false, hidedlg:true, align:"center"},
                {name:'link', index:'link', hidedlg:true, hidden: true}


                ],
                postData: {
                    detalle_unico: unico,
                    desde: vista,
                    erptkn: tkn
                },
                //  caption: "Stack Overflow Adjacency Example",
                height: "auto",
                treeGridModel: 'adjacency',
                treedatatype:"json",
                ExpandColumn: 'numero',
                treeIcons: {leaf: 'ui-icon-blank'},
                autowidth: true,
                treeGrid: true,
                rowList: [10, 20, 50, 100],
                rowNum: 10,
                page: 1,
                pager: "#" + gridId + "Pager",
                loadtext: '<p>Cargando...</p>',
                hoverrows: false,
                viewrecords: true,
                refresh: true,
                gridview: true,
                sortname: "int_intereses_asegurados_detalles.id",
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

                if (gridObj.getGridParam('records') === 0) {
                    $('#gbox_' + gridId).hide();
                    $('#' + gridId + 'NoRecords').empty().append('No se han agregado intereses asegurados.').css({"color": "#868686", "padding": "30px 0 0"}).show();
                } else {
                    $('#gbox_' + gridId).show();
                    $('#' + gridId + 'NoRecords').empty();
                }

                //---------
                // Cargar plugin jquery Sticky Objects
                //----------
                //add class to headers
                gridObj.jqGrid('setGridWidth', $('.tabladetalle_personas').width());
                gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeader");
                //floating headers
                $('#gridHeader').sticky({
                    getWidthFrom: '.ui-jqgrid-view',
                    className: 'jqgridHeader'
                });
                var DataGrid = gridObj;

                 //sets the grid size initially
                 DataGrid.jqGrid('setGridWidth', parseInt(grid_obj) - 20);
                //Arreglar tamaño de TD de los checkboxes
                //FALTA ADAPTAR EL CODIGO PARA QUE LOS CHECKBOX SE VEAN BIEN
                $('#jqgh_' + gridId + "_cb").css("text-align", "center");
                $('.s-ico').removeAttr('style');
            },
            onSelectRow: function (id) {
                $(this).find('tr#' + id).removeClass('ui-state-highlight');
            }
        });
        //Al redimensionar ventana
        $(window).resizeEnd(function () {
            tablaSolicitudesVehiculo.redimencionar_tabla();
        });
    };



    var eventos = function () {
        //Bnoton de Opciones
        
        gridObj.on("click", botones.opciones, function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            var id = $(this).attr("data-id");
            console.log(id);
            var rowINFO = $.extend({}, gridObj.getRowData(id));
            var options = rowINFO.link;
            console.log(rowINFO.nombrePersona);
            var numero_interes = rowINFO.numero;
            //Init Modal
            opcionesModal.find('.modal-title').empty().append('Opciones: ' +numero_interes  +'');
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
        });

        //Documentos Modal
        $(opcionesModal).on("click", botones.subir_archivo, function (e){
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            var id_interes = $(this).attr("data-int-id");
            var tipo_interes = $(this).attr("data-tipo-interes");
            //Inicializar opciones del Modal
            documentosModal.modal({
                    backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
                    show: false
                });
            $('#opcionesModalIntereses').modal('hide');
            documentosModal.modal('show');
            var scope = angular.element('[ng-controller="subirDocumentosController"]').scope();
            console.log(scope);
            scope.safeApply(function () {
                scope.campos.id = id_interes;
                scope.campos.intereses_type = tipo_interes;
            });
            documentosModal.modal('show');
        });

        gridObj.on("click", botones.quitar_interes, function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            var intgr = $(this).attr("data-int-gr");
            console.log("intgr="+intgr);            
        });

    };

    
    //Boton de Cambiar estado InteresesAsegurados
    $(opcionesModal).on("click", ".quitarInteres", function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        var intgr = $(this).attr("data-int-gr");
        var unico = $("#detalleunico").val();
        var datos = {campo: {id_intereses: intgr, detalle_unico: unico}};
        var quitar = modIntereses.quitarDetalleAsociado(datos);
        quitar.done(function (response) {
            recargar();
            $("#selInteres").val("");
            $("#selInteres").trigger('change'); 
            $("#opcionesModalIntereses").modal("hide");
            $("#certificadodetalle_personas, #sumaaseguradadetalle_personas, #primadetalle_personas, #deducibledetalle_personas").val("");
            toastr.success('Registro eliminado');

            var unico = $("#detalleunico").val();
            var camposi = {campo:unico};
            var obtener = modIntereses.prima(camposi);
            obtener.done(function (resp) {
                formularioCrear.getPrimaAnual(resp);
            });
        }); 
    });

    $(opcionesModal).on("click", ".linkCargaInfoPersona", function (e) {

        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();

        var selInteres = $(this).attr("data-int-id");
        $("#selInteres").val(selInteres);
        $("#selInteres").trigger('change'); 
        formularioCrear.getIntereses();        
        
        var intgr = $(this).attr("data-int-gr");
        var unico = $("#detalleunico").val();
        var datos = {campo: {id_intereses: intgr, detalle_unico: unico}};
        var obtener = modIntereses.obtenerDetalleAsociado(datos);
        obtener.done(function (response) {
            console.log(response);
            //detalle_relacion
            /*
detalle_beneficio
detalle_certificado:"1111"
detalle_deducible:""
detalle_int_asociado:0
detalle_monto:"11.00"
detalle_prima:"11.00"
detalle_relacion:"Principal"
detalle_suma_asegurada:""
detalle_unico:"1484325512"
fecha_exclusion:null
fecha_inclusion:null
id:453
id_intereses:149
id_solicitudes:null */
$('#relaciondetalle_persona').val(response.detalle_relacion);
response.detalle_int_asociado!==0 ? $('#asociadodetalle_persona').val(response.detalle_int_asociado).trigger("change"):'';
$("#certificadoPersona").val(response.detalle_certificado);
$("#beneficiodetalle_persona").val(response.detalle_beneficio);
$("#montodetalle_persona").val(response.detalle_monto);
$("#primadetalle_persona").val(response.detalle_prima);
$("#opcionesModalIntereses").modal("hide");
}); 
    });

    /*$("#"+gridId).on("click", ".linkCargaInfo", function(e){
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();
        
        var selInteres = $(this).attr("data-int-id");
        $("#selInteres").val(selInteres);
        $("#selInteres").trigger('change'); 
        formularioCrear.getIntereses();

        var intgr = $(this).attr("data-int-gr");
        var unico = $("#detalleunico").val();
        var datos = {campo: {id_intereses: intgr, detalle_unico: unico}};
        var obtener = modIntereses.obtenerDetalleAsociado(datos);
        obtener.done(function (response) {
            console.log(response);
            console.log(response.detalle_certificado);
            $("#certificadodetalle_personas").val(response.detalle_certificado);
            $("#sumaaseguradadetalle_personas").val(response.detalle_suma_asegurada);
            $("#primadetalle_personas").val(response.detalle_prima);
            $("#deducibledetalle_personas").val(response.detalle_deducible);
        }); 

    });*/



    var recargar = function () {

        //Reload Grid
        gridObj.setGridParam({
            url: tablaUrl,
            datatype: "json",
            postData: {
                edad: "" ,
                sexo :"",
                estatura:"" ,
                telefono_residencial: "" ,
                created_at: "" ,
                nombre:"" ,
                estado:"",
                prima:"",
                numero:"",
                nombrePersona:"",
                erptkn: tkn
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
            gridObj.jqGrid('setGridWidth', 900);
            //redimencionar_tabla();
        },
        recargar: function () {
            gridObj.setGridParam({
                url: tablaUrl,
                datatype: "json",
                postData: {
                    edad: "" ,
                    sexo :"",
                    estatura:"" ,
                    telefono_residencial: "" ,
                    created_at: "" ,
                    nombre:"" ,
                    estado:"",
                    prima:"",
                    numero:"",
                    nombrePersona:"",
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }
    };

})();

$(function () {
    tablaSolicitudesPersonas.init();
    $("#jqgh_tablaSolicitudesPersonasGrid_cb span").removeClass("s-ico");
    $('#jqgh_tablaSolicitudesPersonasGrid_options span').removeClass("s-ico");
});



}