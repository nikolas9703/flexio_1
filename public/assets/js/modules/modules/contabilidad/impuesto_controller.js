bluapp.controller("configImpuestoController", function($scope, $http){

  $scope.impuestolimpiar={
    nombre:'',
    descripcion:'',
    impuesto:'',
    retiene_impuesto:'no'
  };

  $scope.impuesto = {
    nombre:'',
    descripcion:'',
    impuesto:'',
    retiene_impuesto:'no'
  };

  $scope.retiene_impuesto = retiene_impuesto ==='si'? true : false;

   var vista={
     formImpuesto: $('#crearImpuestoForm'),
     grid_obj: $('#tablaImpuestoGrid'),
     grid_id:  '#tablaImpuestoGrid',
     url: phost() + 'contabilidad/ajax-listar-impuestos',
     opcionesBoton: $('button.viewOptions'),
     modalOpciones: $('#opcionesModal'),
     modalEstado: $('#modalCambiarEstado')
   };
   var botonModal = {
     editar: 'a.editarImpuestoBtn',
     cambiarEstado: 'a.cambiarEstadoImpuestoBtn'
   };
   $scope.listar_grid = function(){
     $http({
  		 method: 'POST',
  		 url: vista.url,
  		 data : $.param({
  			 erptkn: tkn,
         rowList: [10, 20,50, 100],
         rows:10,
         page:1,
         sidx:'nombre',
         sord:'asc'
  		 }),  // pass in data as strings
  		 cache: false,
       xsrfCookieName: 'erptknckie_secure',
       headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
  	}).then(function (results) {

  		if(results){
  			 $scope.dataImpuesto = results.data.rows;
         $('#tablaImpuestoGrid').jqGrid("clearGridData");
         angular.forEach($scope.dataImpuesto,function(val,i){
           $('#tablaImpuestoGrid').jqGrid('addRowData', (i + 1), val);
         });
         $('#tablaImpuestoGrid').trigger('reloadGrid');

  		}
      });
   };


    $scope.grida = function() {
    $('#tablaImpuestoGrid').jqGrid({
      datatype: "local",
      colNames: ['', 'Nombre', 'Descripcion', 'Tasa de Impuesto (%)','Cuenta Contable','Estado', '', ''],
      colModel: [{
        name: 'id_impuesto',
        index: 'id_impuesto',
        hidedlg: true,
        key: true,
        hidden: true
      }, {
        name: 'nombre',
        index: 'nombre',
        sorttype: "text",
        formatter: 'text',
        sortable: true,
        width: 150
      }, {
        name: 'descripcion',
        index: 'descripcion',
        formatter: 'text',
        sortable: true
      },{
        name: 'impuesto',
        index: 'impuesto',
        formatter: 'text',
        sortable: true
      },{
        name: 'nombre_cuenta',
        index: 'nombre_cuenta',
        formatter: 'text',
        sortable: false
      }, {
        name: 'estado',
        index: 'estado',
        formatter: 'text',
        sortable: true,
        align: 'center'
      }, {
        name: 'opciones',
        index: 'opciones',
        sortable: false,
        align: 'center'
      }, {
        name: 'link',
        index: 'link',
        hidedlg: true,
        hidden: true
      }],
      height: "auto",
     autowidth: true,
     rowList: [10, 20,50, 100],
     rowNum: 10,
     page: 1,
     pager: vista.grid_id +"Pager",
     loadtext: '<p>Cargando...</p>',
     hoverrows: false,
       viewrecords: true,
       refresh: true,
       loadonce: true,
       localReader: { repeatitems: false },
       loadui:'block',
       gridview: true,
       multiselect: false,
       sortname: 'nombre',
       sortorder: "ASC",
       loadComplete : function () {

       },
       onSelectRow: function(id) {
         $(this).find('tr#' + id).removeClass('ui-state-highlight');
       }
    });
    $('#tablaImpuestoGrid').jqGrid('navGrid', + vista.grid_id +"Pager", {
                edit: false,
                add: false,
                del: false
            });
  };

  $scope.guardarImpuesto = function(impuesto) {

        $scope.impuesto = angular.copy(impuesto);

        var formValidado = vista.formImpuesto.validate();
        if(formValidado.form() === true)
        {
          //$(selfButton).unbind("click");

          var guardar = moduloContabilidad.guardarImpuesto(vista.formImpuesto);
          guardar.done(function(data){
            var respuesta = $.parseJSON(data);
            if(respuesta.estado==200)
            {
              $scope.listar_grid();
              $("#mensaje_info").empty().html('<div id="success-alert" class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+respuesta.mensaje+'</div>');
              $('#editarId').remove();
              $('#cuenta_id').val('').trigger('chosen:updated');
              vista.formImpuesto.trigger('reset');
            }
              //$(selfButton).bind("click");
              //$("#addCuentaModal").modal('hide');
          });

        }
      };

  $scope.limpiarFormImpuesto = function(e) {
    $scope.impuesto = angular.copy($scope.impuestolimpiar);
  };

  $scope.inicializar_plugin = function(){
    vista.formImpuesto.validate({
      focusInvalid: true,
      ignore: ".ignore",
      wrapper: '',
    });
  };

vista.grid_obj.on("click", "button.viewOptions", function(e) {
//vista.opcionesBoton.click(function(e){
    e.preventDefault();
    e.returnValue = false;
    e.stopPropagation();
    var id = $(this).data("id");
    var rowINFO = $.extend({}, vista.grid_obj.getRowData(id));
    var options = rowINFO.link;

    $scope.impuestoEditar= {
      id:id,
      nombre: rowINFO.nombre,
      impuesto:rowINFO.impuesto,
      descripcion:rowINFO.descripcion,
      nombre_cuenta:rowINFO.nombre_cuenta
    };

    vista.modalOpciones.find('.modal-title').empty().append('Opciones: ' + rowINFO.nombre + '');
    vista.modalOpciones.find('.modal-body').empty().append(options);
    vista.modalOpciones.find('.modal-footer').empty();
    vista.modalOpciones.modal('show');

  });

  vista.modalOpciones.on("click",botonModal.editar,function(e){
    e.preventDefault();
    e.stopPropagation();
    $('#editarId').remove();
    $scope.impuesto = angular.copy($scope.impuestoEditar);
    $('#nombre').val($scope.impuesto.nombre);
    $('#impuesto').val($scope.impuesto.impuesto);
    $('#descripcion').val($scope.impuesto.descripcion);

    var cuenta = $scope.impuesto.nombre_cuenta.split(',');
    var valores = [];
    if(cuenta.length > 0){
      $('#cuenta_id').find('option').each(function(i,val){
        var a = $(this).text();
        var self = this;
        $.each(cuenta,function(j,p){
          if(p===a){
            valores.push($(self).val());
          }
        });

      });
    }
    $('#cuenta_id').val(valores);
    $('#cuenta_id').trigger("chosen:updated");


    vista.formImpuesto.append('<input type="hidden" name="id" id="editarId" value="'+ $scope.impuesto.id+'"/>');
    vista.modalOpciones.modal('hide');
  });

  vista.modalOpciones.on("click",botonModal.cambiarEstado,function(e){
    vista.modalOpciones.modal('hide');
    e.preventDefault();
    e.stopPropagation();
    var uuid = $(this).data('uuid');
    var estado = $(this).data('estado');
    var parametros = {uuid_impuesto:uuid, estado:estado};
    var ajaxEstado = moduloContabilidad.cambiarEstadoImpuesto(parametros);
    vista.modalEstado.find('.modal-title').empty().html('Cambiar Estado');
    var opciones = '<div class="loading-progress"></div>';
    vista.modalEstado.find('.modal-body').empty().html(opciones);
    vista.modalEstado.find('.modal-footer').empty();
    vista.modalEstado.modal('show');
    var progress = $(".loading-progress").progressTimer({
      timeLimit: 300,
      completeStyle: 'progress-bar-success',
      onFinish: function() {
        $scope.listar_grid();
        vista.modalEstado.modal('hide');
      }
    });

    ajaxEstado.fail(function(){
      progress.progressTimer('error', {
        errorText: 'error al cambiar el estado!',
        onFinish: function() {
          console.log('hubo un error en cambiar el estado');
        }

      });
    });

    ajaxEstado.done(function(data) {
      var respuesta = $.parseJSON(data);
      if (respuesta.estado == 200) {
        $("#mensaje_info").empty().html('<div id="success-alert" class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + respuesta.mensaje + '</div>');
        progress.progressTimer('complete');
      }
    });




  });

  $scope.inicializar_plugin();
  $scope.grida();
  $scope.listar_grid();
});
