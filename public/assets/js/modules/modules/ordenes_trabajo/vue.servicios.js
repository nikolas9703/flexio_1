var deleteServicios = [];
Vue.component('servicios', {
	template:'#servicios',
	props:{
		categorias: Array,
		index: Number,
		servicio: Object,
		//lista_servicios: Array,
		//mostrarCategoriaServicio:false

    },
	data:function(){
		return {
			listaCategoriasItemOptions: typeof listaCategoriasItemArray != 'undefined' ? $.parseJSON(listaCategoriasItemArray) : [],
	    listaEquiposTrabajoOptions: typeof listaEquiposTrabajoArray != 'undefined'  ? $.parseJSON(listaEquiposTrabajoArray) : [],

		};
	},
	ready: function(){

		
		//
		// Inicializar jQuery Input Mask plugin
		//
		//Primero verificar si existe la funcion, para evitar errores de js
		if (typeof $.fn.inputmask !== 'undefined' && $.isFunction($.fn.inputmask)) {
			if($(':input[data-inputmask]').attr('class') != undefined){
				setTimeout(function(){
					$(':input[data-inputmask]').inputmask();
				}, 400);
			}
		}
		//this.tipoSevicioSelect();
	},
	methods:{
		agregarServicio: function(){
            var scope =  this;
            var listaItems = [{id: '', categoria_id:'', cantidad:1, impuesto_uuid:'', impuesto_porcentaje:'', precio_unidad:'', precio_total:'', impuestos: typeof impuestos != 'undefined' ? impuestos : [], items: [], atributos:[],  unidades:[], cuentas: typeof cuentas != 'undefined' ? cuentas : []}];
            this.$nextTick(function () {
            	scope.listaservicios.push({id: '', categoria_id:'', item_id: '', serie_id: '', equipo_id: '', itemseleccionado:'', verificando_capacidad: '', itemsservicio:[], items: listaItems, series:[]});
            });

            //this.$parent.agregarServicio();
    	},
    	eliminarServicio: function(index){

    		var modal = $('#opcionesModal');
            var id = this.listaservicios[index]['id'];

            if(typeof id != 'undefined' && id != '') {

            	var botones = [
	         	   '<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>',
	         	   '<button class="btn btn-w-m btn-danger" type="button" id="elimiarServicioBtn">Eliminar</button>'
	         	].join('\n');

            	//Modal
            	this.$root.modal.titulo = 'Confirme';
            	this.$root.modal.contenido = '<p>&#191;Esta seguro que desea eliminar?</p><div class="alert alert-warning">El servicio ser&aacute; eliminado al guardar los cambios de la orden.</div>';
            	this.$root.modal.footer = botones;

				modal.modal('show');
				modal.on('click', '#elimiarServicioBtn', {id: id, index: index}, this.eliminar);

           }else{
        	 //eliminar fila
               this.listaservicios.splice(index, 1);
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
            var existe = _.find(deleteServicios, function(servicio){
				return servicio == id;
            });

            if(typeof existe != 'undefined'){
            	return;
            }

            //agregar a array de items a eliminar
            deleteServicios.push(id);
            this.$root.delete_servicios = deleteServicios;

            //eliminar fila
            this.listaservicios.splice(index,1);

            //Verificar si no quedan mas piezas
 	   		//e introducir una pieza en blanco
 	   		if(this.listaservicios.length == 0){
 	   			scope.$nextTick(function () {
     	   			scope.agregarServicio({});
 	   			});
 	   		}
    	},
    	popularItems: function(e, servicio){
    		e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var categoria_id = $(e.currentTarget).find('option:selected').val();

            if(categoria_id==""){
            	servicio.itemseleccionado = '';
            	servicio.itemsservicio = [];
            	return false;
            }

            var response = this.$root.ajax('ordenes_trabajo/ajax-seleccionar-items-serializados', {categoria_id: categoria_id});
    		response.then(function (response) {

    			//popular lisatdo de items
    			servicio.itemsservicio = !_.isEmpty(response.data.items) ? response.data.items : [];

    			//reiniciar series
    			servicio.series = [];

            }, (response) => {
                //console.log('FALLO',response);
            });
    	},
    	popularSerial: function(e, servicio){
    		e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var item_id = $(e.currentTarget).find('option:selected').val();

            if(item_id==""){
            	servicio.itemseleccionado = '';
            	return false;
            }

            var item = _.find(servicio.itemsservicio, function(item){
                return item.id == item_id;
            });

            servicio.itemseleccionado = typeof item != 'undefined' ? item.nombre : '';

            servicio.series = !_.isEmpty(item) ? item.seriales : [];
    	},
    	validarCapacidadAtencion: function(e, servicio){

    		var equipo_id = $(e.currentTarget).find('option:selected').val();

    		if(equipo_id==''){
    			servicio.verificando_capacidad = '';
    			return false;
    		}

    		servicio.verificando_capacidad = '<i class="fa fa-cog fa-spin fa-fw"></i> Verificando capacidad de atenci&oacute;n de ordenes.';

    		var ajax = this.$root.ajax('ordenes_trabajo/ajax-get-equipotrabajo-info', {equipo_id: equipo_id});
    		ajax.then(function(response) {

     			if(response.data.info.ordenes_trabajo.length >= response.data.info.ordenes_atender){
     				servicio.equipo_id = '';
     				servicio.verificando_capacidad = 'El equipo no puede ser seleccionado ya cuenta con la capacidad m&aacute;xima de &oacute;rdenes a atender.';
     			}else{
     				servicio.verificando_capacidad = '';
     			}

            });
    	},
		/*tipoSevicioSelect: function () {

			var tipo = $('#tipo_orden_id').find('option:selected').val();
			if(tipo === '1'){
				this.mostrarCategoriaServicio = false;
			}else{
				this.mostrarCategoriaServicio = true;
			}
			console.log(tipo);
			//return true;
		}*/
	}
});
