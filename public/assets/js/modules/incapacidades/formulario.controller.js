/**
 * Servicio Incapacidades
 */
/*$('#colaborador_id').change(function()
 {
 var colaboradoriidd = $('#colaborador_id option:selected').val();
 alert('Has hecho un change'+colaboradoriidd);
 });*/
//********************************************** OCULTAR TODOS LOS UPLOAD AL CARGAR EL FORMULARIO ***********************************************************
$('input[id="campo[certificado_medico]"]').closest('.row').hide();
$('input[id="campo[carta_descuento]"]').closest('.row').hide();
$('input[id="campo[constancia_institucion_medica]"]').closest('.row').hide();
$('input[id="campo[orden_medica_hospitalizacion]"]').closest('.row').hide();
$('input[id="campo[orden_css_pension]"]').closest('.row').hide();
$('input[id="campo[desgloce_salario]"]').closest('.row').hide();
$('input[id="campo[reporte_accion_trabajo]"]').closest('.row').hide();
$('input[id="campo[certificado_incapacidad_accidente_trabajo]"]').closest('.row').hide();
//**********************************************************************************************************************************************************

$('#tipo_incapacidad_id').change(function () //EVENTO SE ACTIVA AL REALIZAR UN CHANGE EN EL CAMPO "Tipo de Incapacidad"
{
    var tipo_identificacion = $('#tipo_incapacidad_id option:selected').val();
    if (tipo_identificacion != '' || tipo_identificacion > 0) {
        switch (tipo_identificacion) {
            case "1": //Si seleccionan  "Comun"
            {
                //inputs a mostrar
                $('input[id="campo[certificado_medico]"]').closest('.row').show();
                $('input[id="campo[carta_descuento]"]').closest('.row').show();

                //inputs a ocultar
                $('input[id="campo[constancia_institucion_medica]"]').closest('.row').hide();
                $('input[id="campo[orden_medica_hospitalizacion]"]').closest('.row').hide();
                $('input[id="campo[orden_css_pension]"]').closest('.row').hide();
                $('input[id="campo[desgloce_salario]"]').closest('.row').hide();
                $('input[id="campo[reporte_accion_trabajo]"]').closest('.row').hide();
                $('input[id="campo[certificado_incapacidad_accidente_trabajo]"]').closest('.row').hide();
                break;
            }

            case "2": //Si seleccionan  "Hospitalizacion"
            {
                //inputs a mostrar
                $('input[id="campo[constancia_institucion_medica]"]').closest('.row').show();
                $('input[id="campo[orden_medica_hospitalizacion]"]').closest('.row').show();
                $('input[id="campo[certificado_medico]"]').closest('.row').show();
                $('input[id="campo[carta_descuento]"]').closest('.row').show();
                //inputs a ocultar
                $('input[id="campo[orden_css_pension]"]').closest('.row').hide();
                $('input[id="campo[desgloce_salario]"]').closest('.row').hide();
                $('input[id="campo[reporte_accion_trabajo]"]').closest('.row').hide();
                $('input[id="campo[certificado_incapacidad_accidente_trabajo]"]').closest('.row').hide();
                break;
            }

            case "3": //Si seleccionan  "Permanente"
            {
                //inputs a mostrar
                $('input[id="campo[orden_css_pension]"]').closest('.row').show();
                $('input[id="campo[certificado_medico]"]').closest('.row').show();
                $('input[id="campo[desgloce_salario]"]').closest('.row').show();
                //inputs a ocultar
                $('input[id="campo[carta_descuento]"]').closest('.row').hide();
                $('input[id="campo[constancia_institucion_medica]"]').closest('.row').hide();
                $('input[id="campo[orden_medica_hospitalizacion]"]').closest('.row').hide();
                $('input[id="campo[reporte_accion_trabajo]"]').closest('.row').hide();
                $('input[id="campo[certificado_incapacidad_accidente_trabajo]"]').closest('.row').hide();
                break;
            }

            case "4": //Si seleccionan  "Riesgo Profesional"
            {
                //inputs a mostrar
                $('input[id="campo[desgloce_salario]"]').closest('.row').show();
                $('input[id="campo[reporte_accion_trabajo]"]').closest('.row').show();
                $('input[id="campo[certificado_incapacidad_accidente_trabajo]"]').closest('.row').show();
                $('input[id="campo[certificado_medico]"]').closest('.row').show();
                $('input[id="campo[carta_descuento]"]').closest('.row').show();
                //inputs a ocultar
                $('input[id="campo[constancia_institucion_medica]"]').closest('.row').hide();
                $('input[id="campo[orden_medica_hospitalizacion]"]').closest('.row').hide();
                $('input[id="campo[orden_css_pension]"]').closest('.row').hide();
                break;
            }
        }
    }
});
bluapp.service('incapacidadesService', ['$http', '$document', '$rootScope', function ($http, $document, $rootScope) {

    var scope = this;
    var requisito_id = '';
    var formulario = '#incapacidadForm';
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

        }, 1000);
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
bluapp.controller("IncapacidadesController", function ($rootScope, $scope, $document, $http, $rootScope, $compile, incapacidadesService) {

    var formulario = '#incapacidadForm';

    $scope.fileBtn1 = 'Seleccione';
    $scope.btn_selected = 0;
    $scope.fileClassBtn1 = 'btn-default';
    $scope.fileBtn2 = 'Seleccione';
    $scope.fileClassBtn2 = 'btn-default';
    $scope.guardarBtn = 'Guardar';
    $scope.disabledBtn = '';

    //Inicializar variables scope
    $scope.incapacidad = {
        erptkn: tkn,
        id: "",
        tipo_incapacidad_id: "",
        dias_disponibles_id: "",
        fecha_desde: "",
        fecha_hasta: "",
        cuenta_pasivo_id: "",
        observaciones: "",
        incapacidad_pagada_id: "",
        estado_id: "",
        certificado_medico: "",
        carta_descuento: "",
        colaborador_id: window.location.href.match(/(colaboradores)/g) != null ? (typeof colaborador_id != 'undefined' ? colaborador_id : '') : ""
    };

    var filesList = [], paramNames = [];

    //Inicializar campos, plugins y validacion
    incapacidadesService.init();

    /**
     * Seleccionar documento formulario incapacidad
     * @param $file
     * @param e Event
     * @param $flow
     */
    $scope.archivoSeleccionado = function ($file, $event, $flow) {
        $event.preventDefault();
        console.log(this, $file, $event, $flow);

        if($scope.btn_selected==1){
        //agregar texto de archivo seleccionado.
        $scope.fileClassBtn = 'btn-default';
        $scope.fileBtn1 = '<i class="fa fa-upload"></i> 1 archivo seleccionado';
        }else{
            $scope.fileClassBtn2 = 'btn-default';
            $scope.fileBtn2 = '<i class="fa fa-upload"></i> 1 archivo seleccionado';
        }
    };

    //Funcion popular formulario
    //con informacion de la incapacidad seleccionada.
    $scope.popularFormulario = function () {
        //Before using local storage, check browser support for localStorage and sessionStorage
        if (typeof(Storage) !== "undefined") {
            //Verificar si existe la variable
            //proveniente de Local Storage
            if (localStorage.getItem("incapacidad_id")) {

                var incapacidad_id = localStorage.getItem("incapacidad_id");

                //Buscar datos para popular campos
                incapacidadesService.ajax(phost() + "incapacidades/ajax-seleccionar-incapacidad", {
                    id: incapacidad_id,
                }).then(function successCallback(response) {

                    //Check Session
                    if ($.isEmptyObject(response.data.session) == false) {
                        window.location = phost() + "login?expired";
                    }

                    $scope.incapacidad.cuenta_pasivo_id = response.data.cuenta_pasivo_id.toString();
                    $scope.incapacidad.estado_id = response.data.estado_id.toString();
                    $scope.incapacidad.tipo_incapacidad_id = response.data.tipo_incapacidad_id.toString();
                    $scope.incapacidad.incapacidad_pagada_id = response.data.incapacidad_pagada_id.toString();
                    $scope.incapacidad.dias_disponibles_id = response.data.dias_disponibles_id.toString();
                    $scope.incapacidad.fecha_desde = response.data.fecha_desde;
                    $scope.incapacidad.fecha_hasta = response.data.fecha_hasta;
                    $scope.incapacidad.observaciones = response.data.observaciones;
                    $scope.incapacidad.carta_descuento = response.data.carta_descuento;
                    $scope.incapacidad.certificado_medico = response.data.certificado_medico;
                    $scope.incapacidad.colaborador_id = response.data.colaborador_id;
                    $scope.incapacidad.id = incapacidad_id;

                    if (window.location.href.match(/(accion_personal)/g)) {

                        //seleccionar el colaborador en el dropdown
                        //que aparece en la barra superior
                        $('select#colaborador_id').find('option[value="' + response.data.colaborador_id + '"]').prop('selected', 'selected');

                        //actualizar chosen barra accion personal
                        accionPersonal.actualizar_chosen();
                    }

                    //actualizar chosen
                    incapacidadesService.actualizar_chosen();

                }, function errorCallback(response) {
                    // called asynchronously if an error occurs
                    // or server returns response with an error status.
                });

                //Borrar variable de localstorage
                localStorage.removeItem("incapacidad_id");
            }
        }
    };

    //Ejecutar funcion
    $scope.popularFormulario();

    $scope.seleccion = function (e) {
        e.preventDefault();

        $scope.fileClassBtn1 = 'btn-default';
        $scope.fileBtn1 = '<i class="fa fa-upload"></i> 1 archivo seleccionado';
    }

    //evento: seleccion de archivo
    $scope.limpiarFormulario = function (e, $flow) {
        if (typeof e != 'undefined') {
            e.preventDefault();
        }

        $scope.incapacidad = {
            erptkn: tkn,
            id: "",
            tipo_incapacidad_id: "",
            dias_disponibles_id: "",
            fecha_desde: "",
            fecha_hasta: "",
            cuenta_pasivo_id: "",
            observaciones: "",
            incapacidad_pagada_id: "",
            estado_id: "",
            certificado_medico: "",
            carta_descuento: "",
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
        $scope.fileBtn = 'Seleccione';
        $scope.fileClassBtn = 'btn-default';
        $scope.guardarBtn = 'Guardar';
        $scope.disabledBtn = '';

        //refresh chosen
        incapacidadesService.actualizar_chosen();
    };

    //Limpiar seleccion de campo: dropdown
    $scope.limpiar_seleccion_dropdown = function (campo) {
        $(formulario).find(campo).attr("disabled", "disabled").empty().append('<option value="">Seleccione</option>').find('option:eq(0)').attr("selected", "selected");

        setTimeout(function () {
            incapacidadesService.actualizar_chosen();
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

        /*LINEA ORIGINAL LA DE ABAJO*/
        //var colaboradorid = $(formulario).find('input[id="campo[colaborador_id]"]').val() != "" ? $(formulario).find('input[id="campo[colaborador_id]"]').val() : $scope.incapacidad.colaborador_id;
        var idcolaborador = $('#colaborador_id option:selected').val();
        var colaboradorid = idcolaborador != "" ? idcolaborador : $scope.incapacidad.colaborador_id;
        var url = phost() + "incapacidades/ajax-guardar-incapacidad";

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
            incapacidad_id: typeof incapacidad_id != "undefined" ? incapacidad_id : $scope.incapacidad.id,
            colaborador_id: colaboradorid,
            tipo_incapacidad_id: $scope.incapacidad.tipo_incapacidad_id,
            dias_disponibles_id: $scope.incapacidad.dias_disponibles_id,
            fecha_desde: $(formulario).find('#fecha_desde').val(),
            fecha_hasta: $(formulario).find('#fecha_hasta').val(),
            cuenta_pasivo_id: $scope.incapacidad.cuenta_pasivo_id,
            observaciones: $scope.incapacidad.observaciones,
            incapacidad_pagada_id: $scope.incapacidad.incapacidad_pagada_id,
            estado_id: $scope.incapacidad.estado_id,
            certificado_medico: $scope.incapacidad.certificado_medico,
            carta_descuento: $scope.incapacidad.carta_descuento,
            //****************************************************************** dath
            constancia_institucion_medica: $scope.incapacidad.constancia_institucion_medica,
            orden_medica_hospitalizacion: $scope.incapacidad.orden_medica_hospitalizacion,

            orden_css_pension: $scope.incapacidad.orden_css_pension,
            desgloce_salario: $scope.incapacidad.desgloce_salario,
            reporte_accion_trabajo: $scope.incapacidad.reporte_accion_trabajo,
            certificado_incapacidad_accidente_trabajo: $scope.incapacidad.certificado_incapacidad_accidente_trabajo
            //******************************************************************
        };

        //Estado de guardando en boton
        $scope.guardarBtn = '<i class="fa fa-circle-o-notch fa-spin"></i> Guardando...';
        $scope.disabledBtn = 'disabled';

        //Verificar si ha seleccionado
        //o no algun archivo
        if ($flow.files.length == 0) {

            //Enviar datos por http/ajax
            incapacidadesService.ajax(url, formData).then(function successCallback(response) {

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

    $scope.setSelectedBtn = function(btn){
        $scope.btn_selected=btn;
        console.log("Btn Selected:",$scope.btn_selected);
    }
});
