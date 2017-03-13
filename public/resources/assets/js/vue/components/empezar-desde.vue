<template>

    <div class="row" style="margin-right: 0px;">

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: #D9D9D9;padding: 7px 0 7px 0px;">

            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" style="padding-top: 7px;">

                <span><strong>{{{empezable.label}}} </strong></span>

            </div>

            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">

                <select name="empezable_type" id="empezable_type" v-model="empezable.type" v-select2="empezable.type" :config="config.select2" :disabled="config.disableEmpezarDesde || disableFromEvent">
                    <option value="">Seleccione</option>
                    <option :value="type.id" v-for="type in empezable.types">{{{type.nombre}}}</option>
                </select>

            </div>

            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <select v-if="typeof config.select2empezableId !== 'undefined'" name="empezable_id"
                v-select2ajax="empezable.id" :config="config.select2empezableId" :disabled="empezable.type == '' || config.disableEmpezarDesde || disableFromEvent">
                    <option value="">Seleccione</option>
                </select>
                <select v-if="typeof config.select2empezableId === 'undefined'" name="empezable_id" id="empezable_id" v-model="empezable.id" v-select2="empezable.id" :config="config.select2" :disabled="empezable.type == '' || config.disableEmpezarDesde">
                    <option value="">Seleccione</option>
                    <option :value="emp.id" v-for="emp in getEmpezables">{{{emp.nombre}}}</option>
                </select>
            </div>

        </div>

    </div>

</template>


<script>

export default {

    props:{

        empezable:Object,
        config:Object,
        detalle:Object

    },

    data:function(){

        return {

            detalle_inicial:{},//se usa para conocer el estado inicial del formualrio (method ready store)
            disableFromEvent:false

        };

    },

    computed:{

        getEmpezables:function(){

            var context = this;
            if(context.empezable.type !== ''){

                return context.empezable[context.empezable.type + 's'];

            }
            return [];

        }

    },

    events:{
        eSetEmpezable: function(empezable){
            var context = this;
            context.empezable = $.extend(context.empezable, JSON.parse(JSON.stringify(empezable)));
            context.disableFromEvent = true;
        }
    },

    watch:{

        'empezable.type':function(val, oldVal){

            if(this.config.vista !== 'crear')return;
            this.empezable.id = '';

        },

        'empezable.id':function(val, oldVal){

            var context = this;
            if(context.config.vista !== 'crear')return;
            if(_.toLength(val)){

                var empezable = _.find(context.empezable[context.empezable.type + 's'], function(empezable){
                    return empezable.id == val;
                });
                if(!_.isEmpty(empezable)){

                    context.config.enableWatch = false;
                    context.detalle = $.extend(context.detalle,JSON.parse(JSON.stringify(empezable)));
                    context.detalle.id = '';
                    Vue.nextTick(function(){
                        context.config.enableWatch = true;
                    });

                }

            }else{

                context.detalle = JSON.parse(JSON.stringify(context.detalle_inicial));

            }

        }

    },

    methods:{

        //...

    },

    ready:function(){

        var context = this;
        context.detalle_inicial = JSON.parse(JSON.stringify(context.detalle));

    }


};

</script>
