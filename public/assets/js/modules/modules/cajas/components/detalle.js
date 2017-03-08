
Vue.component('detalle',{
    
    template:'#detalle_template',
    
    props:{
    
        config:Object,
        detalle:Object,
        catalogos:Object,
        empezable:Object,
        
    },
    
    watch:{
     
    },
    computed:{
         total_monto: function(){
                var context = this;
                
                 
               context.config.botonGuardarDisabled = true;
               var sumatoria =   context.detalle.metodos_pagos.reduce(function(prev, linea){
                  return prev + parseFloat(linea.monto); 
                },0);
 
                if(sumatoria == parseFloat(context.detalle.monto) && sumatoria <= context.detalle.maximo_transferir){ //Bloquear Guardar
                   
                     context.config.botonGuardarDisabled = false;
                }
                
                return sumatoria;
          },
   
         getTransferenciaDesde:function(){
     

             var context = this;      
               if(context.empezable.type != ''){
                if(context.empezable.type == 'caja'){
                    context.detalle.label_acuenta = 'Transferir a caja';
                }
                else{
                   context.detalle.label_acuenta = 'Transferir a cuenta de banco';
                }    
                 return context.empezable[context.empezable.type + 's'];                              
            } 
          
               
        }, 
        
        
    },
 
    methods: {
      addFilasCorreo: function(event){
           var context = this;           
           
             context.detalle.metodos_pagos.push({monto:'0',tipo:''});
             
             
         },
        deleteFilasCorreo:function(index){
            var context = this;       
            context.detalle.metodos_pagos.splice(index, 1);
        }
      
        
    },
            
    
     
    
        
    
});