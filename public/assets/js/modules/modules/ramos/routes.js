var moduloAseguradora = (function() {
  return {
    listarRamosTree: function(parametros) {
      return $.post(phost() + 'configuracion_seguros/ajax-listar-ramos-tree', $.extend({
        erptkn: tkn
      }, parametros));
    },
    cambiarEstadoRamo: function(parametros) {
      return $.post(phost() + 'configuracion_seguros/ajax-cambiar-estado-ramo', $.extend({
        erptkn: tkn
      }, parametros));
    },
    guardarRamos:function(element){
      var parametros = $(element).serialize();
      return $.post(phost() + 'configuracion_seguros/ajax_guardar_ramos', parametros);
    },
    getRamo:function(parametros){
      return $.post(phost() + 'configuracion_seguros/ajax-buscar-ramo', $.extend({
        erptkn: tkn
      }, parametros));
    },
    getRoles:function(parametros){
      return $.post(phost() + 'catalogos/getActiveUsersByRol', $.extend({
        erptkn: tkn
      }, parametros));
    },
    ajaxcambiarObtenerPoliticas:function(){
     return $.ajax({
      url: "catalogos/obtener_politicas",
      dataType: "json",
    });
   },
   ajaxcambiarObtenerPoliticasGeneral:function(){
     return $.ajax({
      url: "catalogos/obtener_politicas_general",
      dataType: "json",
    });
   },
   guardarDocumentos:function(parametros){
      return $.post(phost()+'catalogos/ajax_guardar_documentos',$.extend({
        erptkn: tkn
      },parametros));
    },
    verDocumento:function(parametros){
      return $.post(phost()+'catalogos/ajax_buscar_documentos',$.extend({
        erptkn: tkn
      },parametros));
    },
    getUsuariosSeleccionado:function(respuesta_usuario,parametros){
      return $.post(phost() + 'catalogos/ajax_buscar_ramo_usuario', $.extend({
        erptkn: tkn
      }, respuesta_usuario,parametros));
    },
    editarDocumeto:function(parametros){
      return $.post(phost()+'catalogos/ajax_editar_documentos',$.extend({
        erptkn: tkn
      },parametros));
    },
    cambiarEstado:function(parametros){
      return $.post(phost()+'catalogos/ajax_cambiarestado_documentos',$.extend({
        erptkn: tkn
      },parametros));
    },
 };
})();
