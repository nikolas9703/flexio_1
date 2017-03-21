
var entrega_item = Vue.component('tabla_series',{

    template:'#tabla_series',

    props: ['parent_index','parent_entregas'],

      events:{
         refrescarEntregaItem:function(){
             var context = this;
            context.myReady();
         },
         refrescarContratoItem:function(){
             var context = this;
            context.myReadyContrato();
         }


    },

    data: function()
    {
        return {
             vista:vista,
             estadoDevuelto: false,
             bodegas: JSON.parse(bodegasArray),
             articulos:[],
         };

    },

    methods:
    {
    	myReadyContrato:function()
        {
    	    var context = this;
            var item = _.find(context.parent_entregas.items, function(item){
               return item.id == context.parent_entregas.item_id;
            });

            var devolucion_alquiler_json = (context.vista == 'editar')?JSON.parse(devolucion_alquiler):[];

            var cantidad_restante = context.parent_entregas.cantidad_restante;
            var serializable = (item.tipo_id == '5' || item.tipo_id == '8');
            context.articulos = [];
            if(devolucion_alquiler_json.estado_id > 1){
            	 context.estadoDevuelto =  true;
            }else{
           	 	context.estadoDevuelto =  false;
            }
          if(context.vista == 'editar')
          {

                _.forEach(context.parent_entregas.detalles, function(detalle){
               	 if(detalle.operacion_id == devolucion_alquiler_json.id){
               		 context.articulos.push({
                            serializable: serializable,
                            serie: detalle.serie,
                            id: detalle.id,
                            cantidad: detalle.cantidad,
                            bodega_id: detalle.bodega_id,
                            fecha_devolucion_estimada:detalle.fecha_format,
                            estado_item_devuelto: detalle.estado_item_devuelto,
                            ubicacion_id: detalle.bodega_id,
                            disabledAddRow:true

                         });
               	 }

               });
          }
          else if(serializable)
           {

	        	   var contador = 0;
	    		   var cantidad_restante = context.parent_entregas.cantidad_alquiler;
 	        	   _.forEach(context.parent_entregas.detalles, function(detalle){
 		 	        		 if(contador < cantidad_restante){
			        			   context.articulos.push({
			                           serializable: serializable,
			                           serie: detalle.serie,
			                           id: detalle.id,
			                           cantidad: detalle.cantidad,
			                           bodega_id: detalle.bodega_id,
			                           fecha_devolucion_estimada:detalle.fecha_format,
			                           estado_item_devuelto: detalle.estado_item_devuelto,
			                           ubicacion_id: detalle.bodega_id,
			                           disabledAddRow:true
			                       });

			                    contador = contador + 1;

		  	        	   }
 	           });

           }
           else
           {

            	 _.forEach(context.parent_entregas.detalles, function(detalle){



            		if(context.parent_entregas.entrega_id == detalle.operacion_id){

            			context.articulos.push({
		                       serializable: false,
		                       serie: detalle.serie,
		                       id:  '',
		                       cantidad:  detalle.cantidad,
		                       cantidad_validacion:  detalle.cantidad,
		                       bodega_id:  '',
		                       fecha_devolucion_estimada: '',
		                       estado_item_devuelto:  '',
		                       ubicacion_id:  '',
		                       disabledAddRow:true
		                   });
            		}

             	});

           }
        },
        myReady:function()
        {
             var context = this;
             var item = _.find(context.parent_entregas.items, function(item){
                return typeof item != 'undefined' && item.id == context.parent_entregas.item_id;
             });

             var devolucion_alquiler_json = (context.vista == 'editar')?JSON.parse(devolucion_alquiler):[];

             var cantidad_restante = context.parent_entregas.cantidad_restante;
             var serializable = typeof item != 'undefined' ? (item.tipo_id == '5' || item.tipo_id == '8') : false;
             context.articulos = [];
              if(devolucion_alquiler_json.estado_id > 1){
            	 context.estadoDevuelto =  true;
             }else{
            	 context.estadoDevuelto =  false;
             }
           if(context.vista == 'editar')
           {

                 _.forEach(context.parent_entregas.detalles, function(detalle){
                	 if(detalle.operacion_id == devolucion_alquiler_json.id){
                		 context.articulos.push({
                             serializable: serializable,
                             serie: detalle.serie,
                             id: detalle.id,
                             cantidad: detalle.cantidad,
                             bodega_id: detalle.bodega_id,
                             fecha_devolucion_estimada:detalle.fecha_format,
                             estado_item_devuelto: detalle.estado_item_devuelto,
                             ubicacion_id: detalle.bodega_id,
                             disabledAddRow:true

                          });
                	 }

                });
           }
           else if(serializable)
            {

	        	   var contador = 0;
	    		   var cantidad_restante = context.parent_entregas.cantidad_alquiler;
  	        	   _.forEach(context.parent_entregas.detalles, function(detalle){
  	        		 //if(detalle.operacion_id == devolucion_alquiler_json.id){
		 	        		 if(contador < cantidad_restante){
			        			   context.articulos.push({
			                           serializable: serializable,
			                           serie: detalle.serie,
			                           id: detalle.id,
			                           cantidad: detalle.cantidad,
			                           bodega_id: detalle.bodega_id,
			                           fecha_devolucion_estimada:detalle.fecha_format,
			                           estado_item_devuelto: detalle.estado_item_devuelto,
			                           ubicacion_id: detalle.bodega_id,
			                           disabledAddRow:true
			                       });

			                    contador = contador + 1;

		  	        	   }
  	        		 //}
	           });

            }
            else
            {

             	 _.forEach(context.parent_entregas.detalles, function(detalle){
              		  if(detalle.operacion_id == context.parent_entregas.id_empezable){
			               	  context.articulos.push({
			                       serializable: false,
			                       serie: detalle.serie,
			                       id:  '',
			                       cantidad:  detalle.cantidad,
			                       cantidad_validacion:  detalle.cantidad,
			                       bodega_id:  '',
			                       fecha_devolucion_estimada: '',
			                       estado_item_devuelto:  '',
			                       ubicacion_id:  '',
			                       disabledAddRow:true
			                   });
             		  }
             	});

            }

         },


        addRow:function(e)
        {
            var context = this;
            var item = _.find(context.parent_articulo.items, function(item){
                return item.id == context.parent_articulo.item_id;
            });
            var serializable = (item.tipo_id == '5' || item.tipo_id == '8');
            e.preventDefault();

            context.articulos.push({
                serializable: serializable,
                serie:'',
                cantidad: serializable ? 1 : '',
                bodega_id:'',
                fecha_devolucion_estimada:'',
                disabledAddRow:true
            });

            context.verificaCantidad();
            context.activarDatepicker();
        },

        removeRow:function(index, e)
        {
            e.preventDefault();
            this.articulos.splice(index,1);
            this.verificaCantidad();
        }

    }

});
