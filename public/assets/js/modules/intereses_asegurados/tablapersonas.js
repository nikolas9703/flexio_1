
if(desde=="solicitudes" || desde == "poliza"){
    var counterCoveragePerson = indCoverageArray.length,
    counterDedutiblePerson = indCoverageArray.length,
    defaultArray = indCoverageArray; 
    var tablaSolicitudesPersonas = (function () {

        var unico = $("#detalleunico").val();
        if(desde == "poliza"){
            var id_poliza = $("#idPoliza").val();
            tablaTipo = tablaTipo2;
            console.log(id_poliza);
            var tablaUrl = phost() + 'polizas/ajax_listar_personas';
        }else{
            var tablaUrl = phost() + 'intereses_asegurados/ajax_listar_personas';   
        }

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
                colNames: ['N° interés','N° Certificado', 'Nombre', 'Cédula', 'Fecha nacimiento','Edad','Nacionalidad','Sexo','Estatura','Peso','Telefono','Relación','Tipo relación','Participación','Fecha inclusión','Fecha exclusión', 'Prima','Estado','',''],
                colModel: desde == "poliza" ?
                [
                {name:'numero', index:'numero',width:45,sortable:true,sorttype:"text"},
                {name:'certificado', index:'detalle_int_asociado',width:45,sortable:true,sorttype:"text"},
                {name:'nombrePersona', index:'nombrePersona', width:60,sortable:true,sorttype:"text"},
                {name:'identificacion', index:'identificacion', width:45,sortable:true,sorttype:"text"},
                {name:'fecha_nacimiento', index:'fecha_nacimiento', width: 40,sortable:true,sorttype:"text"},
                {name:'edad', index:'edad', width:20,sortable:true,sorttype:"text"},
                {name:'nacionalidad', index:'fecha_nacimiento', width: 40,sortable:true,sorttype:"text",hidden:setting.nacionalidad},
                {name:'sexo', index:'sexo', width:20,sortable:true,sorttype:"text"},
                {name:'estatura', index:'estatura', width: 20,sortable:true,sorttype:"text",hidden:setting.estatura},
                {name:'peso', index:'peso', width:20,sortable:true,sorttype:"text",hidden:setting.peso},
                {name:'telefono', index:'telefono_residencial', width: 40,sortable:true,sorttype:"text"},
                {name:'relacion', index:'peso', width:45,sortable:true,sorttype:"text",hidden:setting.relacion},
                {name:'tipo_relacion', index:'int_intereses_asegurados_detalles.detalle_relacion', width:45,sortable:true,sorttype:"text",hidden:setting.tipo_relacion},
                {name:'participacion', index:'peso', width:20,sortable:true,sorttype:"text",hidden:setting.participacion},
                {name:'fecha_inclusion', index:'fecha_inclusion', width: 40,sortable:true,sorttype:"text"},
                {name:'fecha_exclusion', index:'fecha_exclusion', width: 40,sortable:true,sorttype:"text"},
                {name:'prima', index:'detalle_prima', width: 30,sortable:true,sorttype:"text"},
                {name:'estado', index:'estado', width: 40,sortable:true,sorttype:"text"},
                {name:'options', index:'options', width:65, sortable:false, resizable:false, hidedlg:true, align:"center"},
                {name:'link', index:'link', hidedlg:true, hidden: true}
                ]
                :
                [
                {name:'numero', index:'int_intereses_asegurados.numero',width:45,sortable:true,sorttype:"text"},
                {name:'certificado', index:'int_intereses_asegurados_detalles.detalle_int_asociado',width:45,sortable:true,sorttype:"text"},
                {name:'nombrePersona', index:'int_personas.nombrePersona', width:60,sortable:true,sorttype:"text"},
                {name:'identificacion', index:'int_personas.identificacion', width:45,sortable:true,sorttype:"text"},
                {name:'fecha_nacimiento', index:'int_personas.fecha_nacimiento', width: 40,sortable:true,sorttype:"text"},
                {name:'edad', index:'int_personas.fecha_nacimiento', width:20,sortable:true,sorttype:"text"},
                {name:'nacionalidad', index:'int_personas.fecha_nacimiento', width: 40,sortable:true,sorttype:"text",hidden:setting.nacionalidad},
                {name:'sexo', index:'int_personas.sexo', width:20,sortable:true,sorttype:"text"},
                {name:'estatura', index:'int_personas.estatura', width: 20,sortable:true,sorttype:"text",hidden:setting.estatura},
                {name:'peso', index:'int_personas.peso', width:20,sortable:true,sorttype:"text",hidden:setting.peso},
                {name:'telefono', index:'int_personas.telefono_residencial', width: 40,sortable:true,sorttype:"text"},
                {name:'relacion', index:'int_intereses_asegurados_detalles.detalle_relacion', width:45,sortable:true,sorttype:"text",hidden:setting.relacion},
                {name:'tipo_relacion', index:'int_intereses_asegurados_detalles.detalle_relacion', width:45,sortable:true,sorttype:"text",hidden:setting.tipo_relacion},
                {name:'participacion', index:'int_intereses_asegurados_detalles.detalle_participacion', width:20,sortable:true,sorttype:"text",hidden:setting.participacion},
                {name:'fecha_inclusion', index:'int_personas.fecha_inclusion', width: 40,sortable:true,sorttype:"text"},
                {name:'fecha_exclusion', index:'int_personas.fecha_exclusion', width: 40,sortable:true,sorttype:"text"},
                {name:'prima', index:'int_intereses_asegurados_detalles.detalle_prima', width: 30,sortable:true,sorttype:"text"},
                {name:'estado', index:'int_intereses_asegurados.estado', width: 40,sortable:true,sorttype:"text"},

                {name:'options', index:'options', width:65, sortable:false, resizable:false, hidedlg:true, align:"center"},
                {name:'link', index:'link', hidedlg:true, hidden: true}


                ],
                postData: {
                    detalle_unico: unico,
                    desde: vista,
                    erptkn: tkn,
                    id_poliza: id_poliza,
                    relacion: tablaTipo
                },
                //  caption: "Stack Overflow Adjacency Example",
                height: "auto",
                treeGridModel: 'adjacency',
                treedatatype:"json",
                ExpandColClick: true,
                treeIcons: {leaf: 'ui-icon-blank'},
                autowidth: true,
                treeGrid: setting.treeview,
                rowList: [10, 20, 50, 100],
                rowNum: 10,
                page: 1,
                pager: "#" + gridId + "Pager",
                loadtext: '<p>Cargando...</p>',
                hoverrows: false,
                viewrecords: true,
                refresh: true,
                gridview: true,
                sortname: desde == "poliza" ? "id" : "int_intereses_asegurados_detalles.id",
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
            beforeRequest: function (data, status, xhr) {

            },
            loadComplete: function (data, status, xhr) {

                if (gridObj.getGridParam('records') === 0) {
                    sendIdividualForm =false;
                    $("#relaciondetalle_persona option").each(function () {
                        ContVidaInd = 0;
                        if ($(this).val() == "Dependiente" || $(this).val() == "Beneficiario") {
                            $(this).prop("disabled", true);

                        }
                        else {
                            $(this).prop("disabled", false);

                        }
                    });
                    $('#gbox_' + gridId).hide();
                    $('#' + gridId + 'NoRecords').empty().append('No se han agregado intereses asegurados.').css({"color": "#868686", "padding": "30px 0 0"}).show();
                } else {
                    ContVidaInd = 1;
                    sendIdividualForm=true;
                    $('#gbox_' + gridId).show();
                    $('#' + gridId + 'NoRecords').empty();
                    $("#relaciondetalle_persona option").each(function () {

                        if ($(this).val() == "Principal" && id_tipo_poliza==1) {
                            $(this).prop("disabled", true);

                        }
                    });
                }

                //---------
                // Cargar plugin jquery Sticky Objects
                //----------
                //add class to headers
                //gridObj.jqGrid('setGridWidth', $('.tabladetalle_personas').width());
                /*gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeader");*/
                //floating headers
                
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
            var numero_interes = rowINFO.numero;
            var numberText = $(numero_interes).text();
            //Init Modal data-int-gr
            
            opcionesModal.find('.modal-title').empty().append('Opciones: ' +numberText+'');
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
            
            var intgr = $(this).attr("data-int-gr");
            var unico = $("#detalleunico").val();
            var datos = {campo: {id_intereses: intgr, detalle_unico: unico}};

            setTimeout(function() {
                var obtener = modIntereses.obtenerDetalleAsociado(datos);
                obtener.done(function (response) {


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
                //$("#certificadoPersona, #primadetalle_persona, #montodetalle_persona,#participacion_persona,#suma_asegurada_persona").val("");
                //

                
                //$(".relaciondetalle_persona").empty();
                $('#validar_editar').val(2);

                $('li').each(function(){
                    $(this).removeAttr("area-selected");
                    console.log($(this).attr("area-selected"));
                });
                $('.relaciondetalle_persona_vida_otros').val(response.detalle_relacion);
                $('.relaciondetalle_persona_vida').val(response.detalle_relacion);
                formularioCrear.getAsociado();

                if(response.detalle_relacion=="Principal"){
                   $('#asociadodetalle_persona').rules(
                     "add",{ required: false, 

                     });
                   $('#relaciondetalle_persona').rules(
                     "add",{ required: false, 

                     });
                   $('#participacion_persona').rules(
                     "add",{ required: false, 

                     });
                   $('#suma_asegurada_persona').rules(
                     "add",{ required: true, 

                     });
                   $('#tipo_relacion_persona').rules(
                     "add",{ required: false, 

                     });
                   if(tablaTipo=="vida"||tablaTipo=="accidentes"){
                      $('#primadetalle_persona').rules(
                         "add",{ required: true, 

                         });
                  }else{
                      $('#primadetalle_persona').rules(
                         "add",{ required: false, 

                         });
                  }

              }else{

                $("#asociadodetalle_persona").attr('disabled',false);
                $('#suma_asegurada_persona').rules(
                 "add",{ required: false, 

                 });
                $('#participacion_persona').rules(
                 "add",{ required: true, 

                 });

                if(jQuery.isEmptyObject(response.Principal)){
                    $('#asociadodetalle_persona').rules(
                     "add",{ required: true, 

                     });
                }else{
                 $('#asociadodetalle_persona').rules(
                     "add",{ required: false, 

                     }); 
             }
             $('#primadetalle_persona').rules(
                 "add",{ required: false, 

                 });
             $('#relaciondetalle_persona').rules(
                 "add",{ required: true, 

                 });
             if(tablaTipo=="salud"){
                $('#primadetalle_persona').rules(
                 "add",{ required: false, 

                 });
            }
            else{
             $('#primadetalle_persona').rules(
                 "add",{ required: false, 

                 });
         }

     }
     response.detalle_int_asociado!==0 ? $('#asociadodetalle_persona').val(response.Principal.interesestable_id).trigger("change"):$('#asociadodetalle_persona').val("").trigger("change");
     $("#certificadoPersona").val(response.detalle_certificado);
     $("#tipo_relacion_persona").val(response.tipo_relacion);

     $("#beneficiodetalle_persona").val(response.detalle_beneficio);
     $('#suma_asegurada_persona').val(response.detalle_suma_asegurada);
     $('#participacion_persona').val(response.detalle_participacion);
     if(response.detalle_relacion == "Principal"){
        $('#participacion_persona').attr('disabled',true);
    }
    $("#montodetalle_persona").val(response.detalle_monto);
    $("#primadetalle_persona").val(response.detalle_prima);
    $("#opcionesModalIntereses").modal("hide");
});
}, 1000);
}




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

    $(opcionesModal).on("click", ".setIndividualCoverage", function (e) {

        e.preventDefault();
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
            modalContainer.find('.modal-header').empty().append(btnDismiss+"<h4 style='text-align:center'>Coberturas Interés: "+$(numero_interes).text()+'-'+rowINFO.nombrePersona+"</h4>");
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
}
});

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
                relacion:tablaTipo,
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
            gridObj.jqGrid('setGridWidth', $('.tabladetalle_personas').width());
            //redimencionar_tabla();
        },
        recargar: function () {
            gridObj.setGridParam({
                url: tablaUrl,
                datatype: "json",
                postData: {
                    edad: "",
                    sexo :"",
                    estatura:"" ,
                    telefono_residencial: "",
                    created_at: "" ,
                    nombre:"" ,
                    estado:"",
                    prima:"",
                    numero:"",
                    relacion:tablaTipo,
                    nombrePersona:"",
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }
    };

})();

}

$( document ).ajaxStop(function() {

    if(desde=="solicitudes" || desde == "poliza"){
        tablaSolicitudesPersonas.init();

        $("#jqgh_tablaSolicitudesPersonasGrid_cb span").removeClass("s-ico");
        $('#jqgh_tablaSolicitudesPersonasGrid_options span').removeClass("s-ico");
    }
});