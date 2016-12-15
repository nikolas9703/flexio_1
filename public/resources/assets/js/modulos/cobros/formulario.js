// jshint esversion:6
import {urls_catalogo,info_header} from './data/data-empezable';
import {referencia} from './data/desde-modulo';
import store from './vuex/empezable/empezable-estado';
import {formEmpezable} from './../../vue/state/empezable';
var formularioCrearCobros = new Vue({
  el:'#formCrearCobros',
  data:{
    catalogoFormulario:{estados:[],metodo_cobro:[],depositable:[],cuenta_bancos:[],cajas:[],bancos:[]},
    empezable:{
      urls_catalogo:urls_catalogo,
      datos_empezable:info_header
    },

    comentario:{
        comentarios: [],
        comentable_type: "Flexio\\Modulo\\Cobros\\Models\\Cobro",
        comentable_id: '',
    },
    config:{
      vista:window.vista,
      disableEditar:false,
      acceso:window.acceso === 1?true:false,
      loading:true
    },
    estado_inicial:'',
    referencia:referencia, //es cuando se hace referencia desde otro modulo e utilizar el empezable
    cobro:{},
    formEmpezable:formEmpezable
  },
  created(){
    this.cargarCatalogos();
  },
  ready(){
    $(".moneda").inputmask('currency',{
      prefix: "",
      autoUnmask : true,
      removeMaskOnSubmit: true
    });
  },
  components:{
    'empezar-desde':require('./../../vue/components/header-empezable.vue'),
    'formulario':require('./formulario/formulario.vue'),
    'vista_comments': require('./../../vue/components/comentario.vue')
  },
  store:store,
  methods:{
    cargarCatalogos(){
      var self = this;
      var datos = {erptkn: tkn};
      var catalogos = this.postAjax('cobros/ajax_formulario_catalogos',datos);

      catalogos.then((response)=>{
        this.logout(response);
        self.$nextTick(function(){
          self.catalogoFormulario.estados = response.data.estados;
          self.catalogoFormulario.bancos = response.data.bancos;
          self.catalogoFormulario.cajas = response.data.cajas;
          self.catalogoFormulario.cuenta_bancos = response.data.cuenta_bancos;
          self.catalogoFormulario.metodo_cobro = response.data.metodo_cobros;
          self.catalogoFormulario.depositable = response.data.tipo_cobro;
          self.referenciaurl();
          self.getCobro();
          self.$store.dispatch('SET_EMPEZABLETYPE',self.empezable.datos_empezable.categoria);
          self.config.loading = false;
        });
      });
    },

    postAjax(ajaxUrl, datos){
      return this.$http.post({url: window.phost() + ajaxUrl, method:'POST',data:datos});
    },
    logout(response){
      if(_.has(response.data, 'session')){
          window.location.assign(window.phost());
          return;
      }
    },
     getCobro(){
         if(!_.isUndefined(window.hex_cobro) && this.config.vista == "ver"){
             var uuid = window.hex_cobro;
             var self = this;
             var datos = {erptkn: tkn,uuid:uuid};
             var info = this.postAjax('cobros/ajax_get_cobro',datos);
             info.then(function(response){
                 self.logout(response);
                 self.$store.dispatch('SET_CURRENT',response.data.empezable_type);
                 self.cobro = response.data;
                 self.comentario.comentarios = response.data.landing_comments;
                 self.comentario.comentable_id = response.data.id;
             });
         }

     },
     referenciaurl(){
         var llaveReferencia =_.keys(this.referencia.desde);
         if(llaveReferencia.length > 0){
           let keyModulo = _.head(llaveReferencia);
           this.$store.dispatch('SET_CURRENT',keyModulo);
           this.formEmpezable.empezable_type = keyModulo;
           this.formEmpezable.aux_empezable_id = this.referencia.desde[keyModulo];
           this.formEmpezable.empezable_id = this.referencia.desde[keyModulo];
         }
     }
  }
});
