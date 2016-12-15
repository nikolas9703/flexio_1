Vue.http.options.emulateJSON = true;
var formCentrosFacturable = new Vue({
    el:"#vue-asignados-cliente",
  data:{
    asignados_clientes:[{id:'',nombre:''}],
    puedeEliminar: false
  },
  ready:function(){
      if(vista === 'ver'){
      if (lista_asignados.length > 0){
          this.$set('asignados_clientes',lista_asignados);
      }
      }
  },
  methods:{
	 addFilasAsignados:function(event){
       this.asignados_clientes.push({id:'',nombre:''});
    },
    	deleteFilasAsignados:function(index,id){
    	this.asignados_clientes.splice(index, 1);
    },
  }

});
