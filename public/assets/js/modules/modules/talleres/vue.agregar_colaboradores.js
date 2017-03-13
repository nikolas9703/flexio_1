Vue.http.options.emulateJSON = true;
Vue.component('agregar_colaboradores', {
	template:'#agregar_colaboradores',
	data:function(){
		return {
			colaboradoresOptions: typeof colaboradoresArray != 'undefined' ? $.parseJSON(colaboradoresArray) : [],
			colaboradoresSeleccionadosOptions: [],
		};
	},
	ready:function(){

		//Inicializar plugin
		$('.multiselectField').multiselect();

		//verificar si existe boton accion
		if($('#moduloOpciones').find('#agregarColaboradorLnk')){
			//agregar evento a boton de accion
			$('#moduloOpciones').find('#agregarColaboradorLnk').on('click', this.abrirModal);
		}
	},
	methods:{
		abrirModal: function() {
			//mostrar modal
			$('#agregarColaboradoresModal').modal('show');
		},
		guardar: function() {

			var scope = this;

			//verificar si hay seleccionados
			if($('#lista_colaboradores_to option:selected').length <= 0){
				return false;
			}

			var colaboradores = [];
			$('#lista_colaboradores_to > option').each(function(option) {
				colaboradores.push(this.value);
			});

			//ajax guardar
			Vue.http({
                url: phost() + 'talleres/ajax-guardar-colaboradores',
                method: 'POST',
                headers: {
                    erptkn: tkn,
                },
                data: {
                	erptkn: tkn,
                	id: equipoID,
                	to: colaboradores
                }
            }).then(function (response) {
                //Check Session
                if ($.isEmptyObject(response.data.session) == false) {
                    window.location = phost() + "login?expired";
                }

                //mensaje
                toastr.success('Se han agregado los colaboradores satisfactoriamente.');

                //actualizar lista
                scope.colaboradoresOptions = typeof response.data.colaboradores != 'undefined' ? $.parseJSON(response.data.colaboradores) : [];

                //recargar grid colaboradores
                tablaColaboradores.recargar();

                //ocultar modal
                $('#agregarColaboradoresModal').modal('hide');

            }, function (response) {
                // error callback
            });
		}
	},
	watch: {
	}
});
