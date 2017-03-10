var moduloEntradaManual  = (function() {
  return {
    guardarEntradaManual:function(element){
      var parametros = $(element).serialize();
      //console.log(parametros);
      return $.post(phost() + 'entrada_manual/ajax-guardar-entada-manual', parametros);
    }
  };
})();
