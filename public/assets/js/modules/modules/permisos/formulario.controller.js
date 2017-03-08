/**
 * Servicio Permisos
 */
bluapp.service('permisosService', ['$http', '$document', '$rootScope', function ($http, $document, $rootScope) {

    var scope = this;
    var requisito_id = '';
    var formulario = '#permisosForm';

    //Funcion para ejecurtar ajax
    this.ajax = function (url, data) {

        return $http({
            method: 'POST',
            url: url,
            data: $.param($.extend({erptkn: tkn}, data)),
            cache: false,
            xsrfCookieName: 'erptknckie_secure',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        });
    };

    //Funcion para inicializar plugins
    this.init = function () {

        setTimeout(function () {

            //Plugin Datepicker
            $(formulario).find('#fecha_desde').daterangepicker({
                singleDatePicker: true,
                autoUpdateInput: false,
                format: 'MM-DD-YYYY',
                showDropdowns: true,
                opens: "left",
                locale: {
                    applyLabel: 'Seleccionar',
                    cancelLabel: 'Cancelar',
                    daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                    firstDay: 1
                }
            }).on('apply.daterangepicker', function (ev, picker) {
                $(formulario).find('#fecha_desde').val(picker.startDate.format('DD/MM/YYYY'));
            });
            $(formulario).find('#fecha_hasta').daterangepicker({
                singleDatePicker: true,
                autoUpdateInput: false,
                format: 'MM-DD-YYYY',
                showDropdowns: true,
                opens: "left",
                locale: {
                    applyLabel: 'Seleccionar',
                    cancelLabel: 'Cancelar',
                    daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                    firstDay: 1
                }
            }).on('apply.daterangepicker', function (ev, picker) {

                $(formulario).find('#fecha_hasta').val(picker.startDate.format('DD/MM/YYYY'));
            });

            //Validacion
            $.validator.setDefaults({
                errorPlacement: function (error, element) {
                    return true;
                }
            });
            $(formulario).validate({
                focusInvalid: true,
                ignore: '',
                wrapper: ''
            });

        }, 600);
    };

    //Funcion para inicializar plugins
    this.actualizar_chosen = function () {

        //refresh chosen
        setTimeout(function () {
            $(formulario).find('select.chosen-select').trigger('chosen:updated');
        }, 50);
    };

}]);


/**
 * Provider Directova ng-flow
 */
bluapp.config(['flowFactoryProvider', function (flowFactoryProvider) {

    flowFactoryProvider.factory = fustyFlowFactory;

    flowFactoryProvider.defaults = {
        permanentErrors: [404, 500, 501],
        maxChunkRetries: 1,
        chunkRetryInterval: 5000,
        progressCallbacksInterval: 100,
        singleFile: true,
        query: {
            erptkn: tkn
        },
        testMethod: 'POST',
        testChunks: false,
        uploadMethod: 'POST'
    };
}]);


/**
 * Controlador Formulario de Incapacidades
 */
bluapp.controller("PermisosController", function ($rootScope, $scope, $document, $http, $compile, permisosService) {

    var formulario = '#permisosForm';
    $scope.fileClassBtn = 'btn-default';

    $scope.fileBtnPermision = 'Seleccione';
    $scope.guardarBtn = 'Guardar';
    $scope.disabledBtn = '';

    //Inicializar variables scope
    $scope.permiso = {
        erptkn: tkn,
        id: "",
        tipo_permiso_id: "",
        fecha_desde: "",
        fecha_hasta: "",
        cuenta_pasivo_id: "",
        estado_id: "",
        observaciones: "",
        constancia_permiso: "",
        documento: "",
        colaborador_id: window.location.href.match(/(colaboradores)/g) != null ? (typeof colaborador_id != 'undefined' ? colaborador_id : '') : ""
    };

    var filesList = [], paramNames = [];

    $(formulario).find('#documento').fileupload({
        url: phost() + "permisos/ajax-guardar-permiso",
        type: 'POST',
        dataType: 'json',
        autoUpload: false,
        singleFileUploads: true,
        //acceptFileTypes: /(\.|\/)(gif|jpe?g|png|mp4|mp3)$/i,
        add: function (e, data) {

            $scope.permiso.documento = data.files[0]["name"];

            //Agregar archivo seleccionado al array
            for (var i = 0; i < data.files.length; i++) {
                filesList.push(data.files[i]);
                paramNames.push(e.delegatedEvent.target.name);
            }
        },
        done: function (e, data) {

            if ($.isEmptyObject(data.result.session) == false) {
                window.location = phost() + "login?expired";
            }

            //Verificar si el formulario esta siendo usado desde
            //Ver Detalle de Colaborador
            if (window.location.href.match(/(colaboradores)/g)) {

                //mostrar mensaje
                toastr.success(response.data.mensaje);

                //recargar tabla
                tablaAccionPersonal.recargar();

                //Limpiar formulario
                $scope.limpiarFormulario();

            } else {
                if (data.result.guardado == true) {
                    window.location = phost() + 'accion_personal/listar';
                }
            }
        }
    });

    //Inicializar campos, plugins y validacion
    permisosService.init();

    //Funcion popular formulario
    //con informacion de la licencia seleccionada.
    $scope.popularFormulario = function () {
        //Before using local storage, check browser support for localStorage and sessionStorage
        if (typeof(Storage) !== "undefined") {
            //Verificar si existe la variable
            //proveniente de Local Storage
            if (localStorage.getItem("permiso_id")) {

                var permiso_id = localStorage.getItem("permiso_id");

                //Buscar datos para popular campos
                permisosService.ajax(phost() + "permisos/ajax-seleccionar-permiso", {
                    id: permiso_id,
                }).then(function successCallback(response) {

                    //Check Session
                    if ($.isEmptyObject(response.data.session) == false) {
                        window.location = phost() + "login?expired";
                    }

                    $scope.permiso.cuenta_pasivo_id = response.data.cuenta_pasivo_id.toString();
                    $scope.permiso.estado_id = response.data.estado_id.toString();
                    $scope.permiso.tipo_permiso_id = response.data.tipo_permiso_id.toString();
                    $scope.permiso.fecha_desde = response.data.fecha_desde;
                    $scope.permiso.fecha_hasta = response.data.fecha_hasta;
                    $scope.permiso.constancia_permiso = response.data.constancia_permiso;
                    $scope.permiso.observaciones = response.data.observaciones;
                    $scope.permiso.colaborador_id = response.data.colaborador_id;
                    $scope.permiso.id = permiso_id;

                    if (window.location.href.match(/(accion_personal)/g)) {

                        //seleccionar el colaborador en el dropdown
                        //que aparece en la barra superior
                        $('select#colaborador_id').find('option[value="' + response.data.colaborador_id + '"]').prop('selected', 'selected');

                        //actualizar chosen barra accion personal
                        accionPersonal.actualizar_chosen();
                    }

                    //actualizar chosen
                    permisosService.actualizar_chosen();

                }, function errorCallback(response) {
                    // called asynchronously if an error occurs
                    // or server returns response with an error status.
                });

                //Borrar variable de localstorage
                localStorage.removeItem("permiso_id");
            }
        }
    };

    /**
     * Seleccionar documento formulario permisos
     * @param $file
     * @param e
     * @param $flow
     */
    $scope.archivoSeleccionado = function ($file, e, $flow) {
        e.preventDefault();
        //agregar texto de archivo seleccionado.
        $scope.fileClassBtn = 'btn-default';
        $scope.fileBtnPermision = '<i class="fa fa-upload"></i> 1 archivo seleccionado';
    };

    //Ejecutar funcion
    $scope.popularFormulario();

    //evento: seleccion de archivo
    $scope.limpiarFormulario = function (e, $flow) {
        if (typeof e != 'undefined') {
            e.preventDefault();
        }

        $scope.permiso = {
            erptkn: tkn,
            id: "",
            tipo_permiso_id: "",
            fecha_desde: "",
            fecha_hasta: "",
            cuenta_pasivo_id: "",
            estado_id: "",
            observaciones: "",
            constancia_permiso: "",
            documento: "",
            colaborador_id: typeof colaborador_id != 'undefined' ? colaborador_id : ''
        };

        $(formulario).find('input[type="text"], input[type="checkbox"]').val('').removeAttr("checked");
        $(formulario).find('textarea').empty().val('');

        if (typeof $flow != 'undefined') {
            //Cancelar upload de archivo
            $flow.cancel();
        }

        //campos select
        $scope.limpiar_seleccion_dropdown();

        //Botones
        $scope.fileBtnPermision = 'Seleccione';
        $scope.fileClassBtn = 'btn-default';
        $scope.guardarBtn = 'Guardar';
        $scope.disabledBtn = '';

        //refresh chosen
        permisosService.actualizar_chosen();
    };

    //Limpiar seleccion de campo: dropdown
    $scope.limpiar_seleccion_dropdown = function (campo) {
        $(formulario).find(campo).attr("disabled", "disabled").empty().append('<option value="">Seleccione</option>').find('option:eq(0)').attr("selected", "selected");

        setTimeout(function () {
            permisosService.actualizar_chosen();
        }, 300);
    };

    /**
     * Funcion Cancelar Formulario
     */
    $scope.cancelar = function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        //Verificar si el formulario esta siendo usado desde
        //Ver Detalle de Colaborador
        if (window.location.href.match(/(colaboradores)/g)) {

            //recargar tabla
            tablaAccionPersonal.recargar();

            //Limpiar formulario
            $scope.limpiarFormulario();

        } else {
            window.location = phost() + 'accion_personal/listar';
        }
    };

    /**
     * Funcion Guardar Formulario
     */
    $scope.guardar = function (e, $flow) {
        e.preventDefault();

        if (!$(formulario).validate().form()) {
            toastr.warning('Todos los campos marcados son requeridos.');
            return;
        }

        //var colaboradorid = $(formulario).find('input[id="campo[colaborador_id]"]').val() != "" ? $(formulario).find('input[id="campo[colaborador_id]"]').val() : $scope.permiso.colaborador_id != "" ? $scope.permiso.colaborador_id : "";
        var colaboradorid = $(formulario).find('input[id="campo[colaborador_id]"]').val() != "" ? $(formulario).find('input[id="campo[colaborador_id]"]').val() : $scope.permiso.colaborador_id;
        var url = phost() + "permisos/ajax-guardar-permiso";
        //verificar que alla seleccionado
        //un colaborador de la barra de filtro
        if (colaboradorid == "" || colaboradorid == undefined) {
            toastr.warning('Debe seleccionar un colaborador.');
            return false;
        }
        //Agregar datos extras del formulario
        //al upload de documentos.
        formData = {
            erptkn: tkn,
            permiso_id: typeof permiso_id != "undefined" ? permiso_id : $scope.permiso.id,
            colaborador_id: colaboradorid,
            tipo_permiso_id: $scope.permiso.tipo_permiso_id,
            fecha_desde: $(formulario).find('#fecha_desde').val(),
            fecha_hasta: $(formulario).find('#fecha_hasta').val(),
            cuenta_pasivo_id: $scope.permiso.cuenta_pasivo_id,
            estado_id: $scope.permiso.estado_id,
            observaciones: $scope.permiso.observaciones,
            constancia_permiso: $scope.permiso.constancia_permiso
        };
        //Verificar si ha seleccionado
        //o no algun archivo
        if ($flow.files.length == 0) {
            //Enviar datos por http/ajax
            permisosService.ajax(url, formData).then(function successCallback(response) {
                //Check Session
                if ($.isEmptyObject(response.data.session) == false) {
                    window.location = phost() + "login?expired";
                }
                //Verificar si el formulario esta siendo usado desde
                //Ver Detalle de Colaborador
                if (window.location.href.match(/(colaboradores)/g)) {
                    //mostrar mensaje
                    toastr.success(response.data.mensaje);
                    //recargar tabla
                    tablaAccionPersonal.recargar();
                    //Limpiar formulario
                    $scope.limpiarFormulario();
                } else {
                    if (response.data.guardado == true) {
                        window.location = phost() + 'accion_personal/listar';
                    }
                }
            }, function errorCallback(response) {
                // called asynchronously if an error occurs
                // or server returns response with an error status.
            });
        } else {
            //Sobreescribir extra datos
            $flow.opts.target = url;
            $flow.opts.query = formData;
            //Subir archivo
            $flow.upload();
            //Evento: al completar subida de archivo.
            $flow.on('fileSuccess', function (file, message, chunk) {
                //response
                var response = $.parseJSON(message);
                //Check Session
                if ($.isEmptyObject(response.session) == false) {
                    window.location = phost() + "login?expired";
                }
                //Verificar si el formulario esta siendo usado desde
                //Ver Detalle de Colaborador
                if (window.location.href.match(/(colaboradores)/g)) {
                    //mostrar mensaje
                    toastr.success(response.data.mensaje);
                    //recargar tabla
                    tablaAccionPersonal.recargar();
                    //Limpiar formulario
                    $scope.limpiarFormulario();
                } else {
                    if (response.guardado == true) {
                        window.location = phost() + 'accion_personal/listar';
                    }
                }
            });

        }
    };
});