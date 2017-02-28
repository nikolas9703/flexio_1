if(desde=="solicitudes" || desde == "poliza"){
    var counterCoverageArt = indCoverageArray.length,
     counterDedutibleArt = indCoverageArray.length;
    var tablaSolicitudesArticulo = (function () {

        var unico = $("#detalleunico").val();

        if(desde == "poliza"){
            var id_poliza = $("#idPoliza").val();
           
            var tablaUrl = phost() + 'polizas/ajax_listar_articulo';
        }else{
            var tablaUrl = phost() + 'intereses_asegurados/ajax_listar_articulo';
        }

        var gridId = "tablaSolicitudesArticulo";
        var gridObj = $("#tablaSolicitudesArticulo");
        var opcionesModal = $('#opcionesModalIntereses');
        var grid_obj = $("#tablaSolicitudesArticulo");

        var botones = {
            opciones: ".viewOptions",
            subir_archivo: ".subir_documento_solicitudes_intereses",
            ver_interes: ".linkCargaInfo"
        };

        var tabla = function () {
            gridObj.jqGrid({
                url: tablaUrl,
                mtype: "POST",
                datatype: "json",
                colNames: ['No. Interés', 'Nombre', 'Clase de equipo', 'Marca','Modelo','Año','Serie','Condición','Valor','Fecha inclusión', 'Fecha exclusión','Estado','',''],
                colModel: desde == "poliza" ? 
                [
                {name:'numero', index:'numero', width:30},
                {name:'nombre', index:'nombre', width:40},
                {name:'clase_equipo', index:'clase_equipo', width:40},
                {name:'marca', index:'marca', width: 40},
                {name:'modelo', index:'modelo', width:40},
                {name:'anio', index:'anio', width:40},
                {name:'numero_serie', index:'numero_serie', width: 40},
                {name:'id_condicion', index:'id_condicion', width:40},
                {name:'valor', index:'valor', width: 40},
                {name:'fecha_inclusion', index:'created_at', width: 40},
                {name:'fecha_exclusion', index:'created_at', width: 40},
                {name:'estado', index:'estado', width: 40},
                
                {name:'options', index:'options', width:50, sortable:false, resizable:false, hidedlg:true, align:"center"},
                {name:'link', index:'link', hidedlg:true, hidden: true}
                ]
                :
                [
                {name:'numero', index:'int_intereses_asegurados.numero', width:30},
                {name:'nombre', index:'int_articulo.nombre', width:40},
                {name:'clase_equipo', index:'int_articulo.clase_equipo', width:40},
                {name:'marca', index:'int_articulo.marca', width: 40},
                {name:'modelo', index:'int_articulo.modelo', width:40},
                {name:'anio', index:'int_articulo.anio', width:40},
                {name:'numero_serie', index:'int_articulo.numero_serie', width: 40},
                {name:'id_condicion', index:'int_articulo.id_condicion', width:40},
                {name:'valor', index:'int_articulo.valor', width: 40},
                {name:'fecha_inclusion', index:'int_articulo.created_at', width: 40},
                {name:'fecha_exclusion', index:'int_articulo.created_at', width: 40},
                {name:'estado', index:'int_intereses_asegurados.estado', width: 40},
                
                {name:'options', index:'options', width:50, sortable:false, resizable:false, hidedlg:true, align:"center"},
                {name:'link', index:'link', hidedlg:true, hidden: true}
                ],
                postData: {
                    detalle_unico: unico,
                    desde: vista,
                    erptkn: tkn,
                    id_poliza: id_poliza,
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
                sortname: desde == "poliza" ? "estado" : "int_intereses_asegurados.estado",
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

                 //sets the grid size initially
                 gridObj.jqGrid('setGridWidth', $('.tabladetalle_articulo').width()); 
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
            var rowINFO = $.extend({}, gridObj.getRowData(id));
            var options = rowINFO.link;
            //Init Modal
            var numero_interes = rowINFO.numero;
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
            $("#certificadodetalle_articulo, #sumaaseguradadetalle_articulo, #primadetalle_articulo, #deducibledetalle_articulo").val("");
            toastr.success('Registro eliminado');

            var unico = $("#detalleunico").val();
            var camposi = {campo:unico};
            var obtener = modIntereses.prima(camposi);
            obtener.done(function (resp) {
                formularioCrear.getPrimaAnual(resp);
            });
        }); 
    });
    
    $(opcionesModal).on("click", ".linkCargaInfoArticulo", function (e) {

        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();

        if(desde == "poliza"){

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
            $("#opcionesModalIntereses").modal("hide");
            
            var intgr = $(this).attr("data-int-gr");
            var unico = $("#detalleunico").val();
            var datos = {campo: {id_intereses: intgr, detalle_unico: unico}};

            setTimeout(function() {
                var obtener = modIntereses.obtenerDetalleAsociado(datos);
                obtener.done(function (response) {
                
                    $("#certificadodetalle_articulo").val(response.detalle_certificado);
                    $("#sumaaseguradadetalle_articulo").val(response.detalle_suma_asegurada);
                    $("#primadetalle_articulo").val(response.detalle_prima);
                    $("#deducibledetalle_articulo").val(response.detalle_deducible);
                    $("#opcionesModalIntereses").modal("hide");
                });
            }, 1000);

        }

    });
    $(opcionesModal).on("click", ".setIndividualCoverageArt", function (e) {

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
            var btnDismiss ='<button type="button" class="close" data-dismiss="modal">&times;</button>';      
            var pantalla = $('.individual');
            var modalContainer = $("#IndCoberturas");
            var botones_coberturas = $('.btnIndidualCoverage');
            $(opcionesModal).modal("hide");
            
            pantalla.css('display', 'block');
            botones_coberturas.css('display', 'block');
            modalContainer.find('.modal-header').empty().append(btnDismiss+"<h4 style='text-align:center'>Coberturas Interés: "+numeroArticulo+"</h4>");
            modalContainer.find('.modal-body').empty().append(pantalla);
            modalContainer.find('.modal-footer').empty().append(botones_coberturas);
            $(modalContainer).modal({
                backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
                show: false
            });
            modalContainer.modal("show");

            var wrapper = $("#indCoveragefields");
            var btnAdd  = $("#btnAddCoverage");
            $(btnAdd).click(function(e){
                e.preventDefault();
                var text = '<div class="resetModal" id="cobertura_'+counterCoverageArt+'"><div class="col-xs-12 col-sm-6 col-md-6 col-lg-5"> <input type="text" name="coverageName[]" class="form-control"></div>'+'<div class="col-xs-12 col-sm-6 col-md-6 col-lg-5"><div class="input-group"><span class="input-group-addon">$</span><input type="text" name="coverageValue[]" class="form-control moneda"  value=""></div></div>'+'<div class="col-xs-12 col-sm-3 col-md-3 col-lg-1 del_row"><button class="btn btn-default btn-block "><i class="fa fa-trash"></i></button></div></div>';
                $(wrapper).append(text);
                counterCoverageArt++;  
            });

    $(wrapper).on("click",".del_row", function(e){ //user click on remove text
        e.preventDefault();  
        counterCoverageArt--;      
        $('#cobertura_'+counterCoverageArt).remove();
    });

    var wrapperDeductibles = $("#indDeductiblefields");
    var btnAddDeductibles  = $("#btnAddDeductible");
    $(btnAddDeductibles).click(function(e){
        e.preventDefault();
        var text = '<div class="resetModal"  id="deductible_'+ counterDedutibleArt+'"><div class="col-xs-12 col-sm-6 col-md-6 col-lg-5"> <input type="text" name="deductibleName[]" class="form-control"></div>'+'<div class="col-xs-12 col-sm-6 col-md-6 col-lg-5"><div class="input-group"><span class="input-group-addon">$</span><input type="text" name="deductibleValue[]"  class="form-control moneda"  value=""></div></div>'+'<div class="col-xs-12 col-sm-3 col-md-3 col-lg-1 remove_deductible"><button class="btn btn-default btn-block "><i class="fa fa-trash"></i></button></div></div>';
        $(wrapperDeductibles).append(text);
         counterDedutibleArt++;     
    });

    $(wrapperDeductibles).on("click",".remove_deductible", function(e){ //user click on remove text
        e.preventDefault();  
         counterDedutibleArt--;      
        $('#deductible_'+ counterDedutibleArt).remove();
        
    });
    
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
          $(".resetModal").remove();
           if(data.coberturas.length){
            temporalArrayArt.coberturas = data.coberturas;
            for (var j = temporalArrayArt.coberturas.length - 1; j >= 0; j--) {
                var value =temporalArrayArt.coberturas[j];
                var text = '<div class="resetModal"  id="cobertura_'+counterCoverageArt+'"><div class="col-xs-12 col-sm-6 col-md-6 col-lg-5"> <input type="text" name="coverageName[]" value="'+value.nombre+'" class="form-control"></div>'+'<div class="col-xs-12 col-sm-6 col-md-6 col-lg-5"><div class="input-group"><span class="input-group-addon">$</span><input type="text"  class="form-control moneda" name="coverageValue[]" value="'+value.cobertura_monetario+'"></div></div>'+'<div class="col-xs-12 col-sm-3 col-md-3 col-lg-1 del_row"><button class="btn btn-default btn-block "><i class="fa fa-trash"></i></button></div></div>';
                $(wrapper).append(text);
                counterCoverageArt++;
            }
        }
        if(data.deducion.length){
            temporalArrayArt.deducion = data.deducion;
            
            for (var i = temporalArrayArt.deducion.length - 1; i >= 0; i--) {
                var value =temporalArrayArt.deducion[i];
                var text = '<div class="resetModal" id="deductible_'+ counterDedutibleArt+'"><div class="col-xs-12 col-sm-6 col-md-6 col-lg-5"> <input type="text" name="deductibleName[]" value="'+value.nombre+'" class="form-control"></div>'+'<div class="col-xs-12 col-sm-6 col-md-6 col-lg-5"><div class="input-group"><span class="input-group-addon">$</span><input type="text"  class="form-control moneda" name="deductibleValue[]"  value="'+value.deducible_monetario+'"></div></div>'+'<div class="col-xs-12 col-sm-3 col-md-3 col-lg-1 remove_deductible"><button class="btn btn-default btn-block "><i class="fa fa-trash"></i></button></div></div>';
                $(wrapperDeductibles).append(text);
                 counterDedutibleArt++;
            } 
        }
        
        
            
        
        
        
        
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
            $("#certificadodetalle_articulo").val(response.detalle_certificado);
            $("#sumaaseguradadetalle_articulo").val(response.detalle_suma_asegurada);
            $("#primadetalle_articulo").val(response.detalle_prima);
            $("#deducibledetalle_articulo").val(response.detalle_deducible);
        }); 

    });*/
	//Fin funciones para botones del grid de maritimo


    var recargar = function () {

        //Reload Grid
        gridObj.setGridParam({
            url: tablaUrl,
            datatype: "json",
            postData: {
                numero: '',
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
    tablaSolicitudesArticulo.init();
    $("#jqgh_tablaSolicitudesArticuloGrid_cb span").removeClass("s-ico");
    $('#jqgh_tablaSolicitudesArticuloGrid_options span').removeClass("s-ico");
});




}