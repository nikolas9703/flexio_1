$(function(){
	$("#agentesGrid").jqGrid({
	   	url: phost() + 'propiedades/ajax-listar-agentes',
	   	datatype: "json",
	   	colNames:[
			'',
			'Agente',
			'% Comisión',
			'Monto Alquiler', 
			'Monto Venta' 
		],
	   	colModel:[
			{name:'porcentaje', width:10,align:"center"},
			{
				name:'agente', 
				index:'agn.nombre', 
				width:30,
				 
			},
	   		{name:'comision', index:'ag.porcentaje', width: 30},
	   		{name:'Monto Alquiler', index:'ag.monto_alquiler', width: 30}, 
	   		{name:'Monto Venta', index:'ag.monto_venta', width: 30} 
 	   	],
		mtype: "POST",
	   	postData: {
	   		uuid_propiedad: uuid_propiedad,
	   		erptkn: tkn
	   	},
		height: "auto",
		autowidth: true,
		rowList: [10,20,30,50],
		rowNum: 4,
		page: 1,
		pager: "#pager_agente",
		footerrow: true, // set a footer row
		userDataOnFooter: true, 
		loadtext: '<p>Cargando Datos...',
		hoverrows: false,
	    viewrecords: true,
	    refresh: true,
	    gridview: true,
	    sortname: 'agn.nombre',
	    sortorder: "ASC",
	    beforeProcessing: function(data, status, xhr){
 			if( $.isEmptyObject(data.session) == false){
				window.location = phost() + "login?expired";
			}
	    },
	    loadBeforeSend: function () {//propiedadesGrid_cb
	    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
	        $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-ftable>tbody>tr>td").css("background-color", "#e6e7e8");
	    }, 
		loadComplete: function(data){
			
			 $("span.pie").peity("pie", {
		         fill: ['#1ab394', '#d7d7d7', '#ffffff']
		     });
			//check if isset data
			if( data['total'] == 0 ){
				$('#gbox_agentesGrid').hide();
				$('.NoRecordsAgente').empty().append('No se encontraron agentes.').css({"color":"#868686","padding":"30px 0 0"}).show();
			}
			else{
				$('.NoRecords').hide();
				$('#gbox_agentesGrid').show();
				
				//init tooltip
				$('.vg-tt').tooltip();
			}

 		},
  		 
		onSelectRow: function(id){
			$(this).find('tr#'+ id).removeClass('ui-state-highlight');
		},
		
	}); 
 
	//-------------------------
	// Redimensioanr Grid al cambiar tamaño de la ventanas.
	//-------------------------
	  
	//add class to headers
	  $("#agentesGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
	 
	//$("#agentesGrid").jqGrid('columnToggle'); 
 	  $(window).resizeEnd(function() {
			$(".ui-jqgrid").each(function(){
				var w = parseInt( $(this).parent().width()) - 6;
				var tmpId = $(this).attr("id");
				var gId = tmpId.replace("gbox_","");
				$("#"+gId).setGridWidth(w);
			});
		});
	 
	 

});
	 