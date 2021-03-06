Vue.http.options.emulateJSON = true;
var formClienteTelefonos = new Vue({
    el:"#vue-telefono-cliente",
    data:{
        asignados_telefonos:[{numero:'',tipo:'trabajo'}],
        puedeEliminar: false
    },
    ready:function(){
        if(vista === 'ver'){
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
            $(".select2").select2();
        }, 500);


        },
        deleteFilasTelefono:function(index){
            this.asignados_telefonos.splice(index, 1);
        },
    }

});
