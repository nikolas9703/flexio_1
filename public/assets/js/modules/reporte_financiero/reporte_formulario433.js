var reporteFormulario433 = Vue.extend({
  template:'#reporte_formulario433',
  props:["info"],
  data:function(){
    return{
      titulo_reporte:"Informe 433",
      datos_reporte:[]
    };
  },
  ready:function(){
    this.datos_reporte = this.info[0];
  },
  computed:{

  },
  methods:{
    tipoIndentificacion:function(tipo){
      if(_.isEmpty(tipo)){
        return "";
      }
      if(tipo == "juridico"){
        return 'J';
      }
      return 'N';
    },
    getRUC:function(proveedor){
      if(_.isEmpty(proveedor.identificacion)){
        return "";
      }
      if(proveedor.identificacion == "juridico"){
        return proveedor.tomo_rollo +'-'+ proveedor.folio_imagen_doc + '-' + proveedor.asiento_ficha;
      }

      if(proveedor.identificacion == "natural"){
        var numero = _.isEmpty(proveedor.provincia)?proveedor.letra: proveedor.provincia;
        return  numero +'-'+ proveedor.tomo_rollo + '-' + proveedor.asiento_ficha;
      }

      if(proveedor.identificacion == "pasaporte"){

        return proveedor.pasaporte;
      }

    },
    getDV:function(proveedor){
      if(_.isEmpty(proveedor.identificacion) || proveedor.identificacion !="juridico"){
        return "";
      }

      return proveedor.digito_verificador;
    }
  }
});
Vue.component('reporte-formulario433', reporteFormulario433);
