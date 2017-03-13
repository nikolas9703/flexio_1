 $(function(){
 	//Al reditablaComentariosmensionar ventana
	$(window).resizeEnd(function() {
		tablaDeducciones.redimensionar();
	});
});
 //Modulo Tabla de Cargos
var tablaDeducciones = (function(){

	var url = 'planilla/ajax_listar_deducciones_liquidacion';
 	var grid_id = "tablaDeduccionesLiquidacionGrid";
	var grid_obj = $("#tablaDeduccionesLiquidacionGrid");
	var opcionesModal = $('#opcionesModal');
  	
	var botones = {
			opciones: ".viewOptions" 
    	};
 
	var lastsel;
	var tabla = function(){
 		grid_obj.jqGrid({
		   	url: phost() + url,
		   	datatype: "json",
		   	colNames:[
 		   	   'Deducci&oacute;n',
   	           'Descuento',
   	           'Tipo de Pago'
    			],
		   	colModel:[
		   	    {name:'deduccion', index:'deduccion'},
 		   	    {name:'descuento', index:'descuento',sortable:false,formatter:"currency", summaryType:'sum',  formatoptions: {prefix:'$', thousandsSeparator:','}},
		   	    {name:'Tipo', index:'tipo'},
  		   	],
			mtype: "POST",
		   	postData: {
		   		planilla_id: planilla_id,
		   		colaborador_id: colaborador_id,
   		   		erptkn: tkn
		   	},
			height: "auto",
			autowidth: true,
			rowList: [10, 20,50, 100],
			rowNum: 50,
			page: 1,
 			loadtext: '<p>Cargando...</p>',
			hoverrows: false,
		    viewrecords: true,
		    refresh: true,
		    gridview: true,
		    grouping:true,
		   	groupingView : {
 		   		
		   		groupField : ['Tipo'],
		   		groupColumnShow : [false],
		   		groupSummary : [true],
		   		groupText : ['<b>{0}</b>']
		   		
		   	},
		    multiselect: false,
		    sortname: 'id',
		    sortorder: "ASC",
		    beforeProcessing: function(data, status, xhr){
		    	//Check Session
				if( $.isEmptyObject(data.session) == false){
					window.location = phost() + "login?expired";
				}
		    },
		    loadBeforeSend: function () {//propiedadesGrid_cb
		    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
		    	$(this).closest("div.ui-jqgrid-view").find('#'+ grid_id+'_cb, #jqgh_'+grid_id+"_link").css("text-align", "center");
 		    }, 
 		    
		    beforeRequest: function(data, status, xhr){},
			loadComplete: function(data){
				
				//check if isset data
				if( data['total'] == 0 ){
					$('#gbox_'+ grid_id).hide();
					$('#'+ grid_id +'NoRecords').empty().append('No se encontraron deducciones.').css({"color":"#868686","padding":"30px 0 0"}).show();
				}
				else{
					$('#'+ grid_id +'NoRecords').hide();
					$('#gbox_'+ grid_id).show();
				}
 				$('#'+ grid_id +'Pager_right').empty();
			},
			
			 onSelectRow: function(id){
				 	var parameter = {erptkn: tkn};
					if(id && id!==lastsel){
						grid_obj.jqGrid('restoreRow',lastsel);
						grid_obj.jqGrid('editRow', id, true, false,  false , false, parameter);
						lastsel=id;
					}
    		},
		});
	};
 
	return{	    
		init: function() {
			tabla();
		},
 		redimensionar: function(){
			//Al redimensionar ventana
			$(".ui-jqgrid").each(function(){
				var w = parseInt( $(this).parent().width()) - 6;
				var tmpId = $(this).attr("id");
				var gId = tmpId.replace("gbox_","");
				$("#"+gId).setGridWidth(w);
			});
		}
	};
})();

tablaDeducciones.init();



