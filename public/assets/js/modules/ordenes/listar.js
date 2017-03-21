// Definimos la variable tabs la cual contendrá todo nuestro modulo.
var listarOrdenes = (function(){
     // Objeto la cual establecemos valores que vamos a usar mas adelante en este ámbito.
    var st = {
        modulosOpciones: "#moduloOpciones ul",
        exportarBtn: "#exportarBtn",
        tabla: "#tabla",
        jqGrid: "#ordenesGrid",
        filename: "ordenes",
        segmento2: "ordenes",
        input: "input",
        chosens: "#estado, #centro,#creado_por, #categoria_id",
        //selects2: "#proveedor",
        fecha_desde: "#fecha_desde",
        fecha_hasta: "#fecha_hasta"
    };

    // Objeto vacío que guardará elementos que se manejan por HTML.
    var dom = {}

    // Función que llenará al objeto dom con los objetos HTML a través de jQuery ($).
    var catchDom = function(){
        dom.modulosOpciones = $(st.modulosOpciones);
        dom.exportarBtn = $(st.exportarBtn);
        dom.tabla = $(st.tabla);
        dom.jqGrid = $(st.jqGrid);
        dom.input = $(st.input);
        dom.chosens = $(st.chosens);
        //dom.selects2 = $(st.selects2);
        dom.fechas = $(st.fechas);
    };

    // Función donde establecemos los eventos que tendrán cada elemento.
    var suscribeEvents = function(){
        dom.modulosOpciones.on("click");

        dom.input.inputmask();

        dom.chosens.chosen({
            width: '100%',
            allow_single_deselect: true
        });

       // dom.selects2.select2({
        //    with:"100%"
        //}),

        dom.fechas.daterangepicker({
            showDropdowns: true,
            opens: "left",
            locale: {
                separator: ' hasta ',
                format: 'DD-MM-YYYY',
                applyLabel: 'Seleccionar',
                cancelLabel: 'Cancelar',
                fromLabel: 'Desde',
                toLabel: 'Hasta',
                customRangeLabel: 'Personalizar',
                daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                firstDay: 1
            },
            startDate: moment(),
            endDate: moment()
        }).val("");
    };


    var mostrar_mensaje = function(){
        //mensaje clase viene desde el controlador...
        if(mensaje_clase != 0)
        {
            if(mensaje_clase == "alert-success")
            {
                toastr.success("¡&Eacute;xito! Se ha guardado correctamente la << Orden/Ordenes de compra >>.");
            }
            else
            {
                toastr.error("¡Error! Su solicitud no fue procesada en la << Orden/Ordenes de compra >>.");
            }
        }
    }

    // Función que inicializará los funciones decritas anteriormente.
    var initialize = function(){
        catchDom();
        suscribeEvents();
        mostrar_mensaje();
    };

    /* Retorna un objeto literal con el método init haciendo referencia a la
       función initialize. */
    return{
        init:initialize,
        dom:dom
    }
})();

// Ejecutando el método "init" del módulo tabs.
listarOrdenes.init();

$(function(){
	//jQuery Daterange
	$("#fecha_desde").datepicker({
		//defaultDate: "+1w",
		dateFormat: 'dd-mm-yy',
		changeMonth: true,
		numberOfMonths: 1,
		onClose: function( selectedDate ) {
			$("#fecha_hasta").datepicker( "option", "minDate", selectedDate );
		}
	});
	$("#fecha_hasta").datepicker({
		//defaultDate: "+1w",
		dateFormat: 'dd-mm-yy',
		changeMonth: true,
		numberOfMonths: 1,
		onClose: function( selectedDate ) {
			$("#fecha_desde").datepicker( "option", "maxDate", selectedDate );
	    }
	});

  var gridObj = $("#ordenesGrid");

  var verificarConversion = function(){

    var ordenes = gridObj.jqGrid('getGridParam','selarrrow');

    if(ordenes.length<=0) {
      return false;
    }
    //Validar seleccion
    var valido = true;
    var check_centros = [];
    var check_bodegas = [];
    var check_proveedores = [];
    var i=0;

    for(id in ordenes) {
        var orden = gridObj.getRowData(ordenes[id]);
        var estado = normalize($(orden['Estado']).text().toLowerCase());
        var centro_id = orden['centro_id'];
        var bodega_id = orden['bodega_id'];
        var proveedor_id = orden['proveedor_id'];

        //si el objeto es vacio continuar la iteracion
        if($.isEmptyObject(orden)){
          continue;
        }

        //Verificar Estados
        if(!estado.match(/por facturar|facturada parcial/gi)){
          toastr.warning('Por favor! seleccione s&oacute;lo ordenes en estado <strong>Por facturar</strong> o <strong>Facturada parcial</strong>.');
          valido = false;
          break;
        }

        //Armar array de proveedores, centros y bodegas
        check_centros[i] = centro_id;
        check_bodegas[i] = bodega_id;
        check_proveedores[i] = proveedor_id;
        i++;
    }

    //Verificar si estado valido
    if(valido==false) {
      $('body').trigger('click');
      return false;
    }

    //Verificar si los pedidos son del mismo proveedor
    if(check_proveedores.allValuesSame()==false) {
      $('body').trigger('click');
      toastr.warning('Por favor! seleccione s&oacute;lo ordenes con el mismo proveedor.');
      return false;
    }

    //Verificar si los pedidos son del mismo Centro
    if(check_centros.allValuesSame()==false) {
      $('body').trigger('click');
      toastr.warning('Por favor! seleccione s&oacute;lo pedidos con el mismo centro contable.');
      return false;
    }

    //Verificar si los pedidos son de la misma bodega
    if(check_bodegas.allValuesSame()==false) {
      $('body').trigger('click');
      toastr.warning('Por favor! seleccione s&oacute;lo pedidos con la misma bodega.');
      return false;
    }

    //Convertir ordenes seleccionados
    //a factura de compras.
    convertirFacturaCompra();
  };

  var convertirFacturaCompra = function(){

    var ordenes = gridObj.jqGrid('getGridParam','selarrrow');
    if(ordenes.length<=0) {
      return false;
    }

    var url = phost() + "facturas_compras/crear/ordenes";
    var fields = "";
    $.each(ordenes, function(i, id){
        var orden = gridObj.getRowData(id);
        fields += '<input type="hidden" name="ordenes_id[]" value="'+ id +'">';
        fields += '<input type="hidden" name="centro_id" value="'+ orden['centro_id'] +'">';
        fields += '<input type="hidden" name="bodega_id" value="'+ orden['bodega_id'] +'">';
        fields += '<input type="hidden" name="bodega_nombre" value="'+ orden['bodega_nombre'].toString() +'">';
        fields += '<input type="hidden" name="proveedor_id" value="'+ orden['proveedor_id'] +'">';
        fields += '<input type="hidden" name="proveedor_nombre" value="'+ $(orden['Proveedor']).text() +'">';
    });

    var form = $(
        '<form action="' + url + '" method="POST" style="display:none;">' +
        fields +
        '<input type="hidden" name="erptkn" value="' + tkn + '">' +
        '<input type="submit">' +
        '</form>'
    );

    $('body').trigger('click').append(form);
    form.submit();
  };

  //Convertir a factura de compra
  $('#moduloOpciones ul').on("click", "#convertirAFacturaBtn", function(e){
    e.preventDefault();
    e.returnValue=false;
    e.stopPropagation();

    //Verificar si selecciono solo
    //pedidos en estado cotizacion.
    verificarConversion();
  });


  $("#proveedor3").select2({
    width:"100%",
    theme: "bootstrap",
    language: "es",
    maximumInputLength: 10,
    ajax: {
      url: phost() + 'proveedores/ajax_catalogo_proveedores',
      dataType: 'json',
      cache: true,
      delay: 250,
      data: function (params) {
          return {
              q: params.term, // search term
              erptkn: tkn
          };
      },
      processResults: function (data, params) {

         var resultados = data.map(function(resp){
             return [{'id': resp.id,'text': resp.nombre}];
         }).reduce(function(a,b){
             return a.concat(b);
         },[]);
           return {
                results:resultados
           };
      },
      escapeMarkup: function (markup) { return markup; },
    }
  });

});
