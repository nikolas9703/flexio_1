// jshint esversion:6
var formulario={
  cliente_id:'',
  centro_facturacion_id:'',
  saldo_pendiente:'',
  credito_favor:'',
  fecha_desde:moment().format('DD/MM/YYYY'),
  fecha_hasta:moment().add(30,'days').format('DD/MM/YYYY'),
  termino_pago:'',
  creado_por:'',
  centro_contable_id:'',
  lista_precio_alquiler_id:'',
  estado:'por_aprobar'
};

import configDate from './config/fechas';
import catClientes from './clientes';
import Articulos from './items';
var  guardar = require('./mixin_guardar_item_alquiler');
Vue.transition('listado',{
    enterClass:'fadeIn',
    leaveClass:'fadeOut'
});
var vmTablaAlquilar = new Vue({
  el:'#formulario_creacion',
  mixins:[guardar.default],
  data:{
    empezable:{
        titulo:'Empezar cotización desde',
        configSelect2:{width:"100%",placeholder: "Seleccione"},
        categoria:[{label:'Clientes',value:'cliente'},{label:'Clientes Potenciales',value:'clientes_potenciales'}],
        catalogos:[],
        empezable_type:''
    },
    catalogoFormulario:{centro_contables:[],estados:[],terminos_pagos:[],vendedores:[],centro_facturable:[],articulos:[], lista_precio_alquiler: lista_precio_alquiler},
    tablaItem:[],
    formulario:formulario,
    configDatepicker:configDate,
    catClientes,
    comentarios:[],
    campoDisabled:{
      clienteDisabled:true,
      estadoDisabled:true,
      botonDisabled:false
    },
    config:{
      vista:window.vista,
      modulo:'none',
      disableEditar:false
    }

  },

  ready(){
    this.cargarCatalogoDefault();
  },
  components:{
    'empezar-desde':require('./vue/components/empezable-desde.vue'),
    'tabla-articulos':require('./vue/components/tabla-articulos.vue'),
    'vista_comments':require('./vue/components/comentario.vue'),
  },
  methods:{
  //se utiliza para cargar los catalogos por default como estado, vendedor,clientes, centro_contables
  cargarCatalogoDefault(){
    var datos = {erptkn: tkn};
    var catalogos = this.postAjax('ajax_catalogo/catalogos_ventas',datos);
    var self = this;
    catalogos.then((response)=>{
      if(_.has(response.data, 'session')){
              window.location.assign(window.phost());
              return;
      }
      self.$nextTick(function(){
      self.catalogoFormulario.centro_contables = response.data.centros_contables;
      self.catalogoFormulario.estados = response.data.estados;
      self.catalogoFormulario.terminos_pagos = response.data.terminos_pagos;
      self.catalogoFormulario.vendedores = response.data.vendedores;
      self.catalogoFormulario.articulos = response.data.articulos;
      if(self.config.vista ==="crear"){
          self.tablaItem = Articulos.items;
          self.formulario.creado_por = window.usuario_id || '';
      }
      if(self.config.vista ==="editar"  && !_.isEmpty(window.uuid_cotizacion)){
          self.config.disableEditar = true;
          self.cargarDatos(window.uuid_cotizacion);
      }
    });
    });
  },
  postAjax(ajaxUrl, datos){
    return this.$http.post({url: window.phost() + ajaxUrl, method:'POST',data:datos});
  },
  cargarDatos(uuid){
    var datos = {erptkn: tkn,uuid:uuid};
    var cotizacionAjax = this.postAjax('cotizaciones_alquiler/ajax-get-cotizacion',datos);
    var self = this;
    cotizacionAjax.then((response)=>{
      if(_.has(response.data, 'session')){
              window.location.assign(window.phost());
              return;
      }
      var cotizacion = response.data;
      self.setData(cotizacion);
    });

  },
  setData(cotizacion){

    catClientes.catalogo.tipo = cotizacion.cliente_tipo;
    catClientes.catalogo.cliente_id = cotizacion.cliente_id;
    this.empezable.empezable_type = cotizacion.cliente_tipo;
    this.formulario = cotizacion;
    this.campoDisabled.estadoDisabled = false;
    cotizacion.items.forEach(function(item){
      item.atributos=item.item.atributos || [];
      item.item_hidden = item.item_id;
      item.nombre = item.item.nombre;
    });

    Articulos.items = cotizacion.items;
    this.tablaItem = cotizacion.items;
    this.comentarios = cotizacion.landing_comments;
  }
 },
watch:{

   'catClientes.catalogo.cliente_id'(val, oldVal){

       var context = this;


     if(this.config.vista === 'crear'){
       this.formulario.cliente_id = val;
     this.formulario.saldo_pendiente ='';
     this.formulario.credito_favor = '';
     this.catalogoFormulario.centro_facturable_id = '';
     this.catalogoFormulario.centro_facturable = [];


     if(catClientes.catalogo.tipo ==='cliente' && !_.isEmpty(val)){
       //se popula saldo, credito,los centros facturables
       var cliente_actual = _.find(catClientes.catalogo.clientes,(query)=> query.id == val);
        this.formulario.saldo_pendiente = cliente_actual.saldo_pendiente;
        this.formulario.credito_favor = cliente_actual.credito_favor;
        this.catalogoFormulario.centro_facturable = cliente_actual.centro_facturable;
        if(cliente_actual.centro_facturable.length > 0){
          var centro_seleccionado = _.head(cliente_actual.centro_facturable);
          this.formulario.centro_facturacion_id = centro_seleccionado.id;
          _.forEach(cliente_actual.centro_facturable, function(o){
              if(o.principal == 1){context.formulario.centro_facturacion_id = o.id;}
          });
        }
     }
     }
   },
   'catClientes.catalogo.clientes'(val,oldVal){
     if(val.length>0 && this.config.vista === 'editar'){
       var valcliente = catClientes.catalogo.cliente_id
       this.formulario.cliente_id = valcliente;

       if(catClientes.catalogo.tipo ==='cliente'){
         var cliente_actual = _.find(val,(query)=> query.id == valcliente);
         this.$set('formulario.saldo_pendiente',cliente_actual.saldo_pendiente);
         this.formulario.credito_favor = cliente_actual.credito_favor;
         this.catalogoFormulario.centro_facturable = cliente_actual.centro_facturable;
       }

     }
   },
   'formulario.lista_precio_alquiler_id': function (val, oldVal) {

     if(_.isEmpty(val) && this.config.vista != 'editar'){
       //Si no selecciona ninguna lista de precio
       //limpiar valores de periodo y tarifa.
       _.forEach(this.catalogoFormulario.articulos, function(articulo) {
         Vue.nextTick(function(){
           articulo.periodo_tarifario = '';
           articulo.tarifa = '';
         });
       });
       return;
     }

     //Al cambiar precio de lista
     //actualizar las tarifas.
     this.$broadcast('setTarifa');
   }
 }
});
