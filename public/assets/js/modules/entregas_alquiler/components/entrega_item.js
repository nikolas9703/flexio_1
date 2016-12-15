
var entrega_item = Vue.component('entrega_item',{

    template:'#entrega_item',

    props: ['parent_index','parent_articulo'],

    ready: function ()
    {

        var context = this;
        context.myReady();

    },

    events:{

        refrescarEntregaItem:function(){

            var context = this;
            context.myReady();

        }

    },

    data: function()
    {

        return {
            vista:vista,

            disabledEditar:false,
            disabledEditarTabla:false,
            disabledAddRow:false,

            categorias: categorias,//catalogo from controller
            ciclos_tarifarios: ciclos_tarifarios,//catalogo from controller,
            bodegas: JSON.parse(bodegas),
            ItemsNoDisponibles: typeof items_disponibles != 'undefined' ? items_disponibles : '',
            articulos:[],
            entrega_alquiler: (vista == 'editar') ? JSON.parse(entrega_alquiler) : '',

        };

    },

    methods:
    {

        myReady:function()
        {

            var context = this;
            var item = _.find(context.parent_articulo.items, function(item){
                return item.id == context.parent_articulo.item_id;
            });
            var cantidad_restante = context.parent_articulo.cantidad_restante;
            var serializable = (item.tipo_id == '5' || item.tipo_id == '8');

            context.articulos = [];

            if(context.vista == 'editar')
            {

                if(context.entrega_alquiler.estado_id > '2')//anulado o terminado
                {
                    context.disabledEditar = true;
                }
                else if(context.entrega_alquiler.estado_id == '2')//vigente
                {
                    context.disabledEditarTabla = true;
                }

                _.forEach(context.parent_articulo.detalles, function(detalle){

                    if(context.entrega_alquiler.id == detalle.operacion_id)//falta condicion del filtro
                    {

                        context.articulos.push({
                            serializable: serializable,
                            serie: detalle.serie,
                            cantidad: detalle.cantidad,
                            bodega_id: detalle.bodega_id,
                            fecha_devolucion_estimada:detalle.fecha_format,
                            ubicacion_id: detalle.bodega_id,
                            disabledAddRow:true
                        });

                    }

                });
            }
            else if(serializable)
            {
                var i = 0;
                for(var i=0;i<cantidad_restante;i++)
                {
                    context.articulos.push({
                        serializable: serializable,
                        serie:'',
                        cantidad: 1,
                        bodega_id:'',
                        fecha_devolucion_estimada:'',
                        ubicacion_id:'',
                        disabledAddRow:true
                    });
                }

            }
            else
            {

                context.articulos.push({
                    serializable: false,
                    serie:'',
                    cantidad: cantidad_restante,
                    bodega_id:'',
                    fecha_devolucion_estimada:'',
                    ubicacion_id:'',
                    disabledAddRow:true
                });

            }

            context.activarDatepicker();

        },

        activarDatepicker:function(){

            var context = this;
            Vue.nextTick(function(){

                $("#entrega_item_detalles"+ context.parent_index).find('.fecha').datepicker({
                    dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    numberOfMonths: 1
                });

            });


        },

        getControles:function(){

            var context = this;
            var cantidad_restante = parseInt(context.parent_articulo.cantidad_restante);
            var cantidad_entrega = _.sumBy(context.articulos, function(articulo){
                return parseInt(articulo.cantidad) || 0;
            });

            return {
                sobrepasa:!(cantidad_restante > cantidad_entrega),
                cantidad_restante: cantidad_restante,
                cantidad_entrega:cantidad_entrega
            };

        },

        verificaCantidad: function(){

            var context = this;
            var controles = context.getControles();

            _.forEach(context.articulos, function(articulo){
                articulo.cantidad = parseInt(articulo.cantidad) || 0;
                articulo.disabledAddRow = controles.sobrepasa;
            });

            if(controles.sobrepasa)//sobrepaso la cantidad restante o el valor es igual a la cantidad restante
            {
                for(var i=context.articulos.length;i>0;i--)
                {

                    var controles = context.getControles();
                    var aux = parseInt(controles.cantidad_restante - (controles.cantidad_entrega - context.articulos[i-1].cantidad)) || 0;
                    context.articulos[i-1].cantidad = aux < 0 ? 0 : aux;

                }
            }

        },

        cambiarCantidad:function()
        {

            var context = this;
            context.verificaCantidad();

        },

        cambiarSerie:function(articulo, parent_articulo, index){

            var context = this;

            $.ajax({
                url: phost() + "entregas_alquiler/ajax-get-serie-ubicacion",
                type:"POST",
                data:{
                    erptkn:tkn,
                    item_id:parent_articulo.item_id,
                    nombre:articulo.serie
                },
                dataType:"json",
                success: function(response){
                    if(!_.isEmpty(response)){
                        articulo.ubicacion_id = response.ubicacion_id;
                    }
                }
            });

      //Verificar si esta disponible
      var objeto = _.find(context.ItemsNoDisponibles, function(obj) {
         if(parent_articulo.item_id == obj.item_id && articulo.serie == obj.serie){
           return obj.serie == articulo.serie;
         }
       });
       if(typeof objeto === "undefined"){
           $('#guardarBtn').attr('disabled', false);
       }else{
           $('#guardarBtn').attr('disabled', true);
           toastr.error('Item no disponible, seleccione otro');          
           $("#series" + index).val("");
       }
        },

        addRow:function(e)
        {
            var context = this;
            var item = _.find(context.parent_articulo.items, function(item){
                return item.id == context.parent_articulo.item_id;
            });
            var serializable = (item.tipo_id == '5' || item.tipo_id == '8');
            e.preventDefault();

            context.articulos.push({
                serializable: serializable,
                serie:'',
                cantidad: serializable ? 1 : '',
                bodega_id:'',
                fecha_devolucion_estimada:'',
                disabledAddRow:true
            });

            context.verificaCantidad();
            context.activarDatepicker();
        },

        removeRow:function(index, e)
        {
            e.preventDefault();
            this.articulos.splice(index,1);
            this.verificaCantidad();
        }

    }

});
