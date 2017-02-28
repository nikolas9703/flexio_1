<template>
    <div class="row" style="margin-right: 15px;">

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 header-empezable">

            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 text-header">

                <span><strong v-text="titulo"></strong></span>

            </div>

            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">

                <select name="empezable_type" id="empezable_type" v-model="empezable_type" v-select3="empezable_type"  :config="config" class="form-control select2" data-rule-required="true" :disabled="isDisable">
                    <option value="">Seleccione</option>
                    <option v-for="type in getOpciones" :value="type.value" >{{type.label}}</option>
                </select>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                 <select class="form-control select2" name="empezable_id" id="empezable_id" v-model="empezable_id" v-select3="empezable_id" :config="config" :disabled="disableEmpezarId || isDisable">
                    <option value="">Seleccione</option>
                    <option :value="emp.id" v-for="emp in catClientes.catalogo.clientes">{{{emp.nombre}}}</option>
                </select>
            </div>

        </div>

    </div>
</template>


<script>
import catClientes from '../../../js/clientes'
export default {

    props:['titulo','options','config','info'],
    data(){

        return {
            empezable_type:'',
            detalle_inicial:{},//se usa para conocer el estado inicial del formualrio (method ready store)
            empezable_id:'',
            catClientes,
            disableEmpezarId:true,
            disableEditar:this.info.disableEditar
        };
    },
    ready(){

    },
    computed:{
        //son los catalogos del primer select
        getOpciones(){
            return this.options;
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

                self.catClientes.catalogo.clientes = response.data;
                Vue.nextTick(function(){
                    self.empezable_id = self.catClientes.catalogo.cliente_id;
                });
                self.disableEmpezarId=false;
            });
        },
        getCatalogo(datos){

            if(_.isEmpty(this.empezable_type)){
                return Promise;
            }
            var urls_catalogo = {
                'cliente':'ajax_catalogo/catalogo_clientes',
                'clientes_potenciales':'ajax_catalogo/catalogo_clientes_potenciales'
            };

            return this.$http.post({url: window.phost() + urls_catalogo[this.empezable_type], method:'POST',data:datos});

        },
        pruebas(key,value){
            console.log("empezable",key,value);
            var self = this;

                if(key ==='empezable_type'){
                    this.catClientes.catalogo.tipo = value;
                    if(!_.isEmpty(value)){
                       self.fillCatalogo();
                   }
                   self.disableEmpezarId=true;
                }

                if(key === 'empezable_id'){
                    this.catClientes.catalogo.cliente_id = value;
                }

        }

    },
    watch:{
        'empezable_type'(val, oldVal){

            this.catClientes.catalogo.tipo = val;
            if(this.info.vista === "crear"){
              this.catClientes.catalogo.cliente_id = '';
              this.empezable_id ='';
            if(!_.isEmpty(val)){
               this.fillCatalogo();
           }
           this.disableEmpezarId=true;
       }
        },
        'empezable_id'(val,oldVal){
            if(this.info.vista === "crear"){
              this.catClientes.catalogo.cliente_id = val;
            }
        },
        'catClientes.catalogo.tipo'(val,oldVal){
            if(this.info.vista === "editar"){
                this.empezable_type = catClientes.catalogo.tipo;
                this.fillCatalogo();
            }
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
