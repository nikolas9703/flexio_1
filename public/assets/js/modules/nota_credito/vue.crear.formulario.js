Vue.http.options.emulateJSON = true;
var notaCreditoFormulario = new Vue({
  el:'#crear_nota_credito',
   mixins: [guardar],
  data:{
    acceso: acceso === 1? true : false,
    vista: vista,
    tablaError:'',
    datosFactura:{cliente:{},created_by:'',centro_contable_id:""},
    datos:{estado:'por_aprobar'},
    filas:[{descripcion:'',cuenta_id:'',monto:'0.00'}],
    mensaje:"",
    cabecera:{tipo:'',tipoId:'',coleccion:[]},
    disabledFactura:false,
    defaultDisable:true,
    estadoDisable:true,
    botonDisabled:false,
    disabledCabecera:false,
    disabledComentario:false,
    disabledOpcionTipo:true,
    incluir:false,
    vista_comments:"",
    comentarios:[],
    itemsFactura:[{id:'',impuesto_total:0,precio_total:0,cuenta_id:'',credito:0,inventario_item:{id:'',nombre:''},impuesto:{id:''}}]
  },
  components:{
    'nota-credito-items':tablaComponenteNotaCredito,
    'comments':notaCreditoComentario
  },
  ready:function(){
    if(this.vista==='ver'){
      this.datos = nota_credito;
      this.cabecera.tipo = nota_credito.tipo;
      this.cabecera.coleccion = [nota_credito.factura];
      this.cabecera.tipoId = [nota_credito.factura_id];
      this.datosFactura = nota_credito.factura;
      this.datosFactura.cliente =  nota_credito.cliente;
      this.comentarios =  nota_credito.comentario;
      nota_credito.items.forEach(function(item){
        var precio_total =  _.find(nota_credito.factura.items,function(query){ return query.item_id == item.item_id; });
        item.precio_total =  precio_total.precio_total;
      });
      this.itemsFactura = nota_credito.items;
      this.disabledComentario = true;
      this.disabledFactura = true;
      this.disabledCabecera = true;
      if(nota_credito.estado=='anulado'){
          this.estadoDisable = true;
          this.botonDisabled = true;
      }else{
          this.estadoDisable = false;
      }

      this.vista_comments ="comments";
      this.$nextTick(function(){
        CKEDITOR.replace('tcomentario',
        {
          toolbar :
      		[
      			{ name: 'basicstyles', items : [ 'Bold','Italic' ] },
      			{ name: 'paragraph', items : [ 'NumberedList','BulletedList' ] }
      		],
          uiColor : '#F5F5F5'
        });
      });
    }
  },
  methods:{
    getDatosSeleccionado:function(){
        //polula el segundo select del header
      var self = this;
      this.$http.post({
        url: phost() + 'facturas/ajax-getFacturadoValido',
        method:'POST',
        data:{erptkn: tkn}
      }).then(function(response){
        if(!_.isEmpty(response.data)){
         self.tablaError="";
         self.$set('cabecera.coleccion',response.data);
         self.$set('tablaError','');
         self.$set('disabledOpcionTipo',false);
       }else{
         //mensaje de error
         self.tablaError="No existen facturas que cumplan con la condiccion para crear nota de credito";
       }
      });

    },
    seleccionarAplicar:function(e){
      this.getDatosSeleccionado();
    },
    llenarFormulario:function(id){

      this.datosFactura = _.find(this.cabecera.coleccion,function(query){
        return query.id == id;
      });
      this.datosFactura.items.forEach(function(item){
        item.credito=0;
        item.impuesto_total=0;
        item.id='';
      });
      this.itemsFactura = this.datosFactura.items;

      if(!_.isEmpty(this.datosFactura)){
        this.disabledFactura = true;
      }
    }

  },
  /*watch:{
    'incluir':function(val,oldVal){
      var context = this;
      if(val){
        this.filas.forEach(function(item){
          item.descripcion = context.datos.narracion;
        });
      }else{
        this.filas.forEach(function(item){
          item.descripcion = '';
        });
      }
    }
  }*/
});
