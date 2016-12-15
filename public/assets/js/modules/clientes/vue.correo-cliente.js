Vue.http.options.emulateJSON = true;
var formClienteCorreo = new Vue({
    el:"#vue-correo-clientes",
    data:{
        asignados_correos:[{correo:'',tipo:''}],
        puedeEliminar: false
    },
    ready:function(){
        if(vista === 'ver' || vista==='creando_desde_potencial'){
            if (lista_correo.length > 0){
                this.$set('asignados_correos',lista_correo);
            }
        }
    },
    methods:{
        addFilasCorreo:function(event){
            console.log('add correo');
            this.asignados_correos.push({correo:'',tipo:''});
        },
        deleteFilasCorreo:function(index){
            this.asignados_correos.splice(index, 1);
        },
    }

});
