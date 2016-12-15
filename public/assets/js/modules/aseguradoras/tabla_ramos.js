var tablaRamos;
var ramos = {
  settings: {
    url: phost() + 'aseguradoras/ajax-listar-ramos',
    grid_id : "#RamosGrid",
 	  grid_obj : $("#RamosGrid"),
 	  opcionesModal : $('#opcionesModal'),
    formId: $('#crearRamosForm'),
    modalCambiarEstado: $('#estadoRamoModal')
   },
   botones:{
     opciones: "button.viewOptions",
     buscar: $("#searchBtn"),
        limpiar: $("#clearBtn"),
     editarCuenta: 'a.editarRamoBtn',
     cambiarEstado: 'a.cambiarEstadoRamoBtn',
     filtrar: $('a.filtro'),
   },
   init:function(){
     tablaRamos = this.settings;
     this.tablaGrid();
     this.redimencionar();
     this.eventos();
   },
   redimencionar:function(){
     $(window).resizeEnd(function() {
       $(".ui-jqgrid").each(function(){
         var w = parseInt( $(this).parent().width()) - 6;
         var tmpId = $(this).attr("id");
         var gId = tmpId.replace("gbox_","");
         $("#"+gId).setGridWidth(w);
       });
     });
   },
   eventos:function(){
     tablaRamos.grid_obj.on("click", this.botones.opciones, function(e){

      e.preventDefault();
 			e.returnValue=false;
 			e.stopPropagation();
       var id = $(this).data("id");

   		var rowINFO = $.extend({},tablaRamos.grid_obj.getRowData(id));
   	  var options = rowINFO.link;

       tablaRamos.opcionesModal.find('.modal-title').empty().append('Opciones: '+ rowINFO.nombre +'');
       tablaRamos.opcionesModal.find('.modal-body').empty().append(options);
       tablaRamos.opcionesModal.find('.modal-footer').empty();
       tablaRamos.opcionesModal.modal('show');

     });


     this.botones.limpiar.click(function(e){
       e.preventDefault();
       tablaRamos.grid_obj.setGridParam({
         url: tablaRamos.url,
         datatype: "json",
         postData: {
           nombre: '',
           erptkn: tkn
         }
       }).trigger('reloadGrid');

       //Reset Fields
       $('#nombre').val('');
     });

     
    tablaRamos.opcionesModal.on("click", this.botones.cambiarEstado, function(e) {
        
      tablaRamos.opcionesModal.modal('hide');
      var id = $(this).data('id');
      var estado =  $(this).data('estado');
      var parametros = {id:id, estado:estado};
      
      var ajaxEstado = moduloAseguradora.cambiarEstadoCuentaContable(parametros);
      tablaRamos.modalCambiarEstado.find('.modal-title').empty().html('Cambiar Estado');
      var opciones = '<div class="loading-progress"></div>';
      $('#estadoRamoModal').find('.modal-body').empty().html(opciones);
      $('#estadoRamoModal').find('.modal-footer').empty();
      tablaRamos.modalCambiarEstado.modal('show');
      var progress = $(".loading-progress").progressTimer({
        timeLimit: 300,
        completeStyle: 'progress-bar-success',
        onFinish: function() {
          tablaRamos.grid_obj.trigger('reloadGrid');
          tablaRamos.modalCambiarEstado.modal('hide');
          

        }
      });

      ajaxEstado.fail(function(){
        progress.progressTimer('error', {
          errorText: 'error al cambiar el estado!',
          onFinish: function() {
            console.log('hubo un error en cambiar el estado');
          }

        });
      });

      ajaxEstado.done(function(data) {
        var respuesta = $.parseJSON(data);
        if (respuesta.estado == 200) {
          $("#mensaje_info").empty().html('<div id="success-alert" class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + respuesta.mensaje + '</div>');
          progress.progressTimer('complete');
        }
      });


    });
    
    tablaRamos.opcionesModal.on("click", this.botones.editarCuenta, function(e) {
      var id = $(this).data('id');
      var confir=0;
      var parametros = {id:id};
      
      var buscarCuenta = moduloAseguradora.getRamo(parametros);
      buscarCuenta.success(function(data){
        //popular
        var datos_ramo = $.parseJSON(data);
        $("#idEdicion").remove();
        tablaRamos.formId.append('<input type="hidden" name="id" id="idEdicion" value="' + datos_ramo.id + '">');
        tablaRamos.formId.find('#nombre').val(datos_ramo.nombre);
        $('#descripcion').val(datos_ramo.descripcion);
        $('#codigo').val(datos_ramo.padre_id);
        $('#codigo_ramo').val(datos_ramo.codigo_ramo);
        $("#tipo_interes_ramo").val(datos_ramo.interes_asegurado);
        $("#tipo_poliza_ramo").val(datos_ramo.tipo_poliza);
        tablaRamos.formId.find('#cuenta_id').val(datos_ramo.formulario_solic);
        tablaRamos.opcionesModal.modal("hide");
        padre_id=datos_ramo.padre_id;
      });

      buscarCuenta.done(function(data){
          
        var datos_cuenta = $.parseJSON(data);
        var cuentas = moduloAseguradora.listarRamosTree();
        cuentas.success(function(){
          $("#treeRamos").jstree("destroy");
        });

        cuentas.done(function(data){

          var arbol = jQuery.parseJSON(data);
          $('#treeRamos').jstree(arbol);
          /**/
          $('#treeRamos').jstree(true).redraw(true);

          $("#treeRamos").bind("loaded.jstree",function(e,data){

             data.instance.select_node(datos_cuenta.id);
             //data.instance.open_node(datos_cuenta.id);

          });
            $("#treeRamos").on("select_node.jstree", function(e, data){
                if(!confir==0){
                    var nodo = data.node;
                    var nodo_id = nodo.id;
                    $('#codigo').val(nodo_id);
                }confir++; 
              
              //console.log(data);
              //console.log(data.instance.get_node(data.node.id).text);

            });
            $("#treeRamos").on("changed.jstree", function(e, data){
              console.log("changed");

            });
        }); // fin del done

      });
    });
   },
   tablaGrid:function(){
     tablaRamos.grid_obj.jqGrid({
     url: tablaRamos.url,
     datatype: "json",
     colNames: ['','Ramo','Descripcion','Estado','',''],
     colModel: [
               {name:'id', index:'id', hidedlg:true,key: true, hidden: true},
                {name:'nombre', index:'nombre',sorttype:"text", sortable:true, width:150},
                {name:'descripcion',index:'descripcion', sortable:false},
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
     treeGridModel: 'adjacency',
     treedatatype:"json",
     ExpandColumn: 'nombre',
     treeIcons: {leaf:'fa fa-calculator',plus:'fa fa-caret-right',minus:'fa fa-caret-down'},
     height: 'auto',
     page: 1,
     pager : tablaRamos.grid_id+"Pager",
     rowNum:10,
     autowidth: true,
     rowList:[10,20,30],
     sortname: 'nombre',
     viewrecords: true,
     beforeProcessing: function(data, status, xhr){
       //Check Session
     if( $.isEmptyObject(data.session) === false){
       window.location = phost() + "login?expired";
     }},
     beforeRequest: function(data, status, xhr){},
 		loadComplete: function(data, status, xhr){
 			tablaRamos.grid_obj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
                        tablaRamos.grid_obj.find('div.tree-wrap').children().removeClass('ui-icon');
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
   }
};
$(document).ready(function(){
    ramos.init();
});
