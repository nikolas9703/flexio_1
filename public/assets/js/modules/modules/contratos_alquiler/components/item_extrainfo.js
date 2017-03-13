
var item_extrainfo = Vue.component('item_extrainfo',{
    
    template:'#item_extrainfo',
    props: ['parent_index','parent_articulo'],
    data: function()
    {
        return {
        	vista:vista,
        	impuestos:impuestos,
        	cuentas:cuentas,
        	disabledEditar:false,
            disabledEditarTabla:false
         };
        
    },
    ready: function ()
    {
        var context = this;
        
        if(context.vista == 'editar')
        {
        	
        
            var contrato_alquiler_json = JSON.parse(contrato_alquiler);
            if(contrato_alquiler_json.estado_id > '2')//anulado o terminado
            {
                context.disabledEditar = true;
                context.disabledEditarTabla = true;
            }
            else if(contrato_alquiler_json.estado_id == '2')//vigente
            {
                context.disabledEditarTabla = false;
            }
        }
    },
    methods: 
    {
    	 
        
    }
    
});

