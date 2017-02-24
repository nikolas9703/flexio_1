var planilla = (function(){

    var formulario = '#crearPlanilla';
    var opcionesModal = $('#opcionesModal');


 var botones = {
     guardarCC: "#guardarBtnCC",
       guardarCol: "#guardarBtnCol",
       guardarNoRegulares: "#guardarBtnPlanillaNoRegular",
       cancelarCol: "#cancelarBtnCol, #cancelarBtnPlanillaNoRegular, #cancelarBtnCC"
 };

 var campos = function(){
     $('#lista_colaboradores').multiselect({
       keepRenderingSort: true,
       search: {
           left: '<input type="text" name="q" class="form-control" placeholder="Buscar..." />',
           right: '<input type="text" name="q" class="form-control" placeholder="Buscar..." />',
       }
   });
    $(".select2").select2();
    //$(".select2").css( 'width', "375px");

   $("#guardarBtnCol").prop('disabled', true);
     $(formulario).validate({
     focusInvalid: true,
     ignore: '',
     wrapper: '',
   });

     var fecha1 = $(formulario).find('#rango_fecha1');
   var fecha2 = $(formulario).find('#rango_fecha2');

   fecha1.daterangepicker({
           singleDatePicker: true,
           showDropdowns: true,
           opens: "left",
           locale: {
              format: 'DD/MM/YYYY',
              applyLabel: 'Seleccionar',
              cancelLabel: 'Cancelar',
              daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
              monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
              firstDay: 1
           }
       });
   fecha2.daterangepicker({
           singleDatePicker: true,
           showDropdowns: true,
           opens: "left",
           locale: {
              format: 'DD/MM/YYYY',
              applyLabel: 'Seleccionar',
              cancelLabel: 'Cancelar',
              daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
              monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
              firstDay: 1
           }
       });
    fecha1.val("");
    fecha2.val("");

    if(tipo_planilla_creacion == 'liquidaciones' ){
     $(formulario).find('select[name="deducciones[]"], select[name="acumulados[]"]').rules(
         "add",{
           required: false
     });
     $(formulario).find('select[name="acumulados[]"]').rules(
         "add",{
           required: false
     });
     $(formulario).find('select[name="ciclo_id"], select[name="centro_contable_id[]"]').rules(
         "add",{
           required: false
     });
     $(formulario).find('select[name="pasivo_id"]').rules(
         "add",{
           required: true
     });

      $(formulario).find('select[name="ciclo_id"]').attr('disabled', true);
      $(formulario).find('#rango_fecha1').attr('disabled', true);
      $(formulario).find('#rango_fecha2').attr('disabled', true);
    }

    if( tipo_planilla_creacion == 'vacaciones' || tipo_planilla_creacion == 'licencias'){
      $(formulario).find('select[name="ciclo_id"]').attr('disabled', true);
      $(formulario).find('select[name="centro_contable_id[]"]').attr('disabled', true);
      $(formulario).find('#rango_fecha1').attr('disabled', true);
      $(formulario).find('#rango_fecha2').attr('disabled', true);

      $(formulario).find('select[name="centro_contable_id[]"],select[name="ciclo_id"]').rules(
           "add",{
             required: false
       });
       $(formulario).find('select[name="deducciones[]"]').rules(
           "add",{
             required: false
       });
       $(formulario).find('select[name="acumulados[]"] ').rules(
           "add",{
             required: false
       });
    }


    if(tipo_planilla_creacion != 'regular'){
      $(formulario).find('#tipo_id').val( tipo_planilla_id );
      $(formulario).find('select[name="tipo_id"]').attr('disabled', true);

    }
    else{

     $(formulario).find('#tipo_id option').each(function( index ) {
       var valor = this.value;

             if(valor != 79 && valor != 96 && valor!=''){

                 //$("#tipo_id option[value="+ valor+ "]").hide();
                 $("#tipo_id option[value="+ valor+ "]").remove();

           }
         });
         $(formulario).find('select[name="cuenta_debito_id"]').rules(
             "add",{
               required: true
         });
         $(formulario).find('select[name="pasivo_id"]').rules(
             "add",{
               required: true
         });
    }

 };

 var eventos = (function(){

   $(botones.guardarCol).on("click", function(e){
     e.preventDefault();
     e.returnValue=false;
     e.stopPropagation();

     agregarPlanillaColaboradores();
   });

   $(botones.guardarNoRegulares).on("click", function(e){
     e.preventDefault();
     e.returnValue=false;
     e.stopPropagation();

     agregarPlanillaNoRegulares();
   });

   $(botones.cancelarCol).on("click", function(e){
     e.preventDefault();
     e.returnValue=false;
     e.stopPropagation();
     window.location.href = phost()+'planilla/listar';
   });

   $("#crearPlanilla").on("click", '#listar_colaborador', function(e){
       e.preventDefault();
       e.returnValue=false;
       e.stopPropagation();
       $("#guardarBtnCol").prop('disabled', true);
       var ciclo_id = $("#ciclo_id").val();
       var centro_contable_id = $("#centro_contable_id").val();
       var fecha_inicio_planilla = $("#rango_fecha1").val();
       var fecha_final_planilla = $("#rango_fecha2").val();
       var area_negocio_id = $("#area_negocio_id").val();

       var button= $(this);
       var tmp= button.html();
       button.html("<i class='fa fa-cog fa-spin'></i> Listando..").attr("disabled", "disabled");

        $.ajax({
         url: phost() + 'planilla/ajax_listar_colaboradores_dependiente',
          data: {
            ciclo_id: ciclo_id,
            centro_contable_id: centro_contable_id,
            area_negocio_id: area_negocio_id,
            fecha_inicio_planilla: fecha_inicio_planilla,
            fecha_final_planilla: fecha_final_planilla,
            erptkn: tkn
             },
         type: "POST",
         dataType: "json",
         cache: false,
       }).done(function(json) {

         //Check Session
         if( $.isEmptyObject(json.session) == false){
           window.location = phost() + "login?expired";
         }
          if(json.response == true){
             $("#crearPlanilla").find('#lista_colaboradores').empty();
             $("#crearPlanilla").find('#lista_colaboradores_to').empty();
             if(json.colaboradores.length > 0){
                   $.each(json.colaboradores, function(i, result){

                       $("#crearPlanilla").find('#lista_colaboradores').append('<option value="'+result.id+'">'+result.cedula+' - '+result.nombre_completo+'</option>');
                   });
                   if(ciclo_id != '')
                       $("#guardarBtnCol").prop('disabled', false);
              }
          }else{
           toastr.error(json.mensaje);
         }
            button.html(tmp).removeAttr("disabled");
        });
   });
 });
 var agregarPlanillaColaboradores = function(){

   var tipo_id = $(formulario).find("#tipo_id").val();

   if(tipo_id  == 96){

       $(formulario).find('select[name="acumulados[]"]').rules(
           "add",{
             required: false
       });
       $(formulario).find('select[name="deducciones[]"]').rules(
           "add",{
             required: false
       });
   }
   $(formulario).find("#lista_colaboradores_to").find("option").prop("selected", true);
   $('#lista_colaboradores_to').rules(
       "add",{
         required: true
   });
   $('#rango_fecha1').rules(
       "add",{
         required: true
   });
   $('#rango_fecha2').rules(
       "add",{
         required: true
   });
       if( $(formulario).validate().form() == true )
     {

         $("#guardarBtnCol").attr('disabled', true);
         $(formulario).find('select').attr('disabled', false);
       $.ajax({
         url: phost() + 'planilla/ajax-crear-planilla',
         data:  $(formulario).serialize()+'&tipo_creacion=colaborador',
         type: "POST",
         dataType: "json",
         cache: false,
       }).done(function(json) {
         if( $.isEmptyObject(json.session) == false){
           window.location = phost() + "login?expired";
         }
          if(json.response == true){
                toastr.success(json.mensaje);
             window.location.href = phost()+'planilla/listar';

         }else{
           toastr.error(json.mensaje);
         }
       });
    }
 };
 var agregarPlanillaNoRegulares = function(){


       if( $(formulario).validate().form() == true )
     {
     //Operacion para generar la lista de las acciones seleccionadas

     $("#guardarBtnPlanillaNoRegular").attr('disabled', true);
     var acciones_personales = [];
      acciones_personales = $("#tablaAccionPersonalGrid").jqGrid('getGridParam','selarrrow');
       if(acciones_personales.length == 0){
         toastr.warning('Debe seleccionar uno o varias acciones personales de vacaciones.');
         return false;
       }else{
         $(formulario).find('select').attr('disabled', false);


           //Verificar que las acciones seleccionadas sean SOLO de vacaciones
         $.each(acciones_personales, function(indice, accion){
           $(formulario).append('<input type="hidden" name="seleccionados['+ indice +']" value="'+ accion.replace( /^\D+/g, '') +'"  />');

         });

         $.ajax({
           url: phost() + 'planilla/ajax-crear-planillaNoRegulares',
           data:  $(formulario).serialize()+'&tipo_creacion='+tipo_planilla_creacion,
           type: "POST",
           dataType: "json",
           cache: false,
         }).done(function(json) {
           if( $.isEmptyObject(json.session) == false){
             window.location = phost() + "login?expired";
           }
            if(json.response == true){
               window.location.href = phost()+'planilla/listar';

           }else{
             toastr.error(json.mensaje);
           }
         });

       }

    }

 };
 return{
   init: function() {
     campos();
     eventos();
   },
   limpiarFormulario: function(){
     limpiarFormulario();
   },
 };
})();
planilla.init();
