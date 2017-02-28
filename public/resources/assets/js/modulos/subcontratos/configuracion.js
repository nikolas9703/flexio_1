Vue.directive('select2', require('./../../vue/directives/select2.vue'));

window.subcontratoConfiguracion = new Vue({
  el:'#configuracion_subcontrato',
  data:{},
  components:{
    'tipo_subcontrato': require('./components/config-tipo-subcontrato.vue'),
  },
  ready:function(){}
});
