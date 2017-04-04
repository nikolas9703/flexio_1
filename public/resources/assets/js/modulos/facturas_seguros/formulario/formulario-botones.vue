<style>
 .last-div{
     background-color: #FFFFFF;
     margin-right: 15px;
 }
</style>
<template>
<div class="row last-div">
    <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
      <a :href="facturaUrl" class="btn btn-default btn-block" id="cancelarFormBtn">Cancelar </a>
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
     <button class="btn btn-primary btn-block" name="guardarBtn" id="guardarBtn"  @click="guardar()" :disabled="campoDisabled.botonDisabled  || isAnulado"><span>Guardar</span></button>
    </div>
</div>
</template>

<script>
import guardar from './../../../vue/mixins/guardar';
export default {
   mixins: [guardar],
   data(){
    return {
       facturaUrl: window.phost() + 'facturas/listar',
       campoDisabled:{
           botonDisabled:false
       }
    };
   },
   computed:{
       IsAnulado(){
           return false;
       }
   }
}
</script>
