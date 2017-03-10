var anexoMenu = Vue.extend({
    template:"#menu-edicion",
    props:['historial'],
    data:function(){
        return{
            moduloId:'',
            comentario:''
        };
    },
    ready:function(){
      
    },
    methods:{
 
        guardar_comentario:function(){

            alert("Hola Mundo!");
        }
    }
});
