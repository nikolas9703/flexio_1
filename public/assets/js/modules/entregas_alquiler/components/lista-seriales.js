var entrega_item = Vue.component('lista-seriales', {

	template: '#lista-seriales',

	props: ['subfila', 'parent_articulo', 'parent_articulos', 'parent_index', 'subparent_index'],

	ready: function () {

		var context = this;
		if(context.vista == 'editar')
		{

			if(context.entrega_alquiler.estado_id > '2')//anulado o terminado
			{
				context.disabledEditar = true;
			}
			else if(context.entrega_alquiler.estado_id == '2')//vigente
			{
				context.disabledEditarTabla = true;
			}
		}

	},

	data: function () {

		return {
			vista:window.vista,
			entrega_alquiler: (window.vista == 'editar') ? JSON.parse(entrega_alquiler) : '',
			disabledEditar:false,
			disabledEditarTabla:false

		};

	},

	computed: {

		getSeries: function () {

			var context = this;
			var series_seleccionadas = [];

			_.forEach(context.parent_articulos, function (fila) {
				_.forEach(fila.detalles, function (subfila) {
					series_seleccionadas.push({serie:subfila.serie, categoria_id:fila.categoria_id, item_id:fila.item_id});
				});
			});

			return _.filter(context.parent_articulo.series, function (serie) {
				aux = _.filter(series_seleccionadas, function(serie_seleccionada){
					return serie_seleccionada.categoria_id == context.parent_articulo.categoria_id && serie_seleccionada.item_id == context.parent_articulo.item_id;
				});
				return _.isEmpty(_.find(aux, function(o){
					return o.serie == serie.codigo;
				})) || serie.codigo == context.subfila.serie;
			});

		}

	},

	methods: {

        //metodo heredado -> no lo realice yo... Francisco Marcano
		cambiarSerie: function (articulo, parent_articulo, index) {

			var context = this;

			$.ajax({
				url: phost() + "entregas_alquiler/ajax-get-serie-ubicacion",
				type: "POST",
				data: {
					erptkn: tkn,
					item_id: parent_articulo.item_id,
					nombre: articulo.serie
				},
				dataType: "json",
				success: function (response) {
					if (!_.isEmpty(response)) {
						articulo.ubicacion_id = response.ubicacion_id;
					}
				}
			});

			//Verificar si esta disponible
			var objeto = _.find(context.ItemsNoDisponibles, function (obj) {
				if (parent_articulo.item_id == obj.item_id && articulo.serie == obj.serie) {
					return obj.serie == articulo.serie;
				}
			});
			if (typeof objeto === "undefined") {
				$('#guardarBtn').attr('disabled', false);
			} else {
				$('#guardarBtn').attr('disabled', true);
				toastr.error('Item no disponible, seleccione otro');
				$("#series" + index).val("");
			}
		}

	}

});
