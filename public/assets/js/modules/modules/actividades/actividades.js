var moduloActividades = (function(){
  return{
    guardarActividad:function(uuid_oportunidad,fecha,asignado,asunto,uuid_tipo_actividad,apuntes,completado){
      var parametros = {campo:{uuid_oportunidad:uuid_oportunidad, fecha:fecha, uuid_asignado:asignado, uuid_tipo_actividad:uuid_tipo_actividad,asunto:asunto, apuntes:apuntes, completada:completado,relacionado_con:6, uuid_relacion:uuid_oportunidad, modulo_relacion:'oportunidades' },erptkn:tkn};
      return $.post(phost() +'actividades/ajax-crear-actividad-modal', parametros);
    },
    clearTipoActividad:function(){
      var radios = $('input:radio[name="campo[uuid_tipo_actividad]"]');
      $.each(radios,function(i, el){
        $(el).parent().removeClass('active');
      });
    }
  };

})();
