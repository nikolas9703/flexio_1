 
$(function() {

	 $('#optionsModal').on("click", "#subirArchivosBtn", function(e){
			
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			var uuid_contacto = $(this).attr("data-contacto");
			
			$('#optionsModal').modal('hide');
			$('#crearDocumentoModal').modal('show');
				 
				 
			 $("#input-dim-1").fileinput({
				    uploadUrl:  phost() + "documentos/ajax-subir-archivos",
				    allowedFileExtensions: null,
				    minImageWidth: 50,
				    minImageHeight: 50,
				    uploadAsync: true,
				    maxFileSize: 500,
				    language: 'es',
				    uploadExtraData: function() {
				          return {
				        	  erptkn: tkn,
				        	  uuid_relacion: uuid_contacto,
				        	  modulo: 'contactos' 
				          };
				    }
				     
			});
			  $('#input-dim-1').on('filebatchuploadcomplete', function(event, files, extra) {
					    $('#crearDocumentoModal').modal('hide');
						 //location.reload();
			});
	});
	 
     //Expotar Contactos a CSV
    $('#moduloOpciones ul').on("click", "#exportarContactosBtn", function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();

            //var contactos = [];

        	if($('#tabla').is(':visible') == true){
				exportarjQgrid();
 			}else{ //Si se está exportando desde el Grid
 				exportarGrid();
	 		}
     });
    
    function exportarjQgrid() {
		//Exportar Seleccionados del jQgrid
		var contactos = [];
		
		contactos = $("#contactosGrid").jqGrid('getGridParam','selarrrow');
		
		var obj = new Object();
		obj.count = contactos.length;
	
		if(obj.count) {
			
			obj.items = new Array();
			
			for(elem in contactos) {
				//console.log(proyectos[elem]);
				var contacto = $("#contactosGrid").getRowData(contactos[elem]);
				
				//Remove objects from associative array
 				delete contacto['options'];
                delete contacto['link'];
				//Push to array
				obj.items.push(contacto);
			}
			
			var json = JSON.stringify(obj);
			var csvUrl = JSONToCSVConvertor(json);
			var filename = 'contactos_'+ Date.now() +'.csv';
			
			//Ejecutar funcion para descargar archivo
			downloadURL(csvUrl, filename);
			
			$('body').trigger('click');
		} 
	}
	 function exportarGrid(){
		 
		 var contactos = [];
		 
		 $("#iconGrid").find('input[type="checkbox"]:checked').filter(function(){
			 contactos.push(this.value);
			});
		 
			//Verificar si ha seleccionado algun proyecto
			if(contactos.length==0){
				return false;
			}
			//Convertir array a srting separado por guion
			var contactos_string = contactos.join('-');
		
			//Armar url
			var url = phost() + 'contactos/ajax-exportar/'+ contactos_string;
			 
			downloadURL(url);
	}
	 
   

	$('#searchBtnCon').bind('click', searchBtnHlrCon);
	$('#clearBtn').click(function(e){
		e.preventDefault();
		
		$('#ticketStatus option[value="OPEN"]').prop('selected', true);
		
		$("#contactosGrid").setGridParam({
			url: phost() + 'contactos/ajax-listar-contactos',
			postData: {
				nombre: '',
                                cliente: '',
                                telefono: '',
                                email: '',
                                erptkn: tkn
			}
		}).trigger('reloadGrid');
		
		//Reset Fields
		$('#nombre, #cliente, #telefono, #email').val('');
		$('#id_agencia option:first, #id_rol option:first').prop('selected', true);
	});



 
});
//** Funcion para llenar la lista de clientes el crear una actividad, solo se usa en contactos **//
function popular_clientes_en_contactos(uuid_contacto){
	if(uuid_contacto==""){
	      return false;
	  }

	  $.ajax({
	      url: phost() + 'actividades/ajax-seleccionar-clientes-via-contactos',
	      data: {
	      	uuid_contacto: uuid_contacto,
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
	      
 	      	
	      //Se borra todos los clientes presente en la lista
	      $('form#crearActividad').find('select[name="campo[uuid_cliente]"]').empty();
           setTimeout(function(){
              $(".chosen-select").chosen({
                  width: '100%'
              }).trigger('chosen:updated');
          }, 500);
 
	      //If json object is not empty.
	      if( $.isEmptyObject(json.results[0]) == false ){
	    	  $('form#crearActividad').find('select[name="campo[uuid_cliente]"]').append('<option  value="">Seleccione</option>');
  	          $.each(json.results[0], function(i, result){
     				$('form#crearActividad').find('select[name="campo[uuid_cliente]"]').append('<option  value="'+ result['uuid_cliente']  +'">'+ result['nombre_cliente'] +'</option>');
    				 
  	          });
  	          		$('form#crearActividad').find('select[name="campo[uuid_cliente]"]').prop("disabled", false);
	      }
	      else{
	    	  $('form#crearActividad').find('select[name="campo[uuid_cliente]"]').append('<option value="" selected="selected">Seleccione</option>').prop("disabled", "disabled");
  	      }
	      setTimeout(function(){
              $(".chosen-select").chosen({
                  width: '100%'
              }).trigger('chosen:updated');
          }, 500);
	      $('form#crearActividad').find('select[name="campo[uuid_sociedad]"]').append('<option value="" selected="selected">Seleccione</option>').prop("disabled", "disabled");
 	   });
}
function searchBtnHlrCon(e) {
 	e.preventDefault();
	$('#searchBtnCon').unbind('click', searchBtnHlrCon);

	var nombre 	= $('#nombre').val();
	var cliente 	= $('#cliente').val();
	var telefono 	= $('#telefono').val();
	var email 	= $('#email').val();

	if(nombre != "" || cliente != "" || telefono != "" || email != "")
	{	
		$("#contactosGrid").setGridParam({
			url: phost() + 'contactos/ajax-listar-contactos',
			datatype: "json",
			postData: {
				nombre: nombre,
                cliente: cliente,
                telefono: telefono,
                email: email,
                erptkn: tkn
			}
		}).trigger('reloadGrid');
		
		$('#searchBtnCon').bind('click', searchBtnHlrCon);
		
	}else{
		$('#searchBtnCon').bind('click', searchBtnHlrCon);
	}
}
/*
function JSONToCSVConvertor(JSONData, ReportTitle, ShowLabel)
{
    //If JSONData is not an object then JSON.parse will parse the JSON string in an Object
    var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;
    var CSV = '';
    //This condition will generate the Label/Header
    if (ShowLabel) {
    	 
        var row = "";

        //This loop will extract the label from 1st index of on array
        for (var index in arrData.items[0]) {
            //Now convert each value to string and comma-seprated
            row += index + ',';
        }
        row = row.slice(0, -1);
        //append Label row with line break
        CSV += row + '\r\n';
    }

    //1st loop is to extract each row
    for (var i = 0; i < arrData.items.length; i++) {
    	
        var row = "";
        //2nd loop will extract each column and convert it in string comma-seprated
        for (var index in arrData.items[i]) {
           console.log('Console:'+quitar_tildes( arrData.items[i][index]) );
            row += '"' + quitar_tildes(arrData.items[i][index]).toString().replace(/(<([^>]+)>)/ig, '') + '",';
        }
        row.slice(0, row.length - 1);
        //add a line break after each row
        CSV += row + '\r\n';
    }

    if (CSV == '') {
        alert("Invalid data");
        return;
    }

    /*
     *
     * FORCE DOWNLOAD
     *
     */
    //this trick will generate a temp "a" tag
   /* var link = document.createElement("a");
    link.id="lnkDwnldLnk";

    //this part will append the anchor tag and remove it after automatic click
    document.body.appendChild(link);

    var csv = CSV;
    blob = new Blob([csv], { type: 'text/csv' });

    var myURL = window.URL || window.webkitURL;

    var csvUrl = myURL.createObjectURL(blob);
    var filename = 'clientes_'+ Date.now() +'.csv';
    jQuery("#lnkDwnldLnk")
        .attr({
            'download': filename,
            'href': csvUrl
        });

    jQuery('#lnkDwnldLnk')[0].click();
    document.body.removeChild(link);
}*/
/*
function quitar_tildes(str) {
    var from = "ÃƒÃ€Ã�Ã„Ã‚ÃˆÃ‰Ã‹ÃŠÃŒÃ�Ã�ÃŽÃ’Ã“Ã–Ã”Ã™ÃšÃœÃ›Ã£Ã Ã¡Ã¤Ã¢Ã¨Ã©Ã«ÃªÃ¬Ã­Ã¯Ã®Ã²Ã³Ã¶Ã´Ã¹ÃºÃ¼Ã»Ã‘Ã±Ã‡Ã§", 
      to   = "AAAAAEEEEIIIIOOOOUUUUaaaaaeeeeiiiioooouuuunncc",
      mapping = {};
 
    for(var i = 0, j = from.length; i < j; i++ )
        mapping[ from.charAt( i ) ] = to.charAt( i );
 
    
    var ret = [];
    for( var i = 0, j = str.length; i < j; i++ ) {
        var c = str.charAt( i );
        if( mapping.hasOwnProperty( str.charAt( i ) ) )
            ret.push( mapping[ c ] );
        else
            ret.push( c );
    }

    return ret.join( '' );
    
}*/

function preparar_titulo(str) {
    //quitamos los piso
    str = str.replace(/\_/g, ' ');
    
    //colocamos la primera letra en mayuscula
    str = str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
        return letter.toUpperCase();
    });
    
    return str;    
}


