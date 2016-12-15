Vue.http.options.emulateJSON = true;
var formClienteTelefonos = new Vue({
    el:"#vue-telefono-cliente",
    data:{
        asignados_telefonos:[{numero:'',tipo:''}],
        puedeEliminar: false
    },
    ready:function(){
        if(vista === 'ver' || vista === 'creando_desde_potencial'){
          if (lista_telefonos.length > 0){
               this.$set('asignados_telefonos',lista_telefonos);
          }
        }
    },
    methods:{
        addFilasTelefono:function(event){
            this.asignados_telefonos.push({numero:'',tipo:''});
            setTimeout(function () {
            $(".telefono").inputmask({
            mask: '999-99999',
            placeholder: ' ',
            clearMaskOnLostFocus: true
            });
        }, 500);
        },
        deleteFilasTelefono:function(index){
            this.asignados_telefonos.splice(index, 1);
        },
    }

});
