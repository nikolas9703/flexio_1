Vue.component('toast',require('./vue/components/toast.vue'));

var vmToast = new Vue({
  data:{
    mensaje:'',
    tipo:'',
    titulo:''
  },
  el:'#app_toast',
  ready:function(){
    if(_.isObject(window.toast_mensaje)){
      this.mensaje = window.toast_mensaje.mensaje;
      this.tipo = window.toast_mensaje.tipo;
      this.titulo = window.toast_mensaje.titulo;
    }
  }
});
