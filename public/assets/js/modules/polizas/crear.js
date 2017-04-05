$(function(){
	"use strict";
    //Init Bootstrap Calendar Plugin

    $(".chosen").chosen({width: "100%"});

    $('#txtCliente').autocomplete({
    	source: '../polizas/ajax_clientes_ac',
    	minLength:2,
    	select: function(event, ui){
    		$("#txtCliente").prop("value",ui.item.label);
    		$("#cliente").prop("value",ui.item.value);
    		return false;
    	},
    	change:function(){
    		if($("#cliente").val()==""){
    			$("#txtCliente").val("");
    		}
    	}
    });

    $("#txtCliente").keyup(function(){
    	$("#cliente").val("");
    });

    $('#ramo').autocomplete({
    	source: 'ajax_ramos_ac',
    	minLength:2,
    	select: function(event, ui){
    		$("#ramo").prop("value",ui.item.label);
    		return false;
    	}
    });
    $("#ramo").blur(function(){
    	var string = $("#ramo").val();
    	var ramo = string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
    	$("#ramo").prop("value",ramo);
    });

    $("#imprimir_poliza").click(function(e){
    	var id_poliza = $('#idPoliza').val();
    	console.log(id_poliza);
    	window.open('../imprimirPoliza/'+id_poliza); 
    });
    $("#exportarPBtn").click(function(e){
    	var id_poliza = $('#idPoliza').val();
    	console.log(id_poliza);
    	window.open('../exportarPoliza/'+id_poliza); 
    });

    $('.datepicker').datepicker({
    	
    	minDate: '+0d',
    	endDate: '2010-12-31',
    	changeMonth: true,
    	changeYear: true
    });
    $('.datepicker2').datepicker({
    	
    	changeMonth: true,
    	changeYear: true
    });

});
var opcionesModal = $('#opcionesModal'),
modalRenovation =$('#opcionesModalRenovation'),
formRenewal;

$(document).ready(function(){
	formRenewal=$('#renovationPolicy').validate({



		rules: {
			"campo[numeroPoliza]": {
				required: true,
			},
			"campo[fechaInicio]": {
				required: true
			},
			"campo[fechaExpiracion]":{
				required:true
			},
			"agente[]":{
				required:true
			},
			'participacion[]':{
				required:true
			},

		}

	});



    if (validavida == 1 && id_tipo_poliza == 2) {
            var relacion = $('.relaciondetalle_persona_vida').val(); 
            if(relacion == "Principal"){
                //$('#participacion_persona').attr('disabled',true);
                console.log("cambio princpial");
                $("#vigencia_vida_colectivo").show();           
            }else{
                //$('#participacion_persona').attr('disabled',false);
                console.log("cambio princpial2");
                $("#vigencia_vida_colectivo").hide();
            }
        }

    

});


var renovationForm = new Vue({
	el: '.div_renovation',
	data: {
		isEditable: false,
		tkn:tkn,
		idPolicy: 0
	},
	methods: {
		renovationModal: function (idPolicy,permiso_comision,permiso_agente,permiso_participacion) {
			formRenewal.resetForm();
			this.$http.post({
				url: phost() + 'polizas/getRenovationData',
				method:'POST',
				data:{idPoliza:idPolicy,erptkn: tkn}
			}).then(function(response){
				if(_.has(response.data, 'session')){
					window.location.assign(phost());
				}     
				if(!_.isEmpty(response.data)){            
					this.$set('PolicyData',response.data);
					this.$set('numero',response.data.numero);
					this.$set('fechaInicio',response.data.fechaInicio);
					this.$set('fechaExpiracion',response.data.fechaExpiracion);
					this.$set('isEditable',response.data.isEditable);
					this.$set('idPolicy',idPolicy);
					this.$set('comision',response.data.comision);
					if(permiso_comision == 0){
						this.$set('disabledComision',true);
					}
					if(permiso_agente == 0){
						this.$set('disabledAgente',true);
					}
					if(permiso_participacion == 0){
						this.$set('isEditable',true);
					}
					$(modalRenovation).find('.modal-title').empty().html('Póliza: '+ response.data.numero +'')
				}           

			});
			

		},
		submitForm: function(){

			this.$set('disabledComision',false);
			this.$set('disabledAgente',false);
			this.$set('isEditable',false);

			if($('#renovationPolicy').validate().form()){

				var participationArray = [];
				participationArray.push({
					nombre: $("select[name='agente[]']").map(function () {
						return $(this).val();
					}).get(),
					valor: $("input[name='participacion[]']").map(function () {
						return $(this).val();
					}).get()

				});

				this.$http.post({
					url: phost() + 'polizas/policyRenewal',
					method:'POST',
					data:{
						numeroPoliza:this.numero,
						erptkn: tkn,
						fechaInicio:this.fechaInicio,
						fechaExpiracion:this.fechaExpiracion,
						participacion:participationArray[0],
						renovarPoliza :true,
						idPolicy :this.idPolicy,
						comision: this.comision,
					}
				}).then(function(response){
					if (!_.isEmpty(response.data) && response.data.msg =='OK') {
						$("#PolizasGrid").trigger('reloadGrid');
						modalRenovation.modal('hide');
						toastr.success('Se ha realizado la revación correctamente.');
					}else{
						
						msg='Ocurrido un error al guardar la revación.'+'<b>'+response.data.errorDetail+'<b>';
						
						toastr.error(msg);
					}			
				});	
			}
		}
	}

});

/*function actualizarPrima(){
    var prima = 0 ;
    $("#tablaSolicitudesVehiculo").find("tbody").find("")
}*/

function inicializaCamposAcreedor(){

    $("#contenedoracreedores").remove();
    $('#contacre').after('<div id="contenedoracreedores"><div class="file_tools_acreedores_adicionales row" id="a1"><div class="col-xs-12 col-sm-6 col-md-2 col-lg-2" style="margin-right: -5px"><input type="text" name="campoacreedores[]" id="acreedor_1" class="form-control"></div><div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><div class="input-group"><span class="input-group-addon">%</span> <input type="text" name="campoacreedores_por[]" id="porcentajecesion_1" class="form-control porcentaje_cesion_acreedor" value="0"></div></div> <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><div class="input-group"><span class="input-group-addon">$</span> <input type="text" name="campoacreedores_mon[]" id="montocesion_1" class="form-control monto_cesion_acreedor" value="0"></div></div> <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><div class="input-group"><span class="input-group-addon"><i class="fa fa-calendar"></i></span> <input type="text" name="campoacreedores_ini[]" id="fechainicio_1" class="form-control fechas_acreedores_inicio"></div></div> <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><div class="input-group"><span class="input-group-addon"><i class="fa fa-calendar"></i></span> <input type="text" name="campoacreedores_fin[]" id="fechafin_1" class="form-control fechas_acreedores_fin"></div></div><div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><button type="button" class="btn btn-default btn-block add_file_acreedores_adicionales" onclick="agregaracre()" style="float: left; width: 40px; margin-right:5px;" ><i class="fa fa-plus"></i></button><button type="button" style="float: left; width: 40px; margin-top:0px!important; display:none" id="del_acre" class="btn btn-default btn-block del_file_acreedores_adicionales" onclick="eliminaracre(1)"><i class="fa fa-trash"></i></button></div><input type="hidden" name="campoacreedores_id[]" value="0"></div><div id="agrega_acre"></div></div>');
                            
    //Inicializa los campos
    $(".monto_cesion_acreedor").inputmask('currency',{ 
        prefix: "", 
        autoUnmask : true, 
        removeMaskOnSubmit: true 
    });

    $(".porcentaje_cesion_acreedor").inputmask('Regex', { regex: "^[1-9][0-9][.][0-9][0-9]?$|^100[.]00?$|^[0-9][.][0-9][0-9]$" });
    //$(".porcentaje_cesion_acreedor").inputmask('decimal',{min:0, max:100});

    $('.fechas_acreedores_inicio').each(function () {
        console.log("veces");
        var f = $(this).val();
        if ($(this).val() == "0000-00-00") {
            var now = new Date();
            now.setDate(now.getDate());
            var dat = now.getDate();
            var mon = now.getMonth() + 1;
            var year = now.getFullYear();
            if (mon < 10) {
                mon = "0" + mon;
            }
            if (dat < 10) {
                dat = "0" + dat;
            }
            var fe = mon + "/" + dat + "/" + year;
            $(this).val(fe);
            f = "";
        }
        $(this).daterangepicker({ 
         locale: { format: 'MM/DD/YYYY' },
         showDropdowns: true,
         defaultDate: '',
         singleDatePicker: true
     }).val(f);
    });
    $('.fechas_acreedores_fin').each(function () {
        console.log("veces2");
        var f = $(this).val();
        if ($(this).val() == "0000-00-00") {
            var now = new Date();
            now.setDate(now.getDate());
            var dat = now.getDate();
            var mon = now.getMonth() + 1;
            var year = now.getFullYear();
            if (mon < 10) {
                mon = "0" + mon;
            }
            if (dat < 10) {
                dat = "0" + dat;
            }
            var fe = mon + "/" + dat + "/" + year;
            $(this).val(fe);
            f = "";
        }
        $(this).daterangepicker({ 
         locale: { format: 'MM/DD/YYYY' },
         showDropdowns: true,
         defaultDate: '',
         singleDatePicker: true
     }).val(f);
    });

    $('#fechafin_1').daterangepicker({ 
     locale: { format: 'MM/DD/YYYY' },
     showDropdowns: true,
     defaultDate: '',
     singleDatePicker: true
 }).val('');

    $(".fechas_acreedores_inicio").change(function () {
        var vigini = $("#vigencia_desde").val();
        var vigfin = $("#vigencia_hasta").val();
        var actual = $(this).val();

        var id = $(this).attr("id");
        var x = id.split('_');
        var final = $("#fechafin_"+x[1]).val();

        /*if (vigini.indexOf('/') > -1) {
            var dat = vigini.split('/');
            var dia = dat[1];
            var mes = dat[0];
            var anio = dat[2];
            vigini = anio + '-' + mes + '-' + dia ;
        }
        if (vigfin.indexOf('/') > -1) {
            var dat = vigfin.split('/');
            var dia = dat[1];
            var mes = dat[0];
            var anio = dat[2];
            vigfin = anio + '-' + mes + '-' + dia ;
        }
        if (actual.indexOf('/') > -1) {
            var dat = actual.split('/');
            var dia = dat[0];
            var mes = dat[1];
            var anio = dat[2];
            actual = anio + '-' + mes + '-' + dia ;
        }*/

        var ini = new Date(vigini);
        var fin = new Date(vigfin);
        var act = new Date(actual);
        var fefin = new Date(final);

        if (act < ini) {
            $(this).val(vigini);
        }else if(act > fin){
            $(this).val(vigfin);
        }else if(final != "" && act > fefin){
            $(this).val(final);
        }
    });

    $(".fechas_acreedores_fin").change(function () {
        var vigini = $("#vigencia_desde").val();
        var vigfin = $("#vigencia_hasta").val();
        var actual = $(this).val();

        var id = $(this).attr("id");
        var x = id.split('_');
        var inicial = $("#fechainicio_"+x[1]).val();

        /*if (vigini.indexOf('/') > -1) {
            var dat = vigini.split('/');
            var dia = dat[1];
            var mes = dat[0];
            var anio = dat[2];
            vigini = anio + '-' + mes + '-' + dia ;
        }
        if (vigfin.indexOf('/') > -1) {
            var dat = vigfin.split('/');
            var dia = dat[1];
            var mes = dat[0];
            var anio = dat[2];
            vigfin = anio + '-' + mes + '-' + dia ;
        }*/

        var ini = new Date(vigini);
        var fin = new Date(vigfin);
        var act = new Date(actual);
        var feini = new Date(inicial);

        if (act < ini) {
            $(this).val(vigini);
        }else if(act > fin){
            $(this).val(vigfin);
        }else if(inicial != "" && act < feini){
            $(this).val(inicial);
        }
    });

    $(".monto_cesion_acreedor").keyup(function(){
        var id = $(this).attr("id");
        var x = id.split('_');
        var monto = $("#montocesion_"+x[1]).val();

        if (id_tipo_poliza == 1) {
            var sumaasegurada = $("#suma_asegurada").val();
        }else if(id_tipo_poliza == 2){
            var sumaasegurada = $("#suma_asegurada_persona").val();
        }        
        if (sumaasegurada == "") { sumaasegurada = 0;}
        var porcentaje = (monto * 100 )/(sumaasegurada);
        if (porcentaje > 100) { porcentaje = 100;}
        $("#porcentajecesion_"+x[1]).val(porcentaje);
    });

    $(".porcentaje_cesion_acreedor").keyup(function(){
        var id = $(this).attr("id");
        var x = id.split('_');
        var porcentaje = $("#porcentajecesion_"+x[1]).val();
        if (porcentaje == "") { porcentaje = 0;}
        if (id_tipo_poliza == 1) {
            var sumaasegurada = $("#suma_asegurada").val();
        }else if(id_tipo_poliza == 2){
            var sumaasegurada = $("#suma_asegurada_persona").val();
        } 
        var monto = (porcentaje * sumaasegurada )/(100);
        if (porcentaje>100) {
            $("#montocesion_"+x[1]).val(sumaasegurada);
        }else{
            $("#montocesion_"+x[1]).val(monto);
        }            
    });
    //------------------------
}