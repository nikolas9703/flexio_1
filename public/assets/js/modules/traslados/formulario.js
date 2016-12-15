// Definimos la variable tabs la cual contendrá todo nuestro modulo.
var formularioTraslado = (function(){
    // Objeto la cual establecemos valores que vamos a usar mas adelante en este ámbito.
    var st = {
        input: "input",
        form: "form",
        segmento2: "traslados",
        cChosen: ".chosen",
        cTableResponsive: ".table-responsive",
        cAgregarBtn: ".agregarBtn",
        cEliminarBtn: ".eliminarBtn",
        iFormularioCrear: "#crearTrasladosForm",
        iFormularioEditar: "#editarTrasladosForm",
        formsModulo: "#crearTrasladosForm, #editarTrasladosForm",
        cGuardar: ".guardarTraslado",
        
        //header
        iEmpezarTipo: "#empezar_tipo",
        iEmpezarUuid: "#empezar_uuid",
        
        //ELEMENTOS - FORMULARIO
        cNumero: ".numero",
        cFecha: ".fecha",
        cDeBodega: ".de_bodega",
        cEstado: ".estado",
        cFechaEntrega: ".fecha_entrega",
        cABodega: ".a_bodega",
        cBodegas: ".de_bodega, .a_bodega",
        
        //ELEMENTOS - TABLA DINAMICA
        iItemsTable: "#itemsTable",
        cItem: "select.item",
        cPrecioUnidad: "input.precio_unidad",
        cDescripcion: "input.descripcion",
        cDescuento: "input.descuento",
        cObservacion: "input.observacion",
        cCantidadEnviada: "input.cantidad_enviada",
        cUnidad: "select.unidad",
        cIdTrasladoItem: ".id_traslado_item",
        button: ".btn"
    };
    
    var config = {
        chosen: {
            width: '100%',
            allow_single_deselect: true
        },
        dateSimple:{
            locale:{
                format: 'DD-MM-YYYY'
            },
            showDropdowns: true,
            defaultDate: '',
            singleDatePicker: true
        },
        tablaDinamica: {
            afterAddRow: function(row){
                var self = $(row);
                
                self.find(st.input).inputmask();
                
                self.find(st.cChosen).chosen({
                    width: '100%',
                    allow_single_deselect: true
                });
                
                self.find(st.cItem).val("-1").trigger("chosen:updated");
                self.find(st.cUnidad).prop("disabled", true).trigger("chosen:updated");
                
                //reload validaciones extra
                self.find(st.cItem).rules( "add", {
                    required: true
                });
                self.find(st.cCantidadEnviada).rules( "add", {
                    validaExistencia: true
                });
                self.find(st.cUnidad).rules( "add", {
                    required: true
                });
                
                display2And4Columnas("none");
            },
            onDeleteRow: function(row){
                console.log("hello world"); //not work!
            }
        }
    };
   
    // Objeto vacío que guardará elementos que se manejan por HTML.
    var dom = {};

    // Función que llenará al objeto dom con los objetos HTML a través de jQuery ($).
    var catchDom = function(){
        dom.form = $(st.form);
        dom.iFormularioCrear = $(st.iFormularioCrear);
        dom.iFormularioEditar = $(st.iFormularioEditar);
        dom.form = $(st.form);
        dom.input = $(st.input);
        dom.cChosen = $(st.cChosen);
        dom.cTableResponsive = $(st.cTableResponsive);
        dom.cAgregarBtn = $(st.cAgregarBtn);
        dom.cEliminarBtn = $(st.cEliminarBtn);
        dom.formsModulo = $(st.formsModulo);
        dom.cGuardar = $(st.cGuardar);
        
        //header
        dom.iEmpezarTipo = $(st.iEmpezarTipo);
        dom.iEmpezarUuid = $(st.iEmpezarUuid);
        
        //ELEMENTOS - FORMULARIO
        dom.cNumero = $(st.cNumero);
        dom.cFecha = $(st.cFecha);
        dom.cDeBodega = $(st.cDeBodega);
        dom.cEstado = $(st.cEstado);
        dom.cFechaEntrega = $(st.cFechaEntrega);
        dom.cABodega = $(st.cABodega);
        dom.cBodegas = $(st.cBodegas);
        
        //ELEMENTOS - TABLA DINAMICA
        dom.iItemsTable = $(st.iItemsTable);
        dom.cItem = $(st.cItem);
        dom.cPrecioUnidad = $(st.cPrecioUnidad);
        dom.cDescripcion = $(st.cDescripcion);
        dom.cDescuento = $(st.cDescuento);
        dom.cObservacion = $(st.cObservacion);
        dom.cCantidadEnviada = $(st.cCantidadEnviada);
        dom.cUnidad = $(st.cUnidad);
        dom.cIdTrasladoItem = $(st.cIdTrasladoItem);
        dom.button = $(st.button);
    };

    // Función donde establecemos los eventos que tendrán cada elemento.
    var suscribeEvents = function(){
        
        dom.cChosen.chosen(config.chosen);
        
        //header
        $(".wrapper-content").on("change", st.iEmpezarTipo, events.eObtenerPedidos);
        $(".wrapper-content").on("change", st.iEmpezarUuid, events.eObtenerItems);
        
        if(dom.iFormularioCrear.length > 0)
        {
            dom.cFechaEntrega.daterangepicker(config.dateSimple).val("");
        }
        
        dom.formsModulo.on("change", st.cItem, events.eLlenarFila);
        dom.formsModulo.on("change", st.cUnidad, events.eCalcularPrecio);
        
        dom.cAgregarBtn.tablaDinamica(config.tablaDinamica);
    };

    /* Objeto que guarda métodos que se van a usar en cada evento definido 
      en la función suscribeEvents. */
    var events = {
         eObtenerPedidos: function(){
            var self = $(this);
            
            if(self.val().length)
            {
                dom.iEmpezarUuid.val("").prop("disabled", false);
            }
            else
            {
                dom.iEmpezarUuid.val("").prop("disabled", true).trigger("change");
            }
        },
        eObtenerItems: function(){
            var self = $(this);
            
            if(self.val().length)
            {
                obtenerTablaDinamica(self.val());
            }
            else
            {
                obtenerTablaDinamica("");
                dom.cABodega.val("").prop("disabled", false).trigger("chosen:updated");
            }
        },
        eCalcularPrecio: function(){
            var self = $(this);
            var fila = self.parent().parent();
            var factor_conversion = parseFloat(self.find(":selected").data("factor_conversion")) || 0;
            var precio_base = parseFloat(fila.find(st.cDescuento).val()) || 0;
            
            var precio_unidad = precio_base * factor_conversion;
            fila.find(st.cPrecioUnidad).val(precio_unidad);
        },
        eLlenarFila: function(e){
            var self = $(this);//item
            var uuid = self.val();
            var de_bodega = dom.cDeBodega.val();
            
            if(!de_bodega || de_bodega.length < 1)
            {
                toastr.error("¡Error! Por favor indique la bodega de donde provienen los items antes de seleccionar el elemento.");
                self.val("-1").trigger("chosen:updated");
                return;
            }
            else
            {
                //deshabilito este campo para evitar que el usuario
                //lo cambie cuando ya ha indicado un item...
                dom.cDeBodega.prop("disabled", true).trigger("chosen:updated");
            }
            
            $.ajax({
                url: phost() + st.segmento2 + "/ajax-get-item",
                type:"POST",
                data:{
                    erptkn:tkn,
                    uuid: uuid,
                    uuid_de_bodega: de_bodega
                },
                dataType:"json",
                success: function(data){
                    if(data.success === true)
                    {
                        var fila = self.parent().parent();
                        populateFila(fila, data.registro);
                    }
                }

            });
        },
        eEliminarFila: function(e){
            console.log("eEliminarFila");
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();

            var indice_fila = $(this).attr('data-index');
            var table = $(this).closest('table');
            var row = $(this).closest('tr');
            var id_registro=  $(this).attr("data-id");
            var agrupador_campos = $(row).attr("id").replace(/[0-9]/g, '');
            
            //SI ESTOY DESDE EL FORMULARIO DE CREACION NO MANDO EL IDENTIFICADOR
            if(formulario !== "#editarItemsForm")
            {
                id_registro = "0";
            }


            //Resaltar la fila que se esta
            //seleccionando para eliminar.
            $(row).addClass('highlight');


            var mensaje_confirmacion = '¿Esta seguro que desea eliminar este registro?';

            //Ventana de Confirmacion
            $('#optionsModal').find('.modal-title').empty().append('Confirme');
            $('#optionsModal').find('.modal-body').empty().append(mensaje_confirmacion);
            $('#optionsModal').find('.modal-footer')
                .empty()
                .append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
                .append(
                    $('<button class="btn btn-w-m btn-danger" type="button" />').append('Eliminar').click(function(e){
                        e.preventDefault();
                        e.returnValue=false;
                        e.stopPropagation();

                        var url = phost() +  'inventarios/ajax-delete-item-unidad';

                        $.ajax({
                            url: url,
                            data: {
                                id_registro: id_registro,
                                erptkn: tkn
                            },
                            type: "POST",
                            dataType: "json",
                            cache: false,
                        }).done(function(json) {

                            //Antes de remover la fila
                            //Primero verificar la cantidad
                            //de filas que quedan
                            var total_filas = $(table).find('tbody').find('tr').length;

                            console.log(total_filas);

                            if(total_filas > 1 && indice_fila == 0){
                                console.log("sd");
                                //si quedan varias filas
                                //remover la fila actual
                                $(row).remove();

                            }else{

                                //si es la unica fila y su indice
                                //es el undice (0) osea el primero
                                //solo limpiar los campos. 
                                if(indice_fila == 0){
                                    //Limpiar Campos
                                    setTimeout(function(){
                                        $(row).find('select option').removeAttr('selected');
                                        $(row).find('input').prop('value', '');
                                        $(row).find('input').removeAttr('data-id');
                                    }, 500);

                                }else{
                                    $(row).remove();
                                }
                            }

                            //
                            // Formatear indices de las filas de la Tabla
                            //
                            //Actualizar los incides
                            //Al eliminar una fila.
                            $.each( $(table).find('tbody').find('tr[id*="'+ agrupador_campos +'"]'), function(i, obj1){
                                var nindex = i;
                                //var cntx = i + 2;
                                $(this).prop("id", agrupador_campos + nindex);

                                //ESTE CODIGO ES SOLO PARA ITEMS
                                if($(this).find('.id_pedido_item').attr('name')){
                                    var name = $(this).find('.id_item_unidad').attr('name');
                                    name = name.replace(/([\d])/, nindex);
        //
                                    $(this).find('.id_item_unidad').attr("name", name);
                                    //
                                }
                                //TERMINA ACA

                                $.each( $(this).find('td'), function(j, obj2){

                                    if($(this).find('input').attr('name')){
                                        var name = $(this).find('input').attr('name');
                                        name = name.replace(/([\d])/, nindex);

                                        var id = $(this).find('input').attr('id');
                                        id = id.replace(/(\d)/, nindex);

                                        $(this).find('input').attr("name", name).attr("id", id);
                                    }
                                    if($(this).find('select').attr('name')){
                                        var name = $(this).find('select').attr('name');
                                        name = name.replace(/([\d])/, nindex);

                                        var id = $(this).find('select').attr('id');
                                        id = id.replace(/(\d)/, nindex);

                                        $(this).find('select').attr("name", name).attr("id", id);
                                    }
                                    if($(this).find('div[id*="_chosen"]')){
                                        if( $(this).find('div[id*="_chosen"]').attr('id') != undefined )
                                        {
                                            var id = $(this).find('div[id*="chosen"]').attr('id');
                                            id = id.replace(/(\d)/, nindex);

                                            $(this).find('div[id*="chosen"]').attr("id", id);
                                        }
                                    }
                                    if($(this).find('a')){
                                        $(this).find('a').attr("data-index", nindex);
                                    }
                                });
                            });


                            $('#optionsModal').modal('hide');
                        
                        });

                    })
                );
            $('#optionsModal').modal('show');

        }
    };
    
    var obtenerTablaDinamica = function (uuid_pedido)
    {
        dom.iItemsTable.load(phost() +'traslados/crear/'+ uuid_pedido +' '+st.iItemsTable, function(){
            
            //activo el chosen
            dom.iItemsTable.find(st.cChosen).chosen(config.chosen);
            
            //activo los botones + y - de la tabla dinamica y el campo de cantidad
            dom.form.find(st.cAgregarBtn).prop("disabled", false);
            dom.form.find(st.cEliminarBtn).prop("disabled", false);
            dom.form.find(st.cCantidad).prop("disabled", false);
            
            //activo el boton de la tabla dinamica
            dom.iItemsTable.find(st.cAgregarBtn).tablaDinamica(config.tablaDinamica);
            
            //marco los otros elementos del pedido que estan fuera de la tabla dinamica
            obtenerPedido(uuid_pedido);
            
            //Limpio el catalogo de unidades
            limpiarUnidades();
            
        });
    };
    
    var obtenerPedido = function(uuid_pedido){
        $.ajax({
            url: phost() + "ordenes/ajax-obtener-pedido",
            type:"POST",
            data:{
                erptkn:tkn,
                uuid_pedido:uuid_pedido
            },
            dataType:"json",
            success: function(data){
                if(data.success === true)
                {
                    dom.cABodega.val(data.registro.uuid_lugar).prop("disabled", true).trigger("chosen:updated");
                }
                else
                {
                    dom.cABodega.val("").prop("disabled", false).trigger("chosen:updated");
                }
                display2And4Columnas("none");
            }
        });
    };
    
    var limpiarUnidades = function()
    {
        console.log("limpiarUnidades");
        dom.form.find(st.cItem).each(function(){
            var item = $(this);
            var uuid = item.val();

            $.ajax({
                url: phost() + "ordenes/ajax-obtener-item",
                type:"POST",
                data:{
                    erptkn:tkn,
                    uuid: uuid,
                    uuid_bodega: dom.cABodega.val()
                },
                dataType:"json",
                success: function(data){
                    var fila = item.parent().parent();
                    var uuid_unidad = fila.find(st.cUnidad).val();
                    
                    //Auxilidar -> necesito marcar mi IMPUESTO de compra por defecto
                    fila.find(st.cImpuesto).val(data.registro.uuid_compra).trigger("chosen:updated");

                    //populate unidades
                    populateUnidades(fila, data.registro.unidades, uuid_unidad);
                    
                    //Activa funcion de calcular...
                    fila.find(st.cCantidad).trigger("change");
                }

            });
        });
    }
    
    var populateFila = function(fila, registro){
        fila.find(st.cPrecioUnidad).val(parseFloat(registro.precio_unidad) || 0);
        fila.find(st.cDescripcion).val(registro.descripcion);
        fila.find(st.cDescuento).val(registro.descuento);
        fila.find(st.cObservacion).val("");
        fila.find(st.cCantidadEnviada).val("");
        
        fila.find(st.cItem).find(":selected").data("cantidad_disponible", registro.cantidad_disponible);
        //popular unidades
        populateUnidades(fila, registro.unidades, '');
    };
    
    var populateUnidades = function(fila, unidades, uuid_unidad){
        var unidad_base = "-1";
        if(unidades.length > 0)
        {
            fila.find('.unidad').empty();
            
            $.each(unidades, function(i, result){
                fila.find(".unidad").append('<option data-base="'+ result.base +'" data-factor_conversion="'+ result.factor_conversion +'" value="'+ result.id +'">'+ result.nombre +'</option>');
                
                if(result.base == "1")
                {
                    unidad_base = result.unidad_id;
                }
                
                if(uuid_unidad.length > 0 && result.uuid_unidad == uuid_unidad)
                {
                    unidad_base = result.id;
                }
            });
            
            

            fila.find(".unidad").val(unidad_base).prop("disabled", false).trigger("chosen:updated");
        }
        else
        {
            fila.find('.unidad').empty().append('<option value="">Seleccione</option>').prop("disabled", true).trigger("chosen:updated");
        }
    };
    
    var display2And4Columnas = function(display){
        dom.iItemsTable.find("tr").each(function(){
            var self = $(this);
            var i = 0;
            
            i=0;
            self.find("th").each(function(){
                var self = $(this);
                if(i == 1 || i == 3)
                {
                    self.css("display", display);
                }
                i += 1;
            });
            
            i=0;
            self.find("td").each(function(){
                var self = $(this);
                if(i == 1 || i == 3)
                {
                    self.css("display", display);
                }
                i += 1;
            });
        });
    };
    
    var bodegaRepopulateAndSet = function(self, uuid){
        self.empty();
        $.ajax({
            url: phost() + "bodegas/ajax-listar",
            type:"POST",
            data:{
                erptkn:tkn,
                rows:'10000',
                pase:'1',
                sidx:'codigo',
                sord:'asc'
            },
            dataType:"json",
            success: function(data){
                $.each(data.rows, function(i,e){
                    var paddingLeft = 0;
                    
                    if(e.cell.parent != 'NULL')
                    {
                        self.find("option").each(function(){
                            var option = $(this);
                            if(option.data("id") == e.cell.parent)
                            {
                                paddingLeft = parseInt(option.data("padding_left")) + 30;
                            }
                        });
                    }
                    
                    var disabled = (!e.cell.isLeaf) ? 'disabled=""':'';
                    var color = (!e.cell.isLeaf) ? 'color:#676A80;':'';
                    self.append('<option value="'+ e.cell.uuid +'" '+ disabled +' style="padding-left:'+ paddingLeft +'px;'+ color +'" data-id="'+ e.cell.id +'" data-padding_left="'+ paddingLeft +'">'+ e.cell.Nombre +'</option>');
                });
                
                self.val(uuid);
            }
        });
    };
    
    var bodegasJerarquicas = function(){
        dom.cBodegas.each(function(){
            var self = $(this);
            var uuid = self.val();
            
            bodegaRepopulateAndSet(self,uuid);
        });
    };
    
    var mejorasVisuales = function(){
        
        //Transformo el select de bodegas en jerarquico
        bodegasJerarquicas();
        
        dom.cTableResponsive.append('<span class="tabla_dinamica_error"></span>');
        
        display2And4Columnas("none");
        dom.formsModulo.find(st.cUnidad).prop("disabled", true).trigger("chosen:updated");
        
        var estado = dom.form.find(st.cEstado);
        if(dom.iFormularioEditar.length > 0)
        {
            //DESHABILITO TODOS LOS CAMPOS
            dom.iEmpezarTipo.prop("disabled", true);
            dom.iEmpezarUuid.prop("disabled", true);
            dom.form.find(st.input).prop("disabled", true);
            dom.form.find(st.cChosen).prop("disabled", true).trigger("chosen:updated");
            dom.form.find("button").prop("disabled", true);
            dom.form.find("textarea").prop("disabled", true);
            
            //SI EL ESTADO ES DISTINTO A --RECIBIDO--
            if(estado.val() != "3")
            {
                estado.find("option[value='3']").remove();//Recibido es por sistema...
                estado.prop("disabled", false).trigger("chosen:updated");
                dom.form.find(st.cGuardar).prop("disabled", false);
            }
        }
        else
        {
            //Por enviar
            estado.prop("disabled", true).val("1").trigger("chosen:updated");
        }
    };
    
    var validaciones = function(){
        
        //SETEO DE VALORES Y COMPORTAMIENTOS POR DEFECTO
        $.validator.setDefaults({
            errorPlacement: function(error, element){
                if($(element).hasClass("item") == true || $(element).hasClass("precio_unidad") == true || $(element).hasClass("descripcion") == true || $(element).hasClass("descuento") == true || $(element).hasClass("observacion") == true || $(element).hasClass("cantidad_enviada") == true || $(element).hasClass("unidad") == true)
                {
                    dom.iItemsTable.parent().find(".tabla_dinamica_error").empty().append('<label class="error pull-left">Debe llenar todos los campos marcados con * y comprueba la disponibilidad en bodega</label>');

                } else {

                    if($(element).hasClass("chosen") == true)
                    {
                        element.parent().append(error);
                    }
                    else
                    {
                        $(element).after(error);
                    }
                }
            }
        });
        
        //Creo mi metodo para validar la existencia
        $.validator.addMethod('validaExistencia', function (value, element, param) {
            var hayExistencia = false;
            var self = $(element);
            var fila = self.parent().parent();
            var factor_conversion = parseFloat(fila.find(st.cUnidad).find(":selected").data("factor_conversion")) || 1;
            var cantidad_disponible = parseFloat(fila.find(st.cItem).find(":selected").data("cantidad_disponible")) || 0;
            
            if((cantidad_disponible >= (value * factor_conversion)) || dom.iEmpezarUuid.length > 0)
            {
                hayExistencia = true;
            }
            
            return hayExistencia;
        }, 'No hay disponibilidad de la cantidad indicada.');

        dom.form.validate({
            focusInvalid: true,
            ignore: '',
            wrapper: '',
            submitHandler: function(form) {

                //Habilitar campos ocultos
                $('input, select:hidden, textarea, select').removeAttr('disabled');
                
                //Anade el input de pedido al formulario
                if(dom.iEmpezarTipo.val().length < 1)
                {
                    dom.iEmpezarUuid.val("");
                }
                
                $('<input>').attr({
                    type: 'hidden',
                    id: 'pedido',
                    name: 'campo[pedido]',
                    value: dom.iEmpezarUuid.val()
                }).appendTo('form');

                //Enviar el formulario
                form.submit();
            }
        });
        
        dom.form.find(st.cItem).rules( "add", {
            required: true
        });
        dom.form.find(st.cCantidadEnviada).rules( "add", {
            validaExistencia: true
        });
        dom.form.find(st.cUnidad).rules( "add", {
            required: true
        });
    };
    
    // Función que inicializará los funciones decritas anteriormente.
    var initialize = function(){
        catchDom();
        suscribeEvents();
        mejorasVisuales();
        validaciones();
    };

    /* Retorna un objeto literal con el método init haciendo referencia a la 
       función initialize. */
    return{
        init:initialize
    };
})();

var formulario = "";
var decimales = 2;
    
formularioTraslado.init();