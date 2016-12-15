Vue.http.options.emulateJSON = true;
var opcionesModal = $('#verCoberturas');
var formularioCrear = new Vue({
    el: ".wrapper-content",
    data:{
        acceso: acceso === 1? true : false,
        disabledOpcionPlanes: true,
        nombre:'',
        ruc:''
    },
    methods: {
	},
    computed: {
       
    }
    
});