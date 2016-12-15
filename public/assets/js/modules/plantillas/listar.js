//Listar Plantillas
var listarPlantillas = (function () {

    var grid_obj = $("#tablaPlantillasGrid");
    var opcionesCrearModal = $('#opcionesCrearModal');
    var crearPlantillaForm = $('#crearPlantillaForm');
    var exportar= $("#exportarPlanillasBtn");
    var botones = {
        crearPlantillaBtn: ".ocionesCrearBtn",
        opcionesCrearPlantillaBtn: "a[data-plantilla-url]",
        exportar: "#exportarPlanillasBtn"
    }; 
  
    //HTML Botones del Modal
    var botones_modal = ['<div class="row">',
        '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
        '<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>',
        '</div>',
        '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
        '<button id="pagarAccionPersonalModalBtn" class="btn btn-w-m btn-primary btn-block" type="button">Confirmar</button>',
        '</div>',
        '</div>'
    ].join('\n');

    /**
     * Generar los botones que se mostraran en
     * el modal para crear plantillas
     */
    var generar_botones = function () {

        //Listado de plantillas agrupadas por tipo
        var grupos = $.parseJSON(grupo_plantillas);

        if (_.isEmpty(grupos) == true) {
            return false;
        }

        var html = [];
        $.each(grupos, function (index, plantillas) {
            var grupo_name = index;
            var grupo_collapse_id = index.replace(/(cartas)/gi, 'carta').replace(/(adendas)/gi, 'adenda').replace(/\s/gi, '-');
            var grupo_collapse_name = ucFirst(index.replace(/(cartas)/gi, 'carta').replace(/(adendas)/gi, 'adenda'));

            //Botones Acordion - Agrupados
            if (plantillas.length > 1) {

                //Agrupador
                html = _.concat(html, ['<a class="btn btn-block btn-outline btn-success m-b-xs" href="#collapse-' + grupo_collapse_id + '" data-toggle="collapse">' + grupo_name + '</a>']);
                html = _.concat(html, ['<div id="collapse-' + grupo_collapse_id + '" class="collapse">']);
                html = _.concat(html, ['<ul class="list-group clear-list">']);

                //Botones del agrupador
                $.each(plantillas, function (indx, plantilla) {
                    var plantilla_url = (grupo_collapse_id + '-' + plantilla['nombre']).replace(/\s/gi, '-');
                    html = _.concat(html, ['<li class="m-sm"><a href="#" data-plantilla-url="' + plantilla_url + '" data-id="' + plantilla['id'] + '">' + grupo_collapse_name + ' - ' + ucFirst(plantilla['nombre']) + '</a></li>']);
                });

                html = _.concat(html, ['</ul>']);
                html = _.concat(html, ['</div>']);

            } else {
                //Boton sin grupo
                html = _.concat(html, ['<a class="btn btn-block btn-outline btn-success" href="#" data-plantilla-url="' + grupo_collapse_id.toLowerCase() + '" data-id="' + plantillas[0]['id'] + '">' + grupo_name + '</a>']);
            }
        });

        return html.join('\n');
    };

    //Inicializar Eventos de Botones
    var eventos = function () {

        //Boton Crear
        $('#moduloOpciones').on("click", botones.crearPlantillaBtn, function (e) {
            console.log("Crear");
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            //Inicializar opciones del Modal
            opcionesCrearModal.modal({
                backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
                show: false
            });

            var botones = generar_botones();

            opcionesCrearModal.find('.modal-title').empty().append('Plantillas: Crear');
            opcionesCrearModal.find('.modal-body').empty().append(botones);
            opcionesCrearModal.find('.modal-footer').empty();
            opcionesCrearModal.modal('show');
        });

        //Opciones de Modal Crear Plantillas
        opcionesCrearModal.on("click", botones.opcionesCrearPlantillaBtn, function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            opcionesCrearModal.modal('hide');

            var id = $(this).attr('data-id');
            var plantilla = $(this).attr('data-plantilla-url');

            //Limpiar formulario
            crearPlantillaForm.find('input[name="plantilla_id"]').remove();
            crearPlantillaForm.attr("action", phost() + 'plantillas/crear/' + plantilla + '/' + id);
            crearPlantillaForm.append('<input type="hidden" name="plantilla_id" value="' + id + '" />');
            crearPlantillaForm.submit();
            //window.location = phost() + 'plantillas/crear/'+ plantilla;
        });
        exportar.on("click", function (e) {
            //console.log('exportar');
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            //Exportar Seleccionados del jQgrid
            var ids = [];
            ids = $('#tablaPlantillasGrid').jqGrid('getGridParam', 'selarrrow');

            //Verificar si hay seleccionados
            if (ids.length > 0) {

                $('#ids').val(ids);
                $('form#exportarPlantillaForm').submit();
                $('body').trigger('click');
                //window.location = phost() + 'plantillas/ajax-listar';
            }
        });
    };

    return{
        init: function () {
            eventos();
        }
    };
})();

listarPlantillas.init();