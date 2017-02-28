<style>
 .ibox,.m-b-sm{
     margin-bottom: 0px;
 }
</style>

<template src="./formulario.html"> </template>

<script>
import { formEmpezable } from './../../../vue/state/empezable';
import { moduloPopularFormulario } from './../class/popular-formulario';
import { formulario} from './../data/data-formulario';

export default {
    props: ['catalogos', 'factura', 'config','campoDisabled'],
    data(){
        return{
            formEmpezable: formEmpezable,
            formulario: formulario,
            estado_inicial:'',
            defaultFormualrio: formulario,
            select2_clients:{url: phost() + 'clientes/catalogo/search',using:['id', 'nombre']},
        };
    },
    ready(){
        this.select2Event();
    },
    destroyed(){
        this.select2offEvent();
    },
    directives: {
		'datepicker': require('./../../../vue/directives/datepicker.vue'),
        'select2-catalog': require('./../../../vue/directives/select2-catalog.vue'),
        'select2': require('./../../../vue/directives/select2.vue')
	},
    vuex: {
		getters: {
			empezable_type: (state) => state.empezable_type,
			currentEmpezableType: (state) => state.current,
			empezable_id: (state) => state.empezable_id
		}
	},
    computed:{
        disableCliente(){
            return this.config.vista == 'editar' || (this.currentEmpezableType !== null);
        },
        disableCampo(){
            return this.config.vista == 'editar';
        },
        disabledEstado(){
            return (this.config.vista =="crear" && this.campoDisabled == true) && (this.estado_inicial !="por_aprobar") ;
        },
        disabledVendedor(){
            return this.disableCampo || (this.config.vista =="crear");
        },
        disabledPorModulo(){
          return this.currentEmpezableType != null && this.currentEmpezableType.toString().match(/orden_alquiler/gi);
        },
        getEstados(){
            let estados = this.catalogos.estados;
            //editar y estado por aprobar
            if(this.config.vista == "editar" && this.estado_inicial == "por_aprobar"){
             estados = estados.filter(filtro =>((filtro.etiqueta !== 'cobrado_completo' && filtro.etiqueta !== 'cobrado_parcial')));
            }
            // editar y estado por cobrar
            if(this.config.vista == "editar" && this.estado_inicial == "por_cobrar"){
             estados = estados.filter(filtro =>((filtro.etiqueta !== 'por_aprobar' && filtro.etiqueta !== 'cobrado_completo' && filtro.etiqueta !== 'cobrado_parcial')));
            }
            return estados;
        },
        getCentroFacturacion(){

            let centro_facturable = this.formulario.centro_facturable;
            if(centro_facturable.length == 1 && this.config.vista =="crear"){
                this.formulario.centro_facturacion_id = _.head(centro_facturable).id;
            }
            return centro_facturable;
        },
        getTerminosPagos(){
            let termino_pago = this.catalogos.termino_pago;
            if(this.config.vista =="crear"){
                this.formulario.termino_pago = 'al_contado';
            }
            return termino_pago;

        },
        getUsuarios(){
            let usuarios = this.catalogos.vendedor;
            //Seleccionar usuario logiado por default
            //no aplicar para empezar desde alquiler.
            if(this.config.vista =="crear" && this.currentEmpezableType != "orden_alquiler"){
                this.formulario.created_by = window.usuario_id;
            }
            return usuarios;
        },
        esAlquiler(){
          return this.currentEmpezableType === 'orden_alquiler' ? true : false;
        },
        esVentas(){
          return this.currentEmpezableType === 'orden_venta' || this.currentEmpezableType === 'contrato_venta' || this.currentEmpezableType == null  ? true : false;
        },
        getFormulario(){
            //el campo formulario solo para identificar el tipo de factura
            // esto es informativo para BI
            if(this.currentEmpezableType === null || _.isEmpty(this.currentEmpezableType)){
                return 'factura_venta';
            }
            return this.currentEmpezableType;
        }
    },
    methods:{
        select2Event(){
            //$("#cliente_id").on("change",this.cambiarCliente);
            $("#lista_precio_id").on("change",this.cambiarListaPrecio);
        },
        select2offEvent(){
            $("#cliente_id").off("change");
            $("#lista_precio_id").off("change");
        },
        cambiarCliente(e){
             let cliente_id = e;
             if(!_.isEmpty(cliente_id) && this.currentEmpezableType === null){

                 let cliente = this.catalogos.clientes.find((q)=> q.id == cliente_id);
                 this.formulario.credito_favor = cliente.credito_favor;
                 this.formulario.saldo_pendiente = cliente.saldo_pendiente;
                 this.formulario.centro_facturable = cliente.centro_facturable;
             }
        },
        cambiarListaPrecio(e){
            let precio_id = e.target.value;

            if(_.isEmpty(precio_id)){
                this.$store.dispatch('SET_PRECIO',null);
                return;
            }

            let precio = this.catalogos.lista_precio.find((q)=>q.id == precio_id);

            if(_.isUndefined(precio)){
                 this.$store.dispatch('SET_PRECIO',null);
                 return;
            }

            this.$store.dispatch('SET_PRECIO',precio);
        },
        llenarFormulario(selecionado) {
			let formulario = new moduloPopularFormulario(this, selecionado);
			formulario[this.currentEmpezableType]();
		},
        setDatosFacturas(datos){
            let formulario = new moduloPopularFormulario(this, datos);
			formulario.editar();
        }
    },
    watch:{
        'empezable_id'(val, oldVal){
            if (!_.isEmpty(val) && this.config.vista == "crear") {
				//this.limpiar_otros_datos();
				this.formEmpezable.opcionSeleccionada = _.find(this.formEmpezable.catalogo, (cat => cat.id == val));
				this.llenarFormulario(this.formEmpezable.opcionSeleccionada);
			}
        },
        'formulario.cliente_id'(val, oldVal){
            if (!_.isEmpty(val) && this.config.vista == "crear") {
				 var self= this;
				  Vue.nextTick(function () {
				  self.cambiarCliente(val);
				});
			}
        },
        'factura' (val, oldVal) {
			this.setDatosFacturas(val);
		},
        'currentEmpezableType' (val, oldVal) {
			if (this.config.vista == 'crear') {
				var self = this;
				var empezable_id = this.formEmpezable.aux_empezable_id;
				Vue.nextTick(function () {
					self.formEmpezable.empezable_type = val;
					self.formEmpezable.aux_empezable_id = empezable_id;
					self.formEmpezable.empezable_id = empezable_id;
					//self.limpiarCampos();
				});
			}
		}
    },
    events: {
        select_result: function(result){
        if (this.config.vista == 'crear') {
            this.catalogos.clientes=result;
            }
        },
        selected: function(result){
            var self=this;
            self.formulario.centro_facturable=result.centro_facturable;
            Vue.nextTick(function () {
               self.formulario.credito_favor = result.credito_favor;
                 self.formulario.saldo_pendiente = result.saldo_pendiente;
				  self.cambiarCliente(result.id);
				});
        }

    }

}
</script>
