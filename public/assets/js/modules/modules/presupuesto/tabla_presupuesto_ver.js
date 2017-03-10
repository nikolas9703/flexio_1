/*bluapp.controller("presupuestoDinamicoVerController", function($scope, $http){
  //variable
  $scope.hideGuardar = true;

  var objFrom = {
    presupuestoForm: $('#form_crear_preosupuesto'),
  };

  var rutas = {
    getDataPresupuesto:  phost() + 'presupuesto/ajax-armarPresupuestoVer',
  };

  $scope.init = function(){
    objFrom.presupuestoForm.validate({
      ignore: '',
      wrapper: '',
    });
    $scope.cambio = {inicio:$('#inicio').val(), periodo:$('#periodo').val()};

    $(window).resizeEnd(function() {
			$(".ui-jqgrid").each(function(){
				var w = parseInt( $(this).parent().width()) - 6;
				var tmpId = $(this).attr("id");
				var gId = tmpId.replace("gbox_","");
				$("#"+gId).setGridWidth(w);
			});
			});
  };

  $scope.guardarPresupuesto = function(){
    var formularioPresupuesto = objFrom.presupuestoForm;
    if(formularioPresupuesto.valid() === true){
     objFrom.presupuestoForm.submit();
   }else{
     console.error("error al guardar");
   }


    //console.log($scope.itemPresupuesto);
  };

  $scope.abrirDialogo = function(){
    console.log('ioen');
  };


 //actualiza la tabla cuando cambia periodo o inicio
  $scope.actualizar = function(){
    if($scope.cambio.inicio !== $('#inicio').val() || $scope.cambio.periodo !== $('#periodo').val()){
      swal({ title: "mensaje",text: "Al hacer este cambio se modificara la tabla de presupuesto, si guarda la informacio modificara su presupuesto",
            type: "warning",   showCancelButton: true,   confirmButtonColor: "#DD6B55",
            confirmButtonText: "Si, Quiero modificarlo",
            cancelButtonText: "No, Cancelar",
            closeOnConfirm: false,   closeOnCancel: false },
            function(isConfirm){
              if (isConfirm) {
                $scope.ver_tabla();
                swal("Modificado", "Vista del presupuesto fue modificado, pero no esta guardado.", "warning");
                $scope.cambio.periodo = $('#periodo').val();
                $scope.cambio.inicio = $('#inicio').val();
              }else {
                $('#inicio').val($scope.cambio.inicio);
                $('#periodo').val($scope.cambio.periodo);
                swal("Cancelado", "Los cambios fueron cancelados", "success");
              }
            }
      );//fin swal
    }
  };

$scope.modificar_tabla = function(){
  $("#presupuestoDinamicoGrid").setGridParam({
    url: rutas.getDataPresupuesto,
    datatype: "json",
    postData: {
      inicio:$("#inicio").val(),
      periodo: $("#periodo").val(),
      erptkn: tkn
    }
  }).trigger('reloadGrid');
};
//function para listar el grid
  $scope.ver_tabla = function(){
$http({
      url: rutas.getDataPresupuesto,
      method: 'POST',
      data : $.param({
        erptkn: tkn,
        presupuesto_id: $("#presupuesto_id").val(),
        inicio:$("#inicio").val(),
        periodo: $("#periodo").val()
      }),
      cache: false,
      xsrfCookieName: 'erptknckie_secure',
      headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
   }).then(function (data) {
     if(data){
       var datos = data.data;
       $scope.presupuesto = data.data.rows;
         $scope.hideGuardar = false;
       var jgridModel = datos.colModel;
       var jgridName = datos.colName;
       var jgridDatos = datos.rows;

       $("#presupuestoDinamicoGrid").jqGrid("clearGridData");
       $.jgrid.gridUnload('presupuestoDinamicoGrid');
       $("#presupuestoDinamicoGrid").jqGrid({
         datatype: "local",
         colNames:jgridName,
         colModel:jgridModel,
         height: "auto",
         autowidth: true,
        shrinkToFit:false,
        forceFit:true,
         rowNum: 1000,
         pager: "#presupuestoDinamicoGridPager",
         loadtext: '<p>Cargando...</p>',
         hoverrows: false,
         viewrecords: true,
         refresh: true,
         page:1,
         //ajaxGridOptions: {cache: false},
          //loadonce:false,
          localReader: { repeatitems: false },
          gridview: true,
          multiselect: false,
          sortname: 'codigo',
          sortorder: "ASC",
          loadComplete : function () {


          },
          onSelectRow: function(id) {
            $(this).find('tr#' + id).removeClass('ui-state-highlight');
          }
       }).trigger('reloadGrid');
       $("#presupuestoDinamicoGrid").jqGrid('navGrid', "#presupuestoDinamicoGridPager", {
                   edit: false,
                   add: false,
                   del: false,
                   search:false
               });

        angular.forEach(jgridDatos,function(val,i){
            $("#presupuestoDinamicoGrid").jqGrid('addRowData', (i + 1) , val);
        });

        $('#gridHeader').sticky({
          getWidthFrom: '.ui-jqgrid-view',
          className: 'jqgridHeader'
        });
        $(":input").inputmask({ "placeholder": "0.00" });
     }
   });

 }; //fin de actualizar

$scope.init();
$scope.ver_tabla();
});*/

$(document).ready(function(){
  $(document).on("click", "a.cog", function(e) {
    $("#presupuestoFillTableModal").find("h4.modal-title").html('Aplicar Formula');
    $('a.cog').each(function(i, elem){
      $(elem).removeClass('active');
    });

    var tdAdjunto = $(this).parent().next('td').next('td');
    var valorPrimerInput = $(tdAdjunto).find('input').val();
    if(_.isEmpty(valorPrimerInput)){
      swal("Debe de llenar el monto del primer mes");
      return false;
    }

    $(this).addClass('active');
    $("#presupuestoFillTableModal").modal('show');
    $("#monto_mensual").val("");
    $("#monto_porcentaje").val("");
    $("#ajuste1").prop("checked", true);
    $("#presupuestoFillTableModal").find('input#monto_fijo').val(valorPrimerInput);
    $("#presupuestoFillTableModal").find('input#aux_monto').val(valorPrimerInput);

  });
  $("#cancelarBtn").click(function(){
    $("#presupuestoFillTableModal").modal('hide');
  });
});
