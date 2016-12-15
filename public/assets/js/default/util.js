//------------------------
// Utility Functions
//------------------------
$.fn.center = function ()
{
    this.css("position","fixed");
    this.css("top", ($(window).height() / 2) - (this.outerHeight() / 2));
    this.css("left", ($(window).width() / 2) - (this.outerWidth() / 2));
    return this;
};

/*
 * $.serializeObject is a variant of existing $.serialize method which,
 * instead of encoding form elements to string, converts form elements
 * to a valid JSON object which can be used in your JavaScript application.
 *
 * Example: http://jsfiddle.net/sxGtM/3/
 */
$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

//Configuracion Default Toastr
toastr.options = {
  "closeButton": true,
  "debug": false,
  "progressBar": true,
  "preventDuplicates": true,
  "positionClass": "toast-top-right",
  "showDuration": "600",
  "hideDuration": "1500",
  "timeOut": "7000",
  "extendedTimeOut": "1000",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut"
};

function resizeJqGrid()
{
	$(".ui-jqgrid").each(function(){
		var w = parseInt( $(this).parent().width()) - 6;
		var tmpId = $(this).attr("id");
		var gId = tmpId.replace("gbox_","");
		$("#"+gId).setGridWidth(w);
	});

}

function overflowTablaDinamica()
{
    var formularios = "#crearOrdenesForm, #editarOrdenesForm, #crearPedidosForm, #editarPedidosForm, #roleForm";

    $(formularios).find(".table-responsive").each(function(){
        var div = $(this);
        var widthTableResponsiveDiv = parseInt(div.width());

        div.find(".tabla-dinamica").each(function(){
            var table = $(this);
            var tableBody = table.find("tbody");
            var widthTableResponsiveTableBody = parseInt(tableBody.width()) || 0;

            //console.log(widthTableResponsiveTableBody +">"+ widthTableResponsiveDiv);
            if(widthTableResponsiveTableBody > widthTableResponsiveDiv)
            {
                div.css("overflow-x", "auto");
                div.css("overflow-y", "hidden");
                div.css("padding-bottom", "180px");

                table.find(".chosen-results").each(function(){
                    var chosen = $(this);

                    chosen.addClass("table-responsive-chosen");
                });
            }
            else
            {
            	var ingreso_horas = window.location.pathname.match(/entrar-horas/g) ? true : false;

            	if(ingreso_horas == false)
            		div.css("overflow", "unset");

                if($(document).find("#crearOrdenesForm, #editarOrdenesForm").length < 1)
                {
                    div.css("padding-bottom", "0px");
                }

                table.find(".chosen-results").each(function(){
                    var chosen = $(this);

                    chosen.removeClass("table-responsive-chosen");
                });
            }
        });
    });
}

$(function() {

    overflowTablaDinamica();

    $(window).resizeEnd(function() {
        overflowTablaDinamica();
    });

    $(".table-responsive").on("change", "select", function(){
        overflowTablaDinamica();
    });

	   //Funcion para reajustar el ancho de la tabla en los tabs
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
		e.target // newly activated tab
		e.relatedTarget // previous active tab
		setTimeout(function(){
			resizeJqGrid();
		}, 300);
	});

 	 $('#sub-panel-grid-modulos').on("click","a",function(){
	    var id_tabs = $(this).attr("data-targe");

  	    if(id_tabs == '#tablaContactos'){
	    	 //Ocultar Formulario Editar Cliente
			$('.editarFormularioClientes, .editarFormularioClientesNaturales').addClass('hide');

			//Mostrar Formulario de Crear Contacto
			$('#sub-panel-grid-modulos').find('.panel-heading').find('.sub-panel-dropdown-contenido').find('a[href*="crearContactos"]').trigger('click');

			//Pasar focus al body
			$('body').click();
	    }
	    if(id_tabs == '#tablaOportunidades'){

			setTimeout(function(){
 				$('#formNuevaOportunidad').find('input[name="campo[fecha_cierre]"]').prop('value', moment().add(90, 'day').format("DD-MM-YYYY"));
 				//Desabilitar dropdown de cliente
 				$('#formNuevaOportunidad').find('select[name="campo[uuid_cliente]"]').prop("disabled", "disabled");

				//Actualizar campo
				$('#formNuevaOportunidad').find(".chosen-select").chosen({
				    width: '100%'
				}).trigger('chosen:updated');

			}, 300);


			//Ocultar Panel de Datos del Cliente
			$('.editarFormularioClientes, .editarFormularioClientesNaturales').addClass('hide');

			//mostrar formulario de editar oportunidad
			$('#sub-panel-grid-modulos').find('.panel-heading').find('.sub-panel-dropdown-contenido').find('a[href*="crearOportunidades"]').trigger('click');

			//Pasar focus al body
			$('body').click();
	    }
	    if(id_tabs == '#tablaActividades' ){


	    	 //Ocultar Formulario Editar Cliente
			$('.editarFormularioClientes, .editarFormularioClientesNaturales').addClass('hide');
			$('#crearActividad').find('#uuid_oportunidad').empty();
 			$('#crearActividad').find(".chosen-select").chosen({
	             width: '100%'
	        }).trigger('chosen:updated');

  			$('#crearActividad').find('#uuid_oportunidad').append($('#oportunidad_cliente').html());
   	         $('#crearActividad').find(".chosen-select").chosen({
	             width: '100%'
	        }).trigger('chosen:updated');
			//Mostrar Formulario de Crear Contacto
			$('#sub-panel-grid-modulos').find('.panel-heading').find('.sub-panel-dropdown-contenido').find('a[href*="crearActividades"]').trigger('click');

			//Pasar focus al body
			$('body').click();
	    }

	    if(id_tabs == '#tablaCasos'){
	    	setTimeout(function(){
 				//Desabilitar dropdown de cliente
				$('form#crearCaso').find('select[name="campo[uuid_cliente]"]').attr("disabled", "disabled").find('option[value="'+ id_cliente +'"]').prop("selected", "selected");
 				$(".chosen-select").chosen({
				    width: '100%'
				}).trigger('chosen:updated');
 			}, 300);

  			//Campos por defecto al crear el caso
			$('form#crearCaso').find('select[name*="campo[id_asignado]"] option[value="'+ uuid_usuario +'"]').prop('selected', 'selected');
  			$('form#crearCaso').find('button [id="uuid_clienteBtn"]').attr("disabled", "disabled");
 			$('form#crearCaso').find('#uuid_clienteBtn').addClass("disabled").attr("disabled", "disabled");

	    	 //Ocultar Formulario Editar Cliente
			$('.editarFormularioClientes, .editarFormularioClientesNaturales').addClass('hide');

			//Mostrar Formulario de Crear Contacto
			$('#sub-panel-grid-modulos').find('.panel-heading').find('.sub-panel-dropdown-contenido').find('a[href*="crearCasos"]').trigger('click');

			//Pasar focus al body
			$('body').click();
	    }

	});

	if(typeof $.validator !== 'undefined'){
		/* Validar formato de email que contenga dominio */
		$.validator.addMethod("checkdata", function(value, element) {
			//si se ha seleccionado un rol y no contiene usuarios
			return $(element).closest('tr').find('select[id*="id_rol"]').find('option:selected').val() != "" && $(element).is(':empty') == true ? false : true;
		}, "No existen usuarios con el rol seleccionado.");

    //metodo para componente vue: tabla-file.vue
    $.validator.addMethod("requiredvalidation", function(value, element) {
      var cargosfield = $('input#cargos_adicionales');
      if(typeof cargosfield.attr('id') != 'undefined') {
        if(cargosfield.is(':checked') == false && value == ""){
          return true;
        }else if(cargosfield.is(':checked') == true && value == ""){
          return false;
        }
        return true;
      }else{
        return value == "" ? false : true;
      }
		}, "Requerido.");

		$.validator.addClassRules({
			hasUsuarios: {
		        required: false,
		        checkdata: true
		    }
		});
	}

	//Mantener la seleccion de los tabs al refrescar la pagina
	/*$('.nav-tabs a').click(function (e) {
        if($(this).closest('ul').attr('id') != 'cuentas_tabs_tabla'){
        e.preventDefault();
        $(this).tab('show');
          window.location.hash = $(this).attr('href');
        }
    });*/

	//Si existe hash en url y existe algun tabs en el dom
	//seleccionar el tab.
	/*if(window.location.hash){
		if($('.nav-tabs').attr('class') != undefined){
			$('.nav-tabs').find('a[href="'+window.location.hash+'"]').tab('show');
		}
	}*/

});

if($('.nailthumb').attr('class') != undefined){
	$('.nailthumb').nailthumb();
}

//Calcula la edad en Clientes Naturales
function getAge(dateString) {
 	if ( !dateString ){
  		return '0';
 	}

    var today = new Date();
    var birthDate = new Date(dateString);
    var age = today.getFullYear() - birthDate.getFullYear();
    var m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}

/* USED TO CHECK IF VALUE IS NUMERIC OR NOT. */
function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

// Convert a date to timestamp
// URL: http://stackoverflow.com/questions/2407052/date-to-timestamp-in-javascript
function getTimestamp(str) {
  if(str != "" && str != undefined){
    var d = str.match(/\d+/g); // extract date parts
    return +new Date(d[0], d[1], d[2], d[3], d[4], d[5]); // build Date object
  }
}

/* Mostrar mensaje de Alerta */
function mensaje_alerta(mensaje, clase)
{
	if(mensaje == ''){
		console.log('function mensaje_alerta(), falta parametro clase');
		return false;
	}

	//Verificar si existe caja de mensajes
	if($('.message-box').attr('class') == undefined){
 		var message_box = ['<div class="alert '+ clase +' alert-dismissable message-box animated fadeInDown">',
		   '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>',
		   mensaje,
		   '</div>'
		].join('\n');

		$('.wrapper-content').prepend( message_box );

		$(".alert").fadeTo(2000, 500).slideUp(500, function(){
		    $(".alert").alert('close');
		});

	}else{
 		//Mostra mensaje
		$('.message-box').removeAttr('class').addClass('alert '+ clase +' alert-dismissable message-box animated fadeInDown');
		$('.message-box').empty().append('<button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button> '+ mensaje).removeClass('hide');
	}
}

/* ROUND A FLOATING VALUE, WITHOUT DECIMAL ERROR */
function roundNumber(number,decimals) {

    //URL: http://www.mediacollege.com/internet/javascript/number/round.html

  var newString;// The new rounded number
  decimals = Number(decimals);
  if (decimals < 1) {
    newString = (Math.round(number)).toString();
  } else {
    var numString = number.toString();
    if (numString.lastIndexOf(".") == -1) {// If there is no decimal point
      numString += ".";// give it one at the end
    }
    var cutoff = numString.lastIndexOf(".") + decimals;// The point at which to truncate the number
    var d1 = Number(numString.substring(cutoff,cutoff+1));// The value of the last decimal place that we'll end up with
    var d2 = Number(numString.substring(cutoff+1,cutoff+2));// The next decimal, after the last one we want
    if (d2 >= 5) {// Do we need to round up at all? If not, the string will just be truncated
      if (d1 == 9 && cutoff > 0) {// If the last digit is 9, find a new cutoff point
        while (cutoff > 0 && (d1 == 9 || isNaN(d1))) {
          if (d1 != ".") {
            cutoff -= 1;
            d1 = Number(numString.substring(cutoff,cutoff+1));
          } else {
            cutoff -= 1;
          }
        }
      }
      d1 += 1;
    }
    if (d1 == 10) {
      numString = numString.substring(0, numString.lastIndexOf("."));
      var roundedNum = Number(numString) + 1;
      newString = roundedNum.toString() + '.';
    } else {
      newString = numString.substring(0,cutoff) + d1.toString();
    }
  }
  if (newString.lastIndexOf(".") == -1) {// Do this again, to the new string
    newString += ".";
  }
  var decs = (newString.substring(newString.lastIndexOf(".")+1)).length;
  for(var i=0;i<decimals-decs;i++) newString += "0";
  return newString;
}

function msieversion() {
	var ua = window.navigator.userAgent;
	var msie = ua.indexOf("MSIE ");

	//If Internet Explorer, return true
	if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)){
		return true;
	}else{ // If another browser,
		return false;
	}
	return false;
}

// download file in JavaScript
// URL: http://pixelscommander.com/en/javascript/javascript-file-download-ignore-content-type/
function downloadURL(sUrl, fileName) {

	//Verificar si el navegador
	//es Internet Explorer
	if(msieversion()){

		csvUrl = new Blob([sUrl], { type: 'text/csv' });
		window.navigator.msSaveOrOpenBlob(csvUrl, fileName);

	}else{

		//Creating new link node.
	    var link = document.createElement('a');
	    link.href = sUrl;

	    if (link.download !== undefined){
	        //Set HTML5 download attribute. This will prevent file from opening if supported.
	        //var fileName = sUrl.substring(sUrl.lastIndexOf('/') + 1, sUrl.length);
	        link.download = fileName;
	    }

	    //Dispatching click event.
	    if (document.createEvent) {
	        var e = document.createEvent('MouseEvents');
	        e.initEvent('click' ,true ,true);
	        link.dispatchEvent(e);
	        return true;
	    }

	    // Force file download (whether supported by server).
	    var query = '?download';
	    window.open(sUrl + query);
	}
}

/**
 * Convierte json a formato de CSV
 *
 * @param JSONData
 * @returns
 */
function JSONToCSVConvertor(JSONData)
{
	//If JSONData is not an object then JSON.parse will parse the JSON string in an Object
	var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;
	var CSV = '';

	//Generate the Label/Header
	var row = "";

	//This loop will extract the label from 1st index of on array
	for (var index in arrData.items[0]) {
		//Now convert each value to string and comma-seprated
		row += index + ',';
	}
	row = row.slice(0, -1);
	//append Label row with line break
	CSV += row + '\r\n';


	//1st loop is to extract each row
	for (var i = 0; i < arrData.items.length; i++) {
		var row = "";
		//2nd loop will extract each column and convert it in string comma-seprated
		for (var index in arrData.items[i]) {
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
	 * DESCARGAR CSV
	 */
	var blob = new Blob([CSV], { type: 'text/csv' });
	var myURL = window.URL || window.webkitURL;
	var csvUrl = msieversion() == true ? blob : myURL.createObjectURL(blob);

	return csvUrl;
}
function quitar_tildes(str) {
    /*var from = "ÃƒÆ’Ã†â€™ÃƒÆ’Ã¢â€šÂ¬ÃƒÆ’Ã¯Â¿Â½ÃƒÆ’Ã¢â‚¬Å¾ÃƒÆ’Ã¢â‚¬Å¡ÃƒÆ’Ã‹â€ ÃƒÆ’Ã¢â‚¬Â°ÃƒÆ’Ã¢â‚¬Â¹ÃƒÆ’Ã…Â ÃƒÆ’Ã…â€™ÃƒÆ’Ã¯Â¿Â½ÃƒÆ’Ã¯Â¿Â½ÃƒÆ’Ã…Â½ÃƒÆ’Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å“ÃƒÆ’Ã¢â‚¬â€œÃƒÆ’Ã¢â‚¬ï¿½ÃƒÆ’Ã¢â€žÂ¢ÃƒÆ’Ã…Â¡ÃƒÆ’Ã…â€œÃƒÆ’Ã¢â‚¬ÂºÃƒÆ’Ã‚Â£ÃƒÆ’Ã‚Â ÃƒÆ’Ã‚Â¡ÃƒÆ’Ã‚Â¤ÃƒÆ’Ã‚Â¢ÃƒÆ’Ã‚Â¨ÃƒÆ’Ã‚Â©ÃƒÆ’Ã‚Â«ÃƒÆ’Ã‚ÂªÃƒÆ’Ã‚Â¬ÃƒÆ’Ã‚Â­ÃƒÆ’Ã‚Â¯ÃƒÆ’Ã‚Â®ÃƒÆ’Ã‚Â²ÃƒÆ’Ã‚Â³ÃƒÆ’Ã‚Â¶ÃƒÆ’Ã‚Â´ÃƒÆ’Ã‚Â¹ÃƒÆ’Ã‚ÂºÃƒÆ’Ã‚Â¼ÃƒÆ’Ã‚Â»ÃƒÆ’Ã¢â‚¬ËœÃƒÆ’Ã‚Â±ÃƒÆ’Ã¢â‚¬Â¡ÃƒÆ’Ã‚Â§",
      to   = "AAAAAEEEEIIIIOOOOUUUUaaaaaeeeeiiiioooouuuunncc",*/
	  var from = "ÃƒÆ’Ãƒâ‚¬Ãƒï¿½Ãƒâ€žÃƒâ€šÃƒË†Ãƒâ€°Ãƒâ€¹ÃƒÅ ÃƒÅ’Ãƒï¿½Ãƒï¿½ÃƒÅ½Ãƒâ€™Ãƒâ€œÃƒâ€“Ãƒâ€�Ãƒâ„¢ÃƒÅ¡ÃƒÅ“Ãƒâ€ºÃƒÂ£ÃƒÂ ÃƒÂ¡ÃƒÂ¤ÃƒÂ¢ÃƒÂ¨ÃƒÂ©ÃƒÂ«ÃƒÂªÃƒÂ¬ÃƒÂ­ÃƒÂ¯ÃƒÂ®ÃƒÂ²ÃƒÂ³ÃƒÂ¶ÃƒÂ´ÃƒÂ¹ÃƒÂºÃƒÂ¼ÃƒÂ»Ãƒâ€˜ÃƒÂ±Ãƒâ€¡ÃƒÂ§Ã‚Â´",
      to   = "AAAAAEEEEIIIIOOOOUUUUaaaaaeeeeiiiioooouuuuNncc'",
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
     return ret.join( '' ).trim();

}
// A JavaScript equivalent of PHPÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¾ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢s unserialize
// URL: http://phpjs.org
function unserialize(data) {
  //  discuss at: http://phpjs.org/functions/unserialize/
  var that = this,
    utf8Overhead = function(chr) {
      // http://phpjs.org/functions/unserialize:571#comment_95906
      var code = chr.charCodeAt(0);
      if (code < 0x0080) {
        return 0;
      }
      if (code < 0x0800) {
        return 1;
      }
      return 2;
    };
  error = function(type, msg, filename, line) {
    throw new that.window[type](msg, filename, line);
  };
  read_until = function(data, offset, stopchr) {
    var i = 2,
      buf = [],
      chr = data.slice(offset, offset + 1);

    while (chr != stopchr) {
      if ((i + offset) > data.length) {
        error('Error', 'Invalid');
      }
      buf.push(chr);
      chr = data.slice(offset + (i - 1), offset + i);
      i += 1;
    }
    return [buf.length, buf.join('')];
  };
  read_chrs = function(data, offset, length) {
    var i, chr, buf;

    buf = [];
    for (i = 0; i < length; i++) {
      chr = data.slice(offset + (i - 1), offset + i);
      buf.push(chr);
      length -= utf8Overhead(chr);
    }
    return [buf.length, buf.join('')];
  };
  _unserialize = function(data, offset) {
    var dtype, dataoffset, keyandchrs, keys, contig,
      length, array, readdata, readData, ccount,
      stringlength, i, key, kprops, kchrs, vprops,
      vchrs, value, chrs = 0,
      typeconvert = function(x) {
        return x;
      };

    if (!offset) {
      offset = 0;
    }
    dtype = (data.slice(offset, offset + 1))
      .toLowerCase();

    dataoffset = offset + 2;

    switch (dtype) {
      case 'i':
        typeconvert = function(x) {
          return parseInt(x, 10);
        };
        readData = read_until(data, dataoffset, ';');
        chrs = readData[0];
        readdata = readData[1];
        dataoffset += chrs + 1;
        break;
      case 'b':
        typeconvert = function(x) {
          return parseInt(x, 10) !== 0;
        };
        readData = read_until(data, dataoffset, ';');
        chrs = readData[0];
        readdata = readData[1];
        dataoffset += chrs + 1;
        break;
      case 'd':
        typeconvert = function(x) {
          return parseFloat(x);
        };
        readData = read_until(data, dataoffset, ';');
        chrs = readData[0];
        readdata = readData[1];
        dataoffset += chrs + 1;
        break;
      case 'n':
        readdata = null;
        break;
      case 's':
        ccount = read_until(data, dataoffset, ':');
        chrs = ccount[0];
        stringlength = ccount[1];
        dataoffset += chrs + 2;

        readData = read_chrs(data, dataoffset + 1, parseInt(stringlength, 10));
        chrs = readData[0];
        readdata = readData[1];
        dataoffset += chrs + 2;
        if (chrs != parseInt(stringlength, 10) && chrs != readdata.length) {
          error('SyntaxError', 'String length mismatch');
        }
        break;
      case 'a':
        readdata = {};

        keyandchrs = read_until(data, dataoffset, ':');
        chrs = keyandchrs[0];
        keys = keyandchrs[1];
        dataoffset += chrs + 2;

        length = parseInt(keys, 10);
        contig = true;

        for (i = 0; i < length; i++) {
          kprops = _unserialize(data, dataoffset);
          kchrs = kprops[1];
          key = kprops[2];
          dataoffset += kchrs;

          vprops = _unserialize(data, dataoffset);
          vchrs = vprops[1];
          value = vprops[2];
          dataoffset += vchrs;

          if (key !== i)
            contig = false;

          readdata[key] = value;
        }

        if (contig) {
          array = new Array(length);
          for (i = 0; i < length; i++)
            array[i] = readdata[i];
          readdata = array;
        }

        dataoffset += 1;
        break;
      default:
        error('SyntaxError', 'Unknown / Unhandled data type(s): ' + dtype);
        break;
    }
    return [dtype, dataoffset - offset, typeconvert(readdata)];
  };

  return _unserialize((data + ''), 0)[2];
}

function ucFirst(string) {
	return string.substring(0, 1).toUpperCase() + string.substring(1).toLowerCase();
};

function ucWords(str) {
    return str.replace(/\w\S*/g, function(txt) {
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
}

/**
 * Buscar un valor en un arreglo multidimensional
 * y retornar el key del valor encontrado.
 *
 * @param string $search_for
 * @param array $search_in
 * @return boolean
 */
function multiarray_buscar_valor(searchfor, field, array) {
   	//console.log(array);
	var found = null;
	$.each(array, function(index, element){
   		if(element[field] == searchfor){
   			found = index;
   			return false;
   		}
    });
	return found;
}
