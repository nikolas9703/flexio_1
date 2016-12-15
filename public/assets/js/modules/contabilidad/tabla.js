var tablaPlanContable;
var cuentas = {
  settings: {
    url: phost() + 'contabilidad/ajax-listar',
    grid_id : "#contabilidadGrid",
 	  grid_obj : $("#contabilidadGrid"),
 	  opcionesModal : $('#opcionesModal'),
    formId: $('#form_crear_cuenta'),
    modalCambiarEstado: $('#crearPlanModal')
   },
   botones:{
     opciones: "button.viewOptions",
     buscar: $("#searchBtn"),
 		 limpiar: $("#clearBtn"),
     editarCuenta: 'a.editarCuentaBtn',
     cambiarEstado: 'a.cambiarEstadoCuentaBtn',
     filtrar: $('a.filtro'),
   },
   init:function(){
     tablaPlanContable = this.settings;
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
     tablaPlanContable.grid_obj.on("click", this.botones.opciones, function(e){

      e.preventDefault();
 			e.returnValue=false;
 			e.stopPropagation();
       var id = $(this).data("id");

   		var rowINFO = $.extend({},tablaPlanContable.grid_obj.getRowData(id));
   	  var options = rowINFO.link;

       tablaPlanContable.opcionesModal.find('.modal-title').empty().append('Opciones: '+ rowINFO.cuenta +'');
       tablaPlanContable.opcionesModal.find('.modal-body').empty().append(options);
       tablaPlanContable.opcionesModal.find('.modal-footer').empty();
       tablaPlanContable.opcionesModal.modal('show');

     });

     this.botones.filtrar.click(function(e){

       e.preventDefault();
       //$(this).unbind('click');

       var item = $(this).data('item');
       var tipo = item;
       if(tipo !== 0){
        tablaPlanContable.grid_obj.setGridParam({
          url: phost() + 'contabilidad/ajax-listar',
          datatype: "json",
          postData: {
            tipo: tipo,
            erptkn: tkn
          }
        }).trigger('reloadGrid');

        // $(this).bind('click');
       }else{
         tablaPlanContable.grid_obj.setGridParam({
           url: phost() + 'contabilidad/ajax-listar',
           datatype: "json",
           postData: {
             tipo: 0,
             erptkn: tkn
           }
         }).trigger('reloadGrid');
         //$(this).bind('click');

       }
       $('ul#cuentas_tabs_tabla').children().removeClass('active');
       $(this).parent().addClass('active');

     });

     this.botones.limpiar.click(function(e){
       e.preventDefault();
       tablaPlanContable.grid_obj.setGridParam({
         url: tablaPlanContable.url,
         datatype: "json",
         postData: {
           nombre: '',
           erptkn: tkn
         }
       }).trigger('reloadGrid');

       //Reset Fields
       $('#nombre').val('');
     });

     this.botones.buscar.click(function(e){
       e.preventDefault();
      	var nombre 	= $('#nombre').val();
      	if(nombre !== ""){
      		tablaPlanContable.grid_obj.setGridParam({
      			url: tablaPlanContable.url,
      			datatype: "json",
      			postData: {
      				nombre: nombre,
      				erptkn: tkn
      			}
      		}).trigger('reloadGrid');
      	}
     });
    tablaPlanContable.opcionesModal.on("click", this.botones.cambiarEstado, function(e) {
      tablaPlanContable.opcionesModal.modal('hide');
      var id = $(this).data('id');
      var estado =  $(this).data('estado');
      var parametros = {id:id, estado:estado};
      var ajaxEstado = moduloContabilidad.cambiarEstadoCuentaContable(parametros);
      tablaPlanContable.modalCambiarEstado.find('.modal-title').empty().html('Cambiar Estado');
      var opciones = '<div class="loading-progress"></div>';
      $('#crearPlanModal').find('.modal-body').empty().html(opciones);
      $('#crearPlanModal').find('.modal-footer').empty();
      tablaPlanContable.modalCambiarEstado.modal('show');
      var progress = $(".loading-progress").progressTimer({
        timeLimit: 300,
        completeStyle: 'progress-bar-success',
        onFinish: function() {
          tablaPlanContable.modalCambiarEstado.modal('hide');
          tablaPlanContable.grid_obj.trigger('reloadGrid');

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
    tablaPlanContable.opcionesModal.on("click", this.botones.editarCuenta, function(e) {
      var id = $(this).data('id');
      var parametros = {id:id};
      var buscarCuenta = moduloContabilidad.getCuenta(parametros);
      buscarCuenta.success(function(data){
        //popular
        var datos_cuenta = $.parseJSON(data);
        $("#idEdicion").remove();
        tablaPlanContable.formId.append('<input type="hidden" name="id" id="idEdicion" value="' + datos_cuenta.id + '">');
        tablaPlanContable.formId.find('#nombre').val(datos_cuenta.nombre);
        $('#padre_id').val(datos_cuenta.padre_id);
        $('#descripcion').val(datos_cuenta.detalle);
        $('#codigo').val(datos_cuenta.codigo);
        tablaPlanContable.formId.find('#impuesto').val(datos_cuenta.impuesto_id);
        tablaPlanContable.opcionesModal.modal("hide");
      });

      buscarCuenta.done(function(data){
        var datos_cuenta = $.parseJSON(data);
        var cuentas = moduloContabilidad.listarCuenta();
        cuentas.success(function(){
          $("#cuentas_tabs li:first-child").addClass('active');
          $('#codigo').prop('readonly', true);
          $("#plan_cuentas").jstree("destroy");
        });

        cuentas.done(function(data){

          var arbol = jQuery.parseJSON(data);
          $('#plan_cuentas').jstree(arbol);
          /**/
          $('#plan_cuentas').jstree(true).redraw(true);

          $("#plan_cuentas").bind("loaded.jstree",function(e,data){

             data.instance.select_node(datos_cuenta.id);
             //data.instance.open_node(datos_cuenta.id);

          });
            $("#plan_cuentas").on("select_node.jstree", function(e, data){
              console.log("select_node:");
              //console.log(data);
              //console.log(data.instance.get_node(data.node.id).text);

            });
            $("#plan_cuentas").on("changed.jstree", function(e, data){
              console.log("changed");
              //
              //console.log("data:");
              //console.log(data);
              //console.log(_.isObject(data.node));
              //debugger;
              //var nodo = $.parseJSON(data.node);
              //console.log("Nodo:");
              //console.log(nodo);
              //var nodo_id = nodo.id;
              //$('#padre_id').val(nodo_id);
              /*if(_.isEmpty(nodo.children)){
                var original = nodo.original;
                var codigo = original.codigo;
                $('#codigo').val(codigo+'01.');
              }else{
                var parametros = {node:nodo_id};
                var nodoCodigo = moduloContabilidad.getCodigo(parametros);
                nodoCodigo.done(function(result){
                  var codigo = $.parseJSON(result);
                  $('#codigo').val(codigo.codigo);
                });
              }*/

            });
          //$("#plan_cuentas").jstree(true).select_node(datos_cuenta.id.toString());
          $('#addCuentaModal').find('.modal-title').empty().html('Editar: Cuenta Contable');
          $('#addCuentaModal').modal('show');
        }); // fin del done

      });


    });
   },
   tablaGrid:function(){
     tablaPlanContable.grid_obj.jqGrid({
     url: tablaPlanContable.url,
     datatype: "json",
     colNames: ['','No. de Cuenta','Cuenta','Tipo','Descripción','Balance','Estado','',''],
     colModel: [
               {name:'id', index:'id', hidedlg:true,key: true, hidden: true},
                {name:'codigo', index:'codigo',sorttype:"text", sortable:true, width:150},
                {name:'cuenta',index:'cuenta', sortable:false},
                {name:'tipo',index:'tipo', sortable:false},
                {name:'detalle', index:'detalle', formatter: 'text', sortable:false},
                {name:'balance', index:'balance', formatter: 'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$ "}, sortable:false},
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
     ExpandColumn: 'codigo',
     treeIcons: {leaf:'fa fa-calculator',plus:'fa fa-caret-right',minus:'fa fa-caret-down'},
     height: 'auto',
     page: 1,
     pager : tablaPlanContable.grid_id+"Pager",
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
 	loadBeforeSend: function () {//propiedadesGrid_cb
	    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
	      $(this).closest("div.ui-jqgrid-view").find("#tablaPlanContable_cb, #jqgh_tablaPlanContable_link").css("text-align", "center");
	    },

     beforeRequest: function(data, status, xhr){},
 		loadComplete: function(data, status, xhr){

 			//check if isset data

       /*if(!_.isUndefined(data)){
 			if(data.total === 0 ){
 				$('#gbox_contabilidadGrid').hide();
 				$('#contabilidadGridNoRecords').empty().append('No se encontraron Cuentas.').css({"color":"#868686","padding":"30px 0 0"}).show();
 			}
 			else{
 				$('.NoRecords').hide();
 				$('#contabilidadGridNoRecords').show();
 			}
    }*/
 			//---------
 			// Cargar plugin jquery Sticky Objects
 			//----------
 			//add class to headers
 			tablaPlanContable.grid_obj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
      tablaPlanContable.grid_obj.find('div.tree-wrap').children().removeClass('ui-icon');
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
    cuentas.init();
});
