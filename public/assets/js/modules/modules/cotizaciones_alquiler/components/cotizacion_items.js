
var cotizacion_items = Vue.component('cotizacion_items',{
    
    template:'#cotizacion_items',
    
    ready: function ()
    {
        var context = this;
        
        if(context.vista == 'editar')
        {
            var cotizacion_alquiler_json = JSON.parse(cotizacion_alquiler);
            var articulos = cotizacion_alquiler_items;
            
            context.setArticulos(articulos);
            
            if(cotizacion_alquiler_json.estado_id != 'por_aprobar')//anulado o terminado
            {
                context.disabledEditar = true;
                context.disabledEditarTabla = true;
            }
        }
    },
    
    data: function()
    {
        return {
            vista:vista,
            
            disabledEditar:false,
            disabledEditarTabla:false,
            
            categorias: categorias,//catalogo from controller
            ciclos_tarifarios: ciclos_tarifarios,//catalogo from controller
            
            articulos:[
                {categoria_id:'',items:[]}
            ]
            
        };
        
    },
    
    methods: 
    {
        
        setArticulos:function(articulos)
        {
            var context = this;
            
            context.articulos = [];
            _.forEach(articulos, function(articulo){
                var categoria = _.find(context.categorias, function(categoria){
                    return categoria.id==articulo.pivot.categoria_id;
                });
                
                context.articulos.push(
                    {
                        categoria_id:articulo.pivot.categoria_id,
                        items:categoria.items_contratos_alquiler,
                        item_id:articulo.pivot.item_id,
                        cantidad:articulo.pivot.cantidad,
                        ciclo_id:articulo.pivot.ciclo_id,
                        tarifa:articulo.pivot.tarifa,
                        en_alquiler:articulo.pivot.en_alquiler,
                        devuelto:articulo.pivot.devuelto,
                        entregado:articulo.pivot.entregado
                    }
                );
            });
        },
        
        cambiarCategoria:function (articulo, index)
        {
            var categoria = _.find(this.categorias, function(categoria){
                return categoria.id==articulo.categoria_id;
            });
            
            if(_.isEmpty(categoria))
            {
                articulo.items = [];
            }
            else
            {
                articulo.items = categoria.items_contratos_alquiler;
            }
        },
        
        addRow:function(e)
        {
            var context = this;
            e.preventDefault();
            context.articulos.push({categoria_id:'',items:[]});
            
            Vue.nextTick(function(){
                $('#cotizacionesAlquilerItems').find('#cotizacion_item'+ (context.articulos.length - 1)).find('input').inputmask();
            });
        },
        
        removeRow:function(index, e)
        {
            e.preventDefault();
            this.articulos.splice(index,1);
        }
        
    }
    
});

