/**
 * Created by angel on 01/03/16.
 */
 $(function() {

    if(typeof id_cliente === 'undefined'){
        id_cliente="";
    }
	var grid_obj = $('#remesasentrantesGrid');
	
    var multiselect = window.location.pathname.match(/remesas/g) ? true : false,
   opcionesModal = $('#opcionesModal'),

    tablaUrl = phost() + 'remesas_entrantes/ajax-listar-remesas-entrantes',
    botones ={
        buscar: "#searchBtn",
        opciones :'.viewOptions',
        clear: "#clearBtn",
        modalState: "label.updateState",
		exportar: "#exportarBtn",
    };


    //Init Contactos Grid
    $("#remesasentrantesGrid").jqGrid({
        url: tablaUrl,
        datatype: "json",
        colNames:[
        '',
        'Nº de Remesa',
        'Pagos Remesados',
        'Aseguradora',
        'Monto',
        'Fecha',
        'Usuario',
        'Estado',
        '',
        ''
        ],
        colModel:[
        {name:'id',index:'id', width: 0, align: "center", sortable: false, hidden: true},
        {name:'no_remesa', index:'no_remesa', width:10},
        {name:'pagos_remesados', index:'pagos_remesados', width:10 },
        {name:'aseguradora_id', index:'aseguradora_id', width:10 },
        {name:'monto', index:'seg_remesas_entrantes.monto', width: 10  },
        {name:'fecha', index:'seg_remesas_entrantes.fecha', width: 10  },
        {name:'usuario_id', index:'usuario_id', width: 10 },
        {name:'estado', index:'estado', width: 10 },
        {name:'link', index:'link', width:10, align:"center", sortable:false, resizable:false},
        {name:'options', index:'options', width: 10, align: "center", sortable: false, hidden: true},

        ],
        mtype: "POST",
        postData: {
            erptkn: tkn,
            id_cliente: id_cliente
        },
        height: "auto",
        autowidth: true,
        rowList: [10, 20,50,100],
        rowNum: 10,
        page: 1,
        pager: "#remesasentrantesGridPager",
        loadtext: '<p>Cargando Remesas...',
        hoverrows: false,
        viewrecords: true,
        refresh: true,
        gridview: true,
        multiselect: true,
        sortname: 'fecha1',
        sortorder: "DESC",
        beforeProcessing: function(data, status, xhr){

            //Check Session
            if( $.isEmptyObject(data.session) === false){
                window.location = phost() + "login?expired";
            }
        },
        loadBeforeSend: function () {
            $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
            $(this).closest("div.ui-jqgrid-view").find("#jqgh_remesasentrantesGrid_cb, #jqgh_remesasentrantesGrid_link").css("text-align", "center");
        },
        beforeRequest: function(data, status, xhr){
            $('.jqgrid-overlay').show();
            $('#load_remesasentrantesGrid').addClass('ui-corner-all');
        },
        loadComplete: function(data){

            $('.jqgrid-overlay').hide();

            //check if isset data
            if($("#remesasentrantesGrid").getGridParam('records') === 0 ){
                $('#gbox_remesasentrantesGrid').hide();
                $('#remesasentrantesGridNoRecords').empty().append('No se encontraron remesas.').css({"color":"#868686","padding":"30px 0 0"}).show();
            }
            else{
                $('#gbox_remesasentrantesGrid').show();
                $('#remesasentrantesGridNoRecords').empty();
            }

            /*if(multiselect == true){
                //add class to headers
                $("#remesasentrantesGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");

                //floating headers
                $('#gridHeader').sticky({
                    getWidthFrom: '.ui-jqgrid-view',
                    className:'jqgridHeader'
                });
                //Arreglar tamaño de TD de los checkboxes
                $("#remesasentrantesGrid_cb").css("width","50px");
                $("#remesasentrantesGrid tbody tr").children().first("td").css("width","50px");
            }*/
        },
        onSelectRow: function(id){
            $(this).find('tr#'+ id).removeClass('ui-state-highlight');
        },
    });

    //Boton Opciones
		grid_obj.on("click", botones.opciones, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			var id = $(this).attr("data-id");
			var rowINFO = grid_obj.getRowData(id);  
			//alert(rowINFO);			
		    var option = rowINFO["options"];
		    //evento para boton collapse sub-menu Accion Personal
		    opcionesModal.on('click', 'a[href="#collapse'+ id +'"]', function(){
		    	opcionesModal.find('#collapse'+ id ).collapse();
		    });

	 	    //Init Modal
		    opcionesModal.find('.modal-title').empty().append('Opciones: Remesa Entrante '+ rowINFO["no_remesa"] +'');
		    opcionesModal.find('.modal-body').empty().append(option);
		    opcionesModal.find('.modal-footer').empty();
		    opcionesModal.modal('show');
		});

    //Resize grid, on window resize end
    $(window).resizeEnd(function() {
        $(".ui-jqgrid").each(function(){
            var w = parseInt( $(this).parent().width()) - 6;
            var tmpId = $(this).attr("id");
            var gId = tmpId.replace("gbox_","");
            $("#"+gId).setGridWidth(w);
        });
    });

    $("#iconGrid").on("click", ".viewOptionsGrid", function(e){

        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();
        //Init boton de opciones
        $('#optionsModal').find('.modal-title').empty().append('Opciones: '+ $(this).closest(".chat-element").find("input[type='checkbox']").data("nombre"));
    });

    $(botones.buscar).click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        var remesa = $('#no_remesa').val();
        var aseguradora = $('#aseguradora').val();
		var inicio_fecha = $('#inicio_fecha').val();
		var fin_fecha = $('#fin_fecha').val();
        var usuario = $("#usuario").val();
        var estado = $("#estado").val();

        if (remesa !== "" || aseguradora !== "" || inicio_fecha!=="" || fin_fecha!=="" || usuario !== "" || estado !=="") {
            //Reload Grid
            grid_obj.setGridParam({
                url: tablaUrl,
                datatype: "json",
                postData: {
                    no_remesa: remesa,
                    nombre_aseguradora: aseguradora,
					inicio_fecha:inicio_fecha,
					fin_fecha:fin_fecha,
                    usuario: usuario,
                    estado: estado,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }


    });
    $(botones.clear).click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        $('#no_remesa').val("");
        $("#no_recibo").val("");
        $("#estado,#aseguradora,#usuario").val("").trigger('chosen:updated');
         //Reload Grid
         reloadGrid();
		 limpiarCampos();
     });
	 
	 //Boton de Exportar remesas entrantes
	$(botones.exportar).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();                        
		if($('#tabla').is(':visible') == true){			
			//Exportar Seleccionados del jQgrid
			var ids = [];
			ids = grid_obj.jqGrid('getGridParam','selarrrow');
			
			//Verificar si hay seleccionados
			if(ids.length > 0){
				//console.log(ids);	
				$('#ids').val(ids);
				$('form#exportarRemesasEntrantes').submit();
				$('body').trigger('click');
			}
		}
	});
		
	//Limpiar campos de busqueda
	var limpiarCampos = function(){
		$('#buscarRemesaEntranteForm').find('input[type="text"]').prop("value", "");
		$('#buscarRemesaEntranteForm').find('input[type="select"]').prop("value", "");
		$('#buscarRemesaEntranteForm').find('.chosen-select').val('').trigger('chosen:updated');
		$('#buscarRemesaEntranteForm').find('input[name="inicio_fecha"]').prop("value", "");
		$('#buscarRemesaEntranteForm').find('input[type="fin_fecha"]').prop("value", "");
	};
	

    var reloadGrid = function () {
       grid_obj.setGridParam({
        url: tablaUrl,
        datatype: "json",
        postData: {
			no_remesa: '',
			nombre_aseguradora: '',
			inicio_fecha:'',
			fin_fecha:'',
			usuario: '',
			estado: '',
			erptkn: tkn
       }
   }).trigger('reloadGrid');

   };

});
