 $(function(){
	
     //verificar si la url actual es contactos
    //de lo contrario no mostrar multiselect del jqgrid
	//var relacionado_con_act= uuid_relacion_act  = '';
	var multiselect = window.location.pathname.match(/actividades/g) ? true : false;
    var esconder_col = window.location.pathname.match(/actividades/g) ? false : true;
      
    if(typeof relacionado_con_act === 'undefined'){ //Es x que no viene de Clientes o Actividades y debe estarblanco la variable
    	var relacionado_con_js = '';
    }
    else{
    	var relacionado_con_js = relacionado_con_act;
    }
    
    if(typeof uuid_relacion_act === 'undefined'){ //Es x que no viene de Clientes o Actividades y debe estarblanco la variable
    	var uuid_relacion_js = '';
    }
    else{
    	var uuid_relacion_js = uuid_relacion_act;
    }
    
    //console(typeof relacionado_con_act);
    if(typeof id_cliente === 'undefined'){
        id_cliente="";
    }
    else if(typeof uuid_cliente === 'undefined'){
        uuid_cliente=id_cliente;
    }
    
     //Esta variable viene directamente del controlador de Cliente
    if(!uuid_cliente){
        var uuid_cliente="";
    }
    if(!uuid_contacto){
        var uuid_contacto="";
    }
 	//console.log(uuid_relacion_act);
	//Init Usuarios Grid
	$("#actividadesGrid").jqGrid({
	   	url: phost() + 'actividades/ajax-listar-actividades',
	   	datatype: "json",
	   	colNames:[
			'Tipo',
			'TipoNombre',
			'Asunto',
			'Relacionado con',
			'Cliente',
			'Contacto',
			'Con',
			'Usuario',
			'Fecha Vencimiento',
			'Estado',
			'Acci&oacute;n',
			'',
			'Acci&oacute;n', //accion para mostrar en modulo contactos
		],
	   	colModel:[
 	        {name:'Tipo', index:'act.tipo', width:30, sortable:false},
			{name:'Tipo de Actividad',  hidden: true},
			{name:'Asunto', index:'act.asunto', width:30,  sortable:true},
			{name:'Relacionado con',  hidden: true},
			{name:'Cliente',  hidden: true},
			{name:'Contacto',  hidden: true},
	   		{name:'Con', index:'con', width: 150,   sortable:false},
	   		{name:'Usuario', index:'usr.id_usuario', width: 30,  sortable:true},
	   		{name:'Fecha Vencimiento', index:'act.fecha', width: 40,   sortable:true},
	   		{name:'Estado', index:'estado', width: 30,  sortable:false },
			{name:'link', index:'link', width:50, align:"center",sortable:false, resizable:false, hidedlg:true, hidden: esconder_col},
			{name:'options', index:'options', hidedlg:true, hidden: true},
			{name:'linkactividad', index:'linkactividad', width:50, align:"center", sortable:false, resizable:false, hidedlg:true, hidden: true},
	   	],
		mtype: "POST",
	   	postData: {
	   		erptkn: tkn,
            relacionado_con: relacionado_con_js,
            uuid_relacion: uuid_relacion_js,
            uuid_cliente: uuid_cliente,
            uuid_contacto: uuid_contacto
	   	},
        firstsortorder: 'desc',
		height: "auto",
		autowidth: true,
		rowList: [10, 20,50,100],
		rowNum: 10,
		page: 1,
		pager: "#pager_actividades",
		loadtext: '<p>Cargando...',
		hoverrows: false,
	    viewrecords: true,
	    refresh: true,
	    gridview: true,
	    multiselect: multiselect,
	    sortname: 'act.fecha',
	    sortorder: "ASC",
	    beforeProcessing: function(data, status, xhr){
	    	//Check Session
			if( $.isEmptyObject(data.session) == false){
				window.location = phost() + "login?expired";
			}
	    },
 
	    loadBeforeSend: function () {//propiedadesGrid_cb
	    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
	    	$(this).closest("div.ui-jqgrid-view").find("#actividadesGrid_cb, #jqgh_actividadesGrid_link").css("text-align", "center");
 		    //$(this).closest("div.ui-jqgrid-view").find("table.table table-striped ui-jqgrid-btable>tbody>tr>td").css("text-align", "right");
 
	    }, 
	    beforeRequest: function(data, status, xhr){},
            loadComplete: function(data){
			
                //check if isset data
                if( data['total'] == 0 ){
                        $('#gbox_actividadesGrid').hide();
                        $('.NoRecordsActividades').empty().append('No se encontraron casos.').css({"color":"#868686","padding":"30px 0 0"}).show();
                }
                else{
                        $('.NoRecordsActividades').hide();
                        $('#gbox_actividadesGrid').show();
                }

                //---------
                // Cargar plugin jquery Sticky Objects
                //----------
                //add class to headers
                $("#actividadesGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");

                
                 //Arreglar tamaño de TD de los checkboxes
                $("#actividadesGrid_cb").css("width","50px");
                $("#actividadesGrid tbody tr").children().first("td").css("width","50px");
                
                //Mostrar asunto al abrir el modal
                /*$(".viewOptions").on("click" ,function(){
                    var i=0;
                    var asunto = "";
                    $(this).parent().parent().find("td").each(function(){
                        if(i==2)
                        {
                            asunto = $(this).html();
                        }
                        i++;
                    });
                    $(".modal-title").html("Opciones: "+asunto);
                });*/
            },
            onSelectRow: function(id){
                    $(this).find('tr#'+ id).removeClass('ui-state-highlight');
            },
	});
	
	//-------------------------
	// Boton de opciones
	//-------------------------
	$("#actividadesGrid").on("click", ".viewOptions", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var id_actividad = $(this).attr("data-actividad");
		var rowINFO = $("#actividadesGrid").getRowData(id_actividad);
	    var options = rowINFO["options"];

	    //Init boton de opciones
		$('#optionsModal').find('.modal-title').empty().append('Opciones: '+ rowINFO['Asunto'] );
		$('#optionsModal').find('.modal-body').empty().append(options);
		$('#optionsModal').find('.modal-footer').empty();
		$('#optionsModal').modal('show');
	});
	
	
	$("#actividadesGrid").jqGrid('columnToggle');
	
	//-------------------------
	// Redimensioanr Grid al cambiar tamaño de la ventanas.
	//-------------------------
	$(window).resizeEnd(function() {
		$(".ui-jqgrid").each(function(){
			var w = parseInt( $(this).parent().width()) - 6;
			var tmpId = $(this).attr("id");
			var gId = tmpId.replace("gbox_","");
			$("#"+gId).setGridWidth(w);
		});
	});
	
	//-------------------------
	// Boton de opciones
	//-------------------------
	$("#actividadesGrid").on("click", ".viewOptions", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var id_actividad = $(this).attr("data-actividad");
		var rowINFO = $("#actividadesGrid").getRowData(id_actividad);
	    var options = rowINFO["options"];
 	    //Init boton de opciones
		$('#optionsModal').find('.modal-title').empty().append('Opciones: '+ rowINFO['Asunto']);
		$('#optionsModal').find('.modal-body').empty().append(options);
		$('#optionsModal').find('.modal-footer').empty();
		$('#optionsModal').modal('show');
	});
	
	//-------------------------
	// Botones de formulario de Busqueda
	//-------------------------
	$('#searchBtn').bind('click', searchBtnHlr);
 	
	$('#clearBtn').click(function(e){
		e.preventDefault();
		
		$("#actividadesGrid").setGridParam({
			url: phost() + 'actividades/ajax-listar-actividades',
			datatype: "json",
			postData: {
				nombre_contacto: '',
				cliente: '',
				telefono: '',
				email: '',
				fecha: '',
				estado: '',
				erptkn: tkn
			}
		}).trigger('reloadGrid');
		
		//Reset Fields
		$('#nombre_contacto, #cliente, #telefono, #email, #fecha, #estado').val('');
	});
});

function searchBtnHlr(e) {

	e.preventDefault();
	$('#searchBtn').unbind('click', searchBtnHlr);

	var nombre_contacto 	= $('#nombre_contacto').val();
	var cliente = $('#cliente').val();
	var telefono = $('#telefono').val();
	var email 	= $('#email').val();
	var fecha 	= $('#fecha').val();
	var estado		= $('#estado').val();

	if(nombre_contacto != "" || cliente != "" || telefono != "" || email != ""  || fecha != "" || estado != "")
	{
		$("#actividadesGrid").setGridParam({
			url: phost() + 'actividades/ajax-listar-actividades',
			datatype: "json",
			postData: {
                nombre_contacto: nombre_contacto,
                cliente: cliente,
                telefono: telefono,
                email: email,
                fecha: fecha,
                estado: estado,
				erptkn: tkn
			}
		}).trigger('reloadGrid');
		
		$('#searchBtn').bind('click', searchBtnHlr);
	}else{
		$('#searchBtn').bind('click', searchBtnHlr);
	}
}
