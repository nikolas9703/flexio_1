//Modulo Tabla de Cargos
var tablaAreas = (function(){
 	var url = 'configuracion_rrhh/ajax_listar_area_negocio';
	var grid_id = "tablaAreaNegocioGrid";
	var grid_obj = $("#tablaAreaNegocioGrid");
	var opcionesModal = $('#opcionesModalAreaNeg');
	var formulario = $('#departamentoForm');

	var botones2 = {
		opciones: ".viewOptions2",
		editar: ".editarCargoBtn",
		duplicar: ".duplicarCargoBtn",
		desactivarAreaNeg: ".desactivarAreaNegBtn",
		activarAreaNeg: ".activarAreaNegBtn",
		buscar: "#searchBtn",
		limpiar: "#clearBtn"
	};

	var tabla = function()
	{
		//inicializar jqgrid
		grid_obj.jqGrid({
		   	url: phost() + url,
		   	datatype: "json",
		   	colNames:[
				'Nombre',
				'Estado',
				'Acci&oacute;n',
				'',
				'',

			],
		   	colModel:[
				{name:'Nombre', index:'nombre', width:70, sortable:false},
		   	{name:'Estado', index:'estado', width: 50, sortable:false, align:"center"},
				{name:'link', index:'link', width:50, sortable:false, resizable:false, hidedlg:true, align:"center"},
				{name:'options', index:'options', hidedlg:true, hidden: true},
				{name:'Idestado', index:'idestado', hidedlg:true, hidden: true},
		   	],
				mtype: "POST",
		   	postData: {
		   		erptkn: tkn
		   	},
			height: "auto",
			autowidth: true,
			rowList: [10, 20,50, 100],
			rowNum: 10,
			page: 1,
			pager: grid_id +"Pager",
			loadtext: '<p>Cargando...</p>',
			hoverrows: false,
		    viewrecords: true,
        loadonce: true,
		    refresh: true,
		    gridview: true,
		    multiselect: false,
		    sortname: 'nombre',
		    sortorder: "ASC",
		    treeGrid: false,
		    //treeGridModel: 'adjacency',
		    //treedatatype: "json",
		    //ExpandColumn: 'departamento',
		    beforeProcessing: function(data, status, xhr)
				{
		    	if(typeof data == 'undefined' || typeof data == '')
					{
						console.log('Vacio');
						return false;
					}

			    	//Check Session
					if( $.isEmptyObject(data.session) == false)
					{
						window.location = phost() + "login?expired";
					}
		    },
		    loadBeforeSend: function () {},
		    beforeRequest: function(data, status, xhr){},
			loadComplete: function(data){

				if(typeof data == 'undefined'){
					return false;
				}
				//check if isset data
				if( data['total'] == 0 ){
					$('#gbox_'+ grid_id).hide();
					$('#'+ grid_id +'NoRecords').empty().append('No se encontraron cargos.').css({"color":"#868686","padding":"30px 0 0"}).show();
				}
				else{
					$('#'+ grid_id +'NoRecords').hide();
					$('#gbox_'+ grid_id).show();
				}
			},
			onSelectRow: function(id){
				$(this).find('tr#'+ id).removeClass('ui-state-highlight');
			},
		});

		//Al redimensionar ventana
		$(window).resizeEnd(function() {
			tablaCargos.redimensionar();
		});
	};//Fin de tabla
//****************************************************************************************************************************************************
	//Inicializar Eventos de Botones
	var eventos = function(){

		//Bnoton de Opciones
		grid_obj.on("click", botones2.opciones, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			var id = $(this).attr("data-id");
			var rowINFO = grid_obj.getRowData(id);
			//console.log(rowINFO);
		    var options = rowINFO["options"];

	 	    //Init Modal
		    opcionesModal.find('.modal-title').empty().append('Opciones: '+ $(rowINFO["Departamento"]).text() +'');
		    opcionesModal.find('.modal-body').empty().append(options);
		    opcionesModal.find('.modal-footer').empty();
		    opcionesModal.modal('show');
		});
//*************************************************************************************************************************************************
		//Boton de Duplicar Cargo
		opcionesModal.on("click", botones2.editar, function(e)
		{
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			var cargo_id = $(this).attr("data-id");
      //alert(cargo_id);
			var rowINFO = grid_obj.getRowData(cargo_id);
	    var cargo = rowINFO["Nombre"];
	    var estado = rowINFO["Estado"];
			var Idestado = rowINFO["Idestado"];
			//alert(Idestado);
	    //var acumulados_lista 			= rowINFO["acumulados"] != undefined && rowINFO["acumulados"] != "" ? unserialize(rowINFO["acumulados"]) : "";
			formulario.find('#nombreAreaNeg').prop("value", cargo);
			setTimeout(function()
			{
								console.log(name);
						formulario.find('#opciones option[value="'+ Idestado +'"]').prop('selected', 'selected');
				}, 300);
			//formulario.find('#opciones').prop("value", estado);
	    //var selected_index = $('#crearCargoForm').find('#departamento_id').find('option[value="'+ departamento_id +'"]').eq();

		    scope.$apply(function(){
	    	scope.cargo.id = cargo_id;
	    	scope.cargo.departamento_id = departamento_id;
	    	scope.cargo.nombre = cargo;
		    });
		    setTimeout(function()
				{
          $('#departamento_id').trigger('chosen:updated')
        }, 300);
		    //Ocultar modal
			//opcionesModal.modal('hide');
      console.log('antes del desactivar');
      recargar();
		});
//, botones2.activarAreaNeg
//******************* MOMENTANEAMENTE
    opcionesModal.on("click", [botones2.desactivarAreaNeg, botones2.activarAreaNeg], function(e)
    {
      //console.log(formulario.serialize());
       e.preventDefault();
      e.returnValue=false;
      e.stopPropagation();
      var departamento_id = $(e.target).attr("data-id");  //**
      var estado_id = $(e.target).attr("data-estado") == 0 ? '0' : 1; //**
      //alert(estado_id);
      toggleCargo(
        {
  				departamento_id: departamento_id,
  				estado_id: estado_id
			   }).done(function(json)
         {

        //Check Session
        if( $.isEmptyObject(json.session) == false){
          window.location = phost() + "login?expired";
        }
        //Check Session
        if( $.isEmptyObject(json.session) == false){
          window.location = phost() + "login?expired";
        }
        console.log(json);
        if(json.status == true)
        {
             toastr.success(json.mensaje);
            //Limpia Area de negocio();
             recargar();
        }else
        {
          toastr.error(json.mensaje);
        }

      });
      opcionesModal.modal('hide');
      recargar();
  	});
//*************************************************************************************************************************************************
//********************************************************
var toggleCargo = function(parametros)
{
  //alert('toggleCargo funciont DESACTIVAR / ACTIVAR ');
  return $.ajax(
    {
      url: phost() + 'configuracion_rrhh/ajax-toggle-area-negocio',
      data: $.extend({erptkn: tkn}, parametros),
      type: "POST",
      dataType: "json",
      cache: false,
    });
};
//**************************************
		//Boton de Desactivar Cargo
		opcionesModal.on("click", [botones2.desactivar2, botones2.activar2], function(e)
		{

			//var rowINFO = grid_obj.getRowData(cargo_id);
			//console.log(rowINFO);
			//alert('En funcion OPCIONES MODAL');
		/*	e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			//var rowINFO = grid_obj.getRowData(cargo_id);
			var departamento_id = $(e.target).attr("data-id");
			//var estado = rowINFO["Estado"];
			var estado_id = $(e.target).attr("data-estado") == 0 ? '0' : 1;
			//alert('ID cargo: '+cargo_id);
			//console.log(rowINFO);
			//toggle cargo
			alert('Llega abntes');
			/*toggleCargo2(
			{
				cargo_id: departamento_id,
				estado_id: estado_id
			})*/ /*$.ajax(
				{
					url: phost() + 'configuracion_rrhh/ajax-toggle-area-negocio',
					data: $.extend({erptkn: tkn}),
					type: "POST",
					dataType: "json",
					cache: false,
				}).done(function(json){
				alert('algo2');
	            //Check Session
				if( $.isEmptyObject(json.session) == false){
					window.location = phost() + "login?expired";
				}

				//If json object is empty.
				if($.isEmptyObject(json) == true){
					return false;
				}

				//verificar si existe el id
				if(json.id == false && json.id == undefined){

					//mensaje error
					toastr.error(json.mensaje);
				}else{

					//mensaje success
					toastr.success(json.mensaje);

					//recargar jqgrid
					recargar();
				}
	        });

		    //Ocultar modal
			opcionesModal.modal('hide');*/
		});
	};
//******************************************************************************************************************************************************
	//Boton de Buscar Cargo
	$(botones2.buscar).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		buscarCargo();
	});
//********************************************************************************************************************************************************
	//Boton de Reiniciar jQgrid
	$(botones2.limpiar).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		recargar();
		limpiarCampos();
	});

	//Reload al jQgrid
	var recargar = function(){

		//Reload Grid
		grid_obj.setGridParam({
			url: phost() + url,
			datatype: "json",
			postData: {
				departamento: '',
				cargo: '',
				rata: '',
				codigo: '',
				erptkn: tkn
			}
		}).trigger('reloadGrid');

	};
//********************************************************************************************************************************************************
	//Buscar cargo en jQgrid
	var buscarCargo = function(){

		var departamento 	= $('#departamento').val();
		var cargo 			= $('#cargo').val();
		var rata 			= $('#rata_valor').val();
		var codigo 			= $('#codigo').val();

		if(cargo != "" /*|| departamento != "" ||  rata != "" || codigo != ""*/ )
		{
			//Reload Grid
			grid_obj.setGridParam({
				url: phost() + url,
				datatype: "json",
				postData: {
					departamento: departamento,
					cargo: cargo,
					rata: rata,
					codigo: codigo,
					erptkn: tkn
				}
			}).trigger('reloadGrid');
		}
	};
//******************************************************************************************************************************************************
	//Limpiar campos de busqueda
	var limpiarCampos = function(){
		$('#buscarCargoForm').find('input[type="text"]').prop("value", "");
	};
//*******************************************************************************************************************************************************
	//Funcion Ajax duplicar cargo
	var duplicarCargo = function(parametros){
		return $.ajax({
			url: phost() + 'configuracion_rrhh/ajax-duplicar-cargo',
			data: $.extend({erptkn: tkn}, parametros),
			type: "POST",
			dataType: "json",
			cache: false,
		});
	};
//*******************************************************************************************************************************************************
	//Funcion Ajax desactivar/activar cargo


	return{
		init: function() {
			tabla();
			eventos();
		},
		recargar: function(){
			//reload jqgrid
			recargar();
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

tablaAreas.init();
