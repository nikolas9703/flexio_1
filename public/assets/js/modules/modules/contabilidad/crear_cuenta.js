var planContable;

var formularioCuenta = {
  settings:{
    changeArbol: $('a.filter'),
    btnCancelar: $('#cancelarBtn'),
    btnGuardar: $('#guardarBtn')
  },
  init:function() {
    planContable = this.settings;
    this.inicializar_plugin();
    this.viewAccion();
  },
  treeRender:function(data){
    var arbol = $.parseJSON(data);
    $('#codigo').val('');
    $('#padre_id').val('');
    $('#plan_cuentas').jstree(true).settings = arbol;
    $('#plan_cuentas').jstree("refresh");
  },
  viewAccion:function(){
    var self = this;
    planContable.changeArbol.click(function(e){
      e.preventDefault();
      var item = $(this).data('item');
      var tipo = item;
      var parametros = {tipo:tipo};
      var filtro = moduloContabilidad.listarCuenta(parametros);
      filtro.done(function(data){
        self.treeRender(data);
      });
      $('ul#cuentas_tabs').children().removeClass('active');
      $(this).parent().addClass('active');
    });

    planContable.btnCancelar.click(function(e){
      e.preventDefault();
      $("#addCuentaModal").modal('hide');
    });
    planContable.btnGuardar.click(function(e){
      e.preventDefault();
      var selfButton = this;
      if($('#form_crear_cuenta').validate().form() === true)
      {
        //$(selfButton).unbind("click");
        $('input').removeAttr("readonly");
        var padre_id = $('input#padre_id').val();
        if(padre_id ===""){
            toastr.warning('debe de seleccionar una cuenta padre','crear cuenta');
            return false;
        }
        var guardar = moduloContabilidad.guardarCuenta($('#form_crear_cuenta'));
        guardar.done(function(data){
          var respuesta = $.parseJSON(data);
          if(respuesta.estado==200)
          {
            //$("#mensaje_info").empty().html('<div id="success-alert" class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+respuesta.mensaje+'</div>');
            toastr.success(respuesta.mensaje,'crear cuenta');
            $("#contabilidadGrid").trigger('reloadGrid');
          }else if(respuesta.estado==500){
            toastr.error(respuesta.mensaje,'Error crear cuenta');
            return false;
          }
            //$(selfButton).bind("click");
            $("#addCuentaModal").modal('hide');
        });

      }
    });
  },
  inicializar_plugin:function(){
    $('#form_crear_cuenta').validate({
      focusInvalid: true,
      ignore: ".ignore",
      wrapper: '',
    });
  }
};
(function(){
  formularioCuenta.init();
})();
