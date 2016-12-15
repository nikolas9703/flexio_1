/**
 * Created by Ivan Cubilla on 18/7/16.
 */
bluapp.controller("proveedorFormularioController", function ($scope, $http) {
    $scope = this;
    var objFrom = {
        presupuestoForm: $('#crearProveedoresForm'),
    };
    $scope.opcionFormulario = {juridico: false, natural: false, pasaporte: false};
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

    angular.element('#crearProveedoresForm').validate({
        submitHandler: function (form) {
            angular.element('#campo[guardar]').attr('disabled', true);
            form.submit();
        }
    });

    function addHidden(theForm, key, value) {
        // Create a hidden input element, and append it to the form:
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        'name-as-seen-at-the-server';
        input.value = value;
        theForm.appendChild(input);
    };
    $scope.inicializar = function () {
        objFrom.presupuestoForm.validate({
            ignore: '',
            wrapper: '',
        });
    };

    $scope.inicializar();
   // $scope.inicializar();
});
