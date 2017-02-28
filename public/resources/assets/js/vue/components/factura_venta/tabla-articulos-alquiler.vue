<style></style>
<template src="./template/tabla-articulos-alquiler.html"></template>

<script>

import {store} from './../../../modulos/facturas/data/data-formulario';
import articulos from './../../../modulos/facturas/data/data-articulos';

export default {
	props: ['tablaHeader','articulos','catalogos','campoDisabled','config'],
  data(){
        return{
            articulo: 'articulo',
            select2Config:{width:'100%'},
			switchery: null,
            formulario:{
			 cargos_adicionales_checked: false,
			 item_precio_id: '',
			}
        }
    },
		store: store,
		vuex: {
			getters: {
				empezable_type: (state) => state.empezable_type,
				currentEmpezableType: (state) => state.current,
				empezable_id: (state) => state.empezable_id,
				cat_alquiler_lista_precio_venta: (state) => state.cat_alquiler_lista_precio_venta,
			}
		},
		ready(){
        this.initSwitchery();
        this.setPrecioVentaDefault();
    },
    directives:{
         'select2': require('./../../directives/select2.vue'),
     },
    components:{
        'articulo': require('./articulo.vue'),
    },
    computed:{
        getArticulos(){
            return this.articulos;
        },
				disableCampo(){
            return this.config.vista == 'editar';
        }
    },
    methods:{
			addRow(){
					this.articulos.push({categoria_id:'',items:[],item_id:'',atributos:[],cantidad:1, periodo_tarifario:'', tarifa:'', en_alquiler:0, precio_unidad:"0.00", impuesto_id:'', descuento:'0.00', cuenta_id:'', atributo_text:'', atributo_id:'', comentario:'',facturado:true, cuentas:[], tipo_id:'', por_entregar:0, entregado:0, devuelto:0,unidades:[],unidad_id:'',total_impuesto:0, total_descuento:0,impuesto:0,subtotal:0 });
			},
			setPrecioVentaDefault(){
				if(this.cat_alquiler_lista_precio_venta==null){
					return false;
				}

				var scope = this;
				let default_lista_precio = this.cat_alquiler_lista_precio_venta.find((q)=> q.principal == 1);

				Vue.nextTick(function(){
					scope.formulario.item_precio_id = typeof default_lista_precio != 'undefined' ? default_lista_precio.id : '';
				});
			},
			initSwitchery(){
				var scope = this;
				var togglecargoadicional = document.querySelector('#cargos_adicionales');
				this.switchery = new Switchery(togglecargoadicional, {color:"#1ab394", size: 'small'});

				// mostrar ocultar cargos adicionales
				// plugin: switchery
				togglecargoadicional.onchange = function() {
						var checked = this.checked;
						//Vue.nextTick(function(){

							//reset detalle articulos
							scope.formulario.cargos_adicionales_checked = checked;
							scope.articulos = articulos.items;
						//});
				};
			},
			cambiarListaPrecio(e){
          let precio_id = e.target.value;
          if(_.isEmpty(precio_id)){
              this.$store.dispatch('SET_PRECIO',null);
              return;
          }

          let precio = this.catalogos.lista_precio.find((q)=>q.id == precio_id);
					alert('CAMBIANDO PRECIO: ', precio);
          if(_.isUndefined(precio)){
               this.$store.dispatch('SET_PRECIO',null);
               return;
          }

          this.$store.dispatch('SET_PRECIO',precio);
      },
    },
		watch:{
			'articulos'(){
				var scope = this;
				this.formulario.cargos_adicionales_checked = (this.articulos.length>0?true:false);
			}
		}
}
</script>
