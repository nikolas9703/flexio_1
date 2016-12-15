Vue.http.options.emulateJSON = true;
var formClienteCorreo = new Vue({
    el:"#vue-correo-clientes",
    data:{
        asignados_correos:[{correo:'',tipo:'trabajo'}],
        puedeEliminar: false
    },
    ready:function(){
        if(vista === 'ver'){
            if (lista_correo.length > 0){
                this.$set('asignados_correos',lista_correo);
            }
        }
    },
    methods:{
        addFilasCorreo:function(event){

            this.asignados_correos.push({correo:'',tipo:''});
            setTimeout(function () {
              $(".select2").select2();
           }, 500);
        },
        deleteFilasCorreo:function(index){
            this.asignados_correos.splice(index, 1);
        },
    }

});
