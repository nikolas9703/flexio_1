// Definimos la variable tabs la cual contendrá todo nuestro modulo.
var formularioConsumos = (function(){
    // Objeto la cual establecemos valores que vamos a usar mas adelante en este ámbito.
    var st = {
        //Generales
        input: "input",
        form: "form",
        segmento2: "consumos",
        segmento3: "inventarios",
        cChosen: ".chosen",
        iFormularioCrear: "#crearConsumoForm",
        iFormularioEditar: "#editarConsumoForm",
        formsModulo: "#crearConsumoForm, #editarConsumoForm",
        cGuardar: ".guardarConsumo",//Boton para enviar el formulario
        cTableResponsive: ".table-responsive",
        button: ".btn",

        //Generales tabla dinamica
        cAgregarBtn: ".agregarBtn",
        cEliminarBtn: ".eliminarBtn",

        //ELEMENTOS - FORMULARIO
        cFecha: ".fecha",
        cCentroContable: ".centro_contable",
        cBodegaSalida: ".bodega_salida",
        cEstado: ".estado",
        cNumero: ".numero",
        cColaborador: ".colaborador",
        cComentarios: ".comentarios",

        //ELEMENTOS - TABLA DINAMICA
        iItemsTable: "#itemsTable",
        cCategoria:"select.categoria",
        cItem: "select.item",
        cDescripcion: "input.descripcion",
        cObservacion: "input.observacion",
        cCuentaGasto: "select.cuenta_gasto",
        cCantidadEnviada: "input.cantidad_enviada",
        cUnidad: "select.unidad",
        cIdConsumoItem: "input.id_consumo_item",
    };

    var config = {
        chosen: {
            width: '100%',
            //width: '125px',
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
                
                $(row).find('#categoria0').closest('td').remove();

                self.find(st.cItem).val("-1").trigger("chosen:updated");
                self.find(st.cDescripcion).val("");
                self.find(st.cObservacion).prop("disabled", true).val("");
                self.find(st.cCuentaGasto).prop("disabled", true).val("").trigger("chosen:updated");
                self.find(st.cCantidadEnviada).prop("disabled", true).val("");

                //popular unidades
                populateUnidades(self, {});

                //reload validaciones extra
                self.find(st.cCategoria).rules( "add", {
                    required: true
                });
                self.find(st.cItem).rules( "add", {
                    required: true
                });
                self.find(st.cCantidadEnviada).rules( "add", {
                    validaExistencia: true
                });
                self.find(st.cCuentaGasto).rules( "add", {
                    required: true
                });
                self.find(st.cCantidadEnviada).rules( "add", {
                    required: true
                });
                self.find(st.cUnidad).rules( "add", {
                    required: true
                });


            },
            onDeleteRow: function(row){
                console.log("hello world"); //not work!

                events.eEliminarFila(row);
            }
        }
    };

    // Objeto vacío que guardará elementos que se manejan por HTML.
    var dom = {};
 
    // Función que llenará al objeto dom con los objetos HTML a través de jQuery ($).
    var catchDom = function(){
        //Generales
        dom.input = $(st.input);
        dom.form = $(st.form);
        dom.segmento2 = $(st.segmento2);
        dom.cChosen = $(st.cChosen);
        dom.iFormularioCrear = $(st.iFormularioCrear);
        dom.iFormularioEditar = $(st.iFormularioEditar);
        dom.formsModulo = $(st.formsModulo);
        dom.cGuardar = $(st.cGuardar);
        dom.cTableResponsive = $(st.cTableResponsive);
        dom.button = $(st.button);

        //Generales tabla dinamica
        dom.cAgregarBtn = $(st.cAgregarBtn);
        dom.cEliminarBtn = $(st.cEliminarBtn);

        //ELEMENTOS - FORMULARIO
        dom.cFecha = $(st.cFecha);
        dom.cCentroContable = $(st.cCentroContable);
        dom.cBodegaSalida = $(st.cBodegaSalida);
        dom.cEstado = $(st.cEstado);
        dom.cNumero = $(st.cNumero);
        dom.cColaborador = $(st.cColaborador);
        dom.cComentarios = $(st.cComentarios);

        //ELEMENTOS - TABLA DINAMICA
        dom.iItemsTable = $(st.iItemsTable);
        dom.cCategoria = $(st.cCategoria);
        dom.cItem = $(st.cItem);
        dom.cDescripcion = $(st.cDescripcion);
        dom.cObservacion = $(st.cObservacion);
        dom.cCuentaGasto = $(st.cCuentaGasto);
        dom.cCantidadEnviada = $(st.cCantidadEnviada);
        dom.cUnidad = $(st.cUnidad);
        dom.cIdConsumoItem = $(st.cIdConsumoItem);
    };

    // Función donde establecemos los eventos que tendrán cada elemento.
    var suscribeEvents = function(){

        dom.cChosen.chosen(config.chosen);

        dom.formsModulo.on("change", st.cBodegaSalida, events.setCantidadesDisponibles);

        dom.formsModulo.on("change", st.cCategoria, events.eLlenarCatalogoItems);
        dom.formsModulo.on("change", st.cItem, events.eLlenarFila);
        dom.formsModulo.on("change", st.cUnidad, events.eCalcularPrecio);

        dom.cAgregarBtn.tablaDinamica(config.tablaDinamica);
    };

    /* Objeto que guarda métodos que se van a usar en cada evento definido
      en la función suscribeEvents. */
    var events = {
        eLlenarCatalogoItems: function()
        {
            var self = $(this);
            var categoria_id = self.val();

            $.ajax({
                url: phost() + st.segmento3 + "/ajax-get-items",
                type:"POST",
                data:{
                    erptkn:tkn,
                    categoria_id: categoria_id
                },
                dataType:"json",
                success: function(data){
                    if(data.success === true)
                    {
                        var fila = self.parent().parent();
                        populateItems(fila, data.registros);

                        var registro = {
                            descripcion: "",
                            uuid_gasto: "",
                            enInventario:{
                              cantidadDisponibleBase:0
                            },
                            unidades:{}
                        };
                        populateFila(fila, registro);
                    }
                }

            });
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
            var id = self.val();

            $.ajax({
                url: phost() + st.segmento2 + "/ajax-get-item",
                type:"POST",
                data:{
                    erptkn:tkn,
                    id: id,
                    uuid_bodega: dom.cBodegaSalida.val()
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
        setCantidadesDisponibles: function(){
            dom.form.find(st.cItem).find(":selected").each(function(){
                setCantidadDisponible(this);
            });

            dom.form.find(st.cUnidad).find(":selected").each(function(){
                setFactorConversion(this);
            });
        },
        eEliminarFila: function(aux){
            var self = $(aux);
            var indice_fila = self.find(st.cEliminarBtn).attr('data-index');
            var table = self.closest('table');
            var row = self.closest('tr');
            var id_registro =  self.find(st.cEliminarBtn).attr("data-id");
            var agrupador_campos = $(row).attr("id").replace(/[0-9]/g, '');

            //SI ESTOY DESDE EL FORMULARIO DE CREACION NO MANDO EL IDENTIFICADOR
            if(dom.iFormularioCrear.length > 0)
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

                        var url = phost() +  st.segmento2 + '/ajax-delete-item';

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
                                if($(this).find(st.cIdConsumoItem).attr('name')){
                                    var name = $(this).find(st.cIdConsumoItem).attr('name');
                                    name = name.replace(/([\d])/, nindex);
        //
                                    $(this).find(st.cIdConsumoItem).attr("name", name);
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

    var populateFila = function(fila, registro){
        var disabled = true;

        if(registro.unidades.length > 0)
        {
            disabled = false;
        }

        fila.find(st.cDescripcion).val(registro.descripcion);
        fila.find(st.cObservacion).prop("disabled", disabled).val("");
        fila.find(st.cCuentaGasto).prop("disabled", disabled).val(registro.uuid_gasto).trigger("chosen:updated");
        fila.find(st.cCantidadEnviada).prop("disabled", disabled).val("");

        fila.find(st.cItem).find(":selected").data("cantidad_disponible", registro.enInventario.cantidadDisponibleBase);
        //popular unidades
        populateUnidades(fila, registro.unidades);
    };

    var populateUnidades = function(fila, unidades){
        var unidad_base = 0;

        if(unidades.length > 0)
        {
            fila.find(st.cUnidad).empty();

            $.each(unidades, function(i, result){
                fila.find(st.cUnidad).append('<option value="'+ result.id +'" data-factor_conversion="'+ result.factor_conversion +'">'+ result.nombre +'</option>');

                if(result.base == "1")
                {
                    unidad_base = result.id;
                }
            });

            fila.find(st.cUnidad).val(unidad_base).prop("disabled", false).trigger("chosen:updated");
        }
        else
        {
            fila.find(st.cUnidad).empty().append('<option value="">Seleccione</option>').prop("disabled", true).trigger("chosen:updated");
        }
    }

    var populateItems = function(fila, registros){

        if(registros.length > 0)
        {
            fila.find(st.cItem).empty();

            $.each(registros, function(i, result){
                fila.find(st.cItem).append('<option value="'+ result.id +'">'+ result.codigo +' '+ result.nombre +'</option>');
            });

            fila.find(st.cItem).val("-1").prop("disabled", false).trigger("chosen:updated");
        }
        else
        {
            fila.find(st.cItem).empty().append('<option value="">Seleccione</option>').prop("disabled", true).trigger("chosen:updated");
        }
    }


    var mejorasVisuales = function(){

        dom.cTableResponsive.append('<span class="tabla_dinamica_error"></span>');


        dom.formsModulo.find(st.cUnidad).prop("disabled", true).trigger("chosen:updated");

        var estado = dom.form.find(st.cEstado);
        if(dom.iFormularioEditar.length > 0)
        {
            //HABILITO CAMPOS QUE ESTAN DESHABILITADOS POR DEFECTO
            dom.form.find(st.cUnidad).prop("disabled", false).trigger("chosen:updated");

            //SI EL ESTADO ES DISTINTO A --POR APROBAR--
            if(estado.val() != "1")
            {
                //DESHABILITO TODOS LOS CAMPOS
                dom.form.find(st.input).prop("disabled", true);
                dom.form.find(st.cChosen).prop("disabled", true).trigger("chosen:updated");
                dom.form.find("button").prop("disabled", true);
            }
            else
            {
                //coloco la cantidad disponible a cada uno de los items seleccionados
                dom.form.find(st.cItem).find(":selected").each(function(){
                    setCantidadDisponible(this);
                });
                dom.form.find(st.cUnidad).find(":selected").each(function(){
                    setFactorConversion(this);
                });
            }
        }
        else
        {
            //Por aprobar
            estado.prop("disabled", true).val("1").trigger("chosen:updated");

            dom.iFormularioCrear.find(st.cItem).prop("disabled", true).trigger("chosen:updated");
            dom.iFormularioCrear.find(st.cObservacion).prop("disabled", true);
            dom.iFormularioCrear.find(st.cCuentaGasto).prop("disabled", true).trigger("chosen:updated");
            dom.iFormularioCrear.find(st.cCantidadEnviada).prop("disabled", true);
        }
    };

    var setCantidadDisponible = function(element){
        var self = $(element);
        var select = self.parent();

        $.ajax({
            url: phost() + st.segmento2 + "/ajax-get-item",
            type:"POST",
            data:{
                erptkn:tkn,
                id: select.val(),
                uuid_bodega: dom.cBodegaSalida.val()
            },
            dataType:"json",
            success: function(data){
                if(data.success === true)
                {
                    self.data("cantidad_disponible", parseFloat(data.registro.enInventario.cantidadDisponibleBase) || 0);
                }
            }

        });
    };

    var setFactorConversion = function(element){
        var self = $(element);
        var select = self.parent();
        var fila = self.parent().parent().parent();

        $.ajax({
            url: phost() + st.segmento2 + "/ajax-get-unidad",
            type:"POST",
            data:{
                erptkn:tkn,
                uuid_unidad: select.val(),
                uuid_item: fila.find(st.cItem).val()
            },
            dataType:"json",
            success: function(data){
                if(data.success === true)
                {
                    self.data("factor_conversion", parseFloat(data.registro.factor_conversion) || 0);
                }
            }

        });
    };

    var validaciones = function(){

        //SETEO DE VALORES Y COMPORTAMIENTOS POR DEFECTO
        $.validator.setDefaults({
            errorPlacement: function(error, element){
                if($(element).hasClass("categoria") == true || $(element).hasClass("item") == true || $(element).hasClass("descripcion") == true || $(element).hasClass("cuenta_gasto") == true || $(element).hasClass("cantidad_enviada") == true || $(element).hasClass("unidad") == true)
                {
                    dom.iItemsTable.parent().find(".tabla_dinamica_error").empty().append('<label class="error pull-left">Debe llenar todos los campos marcados con * y comprueba la disponibilidad en bodega.</label>');

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

            console.log(self.prop("id"));
            if(cantidad_disponible >= (value * factor_conversion))
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
                $('input, select:hidden, textarea').removeAttr('disabled');

                //Enviar el formulario
                form.submit();
            }
        });

        dom.form.find(st.cCategoria).rules( "add", {
            required: true
        });
        dom.form.find(st.cItem).rules( "add", {
            required: true
        });
        dom.form.find(st.cCantidadEnviada).each(function(){
            var self = $(this);
            self.rules( "add", {
                validaExistencia: true
            });
        });
        dom.form.find(st.cCuentaGasto).rules( "add", {
            required: true
        });
        dom.form.find(st.cCantidadEnviada).rules( "add", {
            required: true
        });
        dom.form.find(st.cUnidad).rules( "add", {
            required: true
        });
        
        //importante, evita problema de validacion en formualrio de creacion
        if(dom.form.prop("id") == "crearConsumoForm")
        {
            $("td.hide").remove();
        }
    };

    // Función que inicializará los funciones decritas anteriormente.
    var initialize = function(){
        catchDom();
        suscribeEvents();
        mejorasVisuales();
        validaciones();
    };
    
    //inicia area de negocio 
setTimeout(function(){
 var centro_contable = $('#centro_contable').val();
 
 $('#areaNegocio').empty();
 
 $.ajax({
            url: phost() + "consumos/ajax-lista-departamentos-asociado-centros",
            type:"POST",
            data:{
                erptkn:tkn,
                uuid_centro: centro_contable              
            },
            dataType:"json",
            success: function(data){
                
            $.each(data['result'], function(i, result){
                                
				$('#areaNegocio').append('<option value="'+ result['id'] +'">'+ result['nombre'] +'</option>');
			});  
            
            $('#areaNegocio').trigger("chosen:updated");
                
            }

        });

}, 800);
    /* Retorna un objeto literal con el método init haciendo referencia a la
       función initialize. */
    return{
        init:initialize
    };
})();

$('#centro_contable').on('change', function() {
 
 var centro_contable = $('#centro_contable').val();
 
 $('#areaNegocio').empty();
 $('#areaNegocio').append('<option value="">Seleccione</option>');
 
 $.ajax({
            url: phost() + "consumos/ajax-lista-departamentos-asociado-centros",
            type:"POST",
            data:{
                erptkn:tkn,
                uuid_centro: centro_contable              
            },
            dataType:"json",
            success: function(data){
             $.each(data['result'], function(i, result){
				$('#areaNegocio').append('<option value="'+ result['id'] +'">'+ result['nombre'] +'</option>');
			});     
                
            $('#areaNegocio').trigger("chosen:updated");
            }

        });
 
 
});

var decimales = 2;

formularioConsumos.init();
