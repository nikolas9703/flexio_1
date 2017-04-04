var deleteItems = [];
Vue.http.options.emulateJSON = true;
Vue.component('items', {
	template:'#items',
	props: {
		categorias: Array,
		parent_index: Number,
		listaitems: Array,
	},
	data:function(){

		return {
			listaCategoriasItemOptions: typeof listaCategoriasItemArray != 'undefined' ? $.parseJSON(listaCategoriasItemArray) : [],
	    	//listaitems: listaitems
		};
	},
	ready:function(){
		this.$parent.$refs.items = this;
		//console.log("ITEM:", this.categorias);
	},
	methods:{
		toggleSubTabla: function(e) {
    		e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

    		//Toggle animacion de icono
    		$(e.currentTarget).find('i').toggleClass('fa-rotate-90');

    		//Toggle td subpanel
    		$(e.currentTarget).closest('tbody').find('tr[class*="itemCamposExtra"] > td:first-child').toggleClass('hide');
    	},
		agregarItem: function(){
            var scope =  this;
            this.$nextTick(function () {
            	scope.listaitems.push({id:'', categoria_id:'', cantidad:1, impuesto_uuid:'', descuento: 0, impuesto_porcentaje:'', precio_unidad:'', precio_total:'', impuestos: typeof impuestos != 'undefined' ? impuestos : [], items:[], atributos:[], unidades:[], cuentas: typeof cuentas != 'undefined' ? cuentas : []});
            });
		},
		eliminarItem: function(index){
            var modal = $('#opcionesModal');
            var id = this.listaitems[index]['id'];

            if(id != '') {

            	var botones = [
	         	   '<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>',
	         	   '<button class="btn btn-w-m btn-danger" type="button" id="elimiarPiezaBtn">Aceptar</button>'
	         	].join('\n');

            	//Modal
            	this.$root.modal.titulo = 'Confirme';
            	this.$root.modal.contenido = '<p>&#191;Esta seguro que desea eliminar?</p><div class="alert alert-warning">El item ser&aacute; eliminado al guardar los cambios de la orden.</div>';
            	this.$root.modal.footer = botones;

				modal.modal('show');
				modal.on('click', '#elimiarPiezaBtn', {id: id, index: index}, this.eliminar);

           }else{
        	   //eliminar fila
               this.listaitems.splice(index,1);
           }
    	},
    	// Al hacer clic en el boton del modal
    	// Elminar item de la tabla
    	// y poner en array delete_items
    	eliminar: function(e) {
    		e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var id = typeof e.data.id != 'undefined' ? e.data.id : '';
            var index = typeof e.data.index != 'undefined' ? e.data.index : '';
            var modal = $('#opcionesModal');
            var scope = this;
            modal.modal('hide');

            if(id==''){
            	return false;
            }

            //verificar si item ya existe en items a eliminar
            var existe = _.find(deleteItems, function(item){
				return item == id;
            });

            if(typeof existe != 'undefined'){
            	return;
            }

            //agregar a array de items a eliminar
            deleteItems.push(id);
            this.$root.delete_items = deleteItems;

            //eliminar fila
            this.listaitems.splice(index,1);

            //Verificar si no quedan mas piezas
 	   		//e introducir una pieza en blanco
 	   		if(scope.listaitems.length == 0){
 	   			scope.$nextTick(function () {
     	   			scope.agregarItem({});
 	   			});
 	   		}
    	},
    	popularItems: function(e, item, index){
    		e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            //
            // Validar Selecion de campo  Lista de precio
            //
            var check = this.verificarSeleccionListaPrecio(index);

            var categoria_id = $(e.currentTarget).find('option:selected').val();
            if(categoria_id=="" || check ==false){
            	item.itemsCat = [];
            	return false;
            }

            var scope = this;
			var response = this.$root.ajax('ordenes_trabajo/ajax-seleccionar-items', {categoria_id: categoria_id});
    		response.then(function (response) {

    			//Check Session
                if ($.isEmptyObject(response.data.session) == false) {
                    window.location = phost() + "login?expired";
                }

    			//popular lisatdo de items
    			item.items = !_.isEmpty(response.data.items) ? response.data.items : [];

    			//reset atributos
                item.atributos = [];
            });
    	},
    	popularItemDatos: function(e, item, index){

    		e.preventDefault();

            var scope = this;
            var item_id 		= $(e.currentTarget).find('option:selected').val();
            var iteminfo 		= _.find(item.items, function(iteminfo){ return iteminfo.id == item_id; });
            var precio_unidad 	= '';

            //
            // Obtener precio del item
            //
        	var precio = _.result(_.find(iteminfo.precios, function(query){
        		return query.id == scope.$root.lista_precio_id;
            }),'pivot');

        	precio_unidad = typeof precio != 'undefined' ? precio.precio : '';

            //popular precio unidad
            item.precio_unidad = !_.isEmpty(precio_unidad) ? precio_unidad : '';

            //popular atributos
            item.atributos = !_.isEmpty(iteminfo) ? iteminfo.atributos : [];

            //popular unidades
            item.unidades = !_.isEmpty(iteminfo) && iteminfo.unidades.length > 0 ? iteminfo.unidades : [];

            //Seleccionar impuesto
            item.impuesto_uuid = iteminfo.uuid_venta;

            //Establecer porcentaje de impuesto
            item.impuesto_porcentaje = typeof iteminfo.impuesto != 'undefined' && iteminfo.impuesto != null ? iteminfo.impuesto.impuesto : [];

            item.cuenta_uuid = iteminfo.uuid_ingreso;

            //Calcular Precio Total del Item
            this.calcularPrecioTotal(index);
    	},
    	calcularPrecioSegunUnidad: function(e, item, index) {

    		var factor_conversion = typeof item.unidad != 'undefined' && item.unidad != null ? parseFloat(item.unidad.pivot.factor_conversion) : 0;
    	    var precio_unidad = roundNumber((factor_conversion * item.precio_unidad),2);
    	    item.precio_unidad = precio_unidad;

    	    //Calcular Precio Total del Item
            this.calcularPrecioTotal(index);
    	},
    	//
        // Verificar si ha seleccionado lista de precio
		// Si no ha seleccionado ninguna opcion
		// resetear seleccion de categoria de item.
        //
    	verificarSeleccionListaPrecio: function(index){
    		var scope = this;
    		if(typeof this.$root.lista_precio_id !== 'undefined'){
            	if(this.$root.lista_precio_id === '') {

            		$(this.$root.$el).validate().element('#lista_precio_id');
            	    toastr.warning('Porfavor seleccione Lista de precio.');

            	    this.$nextTick(function () {
            	    	scope.listaitems[index].categoria_id = '';
            	    	scope.listaitems.$set(index, scope.listaitems[index]);
                    });
            	    return false;
            	}
            }
    		return true;
    	},
    	//
    	//Calcular precio total de item
    	//
    	calcularPrecioTotal: function(index){
    		var scope = this;

    		var precio_total = typeof this.listaitems != 'undefined' && typeof this.listaitems[index] != 'undefined' ? roundNumber(parseFloat((this.listaitems[index].precio_unidad * this.listaitems[index].cantidad)),2) : 0;

			this.$nextTick(function() {
				//popular precio total de item seleccionado
				if(typeof this.listaitems != 'undefined' && typeof this.listaitems[index] != 'undefined'){
					scope.listaitems[index].precio_total = precio_total;
					scope.listaitems.$set(index, scope.listaitems[index]);
				}
            });
    	}
	},
	computed: {
		disableValidate: function (){
			console.log('LISTA?', this.listaCategoriasItemOptions);
			/*var scope =  this;
			if(typeof this.detalle.cargos_adicionales_checked != 'undefined'){
				return this.detalle.cargos_adicionales_checked === 'true' ? true : false;
			}else{
				return true;
			}*/
		}
	}
});
