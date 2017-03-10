$(document).ready(function(){
  $(function(){
    var grid = $("#contabilidadGrid");
    grid.jqGrid({
    url: phost() + 'contabilidad/ajax-listar',
    datatype: "json",
    colNames: ['','Codigo','Cuenta','Tipo','Detalle de Tipo','Balance','Estado','',''],
    colModel: [
              {name:'id', index:'id', hidedlg:true,key: true, hidden: true},
               {name:'codigo', index:'codigo',sorttype:"text", sortable:true, width:150},
               {name:'cuenta',index:'cuenta', sortable:false},
               {name:'tipo',index:'tipo', sortable:false},
               {name:'detalle', index:'detalle', formatter: 'text', sortable:false},
               {name:'balance', index:'balance', formatter: 'text', sortable:false},
               {name:'estado', index:'estado', formatter: 'text', sortable:false, align:'center'},
               {name:'opciones', index:'opciones', sortable:false, align:'center'},
               {name:'link', index:'link', hidedlg:true, hidden: true}
             ],
    mtype: "POST",
    postData: { erptkn:tkn},
    gridview: true,
    ExpandColClick: true,
    treeGrid: true,
    sortorder: "asc",
    hiddengrid: false,
    hoverrows: false,
    //multiselect: true,
    treeGridModel: 'adjacency',
    treedatatype:"json",
    ExpandColumn: 'codigo',
    treeIcons: {leaf:'fa fa-calculator',plus:'fa fa-caret-right',minus:'fa fa-caret-down'},
  //  jsonReader: { repeatitems: false, root: function (obj) { return obj.rows; } },
    height: 'auto',
    //pager: false,
    page: 1,
    pager : "#contabilidadGridPager",
    rowNum:10,
    autowidth: true,
    rowList:[10,20,30],
    sortname: 'codigo',
    viewrecords: true,
    beforeProcessing: function(data, status, xhr){
      //Check Session
    if( $.isEmptyObject(data.session) === false){
      window.location = phost() + "login?expired";
    }},
    beforeRequest: function(data, status, xhr){},
		loadComplete: function(data, status, xhr){

			//check if isset data
      //console.log(this);
      if(!_.isUndefined(data)){
			if(data.total === 0 ){
				$('#gbox_contabilidadGrid').hide();
				$('.NoRecords').empty().append('No se encontraron Empresas.').css({"color":"#868686","padding":"30px 0 0"}).show();
			}
			else{
				$('.NoRecords').hide();
				$('#gbox_contabilidadGrid').show();
			}
    }
			//---------
			// Cargar plugin jquery Sticky Objects
			//----------
			//add class to headers
			$("#contabilidadGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
      $("#contabilidadGrid").find('div.tree-wrap').children().removeClass('ui-icon');
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

  $("#contabilidadGrid").on("click", ".viewOptions", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var id = $(this).attr("data-id");

		var rowINFO = $("#contabilidadGrid").getRowData(id);
	  var options = rowINFO["link"];

 	    //Init boton de opciones
		$('#opcionesModal').find('.modal-title').empty().html('Opciones: '+ rowINFO["cuenta"] +'');
		$('#opcionesModal').find('.modal-body').empty().html(options);
		$('#opcionesModal').find('.modal-footer').empty();
		$('#opcionesModal').modal('show');
	});


});
//Busqueda por botones del filtro
$('a.filtro').bind('click', searchFiltro);

 function searchFiltro(e){
   e.preventDefault();
   $('a.filtro').unbind('click', searchFiltro);

   var item = $(this).data('item');
   var tipo = item;
   if(tipo != 0){
     $("#contabilidadGrid").setGridParam({
 			url: phost() + 'contabilidad/ajax-listar',
 			datatype: "json",
 			postData: {
 				tipo: tipo,
 				erptkn: tkn
 			}
 		}).trigger('reloadGrid');

     $('a.filtro').bind('click', searchFiltro);
   }else{
     $("#contabilidadGrid").setGridParam({
       url: phost() + 'contabilidad/ajax-listar',
       datatype: "json",
       postData: {
         tipo: 0,
         erptkn: tkn
       }
     }).trigger('reloadGrid');
     $('a.filtro').bind('click', searchFiltro);

   }
   $('ul#cuentas_tabs_tabla').children().removeClass('active');
   $(this).parent().addClass('active');
 }

 //busqueda por nombre

 $('#searchBtn').bind('click', searchBtnHlr);
 $('#clearBtn').click(function(e){
   e.preventDefault();

   $("#contabilidadGrid").setGridParam({
     url: phost() + 'contabilidad/ajax-listar',
     datatype: "json",
     postData: {
       nombre: '',
       erptkn: tkn
     }
   }).trigger('reloadGrid');

   //Reset Fields
   $('#nombre').val('');
 });

 function searchBtnHlr(e) {
 	e.preventDefault();
 	$('#searchBtn').unbind('click', searchBtnHlr);

 	var nombre 	= $('#nombre').val();


 	if(nombre !== "")
 	{
 		$("#contabilidadGrid").setGridParam({
 			url: phost() + 'contabilidad/ajax-listar',
 			datatype: "json",
 			postData: {
 				nombre: nombre,
 				erptkn: tkn
 			}
 		}).trigger('reloadGrid');

 		$('#searchBtn').bind('click', searchBtnHlr);
 	}else{
 		$('#searchBtn').bind('click', searchBtnHlr);
 	}
 }

});
