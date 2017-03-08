var contratoFormulario = new Vue({
  el:'#form_crear_contrato',
  data:{
    acceso: acceso === 1 ? true: false,
    vista: vista,
    disabledMonto: true,
    tablaDatos:[{cuenta_id:'',descripcion:'',monto:'0.00'}],
    campo:{
      cliente:'', centro:'', monto_contrato:"0.00",
      abono:{monto:'0',porcentaje:'0'},
      retenido:{monto:'0',porcentaje:'0'}
    }
  },
  computed:{
     monto_contrato:function(){
       var monto = _.sumBy(this.tablaDatos,function(o) {return parseFloat(o.monto);});
       this.campo.monto_contrato = monto;
       return accounting.toFixed(monto,2);
     },
     validate_montos:function(){
       var porcentajes = parseFloat(this.campo.abono.porcentaje) +  parseFloat(this.campo.retenido.porcentaje);
       var montos =  parseFloat(this.campo.abono.monto) +  parseFloat(this.campo.retenido.monto);
       if(porcentajes > 100 || montos > this.campo.monto_contrato){
        return true;
      }else{
        return false;
      }
     }
  },
  watch:{
    'campo.monto_contrato': function(val, oldVal){
      this.abono_monto(this.campo.abono.monto);
      this.abono_porcentaje(this.campo.abono.porcentaje);
      this.retenido_monto(this.campo.retenido.monto);
      this.retenido_porcentaje(this.campo.retenido.porcentaje);
    }
  },
  methods:{
    addRow:function(event){
      this.tablaDatos.push({cuenta_ingreso:'',descripcion:'',monto:'0.00'});
      setTimeout(function() {
      $(".select2").select2({
         theme: "bootstrap",
         width:"100%"
      });
    }, 300);
    },
    deleteRow:function(fila){
      this.tablaDatos.$remove(fila);
    },
    //ABONOS
    abono_monto:function(value){
      var monto_contrato =  parseFloat(this.monto_contrato);
      var monto_abono = parseFloat(value);
      if(monto_contrato > monto_abono){
       var porcentaje = (monto_abono / monto_contrato) * 100;
       this.campo.abono.porcentaje = accounting.toFixed(porcentaje,2);
     }else{
       this.campo.abono.monto="0";
     }
    },
    abono_porcentaje:function(value){
      var monto_contrato =  parseFloat(this.monto_contrato);
      var monto_porcentaje = parseFloat(value);
      if(100 > monto_porcentaje){
       var abono = (monto_porcentaje / 100) * monto_contrato;
       this.campo.abono.monto = accounting.toFixed(abono,2);
     }else{
       this.campo.abono.porcentaje="0";
     }
   },
    //RETENIDOS
    retenido_monto:function(value){
      var monto_contrato =  parseFloat(this.monto_contrato);
      var monto_retenido = parseFloat(value);
      if(monto_contrato > monto_retenido){
       var porcentaje = (monto_retenido / monto_contrato) * 100;
       this.campo.retenido.porcentaje = accounting.toFixed(porcentaje,2);
     }else{
       this.campo.retenido.monto="0";
     }
    },
    retenido_porcentaje:function(value){
      var monto_contrato =  parseFloat(this.monto_contrato);
      var monto_porcentaje = parseFloat(value);
      if(100 > monto_porcentaje){
       var abono = (monto_porcentaje / 100) * monto_contrato;
       this.campo.retenido.monto = accounting.toFixed(abono,2);
     }else{
       this.campo.retenido.porcentaje="0";
     }
   },
   ocultarBoton:function(vista){
     return vista=='crear'? '' : 'hide';
   },
   desabilitado:function(vista){
    return vista=='crear'? false : true;
   },
    guardar:function(){
      $('#form_crear_contrato').validate({
          ignore: '',
          wrapper: '',
        errorPlacement:function(error, element){
          if($('#prueba').find('input[id*="items_monto"]').length > 0 ||  $('#prueba').find('input[id*="items_descripcion"]').length > 0 || $('#prueba').find('input[id*="items_cuenta_id"]').length > 0 ) {
            $("#tablaError").html(error);
          }else{
            error.insertAfter(element);
          }
        },
        submitHandler: function(form) {
          $("#monto_contrato").removeAttr("disabled");
          $("#guardarBtn").prop("disabled",true);
          form.submit();
        }
      });
    }
  }
});
