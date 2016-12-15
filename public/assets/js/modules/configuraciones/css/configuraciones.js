var moduloConfiguracion = (function(){
  function getfromdata($form){
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
  }
  return{
    getTipoActividades:function(){
      var parametros = {tipo:nombre_tabla,erptkn: tkn};
      return $.ajax({
				  type: "post",
					url: phost() + 'configuracion/ajax-polular-tabla',
					datatype:'json',
					cache: false,
					data:parametros,
					success:function(data){
            resultados = $.parseJSON(data);
						$("#tablaGrid").jqGrid({
               url: phost() + 'configuracion/ajax-listar-tabla',
               colNames:resultados.colName,
               colModel: resultados.colModel,
               height: "auto",
               autowidth: true,
               datatype: 'json',
               mtype: "POST",
               pager: "#pager_tabla",
               postData:parametros,
               loadtext: '<p>Cargando...',
               rowList: [10, 20,50,100],
           		 rowNum: 10,
           		 page: 1,
               sortname: 'puntaje',
               hoverrows: false,
               viewrecords: true,
               gridview: true,
               multiselect: false,
               loadComplete: function(){}
            });
          },complete:function(data){
						$("#tablaGrid").jqGrid('setGridParam', { datatype: 'json',postData:parametros }).trigger('reloadGrid');
					}
          });
    },
    initGridCatalogo: function(id_grid, modulo, campo){

    	//Verificar si el elemento existe en el DOM
    	if(typeof $(id_grid).attr('id') === "undefined" ){
    		return false;
    	}

    	//Init Grid Catalogos
    	$(id_grid).dynamicGrid({
    		url: phost() + 'configuracion/ajax-grid-catalogo',
    		colModel:  [
    		   {name:'Valor', index:'cat.etiqueta', width:80},
    		   {name:'', index:'link', width:30, align:"center", sortable:false, resizable:false, hidedlg:true},
    		   {name:'options', index:'options', hidedlg:true, hidden: true},
    		],
    		postData: {
    	   		erptkn: tkn,
    	   		modulo: modulo,
    	   		campo: campo,
    	   	},
    	   	sortname: 'cat.id_cat'
    	});
    },
    guardarEditarCatalogo: function(parametros){
    	return $.post(phost() +'configuracion/ajax-guardar-editar-catalogo', $.extend({erptkn: tkn}, parametros));
    },
    eliminarCatalogo: function(parametros){
    	return $.post(phost() +'configuracion/ajax-eliminar-catalogo', $.extend({erptkn: tkn}, parametros));
    },
    guardarTipoActividad:function(parametros){
    	return $.post(phost() +'actividades/ajax-guardarTipoActividad', $.extend({erptkn: tkn}, parametros));
    },
    getInfoTipoActividades:function(parametros){
    	return $.post(phost() +'actividades/ajax-getTipoActividades', $.extend({erptkn: tkn}, parametros));
    },
    eliminarTipoActividad:function(parametros){
        return $.post(phost() +'actividades/ajax-eliminarTipoActividades', $.extend({erptkn: tkn}, parametros));
    },
    getUsuariosByrol:function(parametros){
      return $.post(phost() +'roles/ajax-getUsuariosByrol', $.extend({erptkn: tkn}, parametros));
    },
    polulateUsuariosSelect:function(element,usuarios){
    	var index = $(element).attr('id').replace( /^\D+/g, '');
    	var option = '';

    	//verificar datos
    	if(usuarios.length > 0){
    		$.each(usuarios,function(i,value){
        		option +='<option value="'+value.uuid_usuario+'">'+value.nombre+'</option>';
        	});
        	$('select#id_usuario'+ index).empty().html(option);
        	$('select#id_usuario'+ index).attr('data-placeholder', 'Seleccione');
    	}else{
    		$('select#id_usuario'+ index).empty();
    		$('select#id_usuario'+ index).attr('data-placeholder', 'No hay usuarios');
    	}

    	//si es un select chosen
    	if( $('select#id_usuario'+ index).hasClass('chosen-select')){
    		//actualizar plugin
    		$('select#id_usuario'+ index).chosen({
    			width: '100%',
            }).trigger('chosen:updated').on('chosen:showing_dropdown', function(evt, params) {
            	$('select#id_usuario'+ index).closest('div.table-responsive').css("overflow", "visible");
            }).on('chosen:hiding_dropdown', function(evt, params) {
            	$('select#id_usuario'+ index).closest('div.table-responsive').css({'overflow-x':'auto !important'});
            });
    	}
    },
    guardarNotificaciones:function(element){
      var parametros = getfromdata(element);
      console.log(parametros);
      console.log($(element).serialize());
    }


  };
})();
