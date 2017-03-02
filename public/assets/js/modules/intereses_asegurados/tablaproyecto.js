if(desde=="solicitudes" || desde=="poliza"){

    var counterCoveragePerson = 1,
    counterDedutiblePerson = 1;
    var tablaSolicitudesProyecto = (function () {

      var unico = $("#detalleunico").val();
      if(desde == "poliza"){
        var id_poliza = $("#idPoliza").val();
        console.log(id_poliza);
        var tablaUrl = phost() + 'polizas/ajax_listar_proyecto';
    }else{
        var tablaUrl = phost() + 'intereses_asegurados/ajax_listar_proyecto';  
    }

    var gridId = "tablaSolicitudesProyecto";
    var gridObj = $("#tablaSolicitudesProyecto");
    var opcionesModal = $('#opcionesModalIntereses');
    var grid_obj = $("#tablaSolicitudesProyecto");
    var documentosModal = $('#documentosModal');


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
        colNames: ['No. interés', 'Nombre del proyecto', 'No. Orden', 'Ubicación','Fecha de inclusión','Fecha de exclusión', 'Estado','',''],
        colModel: desde == "poliza" ?
        [
        {name:'numero', index:'numero', width:30},
        {name:'nombre_proyecto', index:'nombre_proyecto', width:40},
        {name:'no_orden', index:'no_orden', width:40},
        {name:'ubicacion', index:'ubicacion', width: 40},
        {name:'fecha_inclusion', index:'created_at', width: 40},
        {name:'fecha_exclusion', index:'created_at', width: 40},
        {name:'estado', index:'estado', width: 40},
        {name:'options', index:'options', width:50, sortable:false, resizable:false, hidedlg:true, align:"center"},
        {name:'link', index:'link', hidedlg:true, hidden: true}
        ]
        :
        [
        {name:'numero', index:'int_intereses_asegurados.numero', width:30},
        {name:'nombre_proyecto', index:'int_proyecto_actividad.nombre_proyecto', width:40},
        {name:'no_orden', index:'int_proyecto_actividad.no_orden', width:40},
        {name:'ubicacion', index:'int_proyecto_actividad.ubicacion', width: 40},
        {name:'fecha_inclusion', index:'int_proyecto_actividad.created_at', width: 40},
        {name:'fecha_exclusion', index:'int_proyecto_actividad.created_at', width: 40},
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
       sortname: desde == "poliza" ? "estado": "int_intereses_asegurados.estado",
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
                //---------
                // Cargar plugin jquery Sticky Objects
                //----------
                //add class to headers

                gridObj.jqGrid('setGridWidth', $('.tabladetalle_proyecto').width());

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
            tablaSolicitudesProyecto.redimencionar_tabla();
        });
    };



    var eventos = function () {
        //Bnoton de Opciones
        
        gridObj.on("click", botones.opciones, function (e) {
        	e.preventDefault();
        	e.returnValue = false;
        	e.stopPropagation();
        	var id = $(this).attr("data-id");
            //console.log(id);
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
            //console.log(scope);
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
        	//console.log("intgr="+intgr);            
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
    		$("#certificadodetalle_proyecto, #sumaaseguradadetalle_proyecto, #primadetalle_proyecto, #deducibledetalle_proyecto").val("");
    		toastr.success('Registro eliminado');
    	}); 
    });
	//Funciones para botones del grid de maritimo
	
	$(opcionesModal).on("click", ".linkCargaInfoProyecto", function(e){

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
            formularioCrear.getInteres(); 


            var intgr = $(this).attr("data-int-gr");
            var unico = $("#detalleunico").val();
            var datos = {campo: {id_intereses: intgr, detalle_unico: unico}};
            console.log(datos);
            setTimeout(function() {
                var obtener = modIntereses.obtenerDetalleAsociado(datos);
                console.log("aqui5");
                obtener.done(function (response) {
                   console.log(response);
                   $("#certificadodetalle_proyecto").val(response.detalle_certificado);
                   $("#sumaaseguradadetalle_proyecto").val(response.detalle_suma_asegurada);
                   $("#primadetalle_proyecto").val(response.detalle_prima);
                   $("#deducibledetalle_proyecto").val(response.detalle_deducible);
                   $("#opcionesModalIntereses").modal("hide");
               }); 
                obtener.fail(function (response) {
                    toastr.error("Errorrrr");
                });
            }, 1000);
        }





});

  $(opcionesModal).on("click", ".setIndividualCoverage", function (e) {

           /* e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();
            var planes = $("#planes");
            if($(planes).val()!==""){
                var id = $(this).attr("data-int-gr");
                var rowINFO = $.extend({}, gridObj.getRowData(id));
                var options = rowINFO.link;
                var numero_interes = rowINFO.numero;
                var numberText = $(numero_interes).text();
            //Init Modal data-int-gr 
            var btnDismiss ='<button type="button" class="close" data-dismiss="modal">&times;</button>';      
            var pantalla = $('.individual');
            var modalContainer = $("#IndCoberturas");
            var botones_coberturas = $('.btnIndidualCoverage');
            $(opcionesModal).modal("hide");
            
            pantalla.css('display', 'block');
            botones_coberturas.css('display', 'block');
            modalContainer.find('.modal-header').empty().append(btnDismiss+"<h4 style='text-align:center'>Coberturas Interés: "+$(numero_interes).text()+"</h4>");
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
                var text = '<div class="resetModal" id="cobertura_'+counterCoveragePerson+'"><div class="col-xs-12 col-sm-6 col-md-6 col-lg-5"> <input type="text" name="coverageName[]" class="form-control"></div>'+'<div class="col-xs-12 col-sm-6 col-md-6 col-lg-5"><div class="input-group"><span class="input-group-addon">$</span><input type="text" name="coverageValue[]" class="form-control moneda"  value=""></div></div>'+'<div class="col-xs-12 col-sm-3 col-md-3 col-lg-1 del_row"><button class="btn btn-default btn-block "><i class="fa fa-trash"></i></button></div></div>';
                $(wrapper).append(text);
                counterCoveragePerson++;  
            });

    $(wrapper).on("click",".del_row", function(e){ //user click on remove text
        e.preventDefault();  
        counterCoveragePerson--;      
        $('#cobertura_'+counterCoveragePerson).remove();
    });

    var wrapperDeductibles = $("#indDeductiblefields");
    var btnAddDeductibles  = $("#btnAddDeductible");
    $(btnAddDeductibles).click(function(e){
        e.preventDefault();
        var text = '<div class="resetModal"  id="deductible_'+counterDedutiblePerson+'"><div class="col-xs-12 col-sm-6 col-md-6 col-lg-5"> <input type="text" name="deductibleName[]" class="form-control"></div>'+'<div class="col-xs-12 col-sm-6 col-md-6 col-lg-5"><div class="input-group"><span class="input-group-addon">$</span><input type="text" name="deductibleValue[]"  class="form-control moneda"  value=""></div></div>'+'<div class="col-xs-12 col-sm-3 col-md-3 col-lg-1 remove_deductible"><button class="btn btn-default btn-block "><i class="fa fa-trash"></i></button></div></div>';
        $(wrapperDeductibles).append(text);
        counterDedutiblePerson++;     
    });

    $(wrapperDeductibles).on("click",".remove_deductible", function(e){ //user click on remove text
        e.preventDefault();  
        counterDedutiblePerson--;      
        $('#deductible_'+counterDedutiblePerson).remove();
        
    });
    
    $.ajax({
        type: "POST",
        data: {
          detalle_unico: unico,
          id_interes :id,
          erptkn: tkn
      },
      url: phost() + 'solicitudes/ajax_get_invidualCoverage',
      success: function(data)
      {    
        if ($.isEmptyObject(data.session) == false) {
            window.location = phost() + "login?expired";
        }else{

           var temporalArray = [];
           temporalArray.coberturas =indCoverageArray.coberturas;
           temporalArray.deducion   =indCoverageArray.deducion; 
           if(data.coberturas.length){
            temporalArray.coberturas = data.coberturas;
        }
        if(data.deducion.length){
            temporalArray.deducion = data.deducion;
        }
        $(".resetModal").remove();
        if(temporalArray.coberturas.length){
            for (var j = temporalArray.coberturas.length - 1; j >= 0; j--) {
                var value =temporalArray.coberturas[j];
                var text = '<div class="resetModal"  id="cobertura_'+counterCoveragePerson+'"><div class="col-xs-12 col-sm-6 col-md-6 col-lg-5"> <input type="text" name="coverageName[]" value="'+value.nombre+'" class="form-control"></div>'+'<div class="col-xs-12 col-sm-6 col-md-6 col-lg-5"><div class="input-group"><span class="input-group-addon">$</span><input type="text"  class="form-control moneda" name="coverageValue[]" value="'+value.cobertura_monetario+'"></div></div>'+'<div class="col-xs-12 col-sm-3 col-md-3 col-lg-1 del_row"><button class="btn btn-default btn-block "><i class="fa fa-trash"></i></button></div></div>';
                $(wrapper).append(text);
                counterCoveragePerson++;
            }
        }
        if(temporalArray.deducion.length){
            for (var i = temporalArray.deducion.length - 1; i >= 0; i--) {
                var value =temporalArray.deducion[i];
                var text = '<div class="resetModal" id="deductible_'+counterDedutiblePerson+'"><div class="col-xs-12 col-sm-6 col-md-6 col-lg-5"> <input type="text" name="deductibleName[]" value="'+value.nombre+'" class="form-control"></div>'+'<div class="col-xs-12 col-sm-6 col-md-6 col-lg-5"><div class="input-group"><span class="input-group-addon">$</span><input type="text"  class="form-control moneda" name="deductibleValue[]"  value="'+value.deducible_monetario+'"></div></div>'+'<div class="col-xs-12 col-sm-3 col-md-3 col-lg-1 remove_deductible"><button class="btn btn-default btn-block "><i class="fa fa-trash"></i></button></div></div>';
                $(wrapperDeductibles).append(text);
                counterDedutiblePerson++;
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

      saveInvidualCoverage(id,numberText);  
  });  
}else{
    $(this).text("Seleccione un plan");
}*/
});   
	//Fin funciones para botones del grid de maritimo


	var recargar = function () {

        //Reload Grid
        gridObj.setGridParam({
        	url: tablaUrl,
        	datatype: "json",
        	postData: {
        		numero: '',
        		nombre_proyecto: '',
        		no_orden: '',
        		ubicacion: '',
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
             //gridObj.jqGrid('setGridWidth', "auto");
            //redimencionar_tabla();
        },
        recargar: function () {
            //Reload Grid
            gridObj.setGridParam({
            	url: tablaUrl,
            	datatype: "json",
            	postData: {
            		numero: '',
            		nombre_proyecto: '',
            		no_orden: '',
            		ubicacion: '',
            		fecha_inclusion: '',
            		estado: '',
            		erptkn: tkn
            	}
            }).trigger('reloadGrid');

        }
    };

})();

$(function () {
	tablaSolicitudesProyecto.init();
	$("#jqgh_tablaSolicitudesProyectoGrid_cb span").removeClass("s-ico");
	$('#jqgh_tablaSolicitudesProyectoGrid_options span').removeClass("s-ico");
});


}