//Tabla Accion de Personal
var tablaAccionPersonal = (function () {
 
    var url = 'plantillas/ajax-listar';
    var grid_id = "tablaPlantillasGrid";
    var grid_obj = $("#tablaPlantillasGrid");
    var opcionesModal = $('#opcionesModal');

    var botones = {
        opciones: ".viewOptions",
        detalle: ".verDetalle",
        buscar: "#searchBtn",
        limpiar: "#clearBtn",
        descargar: ".descargarAdjuntoBtn",
        exportar: "#exportarPlanillasBtn"
    };

    var tabla = function () {
        
        var ocultar_opciones = false; 
        //icialización de jqgrid de plantilla
        grid_obj.jqGrid({
            url: phost() + url,
            datatype: "json",
            colNames: [
                'No. de plantilla',
                'Nombre de plantilla',
                'Fecha de creaci&oacute;n',                
                '',
                ''
            ],
            colModel: [
                {name: 'No. de plantilla', index: 'codigo', width: 40},
                {name: 'Nombre de plantilla', index: 'nombre_plantilla', width: 65},
                {name: 'Fecha de creacion', index: 'fecha_creacion', width: 50},
                {name: 'link', index: 'link', width: 40, sortable: false, resizable: false, hidedlg: true, align: "center", hidden: ocultar_opciones},
                {name: 'options', index: 'options', hidedlg: true, hidden: true}
            ],
            mtype: "POST",
            postData: {
                erptkn: tkn,
                colaborador_id : colaborador_id != 'undefined' ? colaborador_id : ''
                
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
	    sortorder: "ASC",
            beforeProcessing: function (data, status, xhr) {
                //Check Session
                if ($.isEmptyObject(data.session) == false) {
                    window.location = phost() + "login?expired";
                }
            },
            loadBeforeSend: function () {},
            beforeRequest: function (data, status, xhr) {},
            loadComplete: function (data) {
                // console.log(data);
                //check if isset data
                if (data['total'] == 0) {
                    $('#gbox_' + grid_id).hide();
                    $('#' + grid_id + 'NoRecords').empty().append('No se encontraron datos de la Planilla.').css({"color": "#868686", "padding": "30px 0 0"}).show();
                } else {
                    $('#' + grid_id + 'NoRecords').hide();
                    $('#gbox_' + grid_id).show();
                }
            },
            onSelectRow: function (id) {
                $(this).find('tr#' + id).removeClass('ui-state-highlight');
            }

        });
        //Al redimensionar ventana
        $(window).resizeEnd(function () {
            tablaAccionPersonal.redimensionar();
        });

    };

    //Inicializar Eventos de Botones
    var eventos = function () {

        //Boton de Opciones
        grid_obj.on("click", botones.opciones, function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var id = $(this).attr("data-id");

            var rowINFO = grid_obj.getRowData(id);
            var options = rowINFO["options"];
            options = options.replace(/0000/gi, id);

            //Init Modal
            opcionesModal.find('.modal-title').empty().append('Opciones: ' + rowINFO["Nombre de plantilla"] + '');
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
        });

        //Ver Detalle
        grid_obj.on("click", botones.detalle, function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            //Cerrar modal de opciones
            opcionesModal.modal('hide');

            var formulario = $(this).attr("data-formulario");
            var accion_id = $(this).attr("data-accion-id");
            var modulo_name_id = formulario.replace(/(es|s)$/g, '') + '_id';

            //Before using local storage, check browser support for localStorage and sessionStorage
            if (typeof (Storage) !== "undefined") {

                //Grabar id de la accion
                localStorage.setItem(modulo_name_id, accion_id);
            }

            //Verificar si existe o no variable
            //colaborador_id
            if (typeof colaborador_id != 'undefined') {

                //Verificar si el formulario esta siendo usado desde
                //Ver Detalle de Colaborador
                if (window.location.href.match(/(colaboradores)/g)) {

                    var scope = angular.element('[ng-controller="' + ucFirst(formulario) + 'Controller"]').scope();
                    scope.popularFormulario();
                }

            } else {
                window.location = phost() + 'accion_personal/crear/' + formulario;
            }
        });

        //Ver Detalle
        $(opcionesModal).on("click", botones.detalle, function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            //Cerrar modal de opciones
            opcionesModal.modal('hide');

            var formulario = $(this).attr("data-formulario");
            var accion_id = $(this).attr("data-accion-id");
            var modulo_name_id = formulario.replace(/(es|s)$/g, '') + '_id';

            //Before using local storage, check browser support for localStorage and sessionStorage
            if (typeof (Storage) !== "undefined") {

                //Grabar id de la accion
                localStorage.setItem(modulo_name_id, accion_id);
            }

            //Verificar si existe o no variable
            //colaborador_id
            if (typeof colaborador_id != 'undefined') {

                //Verificar si el formulario esta siendo usado desde
                //Ver Detalle de Colaborador
                if (window.location.href.match(/(colaboradores)/g)) {

                    var scope = angular.element('[ng-controller="' + ucFirst(formulario) + 'Controller"]').scope();
                    scope.popularFormulario();

                    //Activar Tab
                    //$('#moduloOpciones').find('ul').find("a:contains('"+ formulario.replace(/(es|s)$/g, '') +"')").trigger('click');
                    $('#moduloOpciones').find('ul').find('a[href*="' + formulario.replace(/(es|s)$/g, '') + '"]').trigger('click');
                    //console.log( formulario.replace(/(es|s)$/g, '') );
                }

            } else {
                window.location = phost() + 'accion_personal/crear/' + formulario;
            }
        });

        //Boton de Descargar Evaluacion
        opcionesModal.on("click", botones.descargar, function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var evaluacion_id = $(this).attr("data-id");
            var rowINFO = grid_obj.getRowData(evaluacion_id);

            var no_accion = rowINFO["No. Accion personal"];
            var archivo_nombre = rowINFO["archivo_nombre"];
            var archivo_ruta = rowINFO["archivo_ruta"];
            var fileurl = phost() + archivo_ruta + '/' + archivo_nombre;

            if (archivo_nombre == '' || archivo_nombre == undefined) {
                return false;
            }

            if (rowINFO["Tipo de accion personal"].match(/incapacidades/ig)) {

                var archivos = $.parseJSON(archivo_nombre);
                if (archivos.length > 1) {

                    //inicializar plugin
                    var zip = new JSZip();

                    //recorrer arreglo de archivos y agregarlos al zip
                    $.each(archivos, function (i, filename) {
                        fileurl = phost() + archivo_ruta + '/' + filename;
                        zip.file(filename, urlToPromise(fileurl), {binary: true});
                    });

                    // when everything has been downloaded, we can trigger the dl
                    zip.generateAsync({type: "blob"}, function updateCallback(metadata) {
                        //console.log( metadata.percent );
                    }).then(function callback(blob) {
                        //see FileSaver.js
                        saveAs(blob, $(no_accion).text() + ".zip");
                    }, function (e) {
                        //console.log(e);
                    });
                    return false;

                } else {

                    fileurl = phost() + archivo_ruta + '/' + archivos;

                    //Descargar archivo
                    downloadURL(fileurl, archivo_nombre);
                }

                console.log(archivos);

            } else {
                //Descargar archivo
                downloadURL(fileurl, archivo_nombre);
            }

            //Ocultar modal
            //opcionesModal.modal('hide');
        });

        //Boton de Buscar
        $('#buscarPlantillasForm').on("click", botones.buscar, function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            buscar();
        });

        //Boton de Reiniciar jQgrid
        $('#buscarPlantillasForm').on("click", botones.limpiar, function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            recargar();
            limpiarCampos();
        });

        //jQuery Daterange
        $('#buscarPlantillasForm').find("#fecha1").datepicker({
            //defaultDate: "+1w",
            showDropdowns: true,
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            numberOfMonths: 1,
            singleDatePicker: true,
            onClose: function (selectedDate) {
                $('#buscarPlantillasForm').find("#fecha2").datepicker("option", "minDate", selectedDate);
            }
        });
        $('#buscarPlantillasForm').find("#fecha2").datepicker({
            //defaultDate: "+1w",
            showDropdowns: true,
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            numberOfMonths: 1,
            singleDatePicker: true,
            onClose: function (selectedDate) {
                $('#buscarPlantillasForm').find("#fecha1").datepicker("option", "maxDate", selectedDate);
            }
        });
        grid_obj.on("click", botones.exportar, function (e) {
            console.log('exportar');
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
            }
        });
    };

    /**
     * Fetch the content and return the associated promise.
     * @param {String} url the url of the content to fetch.
     * @return {Promise} the promise containing the data.
     */
    var urlToPromise = function (url) {
        return new Promise(function (resolve, reject) {
            JSZipUtils.getBinaryContent(url, function (err, data) {
                if (err) {
                    reject(err);
                } else {
                    resolve(data);
                }
            });
        });
    };

    //Reload al jQgrid
    var recargar = function () {

        //Reload Grid
        grid_obj.setGridParam({
            url: phost() + url,
            datatype: "json",
            postData: {
                plantilla_tipo: '',
                fecha_desde: '',
                fecha_hasta: '',
                estado: '',
                erptkn: tkn
            }
        }).trigger('reloadGrid');
    };

    //Buscar cargo en jQgrid
    var buscar = function () {

        var plantilla_tipo = $('#buscarPlantillasForm').find('#plantilla_id').val();
        var fecha_desde = $('#buscarPlantillasForm').find('#fecha1').val();
        var fecha_hasta = $('#buscarPlantillasForm').find('#fecha2').val();
        var estado = $('#buscarPlantillasForm').find('#tipo_accion').val();

        if (plantilla_tipo != "" || fecha_desde != "" || fecha_hasta != "" || estado != "")
        {
            //Reload Grid
            grid_obj.setGridParam({
                url: phost() + url,
                datatype: "json",
                postData: {
                    plantilla_tipo: plantilla_tipo,
                    fecha_desde: fecha_desde,
                    fecha_hasta: fecha_hasta,
                    estado: estado,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }
    };

    //Limpiar campos de busqueda
    var limpiarCampos = function () {
        $('#buscarPlantillasForm').find('input[type="text"]').prop("value", "");
        $('#buscarPlantillasForm').find('select').find('option:eq(0)').prop("selected", "selected");
        actualizar_chosen();
    };

    var actualizar_chosen = function () {
        //refresh chosen
        setTimeout(function () {
            $('#buscarPlantillasForm').find('select.chosen-select').trigger('chosen:updated');
        }, 50);
    };

    return{
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

tablaAccionPersonal.init();