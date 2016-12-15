Vue.http.options.emulateJSON = true;
Vue.component('tabla_entregas', {
	template:'#tabla_entregas',
	ready: function ()
    {
        var context = this;
        if(context.vista == 'editar')
        {
             var devolucion_alquiler_json = JSON.parse(devolucion_alquiler);
             var entregas = devolucion_items;
             context.setEntregas(entregas);
        }
    },

	data:function(){
 	var servicioValue = typeof servicios != 'undefined' && servicios != '' ? $.parseJSON(servicios) : [{id: '', categoria_item_id:'', item_id: '', serie_id: '', categoria_servicio_id: '', item_servicio_id: '', equipo_id: '', items:[], series:[], items_servicios:[]}];
 		return {
 			vista:vista,
 		 	showNoEntrega: false,
 			disabledAgregar:true,
 			disabledCategoria:true,
 			disabledItem:true,
			listaCategoriasOptions: typeof categoriasArray != 'undefined' ? categoriasArray : [],
	    	listaEntregasOptions: typeof entregasArray != 'undefined' ? $.parseJSON(entregasArray) : [],
	    	listaCategoriasServicioOptions: typeof listaCategoriasServiciosArray != 'undefined' ? $.parseJSON(listaCategoriasServiciosArray) : [],
	    	listaEquiposTrabajoOptions: typeof listaEquiposTrabajoArray != 'undefined'  ? $.parseJSON(listaEquiposTrabajoArray) : [],
 	    	entregas: servicioValue,
 	    	cantidad_alquiler: typeof cantidad_alquiler != 'undefined' && _.isNumber(cantidad_alquiler) ? cantidad_alquiler : '',
		};
	},

	 events: {
	        'cambiarEmpezable': function(items, id_empezable){
	        	var context = this;
	        	context.showNoEntrega = false;
	        	 context.setEntregas(items, id_empezable);
	        },
			'cambiarEmpezableContrato': function(items, id_empezable ){
		         var context = this;//entregas = contratos_items from formulario.js
		         context.showNoEntrega = true;
 		         context.setContratos(items, id_empezable);
		    }
	},

	methods:{

		setEntregas:function(entregas, id_empezable)//contrato_item => crear
        {
              var context = this;
             context.entregas = [];
            _.forEach(entregas, function(entrega){

            	console.log("==>"+entrega);
                   var categoria = _.find(context.listaCategoriasOptions, function(categoria){
                     return categoria.id==entrega.categoria_id;
                 });
                  if(entrega.en_alquiler > 0 || context.vista == 'editar')
                  {


                	  context.entregas.push(
                              {
                              	categoria_id:entrega.categoria_id,
                              	items:categoria.items_contratos_alquiler,
                              	item_id:entrega.item_id,
                              	cantidad_alquiler: entrega.en_alquiler,
                                series:entrega.contratos_items_detalles_devoluciones,
                                detalles:(context.vista == 'editar') ? entrega.contratos_items_detalles_devoluciones : entrega.contratos_items_detalles_entregas,
                                cantidad_restante:2,
                                id_empezable:id_empezable

                              }
                          );
                  }

             });

            Vue.nextTick(function(){

                context.$broadcast('refrescarEntregaItem');

            });

        },

        setContratos:function(entregas, id_empezable)//En realidad entregas respresenta un item con su respectivo entrega y detalle, mal puesto el nombre de la variable ;(
        {
              var context = this;
              context.entregas = [];
              _.forEach(entregas, function(entrega){

                  var categoria = _.find(context.listaCategoriasOptions, function(categoria){
                     return categoria.id==entrega.categoria_id;
              });
                  if(entrega.en_alquiler > 0 || context.vista == 'editar')
                  {

                   	  context.entregas.push(
                              {		//contratos_items_detalles_entregas:Array
                            	    entrega_id:entrega.contratos_items_detalles_entregas[0].operacion_id,
                            		categoria_id:entrega.categoria_id,
                                  	items:categoria.items_contratos_alquiler,//Lista de Items
                                  	item_id:entrega.item_id, //Item seleccionado
                                  	cantidad_alquiler: entrega.en_alquiler,
                                    series:entrega.contratos_items_detalles_entregas,
                                    detalles:(context.vista == 'editar') ? entrega.contratos_items_detalles_devoluciones : entrega.contratos_items_detalles_entregas,
                                    cantidad_restante:0,//NO se usa
                                    id_empezable:id_empezable
                              }
                          );
                 }

             });

            Vue.nextTick(function(){

                context.$broadcast('refrescarContratoItem');

            });

        },
 		ajax: function(url, data) {
			var scope = this;
			return Vue.http({
                url: phost() + url,
                method: 'POST',
                data: $.extend({erptkn: tkn}, data)
            });
		},
		toggleSubTabla: function(e) {
    		e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

    		//Toggle animacion de icono
    		$(e.currentTarget).find('i').toggleClass('fa-rotate-90');

    		//Toggle td subpanel
    		$(e.currentTarget).closest('tbody').find('tr[class*="itemsUtilizados"] > td:first-child').toggleClass('hide');
    	},
    	agregarItemOrden: function(e){
    		if(typeof e == 'undefined') {
    			e.preventDefault();
                e.returnValue = false;
                e.stopPropagation();
    		}
             this.entregas.push({categoria_item_id:'',items:[], series:[], items_servicios:[]});
    	},
    	eliminarItemOrden: function(index, e){
    		e.preventDefault();

    		var modal = $('#opcionesModal');
            var id = this.entregas[index]['id'];

            if(typeof id != 'undefined' && id != '') {

            	var botones = [
	         	   '<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>',
	         	   '<button class="btn btn-w-m btn-danger" type="button" id="elimiarServicioBtn">Eliminar</button>'
	         	].join('\n');

            	//Modal
            	this.$root.modal.titulo = 'Confirme';
            	this.$root.modal.contenido = '&#191;Esta seguro que desea eliminar?';
            	this.$root.modal.footer = botones;

				modal.modal('show');
				modal.on('click', '#elimiarServicioBtn', {id: id, index: index}, this.eliminarItem);

           }else{
        	 //eliminar fila
               this.entregas.splice(index, 1);
           }
    	},
    	eliminarItem: function(e) {
    		e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var id = typeof e.data.id != 'undefined' ? e.data.id : '';
            var index = typeof e.data.index != 'undefined' ? e.data.index : '';
            var modal = $('#opcionesModal');
            var scope = this;

            if(id==''){
            	return false;
            }

            var response = this.ajax('ordenes_trabajo/ajax-eliminar-servicio', {id: id});
     	   	response.then(function (response) {
     	   		modal.modal('hide');

     	   		if(response.data.eliminado){
     	   			toastr.success('Se ha eliminado el item satisfactoriamente.');
     	   		}

     	   		//Eliminar Item de la tabla
     	   		scope.entregas.splice(index, 1);

     	   		//Verificar si no quedan mas servicios
     	   		//e introducir un servicio en blanco
     	   		if(scope.entregas.length == 0){
     	   			scope.$nextTick(function () {
	     	   			scope.agregarItemOrden({});
     	   			});
     	   		}
            });
    	},
    	popularCampoItems: function(e, articulo){
    		e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var categoria_id = $(e.currentTarget).find('option:selected').val();

            if(categoria_id==""){
            	return false;
            }

            this.popularItemPorCategoria(categoria_id, articulo);
    	},

    	popularCampoSerial: function(e, articulo){
    		e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var item_id = $(e.currentTarget).find('option:selected').val();

            if(item_id==""){
            	return false;
            }

            this.popularItemSeriales(item_id, articulo.categoria_item_id, articulo);
    	},

    	popularCampoItemServicio: function(e, articulo){
    		e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var categoria_servicio_id = $(e.currentTarget).find('option:selected').val();

            if(categoria_servicio_id==""){
            	return false;
            }

            this.popularItemServicioPorCategoria(categoria_servicio_id, articulo);
    	},

	}
});
