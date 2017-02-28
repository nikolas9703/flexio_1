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
            <div class="m-t-xs text-success" v-if="cargando"><i class="fa fa-circle-o-notch fa-spin fa-1x fa-fw"></i> cargando...</span>
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
            cargando: false,
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
        selectEmpezarDesde(){
                var context = this;
                var ajaxurl = '';
                if(empezable_type.value=='cliente'){
                    ajaxurl = 'ajax_catalogo/cat_clientes';
                } else if(empezable_type.value=='clientes_potenciales'){
                    ajaxurl = 'ajax_catalogo/catalogo_clientes_potenciales';
                }
                $("#empezable_id").select2({
                width:'100%',
                ajax: {
                    url: phost() + ajaxurl,
                    method:'POST',
                    dataType: 'json',
                    delay: 100,
                    cache: true,
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            page: params.page,
                            limit: 10,
                            erptkn: tkn
                        };
                    },
                    processResults: function (data, params) {
                      let resultados = data.map(resp=> [{'id': resp.id,'text': resp.nombre}]).reduce((a, b) => a.concat(b),[]);
                      context.catClientes.catalogo.clientes = data;
                      return {results:resultados};
                    },
                    escapeMarkup: function (markup) { return markup; },
                }
            });
          },
        ajax: function(url, data) {
          var scope = this;
          return Vue.http({
              url: phost() + url,
              method: 'POST',
              data: $.extend({erptkn: tkn}, data)
          });
        },
        fillCatalogo(){
            var datos = {erptkn: tkn};
            var self = this;

            //Para cotizaciones de alquiler, listar clientes de 10 en 10
            if(window.location.pathname.match(/cotizacion/gi)){
              Vue.nextTick(function(){
                
                self.disableEmpezarId=false;
                if(self.info.vista ==="crear"){
                  self.catClientes.catalogo.clientes = [];
                  self.selectEmpezarDesde();}
              });
              return false;
            }

            var catalogo = this.getCatalogo(datos);
            this.cargando = true;
            catalogo.then((response)=>{
                console.log(response.data);
                if(_.has(response.data, 'session'))
                {
                
                  window.location.assign(window.phost());
                  self.cargando = false;
                  return;
                }

                self.cargando = false;
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
                'cliente':'ajax_catalogo/cat_clientes',
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
                       //self.fillCatalogo();
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
            var self = this;
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
