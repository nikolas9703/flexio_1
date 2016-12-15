
//Este componente se esta usando en los modulos de:
//1.- Ordenes de compra

Vue.component('empezar_desde',{

    template:'#empezar_desde_template',

    props:{

        empezable:Object,
        config:Object,
        detalle:Object

    },

    data:function(){

        return {

            detalle_inicial:{}//se usa para conocer el estado inicial del formualrio (method ready store)

        };

    },

    computed:{

        getEmpezables:function(){

            var context = this;
            if(context.empezable.type != ''){

                return context.empezable[context.empezable.type + 's'];

            }
            return [];

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
                if (context.config.modulo == 'cotizaciones') {
                    empezable_id.disabled = true;
                    empezable_type.disabled = true;
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


});
