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

import configDate from './../../config/fechas';
import catClientes from './../../clientes';
import Articulos from './../../items';
var  guardar = require('./../../mixin_guardar_item_alquiler');
Vue.transition('listado',{
    enterClass:'fadeIn',
    leaveClass:'fadeOut'
});
Vue.directive('select2', require('./../../vue/directives/select2.vue'));
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
    config:{
      vista:window.vista,
      acceso:window.acceso == 1? true: false,
      modulo:'none',
      disableEditar:false,
      select2:{
        width:'100%'
      },
      inputmask:{

          cantidad: {'mask':'9{1,4}','greedy':false},
          descuento: {'mask':'9{1,2}[.9{0,2}]','greedy':false},
          currency: {'mask':'9{1,8}[.9{0,2}]','greedy':false},
          currency2: {'mask':'9{1,8}[.9{0,4}]','greedy':false}

      },
      modulo:'ordenes_alquiler',//debe ir ordenes_ventas
      disableValidate: true, //no validar campos
      //editarPrecio: window.editar_precio,
    },
    catalogos:{
      clientes:window.clientes,
      precios: window.precios,
      categorias:window.categorias,
      cuentas:window.cuentas,
      impuestos:window.impuestos,
      usuario_id:window.usuario_id,
      periodos_tarifario: [],
      aux:{}
    },
    catalogoFormulario:{
      centro_contables:[],
      estados:[],
      terminos_pagos:[],
      vendedores:[],
      centro_facturable:[],
      lista_precio_alquiler: window.lista_precio_alquiler,
      precios:[]
    },
    //tablaItem:[],
    formulario:formulario,
    detalle:{
      item_precio_id: window.precios_venta_id_default,
      cargos_adicionales_checked: false,
      articulos_alquiler:[],
      articulos:[
          {
              id:'',
              cantidad: 1,
              categoria_id: '',
              cuenta_id: '',
              cuentas:'[]',
              descuento: '',
              impuesto_id: '',
              item_id: '',
              item_hidden_id: '',
              items:[],
              precio_total: '',
              precio_unidad: '',
              precios:[],
              unidad_id: '',
              unidad_hidden_id:'',
              unidades:[],
              descripcion: '',
              facturado:false,
              atributos:[],
              atributo_text:'',
              atributo_id:''
          }
      ],
    },
    configDatepicker:configDate,
    catClientes,
    comentarios:[],
    campoDisabled:{
      clienteDisabled:true,
      estadoDisabled:true,
      botonDisabled:false
    }
  },


  ready(){
    this.cargarCatalogoDefault();

    var scope = this;
    var togglecargoadicional = document.querySelector('#cargos_adicionales');
    window.switcherycheck = new Switchery(togglecargoadicional, {color:"#1ab394", size: 'small'});

    // mostrar ocultar cargos adicionales
    // plugin: switchery
    togglecargoadicional.onchange = function() {
        var checked = this.checked;
        Vue.nextTick(function(){

        //reset detalle articulos
        scope.detalle.cargos_adicionales_checked = checked;
        checked == false ? scope.detalle.articulos = [{
              id:'',
              cantidad: '',
              categoria_id: '',
              cuenta_id: '',
              cuentas:'[]',
              descuento: '',
              impuesto_id: '',
              item_id: '',
              item_hidden_id: '',
              items:[],
              precio_total: '',
              precio_unidad: '',
              precios:[],
              unidad_id: '',
              unidad_hidden_id:'',
              descripcion: '',
              facturado:false,
              atributos:[],
              atributo_text:'',
              atributo_id:''
          }] : '';
        });
    };

    if (this.config.vista == 'editar') {
        this.editEmepezarDesde();
    }

    console.log('ready');

  },
  components:{
    'empezar-desde':require('./../../vue/components/empezable.vue'),
    'tabla-articulos':require('./../../vue/components/tabla-articulos.vue'),
    'articulos':require('./../../vue/components/tabla-dinamica.vue'),
    'vista_comments':require('./../../vue/components/comentario.vue'),
    'totales-alquiler':require('./../../vue/components/tabla-totales-alquiler.vue')
  },
  compute: {
      centrofacturadisable: function() {
          var centrofacturablelength = typeof(catalogoFormulario.centro_facturable.length)!= 'undefined' ? catalogoFormulario.centro_facturable.length : 0;
          if (this.disabledEditar || (centrofacturablelength === 0)){
              return true;
          }else{
              return false;
          }
      }
  },
  methods:{
    ajax: function(url, data) {
      var scope = this;
      return Vue.http({
          url: phost() + url,
          method: 'POST',
          data: $.extend({erptkn: tkn}, data)
      });
    },
    editEmepezarDesde(){
        var context = this;
        var ajaxurl = '';
        if(cotizacion_alquiler.cliente_tipo=='cliente'){
            ajaxurl = 'ajax_catalogo/cat_clientes';
        } else if(cotizacion_alquiler.cliente_tipo=='clientes_potenciales'){
            ajaxurl = 'ajax_catalogo/catalogo_clientes_potenciales';
        }
        var cid = cotizacion_alquiler.cliente_id;
        this.ajax(ajaxurl, {cliente_id: cid}).then(function (response) {
            context.$nextTick(function(){
              if(context.config.vista ==="editar"  && !_.isEmpty(window.uuid_cotizacion)){
                  catClientes.catalogo.clientes = response.data;
                  console.log(response.data);
                  Vue.nextTick(function(){
                      catClientes.catalogo.cliente_id = cid;
                      catClientes.catalogo.tipo = cotizacion_alquiler.cliente_tipo;
                      window.empezable_id.value = cid;
                  });
                }
            });
        });
       // catClientes.catalogo.cliente_id = cid;
        //jaime

    },

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
          self.catalogos.periodos_tarifario = response.data.articulos.periodos_tarifario;

          if(self.config.vista ==="crear"){

              self.detalle.articulos_alquiler = Articulos.items;
              //self.tablaItem = Articulos.items;
              self.formulario.creado_por = window.usuario_id || '';
              self.formulario.termino_pago = '30_dias';

              self.popular_precio_alquiler();
              self.popular_centro_contable();
          }
          if(self.config.vista ==="editar"  && !_.isEmpty(window.uuid_cotizacion)){
              self.config.disableEditar = true;
              self.cargarDatos(window.uuid_cotizacion);
          }
      });
    });
  },
  popular_centro_contable(){
    if(this.catalogoFormulario.centro_contables.length > 0){
      let primer_centro = _.head(this.catalogoFormulario.centro_contables);
      this.formulario.centro_contable_id = primer_centro.centro_contable_id;
      $('#centro_contable_id').val(primer_centro.centro_contable_id);
      $('#centro_contable_id').trigger('change.select2');
    }
  },
  popular_precio_alquiler(){
    if(_.some(this.catalogoFormulario.lista_precio_alquiler,{principal:1})){
      let precio_princial = _.find(this.catalogoFormulario.lista_precio_alquiler,['principal',1]);
      this.formulario.lista_precio_alquiler_id = precio_princial.id;
  }else if(this.catalogoFormulario.lista_precio_alquiler.length > 0 ){
      let precio_princial = _.head(this.catalogoFormulario.lista_precio_alquiler);
      this.formulario.lista_precio_alquiler_id = precio_princial.id;
    }else if(this.catalogoFormulario.lista_precio_alquiler.length === 0){
      toastr.info("configure su lista de precios");
    }
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
          cotizacion.items = response.data.items_alquiler;

      self.setData(cotizacion);
    });

  },
  setData(cotizacion){

    catClientes.catalogo.tipo = cotizacion.cliente_tipo;
    catClientes.catalogo.cliente_id = cotizacion.cliente_id;
    this.empezable.empezable_type = cotizacion.cliente_tipo;
    this.formulario = cotizacion;
    this.campoDisabled.estadoDisabled = false;

    //Items Alquiler
    cotizacion.items.forEach(function(item){
      item.atributos=item.item.atributos || [];
      item.item_hidden = item.item_id;
      item.nombre = item.item.nombre;
    });

    //Items Adicionales
    cotizacion.items_adicionales.forEach(function(item){
      item.atributos=item.item.atributos || [];
      item.item_hidden_id = item.item_id;
      item.nombre = item.item.nombre;
      item.unidades = item.item.unidades;
      item.impuestos = item.item.impuestos;
      item.unidad_id = item.unidad_id;
    });

    Articulos.items = cotizacion.items;
    this.detalle.articulos_alquiler = cotizacion.items;
    this.comentarios = cotizacion.landing_comments;

    //Vista de editar
    if(this.config.vista=='editar') {

        var scope = this;
        Vue.nextTick(function(){
            scope.detalle.articulos = cotizacion.items_adicionales != 'undefined' ? cotizacion.items_adicionales : [];
            if(scope.detalle.articulos.length > 0){
              scope.detalle.cargos_adicionales_checked = true;
              window.switcherycheck.bindClick();
            }else{
              scope.detalle.articulos = [{
                id:'',
                cantidad: '',
                categoria_id: '',
                cuenta_id: '',
                cuentas:'[]',
                descuento: '',
                impuesto_id: '',
                item_id: '',
                item_hidden_id: '',
                items:[],
                precio_total: '',
                precio_unidad: '',
                precios:[],
                unidad_id: '',
                unidad_hidden_id:'',
                descripcion: '',
                facturado:false,
                atributos:[],
                atributo_text:'',
                atributo_id:''
              }];
            }
        });
    }

  }
 },
watch:{



   'catClientes.catalogo.cliente_id'(val, oldVal){
     var self = this;
     if(this.config.vista === 'crear'){
       this.formulario.cliente_id = val;
     this.formulario.saldo_pendiente ='';
     this.formulario.credito_favor = '';
     this.catalogoFormulario.centro_facturable_id = '';
     this.catalogoFormulario.centro_facturable = [];

     if(catClientes.catalogo.tipo ==='cliente' && !_.isEmpty(val)){
       //se popula saldo, credito,los centros facturables
       var cliente_seleccionado = _.find(catClientes.catalogo.clientes,(query)=> query.id == val);

        this.formulario.saldo_pendiente = cliente_seleccionado.saldo_pendiente;
        this.formulario.credito_favor = cliente_seleccionado.credito_favor;

        //Buscar info centros de facturacion
        //del cliente seleccionado
        this.ajax('clientes/ajax_get_centros_facturacion', {cliente_id: val}).then(function (response) {
            if(response.data.centro_facturable.length > 0){
              self.catalogoFormulario.centro_facturable = response.data.centro_facturable;
              var centro_seleccionado = _.head(response.data.centro_facturable);
              self.formulario.centro_facturacion_id = centro_seleccionado.id;
            }
        });
     }
     } else if (this.config.vista === 'editar' && catClientes.catalogo.tipo === 'cliente') {
         //Buscar info centros de facturacion
        //del cliente seleccionado al editar #case981

        this.ajax('clientes/ajax_get_centros_facturacion', {cliente_id: val}).then(function (response) {
            self.$nextTick(function () {
                if(response.data.centro_facturable.length > 0){
                  self.catalogoFormulario.centro_facturable = response.data.centro_facturable;
                  var centro_seleccionado = _.head(response.data.centro_facturable);
                  self.formulario.centro_facturacion_id = centro_seleccionado.id;

                }
            });
        });
     }
   },
   'catClientes.catalogo.clientes'(val,oldVal){
       console.log("clientes:",val);
     if(val.length>0 && this.config.vista === 'editar'){
       var valcliente = catClientes.catalogo.cliente_id;
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
       _.forEach(this.detalle.articulos_alquiler, function(articulo) {
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

Vue.nextTick(function () {
    vmTablaAlquilar.guardar();
});
