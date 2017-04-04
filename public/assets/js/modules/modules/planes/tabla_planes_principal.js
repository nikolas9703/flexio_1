//Modulo
var tablaPlanes = (function(){
  var url = 'planes/ajax-listar-planes-principal';
  var grid_id = "PlanesTabGrid";
  var grid_obj = $("#PlanesTabGrid");
  var opcionesModal = $('#opcionesModal');
  //var opcionesModalEstado = $('#opcionesModalEstado');
  //var crearContactoForm = $("#crearContactoForm");  
  
  var botones = {
    opciones: ".viewOptions",
    //editar: "", 
    //crearContacto: ".agregarContacto",    
    //buscar: "#searchBtn",
    //limpiar: "#clearBtn",
    exportar: "#exportarBtn",
    //cambioGrupal: "#cambiarEstadoPlanesLnk",
  };
  
  var tabla = function(){   
    
    //inicializar jqgrid
    grid_obj.jqGrid({
        url: phost() + url,
        datatype: "json",
        colNames:[
            '',
            'Nombre',
			      'Aseguradora',
            'Producto',
            'Comisi&oacute;n',
            'Sobre comisi&oacute;n',
            'Desc. Comisi&oacute;n',
            '',
            ''
        ],
        colModel:[
            {name:'id', index:'id', width:30,  hidedlg:true, hidden: true},
            {name:'seg_planes.nombre', index:'seg_planes.nombre', width:70 },
			      {name:'aseg.nombre', index:'aseg.nombre', width:70 },
            {name:'producto.nombre', index:'producto.nombre', width:70 },
            {name:'comi.comision', index:'comi.comision', width: 30 },
            {name:'comi.sobre_comision', index:'comi.sobre_comision', width: 30 },
            {name:'seg_planes.desc_comision', index:'seg_planes.desc_comision', width: 30 },
            {name:'link', index:'link', width:50, sortable:false, hidedlg:true, align:"center", resizable:false, search:false},
            {name:'options', index:'options',hidedlg:true, hidden: true, search:false}
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
        pager: "#"+ grid_id +"Pager",
        loadtext: '<p>Cargando Planes...</p>',
        hoverrows: false,
        viewrecords: true,
        refresh: true,
        gridview: true,
        search: true,
        multiselect: true,
        sortname: 'aseg.nombre',
        sortorder: "ASC",
        beforeProcessing: function(data, status, xhr){
          //Check Session
        if( $.isEmptyObject(data.session) == false){
          window.location = phost() + "login?expired";
        }
        },
        loadBeforeSend: function () {
        $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
        grid_obj.find('input[type="text"]').css("width", "95% !important");
                $(this).closest("div.ui-jqgrid-view").find("#"+grid_id+"_cb, #jqgh_tablaPlanesGrid_link").css("text-align", "center");
        
      },
        beforeRequest: function(data, status, xhr){},
        loadComplete: function(data){
        $('#'+ grid_id +'NoRecords').hide();
          $('#gbox_'+ grid_id).show();
      },
      onSelectRow: function(id){
        $(this).find('tr#'+ id).removeClass('ui-state-highlight');
      },
      /*loadComplete: function(data){
        //check if isset data
        if( data['total'] == 0 ){
          $('#gbox_'+ grid_id).hide();
          $('#'+ grid_id +'NoRecords').empty().append('No se encontraron Planes.').css({"color":"#868686","padding":"30px 0 0"}).show();
        }
        else{
          $('#'+ grid_id +'NoRecords').hide();
          $('#gbox_'+ grid_id).show();
        }
      },
      onSelectRow: function(id){
        $(this).find('tr#'+ id).removeClass('ui-state-highlight');
      },*/
    });
    
    //Al redimensionar ventana
    $(window).resizeEnd(function() {
      tablaPlanes.redimensionar();
    });
	
	grid_obj.jqGrid('navGrid',grid_id,{del:false,add:false,edit:false,search:true});
    grid_obj.jqGrid('filterToolbar',{searchOnEnter : false});
	
  };
  
  //Inicializar Eventos de Botones
  var eventos = function(){   
    
    //Boton Opciones
    grid_obj.on("click", botones.opciones, function(e){
      e.preventDefault();
      e.returnValue=false;
      e.stopPropagation();

      var id = $(this).attr("data-id");
      var rowINFO = grid_obj.getRowData(id);  
      var option = rowINFO["options"];
      //evento para boton collapse sub-menu Accion Personal
      opcionesModal.on('click', 'a[href="#collapse'+ id +'"]', function(){
        opcionesModal.find('#collapse'+ id ).collapse();
      });

      //Init Modal
      opcionesModal.find('.modal-title').empty().append('Opciones: '+ rowINFO["plan"] +'');
      opcionesModal.find('.modal-body').empty().append(option);
      opcionesModal.find('.modal-footer').empty();
      opcionesModal.modal('show');
    });
    
    //Boton de Exportar aseguradores
    $(botones.exportar).on("click", function(e){
      e.preventDefault();
      e.returnValue=false;
      e.stopPropagation();                        
      if($('#id_tab_planes').is(':visible') == true){     
        //Exportar Seleccionados del jQgrid
        var ids = [];
        ids = grid_obj.jqGrid('getGridParam','selarrrow');
        
        //Verificar si hay seleccionados
        if(ids.length > 0){
          console.log(ids); 
          $('#ids2').val(ids);
          $('form#exportarPlanesLnk').submit();
          $('body').trigger('click');
		  if($("#cb_"+grid_id).is(':checked')) {
				$("#cb_"+grid_id).trigger('click');
			}
			else
			{
				$("#cb_"+grid_id).trigger('click');
				$("#cb_"+grid_id).trigger('click');
			}
        }
      }
    });
  };
 
  //Reload al jQgrid
  var recargar = function(){
    
    //Reload Grid
    grid_obj.setGridParam({
      url: phost() + url,
      datatype: "json",
      postData: {
        plan: '',
        producto: '',
        ramo: '',
        comision: '',
        sobre_comision: '',
        desc_comision: ''
      }
    }).trigger('reloadGrid');
  };
  
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
tablaPlanes.init();

$("#jqgh_PlanesTabGrid_cb span").removeClass("s-ico");
$('#jqgh_PlanesTabGrid_link span').removeClass("s-ico");