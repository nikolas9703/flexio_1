<template>
    <div class="row" style="margin-right: 15px;">

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 header-empezable">

            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 text-header">

                <span><strong v-text="titulo"></strong></span>

            </div>

            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">

                <select name="empezable[empezable_type]" id="empezable_type" v-select3="formEmpezable.empezable_type" :config="config" class="form-control select2" :disabled="disableEmpezarType">
                    <option value="">Seleccione</option>
                    <option v-for="type in getOpciones" :value="type.value" >{{type.label}}</option>
                </select>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                 <select class="form-control select2" name="empezable[empezable_id]" id="empezable_id"  v-select3="formEmpezable.empezable_id" :config="config" :disabled="disableEmpezarId || isDisable">
                    <option value="">Seleccione</option>
                    <option :value="emp.id" v-for="emp in formEmpezable.catalogo" v-text="emp.nombre"></option>
                </select>
            </div>

        </div>

    </div>
</template>


<script>
import {formEmpezable} from './../state/empezable';
export default {

    props:['info','empezable'],
    data(){

        return {
            disableEmpezarId:true,
            disableEmpezarType:false,
            disableEditar:this.info.disableEditar,
            urls_catalogo:[],
            titulo:'',
            config:{},
            formEmpezable:formEmpezable
        };
    },
    created(){
        this.titulo = this.empezable.datos_empezable.titulo;
        this.urls_catalogo = this.empezable.urls_catalogo;
        this.config = this.empezable.datos_empezable.configSelect2;
    },
    vuex:{
        getters:{
            empezable_type: (state) => state.empezable_type,
            currentEmpezableType: (state) => state.current,
            empezable_id:(state) => state.empezable_id
        }
    },
    computed:{
        //son los catalogos del primer select
        getOpciones(){
            return this.empezable_type;
        },
        isDisable(){
            return this.info.disableEditar;
        }
    },

    methods:{
        fillCatalogo(){
            var datos = {erptkn: tkn};
            var catalogo = this.getCatalogo(datos);
            var self = this;
            catalogo.then((response)=>{
                if(_.has(response.data, 'session')){
                  window.location.assign(window.phost());
                  return;
                }

                self.formEmpezable.catalogo = response.data;

                if(this.info.vista === "crear"){
                   self.disableEmpezarId=false;

               }else{
                   self.disableEmpezarId=true;
                   self.disableEmpezarType=true;
                   self.formEmpezable.empezable_id = self.formEmpezable.aux_empezable_id;
               }

               if(!_.isNull(self.formEmpezable.aux_empezable_id)){
                   self.disableEmpezarId=true;
                   self.disableEmpezarType=true;
                   self.formEmpezable.empezable_id = self.formEmpezable.aux_empezable_id;
               }
            });
        },

        getCatalogo(datos){

            if(_.isEmpty(this.formEmpezable.empezable_type)){

                toastr.error("no seleccionado nada del catalogo")
                throw "500";
                return '';
            }
            var inKey = _.has(this.urls_catalogo,this.formEmpezable.empezable_type);

            if(inKey){
                if(!_.isNull(this.formEmpezable.aux_empezable_id)){
                   datos = $.extend(datos,{id:this.formEmpezable.aux_empezable_id});
               }
                return this.$http.post({url: window.phost() + this.urls_catalogo[this.formEmpezable.empezable_type], method:'POST',data:datos});
            }
            throw "500";
            return '';
        }
    },
    watch:{
        'formEmpezable.empezable_type'(val, oldVal){

            if(this.info.vista === "crear"){

                this.formEmpezable.empezable_id ='';
                if(!_.isEmpty(val)){
                   this.$store.dispatch('SET_CURRENT',val);
                   this.fillCatalogo();
                }
                this.disableEmpezarId=true;
            }else{
                this.formEmpezable.empezable_id = this.formEmpezable.aux_empezable_id;
                this.$store.dispatch('SET_CURRENT',val);
                this.fillCatalogo();
            }
        },
        'formEmpezable.empezable_id'(val, oldVal){
            //if(!_.isEmpty(val)){
               this.$store.dispatch('SET_EMPEZABLEID',val);
            //}
        }
    }
}

</script>
<style lang="sass">
.header-empezable{
    background-color: #D9D9D9;
    padding: 6px 0;
    div.text-header{
        padding-top: 8px;
        font-size: 14px;
        font-weight: bolder;
        color:#666666;
    }
}
</style>
