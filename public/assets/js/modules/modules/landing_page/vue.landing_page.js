Vue.transition('listado',{
    enterClass:'fadeIn',
    leaveClass:'fadeOut'
});

var pusher = new Pusher('323c4c72368dae9707b9', {
      encrypted: false
});

var LandingPage = new Vue({
  el:'#app_landing_page',
  data:{
    showComponente:true
  },
  ready:function(){
    if(_.has(landing,'errors')){
      this.showComponente = false;
      toastr.error("Los Comentarios no se pueden mostrar en estos momentos","Error");
    }
  },
  components:{
    'landing-page':ComponentLandingPage
  }
});
