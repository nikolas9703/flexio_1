/**
 * Created by angel on 01/03/16.
 */
 $(function() {
	var grid_obj = $('#honorariossegurosGrid');
	
   // var multiselect = window.location.pathname.match(/honorarios/g) ? true : false,
   opcionesModal = $('#opcionesModal'),

    tablaUrl = phost() + 'honorarios_seguros/ajax-listar-tabla',
    botones ={
        buscar: "#searchBtn",
        opciones :'.viewOptions',
        clear: "#clearBtn",
        modalState: "label.updateState",
		exportar: "#exportarBtn",
    };


    //Init Contactos Grid
    $("#honorariossegurosGrid").jqGrid({
        url: tablaUrl,
        datatype: "json",
        colNames:[
        '',
        'Nº Honorario',
        'Comisiones pagadas',
        'Agente',
        'Monto',
		'Fecha',
        'Usuario',
        'Estado',
        '',
        ''
        ],
        colModel:[
        {name:'id',index:'id', width: 0, align: "center", sortable: false, hidden: true},
        {name:'no_honorario', index:'no_honorario', width:10},
        {name:'comisiones_pagadas', index:'comisiones_pagadas', width:10 },
        {name:'agente_id', index:'agente_id', width:10 },
        {name:'monto_total', index:'monto_total', width: 10  },
		{name:'fecha', index:'fecha', width: 10  },
        {name:'usuario_id', index:'usuario_id', width: 10  },
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
        pager: "#honorariossegurosGridPager",
        loadtext: '<p>Cargando Honorarios...',
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
            $(this).closest("div.ui-jqgrid-view").find("#jqgh_honorariossegurosGrid_cb, #jqgh_honorariossegurosGrid_link").css("text-align", "center");
        },
        beforeRequest: function(data, status, xhr){
            $('.jqgrid-overlay').show();
            $('#load_honorariossegurosGrid').addClass('ui-corner-all');
        },
        loadComplete: function(data){

            $('.jqgrid-overlay').hide();

            //check if isset data
            if($("#honorariossegurosGrid").getGridParam('records') === 0 ){
                $('#gbox_honorariossegurosGrid').hide();
                $('#honorariossegurosGridNoRecords').empty().append('No se encontraron honorarios.').css({"color":"#868686","padding":"30px 0 0"}).show();
            }
            else{
                $('#gbox_honorariossegurosGrid').show();
                $('#honorariossegurosGridNoRecords').empty();
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
		    opcionesModal.find('.modal-title').empty().append('Opciones: Honorario '+ rowINFO["no_honorario"] +'');
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
        var no_honorario = $('#no_honorario').val();
        var agente = $('#agente').val();
		var inicio_fecha = $('#inicio_fecha').val();
		var fin_fecha = $('#fin_fecha').val();
        var usuario = $("#usuario").val();
        var estado = $("#estado").val();
		
		console.log(usuario);

        if (no_honorario !== "" || agente !== "" || inicio_fecha!=="" || fin_fecha!=="" || usuario !== "" || estado !=="") {
            //Reload Grid
            grid_obj.setGridParam({
                url: tablaUrl,
                datatype: "json",
                postData: {
                    no_honorario: no_honorario,
                    agente: agente,
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
        $('#no_honorario').val("");
		$("#inicio_fecha").val("");
		$("#fin_fecha").val("");
        $("#estado,#agente,#usuario").val("").trigger('chosen:updated');
         //Reload Grid
         reloadGrid();
		 limpiarCampos();
     });
	 
	 //Boton de Exportar honorarios
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
				$('form#exportarHonorarios').submit();
				$('body').trigger('click');
			}
		}
	});
		
	//Limpiar campos de busqueda
	var limpiarCampos = function(){
		$('#buscarHonorariosForm').find('input[type="text"]').prop("value", "");
		$('#buscarHonorariosForm').find('input[type="select"]').prop("value", "");
		$('#buscarHonorariosForm').find('.chosen-select').val('').trigger('chosen:updated');
		$('#buscarHonorariosForm').find('input[name="inicio_fecha"]').prop("value", "");
		$('#buscarHonorariosForm').find('input[type="fin_fecha"]').prop("value", "");
	};
	

    var reloadGrid = function () {
       grid_obj.setGridParam({
        url: tablaUrl,
        datatype: "json",
        postData: {
			no_honorario: '',
			agente: '',
			inicio_fecha:'',
			fin_fecha:'',
			usuario: '',
			estado: '',
			erptkn: tkn
       }
   }).trigger('reloadGrid');

   };

});
