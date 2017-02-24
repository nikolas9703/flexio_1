$(function(){
	"use strict";
    //Init Bootstrap Calendar Plugin
    $('#inicio_vigencia, #fin_vigencia').daterangepicker({
    	locale: {
    		format: 'YYYY-MM-DD'
    	},
    	showDropdowns: true,
    	defaultDate: '',
    	singleDatePicker: true
    }).val('');

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
    	
    	maxDate: '+0d',
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