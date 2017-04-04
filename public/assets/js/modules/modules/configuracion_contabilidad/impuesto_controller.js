bluapp.controller("configImpuestoController", function($scope, $http,$timeout){

  $scope.impuestolimpiar={
    nombre:'',
    descripcion:'',
    impuesto:''
  };

 $scope.impuesto = {
   nombre:'',
   descripcion:'',
   impuesto:'',
   retiene_impuesto:'no',
   cuenta_id:'',
   cuenta_retenida_id:''
 };

 /*$("#retiene_impuesto").change(function(){
   $scope.cambioRetencion(this.value);
 });*/

 $scope.cambioRetencion = function(retiene){
   console.log(retiene);
   if(retiene == "si"){
     angular.element("#cuenta_retenida_id").prop('disabled',false);
     angular.element("#porcentaje_retenido").prop('disabled',false);
   }else{
     angular.element("#cuenta_retenida_id").prop('disabled',true);
     angular.element("#porcentaje_retenido").prop('disabled',true);
   }
   angular.element("#cuenta_retenida_id").trigger("chosen:updated");
 };
$scope.retiene_impuesto = window.retiene_impuesto ==='si'? false : true;
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
      $('#tablaImpuestoGrid').setGridParam({
          url: vista.url,
          datatype: "json",
          postData: {
            erptkn: tkn
          }
    }).trigger('reloadGrid');
   };


    $scope.grida = function() {
    $('#tablaImpuestoGrid').jqGrid({
      url: vista.url,
      datatype: "json",
       mtype: "POST",
      colNames: ['', 'Nombre', 'Descripción', 'Tasa de Impuesto (%)','Cuenta Contable','Retiene Impuestos','Estado', '', '','cuenta retenida','% retenido'],
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
        name: 'retiene_impuesto',
        index: 'retiene_impuesto',
        formatter: 'text',
        sortable: false
      },{
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
      },{
        name: 'cuenta_retenida_id',
        index: 'cuenta_retenida_id',
        formatter: 'text',
        hidedlg: true,
        hidden: true
      },{
        name: 'porcentaje_retenido',
        index: 'porcentaje_retenido',
        formatter: 'text',
        hidedlg: true,
        hidden: true
      }
    ],
      postData: {
  	   		erptkn: tkn
  	   	},
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
      // localReader: { repeatitems: false },
       //jsonReader: {repeatitems: false, root: function (obj) { return obj; }},
       loadui:'block',
       gridview: true,
       multiselect: false,
       sortname: 'nombre',
       sortorder: "ASC",
       loadBeforeSend: function () {//propiedadesGrid_cb
	    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
	      $(this).closest("div.ui-jqgrid-view").find("#tablaPlanContable_cb, #jqgh_tablaPlanContable_link").css("text-align", "center");
	    },
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

          var guardar = moduloConfiguracionContabilidad.guardarImpuesto(vista.formImpuesto);
          guardar.done(function(data){
            var respuesta = data;
            if(respuesta.estado==200)
            {
              $scope.listar_grid();
              toastr.success(respuesta.mensaje,'');
              //$("#mensaje_info").empty().html('<div id="success-alert" class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+respuesta.mensaje+'</div>');
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

    $scope.impuestoEditar = {
      id:id,
      nombre: rowINFO.nombre,
      impuesto:rowINFO.impuesto,
      descripcion:rowINFO.descripcion,
      nombre_cuenta:rowINFO.nombre_cuenta,
      retiene_impuesto:rowINFO.retiene_impuesto,
      porcentaje_retenido:rowINFO.porcentaje_retenido,
      cuenta_retenida_id:rowINFO.cuenta_retenida_id
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
    $('#porcentaje_retenido').val($scope.impuesto.porcentaje_retenido);
    $('#retiene_impuesto').val($scope.impuesto.retiene_impuesto);
    $('#cuenta_retenida_id').val($scope.impuesto.cuenta_retenida_id);

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

    $scope.cambioRetencion($scope.impuesto.retiene_impuesto);
    $('#cuenta_id').val(valores);
    $('.chosen-select').trigger("chosen:updated");


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
    var ajaxEstado = moduloConfiguracionContabilidad.cambiarEstadoImpuesto(parametros);
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
        //$('#tablaImpuestoGrid').trigger('reloadGrid');
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
        //$scope.grida();
        //$('#tablaImpuestoGrid').trigger('reloadGrid');
        $("#mensaje_info").empty().html('<div id="success-alert" class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + respuesta.mensaje + '</div>');
        progress.progressTimer('complete');
      }
    });




  });

  $scope.inicializar_plugin();
  $scope.grida();
  //$scope.listar_grid();
});
