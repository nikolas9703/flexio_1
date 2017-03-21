Vue.http.options.emulateJSON = true;
var opcionesModal = $('#verCoberturas');
var conta1=0;
var formularioCrear = new Vue({
    el: ".wrapper-content",
    data:{
        acceso: acceso === 1? true : false,
        disabledOpcionPlanes: true,
        distritosInfo:[],
		corregimientosInfo:[],
		distrito_seleccionado:'',
		corregimiento_seleccionado:'',
		distrito:'',
		corregimiento:''
    },
    methods: {
		getObtenerProvincias: function(datos)
		{
			//polula el select de distritos
            var self = this;
            self.$set('distritosInfo', "");

			moduloRutas.seleccionarDistrito({provincia_id:datos}).then(function (response) {
                if (_.has(response.distritos, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.distritos)) {
					self.$set('distritosInfo', response.distritos);
                }
            });
			
		},
		getObtenerProvinciasDetalle: function(datos,distrito_id,corregimiento_id)
		{
			//polula el select de distritos
            var self = this;
            //self.$set('distritosInfo', "");
			self.$set('distrito', distrito_id);
			moduloRutas.seleccionarDistrito({provincia_id:datos}).then(function (response) {
                if (_.has(response.distritos, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.distritos)) {
					self.$set('distritosInfo', response.distritos);
					console.log(distrito_id);
					self.$set('distrito_seleccionado', distrito_id);
					console.log(response.distritos);
                }
            });
		},
		getObtenerCorregimientos: function(datos){
			//polula el select de distritos
            var self = this;
            self.$set('corregimientosInfo', "");

			moduloRutas.seleccionarCorregimiento({distrito_id:datos}).then(function (response) {
                if (_.has(response.corregimientos, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.corregimientos)) {
					self.$set('corregimientosInfo', response.corregimientos);
					console.log(response.corregimientos);
                }
            });
		},
		getObtenerCorregimientosDetalle: function(datos,corregimiento_id){
			//polula el select de distritos
            var self = this;
            //self.$set('corregimientosInfo', "");
			self.$set('corregimiento', corregimiento_id);

			moduloRutas.seleccionarCorregimiento({distrito_id:datos}).then(function (response) {
                if (_.has(response.corregimientos, 'session')) {
                    window.location.assign(phost());
                }
                if (!_.isEmpty(response.corregimientos)) {
					self.$set('corregimientosInfo', response.corregimientos);
                }
            });		
			
		}
	},
    computed: {
       
    }
    
});