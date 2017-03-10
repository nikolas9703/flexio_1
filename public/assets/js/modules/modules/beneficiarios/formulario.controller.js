/**
 * Servicio Beneficiarios
 */
bluapp.service('beneficiarioService', ['$http', '$document', '$rootScope', function($http, $document, $rootScope) {

    var scope = this;
    var requisito_id = '';
    var formulario = '#crearBeneficiarioForm';

    //Funcion para ejecutar ajax
    this.ajax = function(url, data) {

        return $http({
            method: 'POST',
            url: url,
            data : $.param($.extend({erptkn: tkn}, data)),
            cache: false,
            xsrfCookieName: 'erptknckie_secure',
            headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
        });
    };

    //Funcion para inicializar plugins
    this.init = function() {

        setTimeout(function(){

            //Plugin Datepicker
            $(formulario).find('#fecha_nacimiento').datepicker({
                singleDatePicker: true,
                autoUpdateInput: false,
                format: 'MM-DD-YYYY',
                showDropdowns: true,
                opens: "left",
                locale: {
                    applyLabel: 'Seleccionar',
                    cancelLabel: 'Cancelar',
                    daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
                    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                    firstDay: 1
                }
            }).on('apply.datepicker', function(ev, picker) {
                $(formulario).find('#fecha_nacimiento').val(picker.startDate.format('DD/MM/YYYY'));
            });

            //Validacion
            $.validator.setDefaults({
                errorPlacement: function(error, element){
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
    this.actualizar_chosen = function() {

        //refresh chosen
        setTimeout(function(){
            $(formulario).find('select.chosen-select').trigger('chosen:updated');
        }, 50);
    };

}]);

/**
 * Controlador Formulario de Beneficiarios
 */
bluapp.controller("BeneficiariosController", function($rootScope, $scope, $document, $http, $rootScope, $compile, beneficiarioService){


    var url = window.phost() + "beneficiarios/ajax-seleccionar-evaluacion";
    var formulario = '#crearBeneficiarioForm';

    $scope.fileBtn = 'Seleccione';
    $scope.fileClassBtn = 'btn-default';
    $scope.guardarBtn = 'Guardar';
    $scope.disabledBtn = '';

    //Inicializar variables scope
    $scope.beneficiario = {
        erptkn: tkn,
        id: "",
        tipo_beneficiario_id: "",
        justificacion_id: "",
        fecha_desde: "",
        fecha_hasta: "",
        cuenta_pasivo_id: "",
        observaciones: "",
        estado_id: ""
    };

    //Inicializar campos, plugins y validacion
    beneficiarioService.init();

    //evento: seleccion de archivo
    $scope.limpiarFormulario = function(e)
    {
        if(typeof e != 'undefined'){
            e.preventDefault();
        }

        $scope.beneficiario = {
            erptkn: tkn,
            id: "",
            tipo_beneficiario_id: "",
            justificacion_id: "",
            fecha_desde: "",
            fecha_hasta: "",
            cuenta_pasivo_id: "",
            observaciones: "",
            estado_id: ""
        };

        $(formulario).find('input[type="text"], input[type="checkbox"]').val('').removeAttr("checked");

        //refresh chosen
        beneficiarioService.actualizar_chosen();
    };

    //Limpiar seleccion de campo: dropdown
    $scope.limpiar_seleccion_dropdown = function(campo){
        $(formulario).find(campo).attr("disabled", "disabled").empty().append('<option value="">Seleccione</option>').find('option:eq(0)').attr("selected", "selected");

        setTimeout(function(){
            beneficiarioService.actualizar_chosen();
        }, 300);
    };

    //AngularJS safe $apply (prevent "Error: $apply already in progress")
    $rootScope.safeApply = function safeApply(operation) {
        var phase = this.$root.$$phase;
        if (phase !== '$apply' && phase !== '$digest') {
            this.$apply(operation);
            return;
        }

        if (operation && typeof operation === 'function')
            operation();
    };

    /**
     * Funcion Guardar Formulario de Beneficiario
     */
    $scope.guardar = function(e)
    {
        e.preventDefault();

        if($(formulario).validate().form() == true)
        {
            var colaborador_id = $(formulario).find('input[id="campo[colaborador_id]"]').val();
            var beneficiario_id = $(formulario).find('input[id="campo[id]"]').val();
            var url = phost() + "beneficiarios/ajax-guardar-beneficiario";

            //verificar que alla seleccionado
            //un colaborador de la barra de filtro
            if(colaborador_id == ""){

                toastr.warning('Debe seleccionar un colaborador.');
                return false;
            }

            //Estado de guardando en boton
            setTimeout(function () {
                $rootScope.safeApply(function () {
                    $scope.guardarBtn = '<i class="fa fa-circle-o-notch fa-spin"></i> Guardando...';
                    $scope.disabledBtn = 'disabled';
                });
            }, 100);

            beneficiarioService.ajax(url, {
                beneficiario_id: beneficiario_id,
                colaborador_id: colaborador_id,
                tipo_beneficiario_id: $scope.beneficiario.tipo_beneficiario_id,
                justificacion_id:  $scope.beneficiario.justificacion_id,
                fecha_desde:  $(formulario).find('#fecha_desde').val(),
                fecha_hasta:  $(formulario).find('#fecha_hasta').val(),
                cuenta_pasivo_id:  $scope.beneficiario.cuenta_pasivo_id,
                estado_id:  $scope.beneficiario.estado_id,
                observaciones: $scope.beneficiario.observaciones
            }).then(function successCallback(response) {

                window.location = phost() + 'accion_personal/listar';

            }, function errorCallback(response) {
                // called asynchronously if an error occurs
                // or server returns response with an error status.
            });

        }
    };
});
