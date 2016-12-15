$(document).ready(function(){
  $(function(){
    var grid = $("#EmpresaGrid");
    grid.jqGrid({
    url: phost() + 'usuarios/ajax-listar-empresas',
    datatype: "json",
    colNames: ['','Nombre','Fecha de Creación','Cantidad de Usuarios','Opciones',''],
    colModel: [
                {name:'id', index:'id', hidedlg:true,key: true, hidden: true},
               {name:'nombre', index:'nombre',sorttype:"text",sortable:true,width:150},
               {name:'fecha_creacion',index:'fecha_creacion', sortable:false},
               {name:'total_usuario', index:'total_usuario',formatter: 'integer',sorttype:"int", sortable:false},
               {name:'opciones', index:'opciones', sortable:false, align:'center'},
               {name:'link', index:'link', hidedlg:true, hidden: true}
             ],
    mtype: "POST",
    postData: { erptkn:tkn},
    gridview: true,
    sortorder: "asc",
    hiddengrid: true,
    hoverrows: false,
    height: 'auto',
    page: 1,
    pager : "#pager_empresa",
    rowNum:10,
    autowidth: true,
    rowList:[10,20,30],
    sortname: 'nombre',
    viewrecords: true,
    beforeRequest: function(data, status, xhr){},
		loadComplete: function(data){

			//check if isset data
			if(data.total == 0 ){
				$('#gbox_EmpresaGrid').hide();
				$('.NoRecords').empty().append('No se encontraron Empresas.').css({"color":"#868686","padding":"30px 0 0"}).show();
			}
			else{
				$('.NoRecords').hide();
				$('#gbox_EmpresaGrid').show();
			}

			//---------
			// Cargar plugin jquery Sticky Objects
			//----------
			//add class to headers
			$("#EmpresaGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
			$("#EmpresaGrid").find('div.tree-wrap').children().removeClass('ui-icon');
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

  $("#EmpresaGrid").on("click", ".viewOptions", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var id = $(this).attr("data-id");
		var rowINFO = $("#EmpresaGrid").getRowData(id);
		var options = rowINFO["link"];

 	    //Init boton de opciones
		$('#opcionesModal').find('.modal-title').empty().html('Opciones: '+ rowINFO["nombre"] +'');
		$('#opcionesModal').find('.modal-body').empty().html(options);
		$('#opcionesModal').find('.modal-footer').empty();
		$('#opcionesModal').modal('show');
	});

	$('#opcionesModal').on('click',"a#empresaVerRoles",function(e){
		var id = $(this).attr("data-empresa");
		var formulario = $('#formOptions');
		var setCache = settingsEmpresa.listarRoles({uuid_empresa:id});
		setCache.done(function(){
			document.formOptions.action = window.phost()+'roles/listar/';
			formulario.submit();
		});
	});

	$('#opcionesModal').on('click',"a#empresaUsuarios",function(e){
		var id = $(this).attr("data-empresa");
		var formulario = $('#formOptions');
		var setCache = settingsEmpresa.listarRoles({uuid_empresa:id});
		setCache.done(function(){
			document.formOptions.action = window.phost()+'usuarios/agregar_usuarios/'+id;
			formulario.submit();
		});
	});




});


});
