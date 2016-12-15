var tablaPoliticas = (function(){
    
    var tablaUrl = phost() + 'politicas/ajax-listar';
    var gridId = "tablaPoliticasTransaccionesGrid";
    var gridObj = $("#" + gridId);
    var opcionesModal = $('#optionsModal');
    var formularioCrear = $('#formularioPoliticas');
    //var documentosModal = $('#documentosModal');
    
    var botones = {
        opciones: ".viewOptions",
        limpiar: "#clearBtn",
        editar: ".editarPolitica"
    };

    var tabla = function(){
        gridObj.jqGrid({
            url: tablaUrl,
            mtype: "POST",
            datatype: "json",
            colNames:['','Empresa','Nombre','Rol(es)','Categor&iacute;a(a)','M&oacute;dulo','Transacci&oacute;n(es)','Monto l&iacute;mite','Estado','', ''],
            colModel:[
                {name:'uuid', index:'uuid', width:30,  hidedlg:true, hidden: true},
                {name:'empresa', index:'empresa', width:55, sortable:true},
                {name:'nombre', index:'codigo', width:55, sortable:true},
                {name:'roles', index:'cliente_id', width:50, sortable:true},
                {name:'categorias', index:'centro_facturacion_id', width: 50,  sortable:false},
                {name:'modulo', index:'fecha_inicio', width:50,  sortable:false, },
                {name:'transacciones', index:'saldo_facturado', width:50,  sortable:false, },
                {name:'monto_limite', index:'total_facturado', width: 50,  sortable:false},
                {name:'estado_id', index:'estado_id', width:50,  sortable:false, },
                {name:'options', index:'options',width: 40},
                {name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false,hidden: true, hidedlg:true},
            ],
            postData: {
                empresa_id: empresa_id_pol,
                erptkn: tkn
            },
            height: "auto",
            autowidth: true,
            rowList: [10,20,50,100],
            rowNum: 10,
            page: 1,
            pager: gridId+"Pager",
            loadtext: '<p>Cargando...',
            hoverrows: false,
            viewrecords: true,
            refresh: true,
            gridview: true,
            multiselect: true,
            sortname: 'id',
            sortorder: "DESC",
            beforeProcessing: function(data, status, xhr){
                if( $.isEmptyObject(data.session) === false){
                    window.location = phost() + "login?expired";
                }
            },
            loadBeforeSend: function () {//propiedadesGrid_cb
                $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                $(this).closest("div.ui-jqgrid-view").find("#tablaPoliticasTransaccionesGrid_cb, #jqgh_tablaPoliticasTransaccionesGrid_link").css("text-align", "center");
            },
            loadComplete: function(data, status, xhr){

                if(gridObj.getGridParam('records') === 0 ){
                    $('#gbox_'+gridId).hide();
                    $('#'+gridId+'NoRecords').empty().append('No se encontraron Politicas de transacciones.').css({"color":"#868686","padding":"30px 0 0"}).show();
                }
                else{
                    $('#gbox_'+gridId).show();
                    $('#'+gridId+'NoRecords').empty();
                }

            //---------
            // Cargar plugin jquery Sticky Objects
            //----------
            //add class to headers
           // gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
            //floating headers
            $('#gridHeader').sticky({
                getWidthFrom: '.ui-jqgrid-view',
                className:'jqgridHeader'
            });
            },
            onSelectRow: function(id){
                $(this).find('tr#'+ id).removeClass('ui-state-highlight');
            }
        });
    };

    var eventos = function(){
        //Bnoton de Opciones
        gridObj.on("click", botones.opciones, function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();

            var id = $(this).attr("data-id");
             var rowINFO = $.extend({}, gridObj.getRowData(id));
             var options = rowINFO.link;
            //Init Modal
            opcionesModal.find('.modal-title').empty().append('Opciones: '+ rowINFO.nombre +'');
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
        });

        //boton limpiaar
        $(botones.limpiar).click(function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();
           $(formularioCrear).find('#guardarBtn').prop('disabled',false); 
           $(formularioCrear).trigger("reset");
           formularioCrear.trigger("reset");
            $(".select2").select2({
                theme: "bootstrap",
                width: "100%"
            });
            
 
        });
   
        
 	 $(opcionesModal).on("click", botones.editar, function(e){
                    
 				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
		//	("html, body").animate({ scrollTop: 0 }, 600);
                
                $(formularioCrear).find('.ibox-content:not(:visible)').prev().find('a').trigger('click');
                
 			var id = $(this).attr("data-id");
 
 			$.ajax({
					url: phost() + 'politicas/ajax-get-politica',
					data: {
                                            id: id,
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
                                        $(formularioCrear).trigger("reset");
					$(formularioCrear).find('#id').val(json.id);
					                   $(formularioCrear).find('#nombre').val(json.nombre);
                                       form_politicas.formulario.nombre = json.nombre;
                                      
                                       $(formularioCrear).find('#modulo_id').val(json.modulo);
                                        
                                        $(formularioCrear).find('#monto_limite').val(json.monto_limite);
                                        $(formularioCrear).find('#estado_id').val(json.estado_id);
                                         
                                       

                                        var categoria = json.categorias;
                                        var categoria_id = _.map(categoria,'id');
                                         $(".select2").select2({
                                            theme: "bootstrap",
                                            width: "100%"
                                        }); 
                                        Vue.nextTick(function(){
                                            form_politicas.formulario.role_id = json.role_id;
                                            form_politicas.formulario.modulo = json.modulo;
                                            form_politicas.formulario.politica_estado = json.politica_estado;
                                            form_politicas.formulario.politica_estado = json.politica_estado;
                                            form_politicas.formulario.categorias = categoria_id;
                                            form_politicas.formulario.monto_limite = json.monto_limite;
                                            form_politicas.formulario.estado_id = json.estado_id;
                                        });
					
  				});
	
 		   	    $(opcionesModal).modal('hide');
			
			 
		});
    };
    var recargar = function(){
        //Reload Grid
        gridObj.setGridParam({
            url: tablaUrl,
            datatype: "json",
            postData: {
                empresa_id: empresa_id_pol,
                erptkn: tkn
            }
        }).trigger('reloadGrid');
    };
    var redimencionar_tabla = function(){
        $(window).resizeEnd(function() {
            $(".ui-jqgrid").each(function(){
                var w = parseInt( $(this).parent().width()) - 6;
                var tmpId = $(this).attr("id");
                var gId = tmpId.replace("gbox_","");
                $("#"+gId).setGridWidth(w);
            });
        });
    };

    return{
        init:function(){
            tabla();
            eventos();
            redimencionar_tabla();
        }
    };

})();

$(function(){
    tablaPoliticas.init();
});