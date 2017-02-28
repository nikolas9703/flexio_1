
<template>


   <div class="col-lg-12">

        <div class="ibox" :class="!showContent ? 'border-bottom' : ''">

            <div class="ibox-title">

                <h5 style="font-weight:normal;width:30%;">{{getNombreItem}}</h5>

                <div v-if="configv.vista == 'editar' && row.tipo_id != 7" class="ibox-tools" style="float: left; font-size: 20px;">

                    <span class="label label-primary" style="padding-left: 36px; padding-right: 36px; margin-left: 20px; font-size:13px;">{{row.entregado}} Entregados</span>
                    <span class="label label-warning" style="padding-left: 36px; padding-right: 36px; margin-left: 20px; font-size:13px;">{{row.devuelto}} Devueltos</span>
                    <span class="label label-info" style="padding-left: 36px; padding-right: 36px; margin-left: 20px; font-size:13px;">{{row.en_alquiler}} En alquiler</span>

                </div>

                <div class="ibox-tools">

                    <a @click="toggleContent()">
                        <i class="fa" :class="showContent ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </a>

                </div>

            </div>

            <div class="ibox-content" :style="showContent ? {display:'block'} : {display:'none'}">

                <div class="row">

                    <div class="col-lg-12">

                        <div class="table-responsive">

                            <table class="table table-noline tabla-dinamica itemsTable">

                                <thead>

                                    <tr>

                                        <th width="1%" style="background: white;"></th>
                                        <th width="14%" class="categoria ">Categoría de item<span class="required" aria-required="true">*</span></th>
                                        <th width="30%" class="item ">Item <span class="required" aria-required="true">*</span></th>
                                        <th width="12%" class="atributo ">Atributo </th>
                                        <th width="8%" class="cantidad ">Cantidad <span class="required" aria-required="true">*</span></th>
                                        <th width="12%" class="unidad ">Periodo tarifario <span class="required" aria-required="true">*</span></th>
                                        <th width="8%" class="precio_unidad ">Tarifa pactada <span class="required" aria-required="true">*</span></th>
                                        <th width="1%" style="background: white;">&nbsp;</th>

                                    </tr>

                                </thead>

                                <tbody>

                                   <!--componente articulo-->
                                  <tr :catalogos="catalogos"
                                    :is="'articulo'"
                                    :parent_index="parent_index"
                                    :row.sync="row"
                                    :mostrar="mostrar"
                                    :desabilitar="desabilitar"
                                    :configv="config"
                                    ></tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


</template>


<script>

export default {
    props:[
       'detalle',
       'catalogos',
       'parent_index',
       'row',
       'empezable',
       'mostrar',
       'desabilitar',
       'configv'
   ],

   data(){

       return {

           config:{

               select2:{width:"100%",placeholder: "Seleccione"},
               inputmask:{numero:'decimal'}

            },

            showContent: false

       };

   },

   methods:{

      toggleContent:function(){

         this.showContent = !this.showContent;

      }

   },

   components:{

       'articulo':require('./articulo.vue')

   },

   computed:{

      getNombreItem:function(){

         var context = this;
         var item = _.find(context.row.items, function(item){
               return item.id == context.row.item_id;
         });

         if(!_.isEmpty(item)){
            return item.nombre +" "+item.codigo;
         }
         return ''

      }

   },

   ready:function(){

      var context = this;
      if(context.configv.vista == 'crear'){
         context.showContent = true;
      }

   }

}

</script>
