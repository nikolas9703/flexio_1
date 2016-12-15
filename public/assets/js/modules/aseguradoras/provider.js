var aseguradoraProvider = function(){
  "use strict";
  var verFormularioContacto = false;
  return {
    config:function(setting){
      this.verFormulario = setting;

    },
    showContacto:function(){
      return this.verFormulario? true : false;
    },
    showAseguradora:function(){
      return this.verFormulario? true : false;
    }
  };
}();
