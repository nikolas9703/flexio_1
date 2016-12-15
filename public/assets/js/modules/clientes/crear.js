/**
 * Servicio Clientes
 */
bluapp.service('clientService', ['$http', '$document', '$rootScope', function ($http, $document, $rootScope) {

        var scope = this;
        var requisito_id = '';
        var formulario = '#formClienteCrear';

        //Funcion para ejecurtar ajax
        this.ajax = function (url, data) {

            return $http({
                method: 'POST',
                url: phost() + url,
                data: $.param($.extend({erptkn: tkn}, data)),
                cache: false,
                xsrfCookieName: 'erptknckie_secure',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            });
        };

    }]);

bluapp.controller("clienteFormularioController", function ($scope, $http, clientService) {
    var objFrom = {
        presupuestoForm: $('#formClienteCrear'),
    };
    $scope.balance = (balance === 0 ? false : true);
    $scope.impuesto = false;
    $scope.opcionFormulario = {juridico: false, natural: false, pasaporte: false};
    $scope.guardarBtn = "Guardar";
    $scope.naturalLetra = {
        valor: null
    };

    $scope.indentificacion = [
        {tipo: 'juridico', nombre: 'RUC'},
        {tipo: 'natural', nombre: 'Cédula'},
        {tipo: 'pasaporte', nombre: 'Pasaporte'}
    ];

    if (tipo_id === 'natural') {
        $scope.tipos = $scope.indentificacion[1];
        $scope.opcionFormulario.natural = true;
        $scope.naturalLetra.valor = letra;
    } else if (tipo_id === 'juridico') {
        $scope.tipos = $scope.indentificacion[0];
        $scope.opcionFormulario.juridico = true;
    } else if (tipo_id === 'pasaporte') {
        $scope.tipos = $scope.indentificacion[2];
        $scope.opcionFormulario.pasaporte = true;
    }

    $scope.verTipo = function (tipos) {
        if (!angular.isObject(tipos)) {
            $scope.opcionFormulario.juridico = false;
            $scope.opcionFormulario.natural = false;
            $scope.opcionFormulario.pasaporte = false;
            return false;
        }
        if (tipos.tipo === 'juridico') {
            $scope.opcionFormulario.juridico = true;
            $scope.opcionFormulario.natural = false;
            $scope.opcionFormulario.pasaporte = false;
            $scope.naturalLetra.valor = null;
        } else if (tipos.tipo === 'natural') {
            $scope.opcionFormulario.juridico = false;
            $scope.opcionFormulario.natural = true;
            $scope.opcionFormulario.pasaporte = false;
        } else if (tipos.tipo === 'pasaporte') {
            $scope.opcionFormulario.juridico = false;
            $scope.opcionFormulario.natural = false;
            $scope.opcionFormulario.pasaporte = true;
        }
    };

    $scope.letras = function (valor) {
        $scope.naturalLetra.valor = valor;
        if (valor === 'N' || valor === 'PAS' || valor === 'PE') {
            document.getElementById("natural[provincia]").value = "";
        }

    };

    angular.element('#formClienteCrear').validate({

        submitHandler: function (form) {
           //var data = clientService.ajax('clientes/ajax-verificar-identificacion', {});
           // data.then(function (res) { });
            angular.element('.guardarCliente').attr('disabled', true);
            form.submit();
        }
    });

    function addHidden(theForm, key, value) {
        // Create a hidden input element, and append it to the form:
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.class ='name-as-seen-at-the-server';
        input.value = value;
        theForm.appendChild(input);
    };
    /**
     * Funcion Guardar
     */
    /*  $scope.guardar = function(e) {
        e.preventDefault();
        if($('#formClienteCrear').validate().form() == true)
        {
            //var url = phost() + "clientes/ajax-verificar-identificacion";
            var data = clientService.ajax('clientes/ajax-verificar-identificacion', {});
            data.then(function (response) {
                if(response.data.tipo == 'success'){
                    toastr.warning('El número de identificación ya existe.');
                }else{

                }
            });

            clientService.ajax(url, {
            }).then(function(response) {

                if(response.data.tipo == 'success'){
                    window.location = phost() + 'cajas/listar';
                }else{
                    //Mensaje
                    toastr.warning(response.data.mensaje);
                }

            });
        }
    };*/
    $scope.inicializar = function () {
        objFrom.presupuestoForm.validate({
            ignore: '',
            wrapper: '',
        });

           if(typeof cliente_potencial_id !== 'undefined'){

                var id = cliente_potencial_id;
                var data = clientService.ajax('clientes/ajax-cliente-potencial', {id: id});

                data.then(function (res) {
                    var potencial = res.data;

                    $('input[id="campo[nombre]"]').val(potencial.nombre);
                    $('#contacto_tipo').val(potencial.id_toma_contacto);
                    $('input[id="campo[comentario]"]').val(potencial.comentarios);
                    $('#formClienteCrear').append('<input id="id_cp" type="hidden" name="id_cp" value="' + id + '"/>');
                    localStorage.removeItem("id");
                });

                setTimeout(function () {
                    $('.filtro-formularios').find('#id').find('option[value="' + id + '"]').prop("selected", "selected").trigger('change');
                }, 1000);

        }
        setTimeout(function () {
        $(".limite_credito").inputmask('currency',{
          prefix: "",
          autoUnmask : true,
          removeMaskOnSubmit: true
        });

        $(".telefono").inputmask({
            mask: '999-99999',
            placeholder: ' ',
            clearMaskOnLostFocus: true
        });
    }, 2000);
    };

    $scope.inicializar();
});
