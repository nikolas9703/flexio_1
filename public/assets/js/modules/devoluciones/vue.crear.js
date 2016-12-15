var devolucionFormulario = new Vue({
  el:'#devoluciones_crear',
   mixins: [guardar],
  data:{
    acceso: acceso === 1? true : false,
    vista: vista,
    tablaError:'',
    datosDevolucion:{cliente_id:''},
    articulos:[],
    listaTipo:[],
    devolucionHeader:{tipo:'',uuid:''},
    disableDevolucion:false,
    headerDisabled:false,
    botonDisabled:false,
    disableCliente:true,
    mensajeError:''
  },
  components:{
    'devolucion-productos':tablaComponenteDevoluciones
  },
  ready:function(){
    if(this.vista==='ver'){
      //moduloDevoluciones.vistaVer();
      this.devolucionHeader.tipo = 'factura';
      this.listaTipo = [devolucion.facturas];
      this.devolucionHeader.uuid =  devolucion.facturas.uuid_factura;
      this.datosDevolucion = devolucion;
      this.datosDevolucion.saldo = devolucion.facturas.cliente.saldo_pendiente;
      console.log('pase');
      this.datosDevolucion.credito = devolucion.facturas.cliente.credito;
      this.articulos = devolucion.items;
      this.disableDevolucion = true;
      this.headerDisabled = true;
      if(this.datosDevolucion.estado !=='por_aprobar'){
        this.botonDisabled = true;
      }
    }
  },
  methods:{
    empezarDesde:function(tipo){
      var self = this;
      var desde = moduloDevoluciones.empezarDesde({tipo: tipo});
      desde.done(function(data){
        self.listaTipo = data;
      });
    },
    llenarFormulario:function(uuid){
      moduloDevoluciones.filtrar(this.listaTipo,uuid);
  },

  }
});
