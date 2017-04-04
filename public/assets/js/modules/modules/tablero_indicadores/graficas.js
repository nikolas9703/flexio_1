var moduloGraficas = (function(){
 return{
   getClientesScore:function(parametros){
     return $.post(phost() +'clientes/ajax-score-clientes', parametros);
   },
   getTopOportunidad:function(parametros){
     return $.post(phost() +'tablero_indicadores/ajax-top-oportunidades', parametros);
   },
   getClienteScoreUsuario:function(parametros){
     return $.post(phost() +'usuarios/ajax-cliente-score-usuario', parametros);
   }
 };
})();
