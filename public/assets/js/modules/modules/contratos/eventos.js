var contratoEvento = (function(){
  var evento={
    exportarAdenda: $("a#exportar_adenda")
  };

  var mostarFormulario = function(){
    evento.exportarAdenda.click(function(e){
      console.log('listar');

    });
  };
  return {
    init:function(){
      mostarFormulario();
    }
  };
})();

$(function(){
   contratoEvento.init();
});
