
var menuEmpresa = new Vue({
  el:"#empresaMenu",
  data:{
    lista_empresa:[],
    empresa_seleccionada:{},
    show_flexio_logo:false,
  },
  ready:function(){
    this.seleccionado();
  },
  methods:{
    seleccionado:function(){
      var self = this;
      this.$http.post({
        url: window.phost() + "empresa/lista",
        method:'POST',
        data:{erptkn: window.tkn}
      }).then(function(response){

        if(_.has(response.data, 'session')){
           window.location.assign(window.phost());
        }
        if(_.isEmpty(response.data.lista)){
          self.show_flexio_logo = true;
        }
       self.lista_empresa = response.data.lista;
       self.$set('empresa_seleccionada',response.data.default);

     }).catch(function(err){});
    },
    seleccionar:function(e, uuid){
      var self = this;
      var uuid_empresa = uuid;
      var nombre = $(e.target).parent().attr('data-nombre');
      var logo = $(e.target).parent().attr('data-logo');
      this.$http.post({
        url: window.phost() + "empresa/cambio",
        method:'POST',
        data:{erptkn: tkn, uuid_empresa: uuid_empresa}
      }).then(function(response){

        if(_.has(response.data, 'session')){
           window.location.assign(window.phost());
        }
       self.$set('empresa_seleccionada.uuid_empresa',uuid_empresa);
       self.$set('empresa_seleccionada.nombre',nombre);
       self.$set('empresa_seleccionada.logo',logo);
       //window.location.href =  window.phost();
       location.reload();
     }).catch(function(err){});
   },
   ver_logo:function(logo){
     var path_images = window.phost() ;
     var default_logo = path_images + 'public/themes/erp/images/logo_flexio_background_transparent_recortado_miniV1.png';
     return _.isEmpty(logo)? default_logo: path_images + 'public/logo/' + logo;
   }
  }

});
