var Operacion = function(monto, porcentaje){
  this.monto = monto;
  this.porcentaje = porcentaje;
};

Operacion.prototype = function() {
  var porcentajeSinTotal = function(){
    return parseFloat(this.monto) * (parseFloat(this.porcentaje)/100);
  };
  return {
    porcentajeDelTotal:porcentajeSinTotal
  };
}();
