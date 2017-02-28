<template>
<div>

	<!-- Se muestra cuando no hay registros -->
	<div :id="dom.no_records_id" class="text-center lead"></div>

	<!-- tabla con registros -->
	<table class="table table-striped" :id="dom.grid_id"></table>

	<!-- paginacion de la tabla con registros  -->
	<div :id="dom.pager_id"></div>

</div>

<div class="modal fade" :id="dom.modal_id" tabindex="-1" role="dialog" :aria-labelledby="dom.modal_id" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                </button>
				<h4 class="modal-title"></h4>
			</div>
			<div class="modal-body"></div>
			<div class="modal-footer"></div>
		</div>
	</div>
</div>
</template>

<script>
import listar from '../mixins/listar';

export default {

	mixins: [listar],

	props: {

		config: Object,
		buscar: Object,
		catalogos: Object,
		dom: Object

	},

	data: function () {

		var context = this;
		return {

			jqgrid: {

				url: phost() + 'series/ajax-listar',
				datatype: "json",
				colNames: [
					'No. de serie',
					'Nombre',
					'&Uacute;lt. movimiento',
					'Ubicaci&oacute;n',
					'Estado',
					'',
					''
				],
				colModel: [{
					name: 'nombre',
					index: 'nombre',
					width: 70
				}, {
					name: 'nombre_item',
					index: 'nombre_item',
					width: 70,
					sortable: false
				}, {
					name: 'ult_movimiento',
					index: 'ult_movimiento',
					width: 70,
					sortable: false
				}, {
					name: 'ubicacion',
					index: 'ubicacion',
					width: 70,
					sortable: false
				}, {
					name: 'estado',
					index: 'estado',
					width: 50,
					align: "center",
					sortable: false
				}, {
					name: 'link',
					index: 'link',
					width: 50,
					sortable: false,
					resizable: false,
					hidedlg: true,
					align: "center"
				}, {
					name: 'options',
					index: 'options',
					hidedlg: true,
					hidden: true
				}],
				mtype: "POST",
				postData: {
					erptkn: tkn
				},
				height: "auto",
				autowidth: true,
				rowList: [10, 20, 50, 100],
				rowNum: 10,
				page: 1,
				pager: '#' + context.dom.pager_id,
				loadtext: '<p>Cargando...',
				hoverrows: false,
				viewrecords: true,
				refresh: true,
				gridview: true,
				//multiselect:true,
				sortname: 'nombre',
				sortorder: "DESC",
				beforeProcessing: function (data, status, xhr) {
					context.beforeProcessing(data, status, xhr);
				},
				loadComplete: function (data) {
					context.loadComplete(data);
				},
				onSelectRow: function (id) {
					context.onSelectRow(id, this);
				},

			}

		};

	},

	methods: {

		beforeProcessing: function (data, status, xhr) {
			//Check Session
			if ($.isEmptyObject(data.session) == false) {
				window.location = phost() + "login?expired";
			}
		},

		loadComplete: function (data) {

			var context = this;
			if (data['total'] == 0) {
				$('#gbox_' + context.dom.grid_id).hide();
				$('#' + context.dom.no_records_id).empty().append('No se encontraron Series.').css({
					"color": "#868686",
					"padding": "30px 0 0"
				}).show();
			} else {
				$('#' + context.dom.no_records_id).hide();
				$('#gbox_' + context.dom.grid_id).show();
			}

			$('#' + context.dom.grid_id).closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeader");
			//floating headers
			$('#gridHeader').sticky({
				getWidthFrom: '.ui-jqgrid-view',
				className: 'jqgridHeader'
			});
		},

		onSelectRow: function (id, row) {
			$(row).find('tr#' + id).removeClass('ui-state-highlight');
		},

		viewOptions: function (e, row) {

			var context = this;
			var id = $(row).attr("data-id");
			var rowINFO = $('#' + context.dom.grid_id).getRowData(id);
			var options = rowINFO["options"];

			context.prevent(e);

			//Init boton de opciones
			$('#' + context.dom.modal_id).find('.modal-title').empty().append('Opciones: ' + rowINFO["nombre"] + '');
			$('#' + context.dom.modal_id).find('.modal-body').empty().append(options);
			$('#' + context.dom.modal_id).find('.modal-footer').empty();
			$('#' + context.dom.modal_id).modal('show');

		},

		resizeEnd: function () {
			$(".ui-jqgrid").each(function () {
				var w = parseInt($(this).parent().width()) - 6;
				var tmpId = $(this).attr("id");
				var gId = tmpId.replace("gbox_", "");
				$("#" + gId).setGridWidth(w);
			});
		},

		prevent: function (e) {
			e.preventDefault();
			e.returnValue = false;
			e.stopPropagation();
		}

	},

	ready: function () {

		var context = this;
		$('#' + context.dom.grid_id).jqGrid(context.jqgrid);

		// Redimensioanr Grid al cambiar tamanio de la ventanas.
		$(window).resizeEnd(function () {
			context.resizeEnd();
		});

		// Boton de opciones
		$('#' + context.dom.grid_id).on("click", ".viewOptions", function (e) {
			context.viewOptions(e, this);
		});

		$('#' + context.dom.modal_id).on("click", ".subirArchivoBtn", function (e) {
			context.prevent(e);

			//Cerrar modal de opciones
			$('#' + context.dom.modal_id).modal('hide');
			var serie_uuid = $(this).attr("data-id");

			//Inicializar opciones del Modal
			$('#documentosModal').modal({
				backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
				show: false
			});

			var scope = angular.element('[ng-controller="subirDocumentosController"]').scope();

			scope.safeApply(function () {
				scope.campos.serie_uuid = serie_uuid;
			});
			$('#documentosModal').modal('show');
		});

	}

}
</script>
