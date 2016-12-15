<style>
.moneda{text-align: right};
</style>
<template>
  <!-- Inicia formulario -->
  <div class="ibox border-bottom" style="margin-right: 15px;">
      <div class="ibox-title">
          <h5>Datos del Cobro</h5>
          <div class="ibox-tools">
              <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
          </div>
      </div>

      <div class="ibox-content" style="display:block;">
  <div class="row">

  <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="fecha_desde">Fecha de cobro <span required="" aria-required="true">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            <input type="text" name="campo[fecha_pago]" class="form-control"  id="fecha_pago" data-rule-required="true" v-datepicker2="formulario.fecha_pago" :config="{dateFormat: 'dd/mm/yy'}" data-rule-required="true" :disabled="isEditar">
      </div>
      <label id="fecha_pago-error" class="error" for="fecha_pago"></label>
    </div>

      <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
          <label for="cliente_id">Cliente <span required="" aria-required="true">*</span></label>
          <select name="campo[cliente_id]" class="form-control" id="cliente_id" data-rule-required="true" v-model="formulario.cliente_id" disabled>
            <option v-for="cliente in clientes" :value="cliente.id" v-text="cliente.nombre"></option>
          </select>
          <label id="cliente_id-error" class="error" for="cliente_id"></label>
      </div>

      <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3" v-show="!isEditar"><label></label>
              <div class="input-group">
                  <span class="input-group-addon">$</span>
                  <input type="input-left-addon" disabled v-model="formulario.saldo_pendiente | moneda" name="campo[saldo]" class="form-control moneda"  id="campo[saldo]">
            </div>
            <label class="label-danger-text">Saldo por cobrar</label>
        </div>

        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3" v-show="!isEditar">
          <label></label>
            <div class="input-group">
              <span class="input-group-addon">$</span>
              <input type="input-left-addon" disabled v-model="formulario.credito | moneda" name="campo[lcredito]"  class="form-control moneda" id="campo[lcredito]">
            </div>
            <label class="label-success-text">Crédito a favor</label>
        </div>

        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3" v-if="isEditar">
          <label for="estado">Estado </label>
              <select name="campo[estado]" id="estado" v-model="formulario.estado" class="form-control">
                <option v-for="estado in filtroEstado" :value="estado.etiqueta" v-text="estado.valor"></option>
              </select>
          </div>

  </div>


  <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <!-- <div style="display:table-cell;"> -->
     <table class="table" id="facturaItems">
      <thead>
        <tr>
        <th width="14%">No. Factura</th>
        <th width="14%">Fecha de Emisión</th>
        <th width="14%">Fecha de finalización</th>
        <th width="14%">Monto</th>
        <th width="14%">Pagado</th>
        <th width="14%">Saldo por cobrar</th>
        <th width="">Pago</th>
      </tr>
      </thead>
      <tbody>

      <tr v-for="factura in facturas" id="items{{$index}}" class="item-listing" >
      <td>
        <input type="hidden" id="cobrable_id{{$index}}" name="factura[{{$index}}][cobrable_id]" value="{{factura.id}}">
        <!-- type quemado si se necesita agregar mas formas de cobro hacer un filtro en la populacion-->
        <input type="hidden" id="cobrable_type{{$index}}" name="factura[{{$index}}][cobrable_type]" value="factura">
        <span v-text="factura.codigo"></span>
      </td >
      <td v-text="factura.fecha_desde"></td>
      <td v-text="factura.fecha_hasta"></td>
      <td v-text="factura.total | currency"></td>
      <td v-text="pagado(factura.cobros) | currency"></td>
      <td v-text="saldo_pendiente(factura) | currency"></td>
      <td><label for="precio_total{{$index}}" class="hide"></label>
          <div class="input-group">
          <span class="input-group-addon">$</span>
          <input type="text" id="precio_total{{$index}}" name="factura[{{$index}}][monto_pagado]" class="form-control moneda"  v-text="factura.precio_total" v-on:click.prevent="getSaldoCobrar(factura, $index)" v-on:focusout="cambiarCantidad($index,$event,factura)"  placeholder="0.00" :value="itemPago[$index]" data-rule-min="0.01" :disabled="isEditar"/></div>
          <label id="precio_total{{$index}}-error" class="error" for="precio_total{{$index}}"></label>
      </td>
      </tr>
      </tbody>

    </table>

  <!-- </div> -->

  </div>


  <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <label>Monto</label>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
      <div class="input-group">
          <span class="input-group-addon">$</span>
          <input type="text" id="monto" name="campo[monto_pagado]" :value="monto | moneda" class="form-control select2 moneda" disabled/>
      </div>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <select name="campo[depositable_type]" id="depositable_type" v-model="formulario.depositable_type" class="form-control" :disabled="isEditar">
        <option v-for="tipo in catalogos.depositable" :value="tipo.etiqueta" v-text="tipo.valor"></option>
      </select>
    </div>
      <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">

        <select name="campo[depositable_id]" v-model="formulario.depositable_id" class="form-control" id="cuenta" data-rule-required="true" :disabled="formulario.depositable_type ==='' || isEditar">
          <option value="">Seleccione</option>
          <option v-for="deposito_en in filtroDepositable" :value="deposito_en.id" v-text="deposito_en.nombre"></option>
        </select>
        <label id="cuenta_id-error" class="error" for="cuenta_id"></label>
        </div>
  </div>

  <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 item-listing lists_opciones" id="opciones{{$index}}"  v-for="row in filas_metodo_cobro">
    <div class="lists_opciones" id="opcionesRow{{$index}}">
      <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>Método de Pago <span required="" aria-required="true">*</span></label>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <select class="form-control" name="metodo_pago[{{$index}}][tipo_pago]" id="tipo_pago{{$index}}" v-model="row.tipo_pago" data-rule-required="true" :disabled="isEditar" @change="anticipos_credito(row)">
          <option value="">Seleccione</option>
            <option v-for="metodo in filtroMetodoCobros" :value="metodo.etiqueta" v-text="metodo.valor"></option>
        </select>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label for="total_pagado{{$index}}">Total Pagado <span required="" aria-required="true">*</span></label>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
        <div class="input-group"><span class="input-group-addon">$</span>
            <input type="text" id="total_pagado{{$index}}" name="metodo_pago[{{$index}}][total_pagado]" class="form-control moneda" style="text-align:right" data-rule-required="true"  placeholder="0.00" v-model="row.total_pagado" data-rule-min="0.01" :disabled="isEditar" @keyUp="anticipos_credito(row)"/>
          </div>
          <label id="total_pagado{{$index}}-error" class="error" for="total_pagado{{$index}}"></label>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <button type="button" class="btn btn-default btn-block" agrupador="opciones" v-on:click="$index===0? addRow(row): deleteRow(row)" v-show="currentEmpezableType !=='cliente' && !isEditar"><i class="{{row.icon}}"></i></button>
          </div>
      </div>
    </div>
    </div>
    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 no-left-padding" v-show="row.tipo_pago ==='ach'">
      <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>Banco del Cliente</label>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <input type="text" name="metodo_pago[{{$index}}][nombre_banco_ach]" id="nombre_banco_ach{{$index}}" class="form-control" :disabled="isEditar"/>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>Número de cuenta del Cliente</label>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 last-input">
        <input type="text" name="metodo_pago[{{$index}}][cuenta_cliente]" id="cuenta_cliente{{$index}}" class="form-control" :disabled="isEditar"/>
      </div>
    </div>

    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 no-left-padding" v-show="row.tipo_pago ==='cheque'">
      <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>Número Cheque</label>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <input type="text" name="metodo_pago[{{$index}}][numero_cheque]" id="numero_cheque{{$index}}" class="form-control" :disabled="isEditar"/>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>Nombre Banco</label>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 last-input">
        <input type="text" name="metodo_pago[{{$index}}][nombre_banco_cheque]" id="nombre_banco_cheque{{$index}}" class="form-control" :disabled="isEditar"/>
      </div>
    </div>

  </div>

  <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 last-div">
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 col-md-offset-9 col-lg-offset-9">
      <div class="input-group"><span class="input-group-addon">$</span>
          <input type="text" id="total_pago" name="campo[monto_pagado]" :value="total_cobrado" class="form-control moneda" disabled/>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 col-md-offset-9 col-lg-offset-9">
      <label class="label-info-text">Total</label>
      <label id="totals-error" class="error" v-if="validacionMonto" v-text="mensajeErrorTotales"></label>
      <label id="totals-error" class="error" v-if="mensajeErrorCredito!==''" v-text="mensajeErrorCredito"></label>
    </div>
  </div>

  <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 last-div">
      <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
      <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
        <a :href="cobrosUrl" class="btn btn-default btn-block" id="cancelarFormBtn">Cancelar </a>
      </div>
      <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
       <input type="hidden" name="campo[id]" id="cobro_id" value="{{formulario.id}}" :disabled="formulario.id ===''"/>
       <button class="btn btn-primary btn-block" name="guardarBtn" id="guardarBtn"  @click="guardar()" :disabled="campoDisabled.botonDisabled || validacionMonto || isAnulado"><span>Guardar</span></button>
      </div>
  </div>
</div>
</div>
</div>
  <!-- Termina formulario -->

</template>

<script>
import guardar from './../../../vue/mixins/metodo_guardar';
import {formEmpezable} from './../../../vue/state/empezable';
import {moduloCobrosInfo} from './../clase/cobros-popular-formulario';
import {get_cobro} from './../data/data-formulario';
export default {
  props:['catalogos','cobro','config'],
  mixins:[guardar],
  data(){
    return{
      formEmpezable:formEmpezable,
      formulario:{
        id:'',
        saldo_pendiente:'0.00',
        credito:'0.00',
        fecha_pago:moment().format('DD/MM/YYYY'),
        monto:'0.00',
        depositable_id:'',
        depositable_type:'banco',
        cliente_id:'',
        estado:'aplicado'
      },
      clientes:[],
      facturas:[],
      filas_metodo_cobro:[
        {icon:'fa fa-plus', tipo_pago:'',total_pagado:0.00,referencia:{
          nombre_banco_ach:'',cuenta_cliente:'',numero_cheque:'',nombre_banco_cheque:''
        }}
      ],
      itemPago:[],//usado pra  la filas de las facturas
      montos:[],
      campoDisabled:{
        botonDisabled:false,
        camposEditar:false,
        estadoDisabled:true,
    },
    estado_inicial:'',
    mensajeErrorTotales:'',
    mensajeErrorCredito:'',
    filtar_metodo:false,
    cobrosUrl: window.phost() + 'cobros/listar'
    };
  },
  vuex:{
      getters:{
          empezable_type: (state) => state.empezable_type,
          currentEmpezableType: (state) => state.current,
          empezable_id:(state) => state.empezable_id
      }
  },
  filters:{
    moneda:require('./../../../vue/filters/currency-two-way.vue'),
  },
  computed:{
    filtroDepositable(){
      if(this.formulario.depositable_type == 'banco'){
        return this.catalogos.cuenta_bancos;
      }
      return this.catalogos.cajas;
    },
    monto(){
      if(this.montos.length === 0){
        return 0;
      }
      return _.sum(this.montos);
    },
    total_cobrado(){
      if(this.montos.length === 0){
        return 0;
      }
      return total_cobrado = _.sumBy(this.filas_metodo_cobro,(o)=>parseFloat(o.total_pagado));
    },
    validacionMonto(){
        if(this.monto !== this.total_cobrado && !this.isEditar){
            this.mensajeErrorTotales = "El total debe ser igual al monto";
            return true;
        }
        this.mensajeErrorTotales = "";
        return false;
    },
    isEditar(){
        return this.config.vista == 'ver';
    },
    filtroEstado(){
        if(this.config.vista =='crear'){
            return [];
        }
        if(this.estado_inicial === "aplicado"){
            return this.catalogos.estados;
        }

        if(this.estado_inicial === "anulado"){
            return this.catalogos.estados.filter(est => est.etiqueta =='anulado');
        }

    },
    filtroMetodoCobros(){
        if(this.filtar_metodo){
            return this.catalogos.metodo_cobro.filter(mtd => mtd.etiqueta != 'credito_favor');
        }
        return this.catalogos.metodo_cobro;
    },
    formatoDinamico(){
        if(this.facturas.length > 0 || this.filas_metodo_cobro.length > 0){
           this.$nextTick(function(){
            this.inputmask_currency();
           });
        }
    },
    isAnulado(){
        return this.estado_inicial === "anulado";
    }
  },
  methods:{
    llenarFormulario(selecionado){
      let formulario = new moduloCobrosInfo(this,selecionado);
      formulario[this.currentEmpezableType]();
    },
    setDatosCobros(datos){
      let formulario = new moduloCobrosInfo(this,datos);
      formulario.editar();
    },
    pagado(cobros){
      return  _.sumBy(cobros, (o) => parseFloat(o.pivot.monto_pagado)) || 0;
    },
    saldo_pendiente(factura){
      let saldo_pendiente = parseFloat(factura.total) - this.pagado(factura.cobros);
      return parseFloat(accounting.toFixed(saldo_pendiente,2));
    },
    getSaldoCobrar(factura,i){
       this.inputmask_currency();
      if(_.isUndefined(this.itemPago[i])){
        this.itemPago.$set(i,this.saldo_pendiente(factura));
        this.montos.$set(i,this.saldo_pendiente(factura));
      }
    },
    cambiarCantidad(i,event,factura){
      var total = parseFloat(accounting.unformat(event.target.value)) || 0;
      if(total < 0.01){
        toastr.error("El pago no puede ser 0", 'Cobros');
        this.campoDisabled.botonDisabled = true;
        return;

      }else if(total > this.saldo_pendiente(factura)){
        toastr.error("El pago no puede mayor a la cantidad a cobrar", 'Cobros');
        this.campoDisabled.botonDisabled = true;
        return;
      }
      this.itemPago.$set(i,total);
      this.montos.$set(i,total);
      this.campoDisabled.botonDisabled = false;
    },
    addRow(){
        this.filas_metodo_cobro.push({icon:'fa fa-trash', tipo_pago:'',total_pagado:0.00,referencia:{
          nombre_banco_ach:'',cuenta_cliente:'',numero_cheque:'',nombre_banco_cheque:''
      }});
      this.formatoDinamico;
    },
    deleteRow(row){
        this.filas_metodo_cobro.$remove(row);
        this.formatoDinamico;
    },
    logica_credito(){
        var metodo_credito = this.filas_metodo_cobro.filter((met)=> met.tipo_pago == 'credito_favor');
        var monto_pagado = _.sumBy(metodo_credito,(o)=> parseFloat(o.total_pagado));
        var credito = parseFloat(this.formulario.credito);
        if(monto_pagado > credito){
            this.mensajeErrorCredito = "su credito es insuficiente para realizar el cobro";
            this.campoDisabled.botonDisabled = true;
            return;
        }else{
            this.mensajeErrorCredito = "";
            this.campoDisabled.botonDisabled = false;
            return;
        }
    },
    anticipos_credito(row){
        if(row.tipo_pago == 'credito_favor'){
            return this.logica_credito();
        }
        this.mensajeErrorCredito = "";
        this.campoDisabled.botonDisabled = false;
        return;
    },
    inputmask_currency(){
        $(".moneda").inputmask('currency',{
          prefix: "",
          autoUnmask : true,
          removeMaskOnSubmit: true
        });
    }
 },
 directives:{
   'datepicker2':require('./../../../vue/directives/datepicker.vue')
 },
 watch:{
  'empezable_id'(val, oldVal){
    if(!_.isEmpty(val) && this.config.vista =="crear"){
      this.formEmpezable.opcionSeleccionada = _.find(this.formEmpezable.catalogo,(cat => cat.id ==val));

      this.llenarFormulario(this.formEmpezable.opcionSeleccionada);
    }
  },
  'cobro'(val,oldVal){
      this.setDatosCobros(val);
  },'currentEmpezableType'(val,oldVal){
      if(this.config.vista =='crear'){
          var self = this;
          var empezable_id = this.formEmpezable.aux_empezable_id;
          Vue.nextTick(function(){
                self.formEmpezable.empezable_type = val;
                self.formEmpezable.aux_empezable_id = empezable_id;
                self.formEmpezable.empezable_id = empezable_id;
          });
      }
  },
  'filas_metodo_cobro'(val,oldval){
      var metodo_credito = this.filas_metodo_cobro.filter((met)=> met.tipo_pago == 'credito_favor');
      if(metodo_credito.length >  0){
          this.logica_credito();
      }else{
          this.mensajeErrorCredito = "";
          this.campoDisabled.botonDisabled = false;
      }
  }
 }
}
</script>
