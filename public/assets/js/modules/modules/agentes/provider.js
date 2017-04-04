var agenteProvider = function(){
  "use strict";
  var verFormularioContacto = false;
  return {
    config:function(setting){
      this.verFormularioContacto = setting;

    },
    showContacto:function(){
      return this.verFormularioContacto? true : false;
    }
  };
}();
