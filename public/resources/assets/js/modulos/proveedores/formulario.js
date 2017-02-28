/**
 * Created by Ivan Cubilla on 20/7/16.
 */


var _vueFocus = require('vue-focus');
Vue.directive('select2', require('./../../vue/directives/select2.vue'));
var ProveedoresCrear = new Vue({

    el: '#crearProveedoresFormDiv',

    data: {

        comentario: {

            comentarios: [],
            comentable_type: 'Flexio\\Modulo\\Proveedores\\Models\\Proveedores',
            comentable_id: '',

        },

        config: {
            vista: window.vista,
            select2:{width:'100%'},
        },

        proveedor: {
            tipo_identificacion:'',
            detalle_identificacion:{tomo:'', folio:'', asiento:'', dv:'', provincia:'', letra:'', pasaporte:''},
            nombre: '',
            telefono: '',
            email: '',
            direccion: '',
            identificacion: '',
            tomo_rollo: '',
            folio_imagen_doc: '',
            asiento_ficha: '',
            digito_verificador: '',
            provincia: '',
            letra: '',
            pasaporte: '',
            numero_cuenta: '',
            limite_credito: '',
            retiene_impuesto: '',
            acreedor: ''
        },

        identificacion: {tipo: '', nombre: ''},
        letra: '',
        mostarPasaporte: false,
        mostrarJuridico: false,
        mostrarNatural: false,
        mostrarCamposNaturales: true,
        mostrarLetra: false,
        vista: vista,
        acceso: window.acceso,
        cambio_estado: window.cambio_estado

    },

    components: {

        'identificacion': require('./../../vue/components/identificacion.vue'),
        'vista_comments': require('./../../vue/components/comentario.vue')

    },

    ready: function () {

        var context = this;
        if (this.vista === 'ver' || this.vista === 'detalle') {

            console.log(window.proveedor);
            context.comentario.comentarios = JSON.parse(JSON.stringify(window.pro_coment));
            context.comentario.comentable_id = JSON.parse(JSON.stringify(window.proveedor.id));
            this.$set('proveedor', proveedor);
            this.seleccionarTipo(this.proveedor.identificacion);
            this.seleccioneLetra(this.proveedor.letra);
            // this.disableEstado(window.acceso);

        }//else{
        //    this.disableEstado(window.acceso);
        //  }

        if (this.vista === 'detalle')this.$set('showActualizar', false);

    },
    methods: {
        seleccionarTipo: function (tipo_id) {
            if (tipo_id === 'natural') {

                this.mostrarNatural = true;
                this.mostrarJuridico = false;
                this.mostarPasaporte = false;
            } else if (tipo_id === 'juridico') {

                this.mostrarNatural = false;
                this.mostrarJuridico = true;
                this.mostarPasaporte = false;
            } else if (tipo_id === 'pasaporte') {

                this.mostrarNatural = false;
                this.mostrarJuridico = false;
                this.mostarPasaporte = true;
            }
        },
        seleccioneLetra: function (letra) {
            if (letra === 'PAS') {
                this.mostrarCamposNaturales = false;
                this.mostrarLetra = true;
            } else {
                this.mostrarCamposNaturales = true;
                this.mostrarLetra = false;
            }

        },
        disableEstado: function (acceso) {
            if (acceso === 'acceso') {
                this.acceso = true;
            } else {
                this.acceso = false;
            }
        },
        getFormData: function ($form) {
            var unindexed_array = $form.serializeArray();
            var indexed_array = {};

            $.map(unindexed_array, function (n, i) {
                indexed_array[n['name']] = n['value'];
            });

            return indexed_array;
        },
        validate: function (success) {
            var context = this;

            this.$http.post({
                url: window.phost() + "proveedores/ajax-valida-identificacion",
                method: 'POST',
                data: context.getFormData($("#crearProveedoresForm"))
            }).then(function (response) {
                if (!_.isEmpty(response.data) && !response.data.isValid) {
                    toastr.error('Identificacion ingresada es inválida', this.owner);
                    if (typeof success == "function") {
                        success(response);
                    }else{
                        context.proveedor.pasaporte = "";
                        context.proveedor.tomo_rollo = "";
                        context.proveedor.folio_imagen_doc = "";
                        context.proveedor.asiento_ficha = "";
                        context.proveedor.digito_verificador = "";
                    }
                }
            });
        }

    }

});
