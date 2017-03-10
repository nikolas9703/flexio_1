/**
 * Created by Ivan Cubilla on 13/6/16.
 */

var tablaTalleres = (function () {

    var url = 'talleres/ajax-listar';
    var grid_id = "tablaTalleresGrid";
    var grid_obj = $("#tablaTalleresGrid");
    var opcionesModal = $('#opcionesModal');
    var documentosModal = $('#documentosModal');

    var botones = {
        opciones: ".viewOptions",
        buscar: "#searchBtn",
        limpiar: "#clearBtn",
        subirArchivo: ".subirArchivoBtn",
    }

    var tabla = function () {

        //inicializar jqgrid
        grid_obj.jqGrid({
            url: phost() + url,
            datatype: "json",
            colNames: [
                'No. de Equipo',
                'Nombre de Equipo',
                'Colaboradores',
                'Ordenes de trabajo',
                'Estado',
                '',
                '',
            ],
            colModel: [
                {name: 'Codigo', index: 'codigo', width: 50, sortable: false},
                {name: 'Nombre', index: 'nombre', width: 70},
                {name: 'Colaboradores', index: 'colaboradores', width: 40},
                {name: 'Ordenes de trabajo', index: 'ordenes_trabajo', width: 50},
                {name: 'Estado', index: 'estado_id', width: 15},
                {
                    name: 'link',
                    index: 'link',
                    width: 50,
                    sortable: false,
                    resizable: false,
                    hidedlg: true,
                    align: "center"
                },
                {name: 'options', index: 'options', hidedlg: true, hidden: true},
            ],
            mtype: "POST",
            postData: {
                erptkn: tkn
            },
            height: "auto",
            autowidth: true,
            rowList: [10, 20, 50, 100],
            rowNum: 10,
            page: 1,
            pager: "#" + grid_id + "Pager",
            loadtext: '<p>Cargando...</p>',
            hoverrows: false,
            viewrecords: true,
            refresh: true,
            gridview: true,
            multiselect: true,
            sortname: 'codigo',
            sortorder: "DESC",
            beforeProcessing: function (data, status, xhr) {
                //Check Session
                if ($.isEmptyObject(data.session) == false) {
                    window.location = phost() + "login?expired";
                }
            },
            loadBeforeSend: function () {
            },
            beforeRequest: function (data, status, xhr) {
            },
            loadComplete: function (data) {

                //check if isset data
                if (data['total'] == 0) {
                    $('#gbox_' + grid_id).hide();
                    $('#' + grid_id + 'NoRecords').empty().append('No se encontraron Equipos de Trabajo.').css({
                        "color": "#868686",
                        "padding": "30px 0 0"
                    }).show();
                }
                else {
                    $('#' + grid_id + 'NoRecords').hide();
                    $('#gbox_' + grid_id).show();
                }
            },
            onSelectRow: function (id) {
                $(this).find('tr#' + id).removeClass('ui-state-highlight');
            },
        });

        //Al redimensionar ventana
        $(window).resizeEnd(function () {
            tablaTalleres.redimensionar();
        });
    };
    var eventos = function () {
        //Boton de Opciones
        grid_obj.on("click", botones.opciones, function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var id = $(this).attr("data-id");
            // console.log(id);
            var rowINFO = grid_obj.getRowData(id);
            var options = rowINFO["options"];

            //Init Modal
            opcionesModal.find('.modal-title').empty().append('Opciones: ' +  $(rowINFO.Codigo).text()+ '');
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
        });
        
        //Documentos Modal
		$(opcionesModal).on("click", botones.subirArchivo, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			//Cerrar modal de opciones
			opcionesModal.modal('hide');
			
			var equipo_id = $(this).attr("data-id");

			//Inicializar opciones del Modal
			documentosModal.modal({
				backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
				show: false
			});
			
			var scope = angular.element('[ng-controller="subirDocumentosController"]').scope();
		    scope.safeApply(function(){
		    	scope.campos.equipo_id = equipo_id;
		    });
			documentosModal.modal('show');
		});

        $(botones.buscar).on("click", function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            
            var codigo 		= $('#codigo').val();
            var nombre 		= $('#nombre').val();
            var estado_id 	= $('#estado_id').val();

            if(nombre != "" || codigo != "" || estado_id != "")
            {
                //Reload Grid
                grid_obj.setGridParam({
                    url: phost() + url,
                    datatype: "json",
                    postData: {
                        nombre: nombre,
                        codigo: codigo,
                        estado_id: estado_id,
                        erptkn: tkn
                    }
                }).trigger('reloadGrid');
            }

        });
        //Boton de Reiniciar jQgrid
        $(botones.limpiar).on("click", function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();

            recargar();
            limpiarCampos();
        });

    };
    //Limpiar campos de busqueda
    var limpiarCampos = function(){
        $('#buscarEquiposTalleresForm').find('input[type="text"]').prop("value", "");
        $('#buscarEquiposTalleresForm').find('select > option:eq(0)').attr("selected", "selected");
    };
    //Reload al jQgrid
    var recargar = function () {

        //Reload Grid
        grid_obj.setGridParam({
            url: phost() + url,
            datatype: "json",
            postData: {
                codigo: '',
                nombre: '',
                estado_id: '',
                erptkn: tkn
            }
        }).trigger('reloadGrid');
    };
    return {
        init: function () {
            tabla();
            eventos();
        },
        recargar: function () {
            //reload jqgrid
            recargar();
        },
        redimensionar: function () {
            //Al redimensionar ventana
            $(".ui-jqgrid").each(function () {
                var w = parseInt($(this).parent().width()) - 6;
                var tmpId = $(this).attr("id");
                var gId = tmpId.replace("gbox_", "");
                $("#" + gId).setGridWidth(w);
            });
        }
    };
})();

tablaTalleres.init();