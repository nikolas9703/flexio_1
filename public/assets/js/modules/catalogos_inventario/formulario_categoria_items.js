Vue.transition('listado',{
    enterClass:'fadeIn',
    leaveClass:'fadeOut'
});
var ConfigInventarioCatItems = new Vue({
el:"#categoriasItems",
data:{
    catalogos:{cuentas:[]},
    campo:{
        nombre:'',
        descripcion:'',
        depreciar:false,
        depreciacion_meses:0,
        porcentaje_depreciacion:0,
        cuenta_id:'',
        id:''
    },
    porcentaje_base: 100,
    disablePorcentaje:false
},
computed:{
    esValido:function(){
     if(_.isEmpty(this.campo.nombre) || _.isEmpty(this.campo.descripcion)){
         return false;
     }
     return true;
    }

},
ready:function(){
    $("#fcuenta").chosen({width: '100%', allow_single_deselect: true});
},
methods:{
    guardar:function(){
        var self = this;
        var datos = {erptkn: tkn};
        if(this.campo.depreciar) this.campo.depreciar = 1;
        if(this.esValido){
          this.disablePorcentaje= true;
          datos = $.extend(datos,{campo:this.campo});
          var guardar = this.postAjax('catalogos_inventario/ajax_guardar',datos);

          guardar.then(function(response){

              toastr[response.data.tipo](response.data.mensaje,response.data.titulo);
              $("#categoriasGrid").trigger("reloadGrid");
              this.limpiarFormulario();
              self.disablePorcentaje= false;
          });
        }else{
            toastr.error("debe llenar los campos requeridos",'validacion');
        }
    },
    postAjax:function(ajaxUrl, datos){
        return this.$http.post({url: window.phost() + ajaxUrl, method:'POST',data:datos});
    },
    limpiarFormulario:function(){
        this.campo = {
        nombre:'',
        descripcion:'',
        depreciar:false,
        depreciacion_meses:'',
        porcentaje_depreciacion:'',
        cuenta_id:'',
        id:''
       };
       $("#fcuenta").val("");
       $('#fcuenta').trigger("chosen:updated");
       this.disablePorcentaje = false;
   },
   calculo_pocentaje:function(){
       var calculo = this.porcentaje_base / _.toNumber(this.campo.depreciacion_meses);
       if(_.isNaN(calculo)){
           this.campo.porcentaje_depreciacion = 0;
           return;
       }
       this.campo.porcentaje_depreciacion = calculo;

   }
}

});
