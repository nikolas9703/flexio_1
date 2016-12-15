var contratoFormulario = new Vue({
  el:'#form_crear_contrato',
  data:{
    acceso: acceso === 1 ? true: false,
    vista: vista,
    disabledMonto: true,
    tablaDatos:[{cuenta_id:'',descripcion:'',monto:'0.00'}],
    campo:{
      cliente_id:'', centro_id:'', monto_contrato:"0.00",referencia:'',fecha_inicio:'',fecha_final:'',
      abono:{monto:'0',porcentaje:'0'},
      retenido:{monto:'0',porcentaje:'0'}
    }
  },
  ready:function(){
    this.$set('tablaDatos',contrato.contrato_montos);
    this.$set('campo.retenido',contrato.tipo_retenido[0]);
    this.$set('campo.abono',contrato.tipo_abono[0]);
    ///set valores del formulario
    this.$set('campo.abono',contrato.tipo_abono[0]);
    this.campo.centro_id = contrato.centro_id;
    this.campo.referencia = contrato.referencia;
    this.campo.monto_contrato = parseFloat(contrato.monto_contrato);
  },
  computed:{
     monto_contrato:function(){
       return accounting.toFixed(this.campo.monto_contrato,2);
     },
     adenda:function(){
       return contrato.adenda.length;
     },
     monto_adenda:function(){
       if(this.adenda === 0) return "0.00";
       var monto_adenda= _.sumBy(contrato.adenda,function(o) {return parseFloat(o.monto_adenda);});
       return accounting.toFixed(monto_adenda,2);
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
