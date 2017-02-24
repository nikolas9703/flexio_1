var contrato_items = Vue.component('contrato_items',{

    template:'#contrato_items',

    ready: function ()
    {
        var context = this;

        if(context.vista == 'editar')
        {
            var contrato_alquiler_json = JSON.parse(contrato_alquiler);
            var articulos = contrato_alquiler_items;

            Vue.nextTick(function(){
                 context.setArticulos(articulos);
            });

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

    data: function()
    {
        return {
            vista:vista,

            disabledEditar:false,
            disabledEditarTabla:false,

            categorias: categorias,//catalogo from controller
            ciclos_tarifarios: ciclos_tarifarios,//catalogo from controller

            articulos:[
                {id:'',categoria_id:'',atributo_id:'',item_id:'',items:[],atributos:[],ciclo_id:'',  caret:'fa-caret-right'}
            ]

        };

    },
    events: {
	        'popular_articulos': function(articulos){
	        	var context = this;
 	        	 context.setArticulos(articulos);
	        },
                'limpiando_articulos': function(){
	        	var context = this;
 	        	 context.limpiandoArticulos();
	        },

	},
    methods:
    {
    	cambiarCaret: function(articulo)
        {

            articulo.caret = articulo.caret == 'fa-caret-down' ? 'fa-caret-right' : 'fa-caret-down';

        },
        setArticulos:function(articulos)
        {
            var context = this;


            context.articulos = [];
            _.forEach(articulos, function(articulo){

                 var categoria = _.find(context.categorias, function(categoria){
                      if(context.vista == 'crear'){
                         var cat_id = articulo.categoria_id;
                     }else{
                         var cat_id = articulo.pivot.categoria_id;
                     }
                     return categoria.id==cat_id;
                });
                  if(context.vista == 'editar'){ //Se usa en caso que ya tenga relacion con el contrato
                         context.articulos.push(
                        {
                            categoria_id:articulo.pivot.categoria_id,
                            items:categoria.items_solo_alquiler,
                            atributos:categoria.items_solo_alquiler[0].atributos,
                            atributo_id:articulo.pivot.atributo_id,
                            item_id:articulo.pivot.item_id,
                            cantidad:articulo.pivot.cantidad,
                            ciclo_id:articulo.pivot.ciclo_id,
                            tarifa:articulo.pivot.tarifa,
                            en_alquiler:articulo.pivot.en_alquiler,
                            devuelto:articulo.pivot.devuelto,
                            entregado:articulo.pivot.entregado,
                            caret:'fa-caret-right',
                            impuesto:articulo.pivot.impuesto,
                            descuento:articulo.pivot.descuento,
                            cuenta_id:articulo.pivot.cuenta_id,
                        }
                    );
                    console.log(context.articulos);
                 }else{

                     var periodo_tarifario = (articulo.periodo_tarifario =='diario')?6:5;  //Esto se debe corregir, en contratos se guarda con id, de cotizacion viene con valor
                           context.articulos.push(
                        {
                            categoria_id:categoria.id,//**
                            items:categoria.items_solo_alquiler,//**
                            atributos:categoria.items_solo_alquiler[0].atributos,////**
                            atributo_id:articulo.atributo_id,//**
                            item_id:articulo.item_id,
                            cantidad:articulo.cantidad,
                            ciclo_id:periodo_tarifario,//articulo.ciclo_id,
                            tarifa:articulo.precio_unidad,
                            en_alquiler:articulo.en_alquiler,
                            devuelto:articulo.devuelto,
                            entregado:articulo.entregado,
                            caret:'fa-caret-right',
                            impuesto:articulo.impuesto_id,
                            descuento:articulo.descuento,
                            cuenta_id:articulo.cuenta_id,
                        } );
                 }

            });
        },

        limpiandoArticulos:function()
        {
           var context = this;
            context.articulos = [];
                         context.articulos.push(
                        {
                            categoria_id:'',
                            items:'',
                            atributos:'',
                            atributo_id:'',
                            item_id:'',
                            cantidad:'',
                            ciclo_id:'',
                            tarifa:'',
                            en_alquiler:'',
                            devuelto:'',
                            entregado:'',
                            caret:'fa-caret-right',
                            impuesto:'',
                            descuento:'',
                            cuenta_id:''
                        }
                    );
         },

        cambiarCategoria:function (articulo, index)
        {
        	articulo.atributos = '';
            var categoria = _.find(this.categorias, function(categoria){
                return categoria.id==articulo.categoria_id;
            });

            if(_.isEmpty(categoria))
            {
            	articulo.items = [];
                articulo.item_id = '';
            }
            else
            {
                articulo.items = categoria.items_solo_alquiler;
            }
        },
        cambiarItemAlquiler:function (articulo, index)
        {
             var categoria = _.find(this.categorias, function(categoria){
                 return categoria.items_solo_alquiler[0].id==articulo.item_id;
            });
             if(_.isEmpty(categoria))
            {
                articulo.atributos = [];
                articulo.atributo_id='';
            }
            else
            {
                articulo.atributos = categoria.items_solo_alquiler[0].atributos;
            }


         },
        addRow:function(e)
        {
            var context = this;
            e.preventDefault();
            context.articulos.push({id:'',categoria_id:'',atributo_id:'',item_id:'',ciclo_id:'',items:[],atributos:[], caret:'fa-caret-right'});
            Vue.nextTick(function(){
                $('#contratosAlquilerItems').find('#contrato_item'+ (context.articulos.length - 1)).find('input').inputmask();
            });
        },

        removeRow:function(index, e)
        {
            e.preventDefault();
            this.articulos.splice(index,1);
        },


    }

});
