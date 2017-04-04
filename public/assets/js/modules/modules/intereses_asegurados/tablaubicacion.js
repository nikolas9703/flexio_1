
if (desde=="solicitudes" || desde=="poliza" || desde == "endosos") {
      
    var tablaSolicitudesUbicacion = (function () {

    var unico = $("input[name='detalleunico']").val();
    if(desde == "poliza" || desde == "endosos"){
        var id_poliza = $("#idPoliza").val();
        console.log(id_poliza);
        var tablaUrl = phost() + 'polizas/ajax_listar_ubicacion';
    }else{
        var tablaUrl = phost() + 'intereses_asegurados/ajax_listar_ubicacion';
    }
    
    var gridId = "tablaSolicitudesUbicacion";
    var gridObj = $("#tablaSolicitudesUbicacion");
    var opcionesModal = $('#opcionesModalIntereses');
    var formularioBuscar = '';
    var documentosModal = $('#documentosModal');
    var grid_obj = $("#tablaSolicitudesUbicacion");



    var botones = {
        opciones: ".viewOptions",
        subir_archivo: ".subir_documento_solicitudes_intereses",
    };

    var tabla = function () {
        gridObj.jqGrid({
            url: tablaUrl,
            mtype: "POST",
            datatype: "json",
            colNames: ['No. Interés', 'Nombre', 'Dirección', 'Edificio','Contenido,Mercancía','Maquinaria','Inventario','Acreedor','Estado','',''],
            colModel: desde == "poliza" || desde == "endosos" ?
            [
                {name:'numero', index:'numero', width:30},
                {name:'nombre', index:'nombre', width:40},
                {name:'direccion', index:'direccion', width:40},
                {name:'edif_mejoras', index:'edif_mejoras', width: 40},
                {name:'contenido', index:'contenido', width:40},
                {name:'maquinaria', index:'maquinaria', width:40},
                {name:'inventario', index:'inventario', width: 40},
                {name:'acreedor', index:'acreedor', width:40},
                {name:'estado', index:'estado', width: 40},
                
                {name:'options', index:'options', width:50, sortable:false, resizable:false, hidedlg:true, align:"center"},
                {name:'link', index:'link', hidedlg:true, hidden: true}
            ]
            :
            [
                {name:'numero', index:'int_intereses_asegurados.numero', width:30},
                {name:'nombre', index:'int_ubicacion.nombre', width:40},
                {name:'direccion', index:'int_ubicacion.direccion', width:40},
                {name:'edif_mejoras', index:'int_ubicacion.edif_mejoras', width: 40},
                {name:'contenido', index:'int_ubicacion.contenido', width:40},
                {name:'maquinaria', index:'int_ubicacion.maquinaria', width:40},
                {name:'inventario', index:'int_ubicacion.inventario', width: 40},
                {name:'acreedor', index:'int_ubicacion.acreedor', width:40},
                {name:'estado', index:'int_intereses_asegurados.estado', width: 40},
                
                {name:'options', index:'options', width:50, sortable:false, resizable:false, hidedlg:true, align:"center"},
                {name:'link', index:'link', hidedlg:true, hidden: true}


            ],
            postData: {
                detalle_unico: unico,
                desde: vista,
                erptkn: tkn,
                id_poliza: desde == "endosos" ? id_poliza_endoso : id_poliza,
                renovar: window.vista=="renovar" ? 1 : 0
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
            sortname: desde ==  "poliza" || desde == "endosos" ? "estado": "int_intereses_asegurados.estado",
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
                   sendIndividualForm =false;
                } else {
                   sendIndividualForm =true;
                }
                var DataGrid = gridObj;

                 //sets the grid size initially
                 gridObj.jqGrid('setGridWidth', $('.tabladetalle_ubicacion').width());
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


    /*var recargar = function () {

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

    };*/
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
            $("#certificadodetalle_ubicacion, #sumaaseguradadetalle_ubicacion, #primadetalle_ubicacion, #deducibledetalle_ubicacion").val("");
            toastr.success('Registro eliminado');

            var unico = $("#detalleunico").val();
            var camposi = {campo:unico};
            var obtener = modIntereses.prima(camposi);
            obtener.done(function (resp) {
                formularioCrear.getPrimaAnual(resp);
            });
        }); 
    });

    $(opcionesModal).on("click", ".linkCargaInfoUbicacion", function (e) {
        
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();

        if(desde == "poliza" || desde == "endosos"){
            var selInteres = $(this).attr("data-int-id");
            $("#selInteres").val(selInteres);
            $("#selInteres").trigger('change'); 
            if(window.vista=="renovar"){
               selInteres = $(this).attr("data-interes-rev"); 
               var selI = $(this).attr("data-int-id");
               $("#idintertabla").val(selI); 
               formularioCrear.getInteres(selInteres);  
               setTimeout(function() {
                    $("#selInteres").trigger('change');
                }, 500);
            } else{
                $("#selInteres").trigger('change');
                formularioCrear.getInteres(selInteres);  

            }      
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
                    $("#certificadodetalle_ubicacion").val(response.detalle_certificado);
                    $("#sumaaseguradadetalle_ubicacion").val(response.detalle_suma_asegurada);
                    $("#primadetalle_ubicacion").val(response.detalle_prima);
                    $("#deducibledetalle_ubicacion").val(response.detalle_deducible);
                    $("#opcionesModalIntereses").modal("hide");
                }); 
            }, 1000);
        }        
        
    });

  $(opcionesModal).on("click", ".setIndividualCoverageUbc", function (e) {

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


  $(opcionesModal).on("click", ".setIndividualCoverageUbcPoliza", function (e) {

        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();
        var poliza = vista==="crear"?vista:(desde == "endosos" ? id_poliza : poliza_id);
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
                  poliza :poliza,
                  planId : $(planes).val(), 
                  erptkn: tkn
                },
                url: phost() + 'polizas/ajax_get_invidualCoverage',
                success: function(data){    
                    if ($.isEmptyObject(data.session) == false) {
                        window.location = phost() + "login?expired";
                    }else{  

                          var temporalArrayArt = [];
                          temporalArrayArt.coberturas=constructJSONArray("cobertura","valor_cobertura",getValuesFromArrayInput("coverageName"),getValuesFromArrayInput("coverageValue"));
                          temporalArrayArt.deducion  =constructJSONArray("deduccion","valor_deduccion",getValuesFromArrayInput("deductibleName"),getValuesFromArrayInput("deductibleValue"));    
                          $(".coverageIntereses").remove();
                          $(".deductibleIntereses").remove();
                          if(data.coberturas.length || data.deducion.length){
                                temporalArrayArt.coberturas = data.coberturas;
                                temporalArrayArt.deducion = data.deducion;
                            }    
                        populateStoredCovergeData('indCoveragefieldsIntereses','coverageIntereses','removecoverageIntereses',temporalArrayArt.coberturas,"cobertura","valor_cobertura");
                        populateStoredCovergeData('indDeductiblefieldsIntereses','deductibleIntereses','removeDeductibleIntereses',temporalArrayArt.deducion,"deduccion","valor_deduccion");

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
            $("#certificadodetalle_ubicacion").val(response.detalle_certificado);
            $("#sumaaseguradadetalle_ubicacion").val(response.detalle_suma_asegurada);
            $("#primadetalle_ubicacion").val(response.detalle_prima);
            $("#deducibledetalle_ubicacion").val(response.detalle_deducible);
        }); 

    });*/
	//Fin funciones para botones del grid de maritimo


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

$(document).ajaxStop (function() {  
    tablaSolicitudesUbicacion.init();
    $("#jqgh_tablaSolicitudesUbicacionGrid_cb span").removeClass("s-ico");
    $('#jqgh_tablaSolicitudesUbicacionGrid_options span').removeClass("s-ico");
});


}
