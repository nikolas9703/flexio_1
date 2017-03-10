//Modulo Tabla de Cheques
var tablaCheques = (function(){

	var url = 'cheques/ajax-listar';
	var grid_id = "chequesGrid";
	var grid_obj = $("#chequesGrid");
	var opcionesModal = $('#optionsModal');

	var botones = {
		opciones: ".viewOptions",
		editar: "",
		duplicar: "",
		desactivar: "",
		buscar: "#searchBtn",
		limpiar: "#clearBtn",
		imprimir_cheque: "#imprimir_cheque"
	};

	var tabla = function(){

		//inicializar jqgrid
		grid_obj.jqGrid({
		   	url: phost() + url,
		   	datatype: "json",
		   	colNames:[
				'N&uacute;mero de cheque',
				'N&uacute;mero de pago',
				'Fecha de cheque',
				'Proveedor',
				'Chequera',
				'Monto',
				'Estado',
				'',
				''
			],
		   	colModel:[
				{name:'Numero de Cheque', index:'che_cheques.numero', width:40, align:'left'},
				{name:'Numero de Pago', index:'che_cheques.numero', width:40, align:'left'},
				{name:'Fecha de cheque', index:'che_cheques.fecha_cheque', width: 40, sortable:false, align:'left'},
				{name:'Proveedor', index:'pro_proveedores.nombre', width: 40, sortable:false, align:'left'},
				{name:'Chequera', index:'che_chequera.nombre', width:40,  sortable:false, align:'left'},
				{name:'Monto', index:'che_cheques.monto_pagado', width:40,  sortable:false, align:'left'},
				{name:'Estado', index:'che_cheques.estado', width: 40, sortable:false, align:'left'},
				{name:'link', index:'link', width:40, align:"center", sortable:false, resizable:false, hidedlg:true},
				{name:'options', index:'options', hidedlg:true, hidden: true},
		   	],
			mtype: "POST",
		   	postData: {
		   		erptkn: tkn
		   	},
			height: "auto",
			autowidth: true,
			rowList: [10, 20,50, 100],
			rowNum: 10,
			page: 1,
			pager: "#"+ grid_id +"Pager",
			loadtext: '<p>Cargando...</p>',
			hoverrows: false,
		    viewrecords: true,
		    refresh: true,
		    gridview: true,
		    multiselect: true,
		    sortname: 'id',
		    sortorder: "DESC",
		    beforeProcessing: function(data, status, xhr){
		    	//Check Session
				if( $.isEmptyObject(data.session) == false){
					window.location = phost() + "login?expired";
				}
		    },
		    loadBeforeSend: function () {},
		    beforeRequest: function(data, status, xhr){},
			loadComplete: function(data){

				//check if isset data
				if( data['total'] == 0 ){
					$('#gbox_'+ grid_id).hide();
					$('#'+ grid_id +'NoRecords').empty().append('No se encontraron datos de cheques.').css({"color":"#868686","padding":"30px 0 0"}).show();
				}
				else{
					$('#'+ grid_id +'NoRecords').hide();
					$('#gbox_'+ grid_id).show();
				}
			},
			onSelectRow: function(id){
				$(this).find('tr#'+ id).removeClass('ui-state-highlight');
			},
		});

		//Al redimensionar ventana
		$(window).resizeEnd(function() {
			 tablaCheques.redimensionar();
		});
	};

	//Inicializar Eventos de Botones
	var eventos = function(){

		//Boton de Opciones
		grid_obj.on("click", botones.opciones, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			var id = $(this).attr("data-id");
			var rowINFO = grid_obj.getRowData(id);
		    var option = rowINFO["options"];
		    options = option.replace(/0000/gi, id);

		    //evento para boton collapse sub-menu Accion Personal
		    opcionesModal.on('click', 'a[href="#collapse'+ id +'"]', function(){
		    	opcionesModal.find('#collapse'+ id ).collapse();
		    });

	 	    //Init Modal
		    opcionesModal.find('.modal-title').empty().append('Opciones: '+ rowINFO["Numero de Cheque"] +'');
		    opcionesModal.find('.modal-body').empty().append(options);
		    opcionesModal.find('.modal-footer').empty();
		    opcionesModal.modal('show');
		});

		opcionesModal.on("click", botones.imprimir_cheque, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			var uuid_cheque = $(this).attr('data-id');

		    //Init boton de opciones
			opcionesModal.find('.modal-title').empty().append('Confirme');
			opcionesModal.find('.modal-body').empty().append('Est&aacute; seguro que desea imprinir este cheque?');
			opcionesModal.find('.modal-footer')
				.empty()
				.append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
				.append('<button id="confirmarImprimir" data-uuid="'+ uuid_cheque +'" class="btn btn-w-m btn-primary" type="button">Imprimir</button>');
		 });

		 opcionesModal.on("click", "#confirmarImprimir", function(e){
				e.preventDefault();
			 e.returnValue=false;
			 e.stopPropagation();

			 var data_uuid = $(this).attr('data-uuid');

			 $.ajax({
 				url: phost() + 'cheques/ajax-cambiando-estado',
 				data: {
 						data_uuid: data_uuid,
 	        	erptkn: tkn
 				},
 				type: "POST",
 				dataType: "json",
 				cache: false
			}).done(function(json) {

 				 //Check Session
 				if( $.isEmptyObject(json.session) == false){
 					window.location = phost() + "login?expired";
 				}
 				//If json object is empty.
 				if($.isEmptyObject(json) == true){
 					return false;
 				}
 				//Mostrar Mensaje
 				if(json.response == "" || json.response == undefined || json.response == 0){
 					toastr.error(json.mensaje);
 				}else{
						opcionesModal.modal('hide');
						recargar();
 						toastr.success(json.mensaje);
 						setTimeout(
							  function()
							  {
							    imprimirPdf(data_uuid)
							  }, 1000);

 					}
  			});



		 });

		//Boton de Buscar
		$(botones.buscar).on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			buscar();
		});

		//Boton de Reiniciar jQgrid
		$(botones.limpiar).on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			recargar();
			limpiarCampos();
		});
	};

	//Buscar
	var imprimirPdf = function(data_uuid){
		var url = phost() + 'cheques/imprimir/'+data_uuid;
		 $(location).attr("href", url);
	}
	var buscar = function(){

        var fecha1 = $('#fecha1').val();
        var fecha2 = $('#fecha2').val();
		var proveedor = $('#proveedor').val();
		var estado = $('#estado').val();
        var numero = $('#numero').val();
        var chequera = $('#chequera').val();

		if(fecha1!= "" || fecha2 != "" || proveedor != ""  || estado != "" || numero != ""  || chequera != "" )
		{
			//Reload Grid
			grid_obj.setGridParam({
				url: phost() + url,
				datatype: "json",
				postData: {
                    desde: fecha1,
                    hasta: fecha2,
                    proveedor: proveedor,
                    estado: estado,
                    numero: numero,
                    chequera: chequera,
					erptkn: tkn
				}
			}).trigger('reloadGrid');
		}
	};

	//Reload al jQgrid
	var recargar = function(){

		//Reload Grid
		grid_obj.setGridParam({
			url: phost() + url,
			datatype: "json",
			postData: {
                fecha1: '',
                fecha2: '',
                proveedor: '',
                estado: '',
                numero: '',
                chequera: '',
				erptkn: tkn
			}
		}).trigger('reloadGrid');
	};


	//Limpiar campos de busqueda
	var limpiarCampos = function(){
		$('#buscarChequesForm').find('input[type="text"]').prop("value", "");
		$('#buscarChequesForm').find('select').find('option:eq(0)').prop('selected', 'selected');
	};

	return{
		init: function() {
			tabla();
			eventos();
		},
		recargar: function(){
			//reload jqgrid
			recargar();
		},
		redimensionar: function(){
			//Al redimensionar ventana
			$(".ui-jqgrid").each(function(){
				var w = parseInt( $(this).parent().width()) - 6;
				var tmpId = $(this).attr("id");
				var gId = tmpId.replace("gbox_","");
				$("#"+gId).setGridWidth(w);
			});
		}
	};

})();

var config = {
    dateRangePicker:{
        locale:{
            format: 'DD-MM-YYYY'
        },
        showDropdowns: true,
        defaultDate: '',
        singleDatePicker: true
    }
};

$("#fecha1").daterangepicker(config.dateRangePicker).val("");
$("#fecha2").daterangepicker(config.dateRangePicker).val("");

//Expotar a CSV
$('#moduloOpciones ul').on("click", "#exportarListaCheques", function(e){
    e.preventDefault();
    e.returnValue=false;
    e.stopPropagation();

    if($('#tabla').is(':visible') == true){
        //Desde la Tabla
        exportarjQgrid();

    }
});

function exportarjQgrid() {
    //Exportar Seleccionados del jQgrid
    var registros_jqgrid = [];

    registros_jqgrid = $("#chequesGrid").jqGrid('getGridParam','selarrrow');

    var obj = new Object();
    obj.count = registros_jqgrid.length;

    if(obj.count) {

        obj.items = new Array();

        for(elem in registros_jqgrid) {
            //console.log(proyectos[elem]);
            var registro_jqgrid = $("#chequesGrid").getRowData(registros_jqgrid[elem]);

            //Remove objects from associative array
            delete registro_jqgrid['link'];
            delete registro_jqgrid['options'];

            //Push to array
            obj.items.push(registro_jqgrid);
        }


        var json = replaceDiacritics(JSON.stringify(obj));
        var csvUrl = JSONToCSVConvertor(json);
        var filename = 'cheques_'+ Date.now() +'.csv';

        //Ejecutar funcion para descargar archivo
        downloadURL(csvUrl, filename);

        $('body').trigger('click');
    }
}

/*
* Función para quitar los acentos antes de exportar el CSV
* */
function replaceDiacritics(str){

    var diacritics = [
        {char: 'A', base: /[\300-\306]/g},
        {char: 'a', base: /[\340-\346]/g},
        {char: 'E', base: /[\310-\313]/g},
        {char: 'e', base: /[\350-\353]/g},
        {char: 'I', base: /[\314-\317]/g},
        {char: 'i', base: /[\354-\357]/g},
        {char: 'O', base: /[\322-\330]/g},
        {char: 'o', base: /[\362-\370]/g},
        {char: 'U', base: /[\331-\334]/g},
        {char: 'u', base: /[\371-\374]/g},
        {char: 'N', base: /[\321]/g},
        {char: 'n', base: /[\361]/g},
        {char: 'C', base: /[\307]/g},
        {char: 'c', base: /[\347]/g}
    ]

    diacritics.forEach(function(letter){
        str = str.replace(letter.base, letter.char);
    });

    return str;
}

tablaCheques.init();

    var data = {
        opcionesModal: '#optionsModal',
        modalTitle: 'Anular cheque: '
    };

    var dom = {
        opcionesModal: $(data.opcionesModal)
    };

    var methods = {
        mGetNotification:function(notificacion, clase){
            var html = '';

            html += '<div class="alert '+ clase +'">';
            html += notificacion;
            html += '</div>';

            return html;
        },
        mGetLabel:function(titulo, subtitulo){
            var html = '';

            html += '<div>';
            html += '<h1>'+ titulo +'</h1>';
            html += '<h3 style="border-bottom: 3px solid silver;padding-bottom:4px;">'+ subtitulo +'</h3>';
            html += '</div>';

            return html;
        },
        mGetButtons:function(cheque){
            var html = '';

            html += '<div style="text-align:center;">';
            html += '   <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">';
            html += '       <a href="#" class="btn btn-default btn-block" id="cancelarModal">Cancelar </a>';
            html += '   </div>';
            html += '   <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">';
            html += '       <input type="submit" data-id="'+ cheque.data("id") +'" id="confirmarModal" class="btn btn-primary btn-block" value="Confirmar">';
            html += '   </div>';
            html += '</div>';

            return html;
        },
        mGetModalBody: function(cheque){
            var modalBody = '';

            modalBody += methods.mGetNotification('¡Atención! Esta acción no puede ser revertida', 'alert-danger');
            modalBody += methods.mGetLabel('$'+ cheque.data("monto"), 'Monto');
            modalBody += methods.mGetLabel(cheque.data("proveedor_nombre"), 'Proveedor');
            modalBody += methods.mGetButtons(cheque);

            return modalBody;
        },
        mAnularCheque: function(e){
            var self = $(this);

            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();

            dom.opcionesModal.on('click', '#cancelarModal, #confirmarModal', function(){
                var self = $(this);
                dom.opcionesModal.modal("hide");
                self.unbind();

                if(self.prop("id") == 'confirmarModal')
                {
                    methods.mAnularChequePost(self.data("id"));
                }
            });

            //Init Modal
            dom.opcionesModal.find('.modal-title').empty().append(data.modalTitle + self.data("numero"));
            dom.opcionesModal.find('.modal-body').empty().append(methods.mGetModalBody(self));
            dom.opcionesModal.find('.modal-footer').empty();
            dom.opcionesModal.modal('show');
        },
        mAnularChequePost: function(cheque_id){
            $.ajax({
                url: phost() +"/cheques/ajax-anular-cheque",
                type:"POST",
                data:{
                    erptkn:tkn,
                    cheque_id:cheque_id
                },
                dataType:"json",
                success: function(data){
                    if(data.success === true)
                    {
                        toastr.success("Éxito! Su solicitud se ha procesado correctamente");
                    }
                    else
                    {
                        toastr.error("Error! Su solicitud no se ha procesado");
                    }
                }
            });
        }
    };



    $('#optionsModal').on('click', '#anular_cheque', methods.mAnularCheque);
