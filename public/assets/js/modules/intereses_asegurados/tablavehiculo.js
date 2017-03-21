if (desde=="solicitudes" || desde=="poliza" || desde == "endosos") {

var tablaSolicitudesVehiculo = (function () {

    var unico = $("#detalleunico").val();
    if(desde == "poliza" || desde == "endosos"){
        var id_poliza = $("#idPoliza").val();
        console.log(id_poliza);
        var tablaUrl = phost() + 'polizas/ajax_listar_vehiculo';
    }else{
        var tablaUrl = phost() + 'intereses_asegurados/ajax_listar_vehiculo';
    }
   
    var gridId = "tablaSolicitudesVehiculo";
    var gridObj = $("#tablaSolicitudesVehiculo");
    var opcionesModal = $('#opcionesModalIntereses');
    var grid_obj = $("#tablaSolicitudesVehiculo");



    var botones = {
        opciones: ".viewOptions",
        subir_archivo: ".subir_documento_solicitudes_intereses",
    };

    var tabla = function () {
        gridObj.jqGrid({
            url: tablaUrl,
            mtype: "POST",
            datatype: "json",
            colNames: ['No. Interés', 'No. Certificado', 'Motor', 'Unidad','Marca','Modelo','Placa','Color','Operador','Fecha de inclusión','Fecha de exclusión','Prima','Estado','',''],
            colModel: desde == "poliza" ?
            [
                {name:'numero', index:'numero', width:30},
                {name:'certificado', index:'detalle_certificado', width:30},
                {name:'chasis', index:'chasis', width:40},
                {name:'unidad', index:'unidad', width:40},
                {name:'marca', index:'marca', width: 40},
                {name:'modelo', index:'modelo', width:40},
                {name:'placa', index:'placa', width:40},
                {name:'color', index:'color', width: 40},
                {name:'operador', index:'operador', width:40},
                {name:'fecha_inclusion', index:'created_at', width:40},
                {name:'fecha_exclusion', index:'created_at', width:40},
                {name:'prima', index:'extras', width: 40},
                {name:'estado', index:'estado', width: 40},
                
                {name:'options', index:'options', width:50, sortable:false, resizable:false, hidedlg:true, align:"center"},
                {name:'link', index:'link', hidedlg:true, hidden: true}
            ]
            :
            [
                {name:'numero', index:'int_intereses_asegurados.numero', width:30},
                {name:'certificado', index:'int_intereses_asegurados_detalles.detalle_certificado', width:30},
                {name:'chasis', index:'int_vehiculo.chasis', width:40},
                {name:'unidad', index:'int_vehiculo.unidad', width:40},
                {name:'marca', index:'int_vehiculo.marca', width: 40},
                {name:'modelo', index:'int_vehiculo.modelo', width:40},
                {name:'placa', index:'int_vehiculo.placa', width:40},
                {name:'color', index:'int_vehiculo.color', width: 40},
                {name:'operador', index:'int_vehiculo.operador', width:40},
                {name:'fecha_inclusion', index:'int_vehiculo.created_at', width:40},
                {name:'fecha_exclusion', index:'int_vehiculo.created_at', width:40},
                {name:'prima', index:'int_vehiculo.extras', width: 40},
                {name:'estado', index:'int_intereses_asegurados.estado', width: 40},
                
                {name:'options', index:'options', width:50, sortable:false, resizable:false, hidedlg:true, align:"center"},
                {name:'link', index:'link', hidedlg:true, hidden: true}


            ],
            postData: {
                detalle_unico: unico,
                desde: vista,
                erptkn: tkn,
                id_poliza: id_poliza
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
            sortname: desde== "poliza" ? "estado" : "int_intereses_asegurados.estado",
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
                 gridObj.jqGrid('setGridWidth', $('.tabladetalle_vehiculo').width());
                //---------
                // Cargar plugin jquery Sticky Objects
                //----------
                //add class to headers
                gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeader");
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
            var numero_interes = rowINFO.numero;
            //Init Modal
            opcionesModal.find('.modal-title').empty().append('Opciones: ' + numero_interes + '');
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
            $("#certificadodetalle_vehiculo, #sumaaseguradadetalle_vehiculo, #primadetalle_vehiculo, #deducibledetalle_vehiculo").val("");
            toastr.success('Registro eliminado');

            var unico = $("#detalleunico").val();
            var camposi = {campo:unico};
            var obtener = modIntereses.prima(camposi);
            obtener.done(function (resp) {
                formularioCrear.getPrimaAnual(resp);
            });
        }); 
    });

    $(opcionesModal).on("click", ".linkCargaInfoVehiculo", function (e) {
        
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();

        if(desde == "poliza" || desde == "endosos"){
            var selInteres = $(this).attr("data-int-id");
            $("#selInteres").val(selInteres);
            $("#selInteres").trigger('change'); 
            formularioCrear.getInteres();       
            $("#opcionesModalIntereses").modal("hide");
        }else{
            var selInteres = $(this).attr("data-int-id");
            $("#selInteres2").val(selInteres);
            $("#selInteres").val(selInteres);
            $("#selInteres").trigger('change'); 
            formularioCrear.getIntereses();        
            
            var intgr = $(this).attr("data-int-gr");
            var unico = $("#detalleunico").val();
            var datos = {campo: {id_intereses: intgr, detalle_unico: unico}};

            setTimeout(function() {
                var obtener = modIntereses.obtenerDetalleAsociado(datos);
                obtener.done(function (response) {
                    console.log(response);
                    $("#certificadodetalle_vehiculo").val(response.detalle_certificado);
                    $("#sumaaseguradadetalle_vehiculo").val(response.detalle_suma_asegurada);
                    $("#primadetalle_vehiculo").val(response.detalle_prima);
                    $("#deducibledetalle_vehiculo").val(response.detalle_deducible);
                    $("#opcionesModalIntereses").modal("hide");
                }); 
            }, 1000);
        }
        
        
        
    });
	//Funciones para botones del grid de maritimo
	
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
            $("#certificadodetalle_vehiculo").val(response.detalle_certificado);
            $("#sumaaseguradadetalle_vehiculo").val(response.detalle_suma_asegurada);
            $("#primadetalle_vehiculo").val(response.detalle_prima);
            $("#deducibledetalle_vehiculo").val(response.detalle_deducible);
        }); 

    });*/
   $(opcionesModal).on("click", ".setIndividualCoverageVeh", function (e) {

        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();
        var solicitud = vista==="crear"?vista:solicitud_id;
        var planes = $("#planes");
        if($(planes).val()!==""){
            var id = $(this).attr("data-int-gr");
            var idFromTable = $(this).attr("data-id");
            var rowINFO = $.extend({}, gridObj.getRowData(idFromTable));
            var options = rowINFO.link;
            var numeroArticulo =rowINFO.numero;
            //Init Modal data-int-gr 
            $(opcionesModal).modal("hide");
            showIndividualCoverageModal(numeroArticulo);
            $.ajax({
                type: "POST",
                data: {
                  detalle_unico: unico,
                  id_interes :id,
                  solicitud :solicitud,
                  planId : $(planes).val(), 
                  erptkn: tkn
              },
              url: phost() + 'solicitudes/ajax_get_invidualCoverage',
              success: function(data)
              {    
                if ($.isEmptyObject(data.session) == false) {
                    window.location = phost() + "login?expired";
                }else{  

                  var temporalArrayArt = [];
                  temporalArrayArt.coberturas=constructJSONArray("nombre","cobertura_monetario",getValuesFromArrayInput("coberturasNombre"),getValuesFromArrayInput("coberturasValor"));
                  temporalArrayArt.deducion  =constructJSONArray("nombre","deducible_monetario",getValuesFromArrayInput("deduciblesNombre"),getValuesFromArrayInput("deduciblesValor"));    
                  $(".coverage").remove();
                  $(".deductible").remove();
                  if(data.coberturas.length || data.deducion.length){
                     temporalArrayArt.coberturas = data.coberturas;
                     temporalArrayArt.deducion = data.deducion;
                 }
                 populateStoredCovergeData('indCoveragefields','coverage','removecoverage',temporalArrayArt.coberturas,"nombre","cobertura_monetario");
                 populateStoredCovergeData('indDeductiblefields','deductible','removeDeductible',temporalArrayArt.deducion,"nombre","deducible_monetario");

                 $(".moneda").inputmask('currency',{
                  prefix: "",
                  autoUnmask : true,
                  removeMaskOnSubmit: true
              });  

             }
         }
     });  

            $("#saveIndividualCoveragebtn").click(function(){

              saveInvidualCoverage(id,numeroArticulo);  
          });  
        }else{
            $(this).text("Seleccione un plan");
        }
    });
	//Fin funciones para botones del grid de maritimo


    var recargar = function () {

        //Reload Grid
        gridObj.setGridParam({
            url: tablaUrl,
            datatype: "json",
            postData: {
                numero: '',
                certificado_detalle: '',
                chasis: '',
                placa: '',
                modelo: '',
                marca: '',
                unidad: '',
                color: '',
                operador: '',
                prima: '',
                fecha_inclusion: '',
                estado: '',
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
            //redimencionar_tabla();
        },
        recargar: function () {
            //Reload Grid
            gridObj.setGridParam({
                url: tablaUrl,
                datatype: "json",
                postData: {
                    numero: '',
                    certificado_detalle: '',
                    chasis: '',
                    placa: '',
                    modelo: '',
                    marca: '',
                    unidad: '',
                    color: '',
                    operador: '',
                    prima: '',
                    fecha_inclusion: '',
                    estado: '',
                    erptkn: tkn
                }
            }).trigger('reloadGrid');

        }
    };

})();

$(function () {
    tablaSolicitudesVehiculo.init();
    $("#jqgh_tablaSolicitudesVehiculoGrid_cb span").removeClass("s-ico");
    $('#jqgh_tablaSolicitudesVehiculoGrid_options span').removeClass("s-ico");
});



}