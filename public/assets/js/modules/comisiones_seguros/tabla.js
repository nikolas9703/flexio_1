/**
 * Created by angel on 01/03/16.
 */
 $(function() {
	var grid_obj = $('#comisionessegurosGrid');
	
    var multiselect = window.location.pathname.match(/remesas/g) ? true : false,
   opcionesModal = $('#opcionesModal'),

    tablaUrl = phost() + 'comisiones_seguros/ajax-listar',
    botones ={
        buscar: "#searchBtn",
        opciones :'.viewOptions',
        clear: "#clearBtn",
        modalState: "label.updateState",
		exportar: "#exportarBtn",
    };


    //Init Contactos Grid
    $("#comisionessegurosGrid").jqGrid({
        url: tablaUrl,
        datatype: "json",
        colNames:[
        '',
        'Nº Comisión',
        'Nº Recibo',
        'Aseguradora',
        'Monto comisión',
		'Monto pendiente',
        'Fecha recibo',
        'Estado',
        '',
        ''
        ],
        colModel:[
        {name:'id',index:'id', width: 0, align: "center", sortable: false, hidden: true},
        {name:'no_comision', index:'no_comision', width:10},
        {name:'no_recibo', index:'no_recibo', width:10 },
        {name:'aseguradora_id', index:'aseguradora_id', width:10 },
        {name:'monto_comision', index:'monto_comision', width: 10  },
		{name:'comision_pendiente', index:'comision_pendiente', width: 10  },
        {name:'fecha', index:'fecha', width: 10  },
        {name:'estado', index:'estado', width: 10 },
        {name:'link', index:'link', width:10, align:"center", sortable:false, resizable:false},
        {name:'options', index:'options', width: 10, align: "center", sortable: false, hidden: true},

        ],
        mtype: "POST",
        postData: {
            erptkn: tkn
        },
        height: "auto",
        autowidth: true,
        rowList: [10, 20,50,100],
        rowNum: 10,
        page: 1,
        pager: "#comisionessegurosGridPager",
        loadtext: '<p>Cargando Comisiones...',
        hoverrows: false,
        viewrecords: true,
        refresh: true,
        gridview: true,
        multiselect: true,
        sortname: 'fecha',
        sortorder: "DESC",
        beforeProcessing: function(data, status, xhr){

            //Check Session
            if( $.isEmptyObject(data.session) === false){
                window.location = phost() + "login?expired";
            }
        },
        loadBeforeSend: function () {
            $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
            $(this).closest("div.ui-jqgrid-view").find("#jqgh_comisionessegurosGrid_cb, #jqgh_comisionessegurosGrid_link").css("text-align", "center");
        },
        beforeRequest: function(data, status, xhr){
            $('.jqgrid-overlay').show();
            $('#load_comisionessegurosGrid').addClass('ui-corner-all');
        },
        loadComplete: function(data){

            $('.jqgrid-overlay').hide();

            //check if isset data
            if($("#comisionessegurosGrid").getGridParam('records') === 0 ){
                $('#gbox_comisionessegurosGrid').hide();
                $('#comisionessegurosGridNoRecords').empty().append('No se encontraron remesas.').css({"color":"#868686","padding":"30px 0 0"}).show();
            }
            else{
                $('#gbox_comisionessegurosGrid').show();
                $('#comisionessegurosGridNoRecords').empty();
            }
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
		    opcionesModal.find('.modal-title').empty().append('Opciones: Comisión '+ rowINFO["no_comision"] +'');
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
        var no_comision = $('#no_comision').val();
        var aseguradora = $('#aseguradora').val();
		var inicio_fecha = $('#inicio_fecha').val();
		var fin_fecha = $('#fin_fecha').val();
        var no_cobro = $("#no_cobro").val();
        var estado = $("#estado").val();

        if (no_comision !== "" || aseguradora !== "" || inicio_fecha!=="" || fin_fecha!=="" || no_cobro !== "" || estado !=="") {
            //Reload Grid
            grid_obj.setGridParam({
                url: tablaUrl,
                datatype: "json",
                postData: {
                    no_comision: no_comision,
                    nombre_aseguradora: aseguradora,
					inicio_fecha:inicio_fecha,
					fin_fecha:fin_fecha,
                    no_cobro: no_cobro,
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
        $('#no_comision').val("");
        $("#no_cobro").val("");
        $("#estado,#aseguradora").val("").trigger('chosen:updated');
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
				$('form#exportarComisiones').submit();
				$('body').trigger('click');
			}
		}
	});
		
	//Limpiar campos de busqueda
	var limpiarCampos = function(){
		$('#buscarComisionesForm').find('input[type="text"]').prop("value", "");
		$('#buscarComisionesForm').find('input[type="select"]').prop("value", "");
		$('#buscarComisionesForm').find('.chosen-select').val('').trigger('chosen:updated');
		$('#buscarComisionesForm').find('input[name="inicio_fecha"]').prop("value", "");
		$('#buscarComisionesForm').find('input[type="fin_fecha"]').prop("value", "");
	};
	

    var reloadGrid = function () {
       grid_obj.setGridParam({
        url: tablaUrl,
        datatype: "json",
        postData: {
			no_comision: '',
			nombre_aseguradora: '',
			inicio_fecha:'',
			fin_fecha:'',
			no_cobro: '',
			estado: '',
			erptkn: tkn
       }
   }).trigger('reloadGrid');

   };

});
