// Definimos la variable tabs la cual contendrá todo nuestro modulo.
var formularioInteresesAsegurados = (function(){
    // Objeto la cual establecemos valores que vamos a usar mas adelante en este ámbito.
    var st = {
        input: "input",
        form: "form",
        segmento2: "intereses-asegurados",
        cChosen: ".chosen",
        cTableResponsive: ".table-responsive",
        cAgregarBtn: ".agregarBtn",
        cEliminarBtn: ".eliminarBtn",
        iFormularioCrear: "#crearInteresAseguradoForm",
        iFormularioEditar: "#editarInteresAseguradoForm",
        cBtnGuardar: ".btnGuardar",

        //ELEMENTOS - FORMULARIO
        cBodega: ".bodega",
        cTotalGeneral: ".total_general",
        cTipoAjuste: ".tipo_ajuste",
        cEstado: ".estado",

        //ELEMENTOS - TABLA DINAMICA
        documentosTable: "#documentosTable",
        cDocumento: ".documento_file",
        cCantidad: ".cantidad",
        cPrecioUnitario: ".precio_unitario",
        cTotal: ".total",
        cIdBeneficiarioDocumento: ".id_ajuste_item",
        cIdBeneficiarioDocumento: ".id_beneficiario_documento",
        button: ".btn"
    };

    var config = {
        chosen: {
            width: '100%',
            allow_single_deselect: true
        },
        tablaDinamica: {
            afterAddRow: function(row){
                var self = $(row);

                self.find(st.input).inputmask();

                self.find(st.cChosen).chosen({
                    width: '100%',
                    allow_single_deselect: true
                });

                //reload validaciones extra
                self.find(st.cDocumento).rules( "add", {
                    required: true
                });

                self.find(st.cCantidad).rules( "add", {
                    required: true,
                    menorQueDisponible: true
                });

                dom.cTipoAjuste.trigger("change");
            },
            onDeleteRow: function(row){
            }
        },
        ckeditor: {
            toolbar: [
                { name: 'basicstyles', items : [ 'Bold','Italic' ] },
                { name: 'paragraph', items : [ 'NumberedList','BulletedList' ] }
            ],
            uiColor : '#F5F5F5'
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
        dom.cBtnGuardar = $(st.cBtnGuardar);
        //ELEMENTOS - FORMULARIO
        dom.cTotalGeneral = $(st.cTotalGeneral);
        dom.cTipoAjuste = $(st.cTipoAjuste);
        dom.cBodega = $(st.cBodega);
        dom.cEstado = $(st.cEstado);

        //ELEMENTOS - TABLA DINAMICA
        dom.documentosTable = $(st.documentosTable);
        dom.cDocumento = $(st.cDocumento);
        dom.cCantidadDisponible = $(st.cCantidadDisponible);
        dom.cCantidad = $(st.cCantidad);
        dom.cPrecioUnitario = $(st.cPrecioUnitario);
        dom.cTotal = $(st.cTotal);
        dom.cIdBeneficiarioDocumento = $(st.cIdBeneficiarioDocumento);
        dom.button = $(st.button);
    };

    // Función donde establecemos los eventos que tendrán cada elemento.
    var suscribeEvents = function(){

        //dom.iUnidadesTable.on('click', st.cEliminarBtn, events.eEliminarFila);

        dom.cChosen.chosen(config.chosen);

        dom.form.on("change", st.cTipoAjuste, events.eCambiarTablaDinamica);
        dom.form.on("change", st.cDocumento, events.eLlenarFila);
        dom.form.on("change", st.cCantidad, events.eCalcular);

        //dom.cAgregarBtn.tablaDinamica(config.tablaDinamica);

        //Deshabilitado el 11-01-2016
        //CKEDITOR.replace("campo[comentarios]", config.ckeditor);
    };

    /* Objeto que guarda métodos que se van a usar en cada evento definido 
     en la función suscribeEvents. */
    var events = {
        eCalcular: function(e){
            var sumaTotal = 0;
            dom.documentosTable.find("tr").each(function(){
                var self = $(this);

                if(self.find(st.cCantidad).length > 0)
                {
                    var cantidad = self.find(st.cCantidad).val() || 0;
                    var precio_unitario = self.find(st.cPrecioUnitario).val() || 0;
                    var total_fila = parseFloat(cantidad) * parseFloat(precio_unitario);

                    self.find(st.cTotal).val(total_fila.toFixed(2));
                    sumaTotal += parseFloat(total_fila);
                }
            });
            dom.form.find(st.cTotalGeneral).val(sumaTotal.toFixed(2));
        },
        eLlenarFila: function(e){
            var self = $(this);//item
            var uuid = self.val();
            var bodega = dom.cBodega.val();

            if(bodega.length < 1)
            {
                toastr.error("¡Error! Por favor indique la bodega antes de seleccionar el item.");
                self.val("-1").trigger("chosen:updated");
                return;
            }
            else
            {
                //deshabilito este campo para evitar que el usuario
                //lo cambie cuando ya ha indicado un item...
                dom.cBodega.prop("disabled", true).trigger("chosen:updated");
            }

            $.ajax({
                url: phost() + st.segmento2 + "/ajax-get-item",
                type:"POST",
                data:{
                    erptkn:tkn,
                    uuid: uuid,
                    uuid_bodega: bodega
                },
                dataType:"json",
                success: function(data){
                    if(data.success === true)
                    {
                        var fila = self.parent().parent();
                        populateFila(fila, data.registro);
                        events.eCalcular();
                    }
                }

            });
        },
        eCambiarTablaDinamica: function(e){
            var self = $(this);
            var ajuste = self.val();
            //ajuste negativo = 1
            //ajuste positivo = 2

            if(ajuste == 1)
            {
                displaySegundaColumna("table-cell");
            }
            else
            {
                displaySegundaColumna("none");
            }
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

    var populateFila = function(fila, registro){
        fila.find(st.cCantidadDisponible).val(registro.cantidad_disponible);
        fila.find(st.cPrecioUnitario).val(parseFloat(registro.precio).toFixed(2));
        fila.find(st.cCantidad).val("0");
        fila.find(st.cTotal).val("0");
    };

    var displaySegundaColumna = function(display){
        dom.documentosTable.find("tr").each(function(){
            var self = $(this);
            var i = 0;

            i=0;
            self.find("th").each(function(){
                var self = $(this);
                if(i == 1)
                {
                    self.css("display", display);
                }
                i += 1;
            });

            i=0;
            self.find("td").each(function(){
                var self = $(this);
                if(i == 1)
                {
                    self.css("display", display);
                }
                i += 1;
            });
        });
    }

    var mejorasVisuales = function(){
        dom.cTableResponsive.css("padding-bottom", "100px");
        dom.cTableResponsive.append('<span class="tabla_dinamica_error"></span>');

        dom.cTotalGeneral.parent().parent().css("float", "right");
        //dom.form.find(".ibox-content").css("min-height", "707px");

        var estado = dom.cEstado;
        if(dom.iFormularioEditar.length > 0)
        {
            //ACTUALIZO LA SEGUNDA COLUMNA DE LA TABLA DINAMICA
            dom.cTipoAjuste.trigger("change");

            //DESHABILITO TODOS LOS CAMPOS
            dom.form.find(st.input).prop("disabled", true);
            dom.form.find(st.cChosen).prop("disabled", true).trigger("chosen:updated");
            dom.form.find("button").prop("disabled", true);
            dom.form.find("textarea").prop("disabled", true);

            if(estado.val() == "3")//Por aprobar
            {
                estado.prop("disabled", false).trigger("chosen:updated");
                dom.cBtnGuardar.prop("disabled", false);
            }

            //OCULTO EL CAMPO DE CANTIDAD DISPONIBLE QUE NO ES NECESARIO EN LA EDICION
            displaySegundaColumna("none");
        }
        else
        {
            //al crear un ajuste por defecto es en estado "por aprobar"
           // estado.val("3").prop("disabled", true).trigger("chosen:updated");
        }
    };

    var validaciones = function(){

        //SETEO DE VALORES Y COMPORTAMIENTOS POR DEFECTO
        $.validator.setDefaults({
            errorPlacement: function(error, element){
                if($(element).hasClass("item") == true || $(element).hasClass("cantidad") == true)
                {
                    dom.documentosTable.parent().find(".tabla_dinamica_error").empty().append('<label class="error pull-left">Debe llenar todos los campos marcados con *.</label>');

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

        $.validator.addMethod("menorQueDisponible", function(value, element) {
            var self = $(element);
            var fila = self.parent().parent();
            var disponible = fila.find(st.cCantidadDisponible).val();
            var tipo_ajuste = parseInt(dom.cTipoAjuste.val()) || 1;

            if(tipo_ajuste == "1" && (parseFloat(value) <= parseFloat(disponible)))
            {
                return true;
            }
            else if(tipo_ajuste == "2")
            {
                return true;
            }

            toastr.error("Error! Cantidad debe ser menor o igual que la cantidad disponible");
            return false;
        }, "* Cantidad debe ser menor o igual que la cantidad disponible");

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


        dom.form.find(st.cDocumento).rules( "add", {
            required: true
        });

        dom.form.find(st.cCantidad).rules( "add", {
            required: true,
            menorQueDisponible: true
        });
    };

    // Función que inicializará los funciones decritas anteriormente.
    var initialize = function(){
        catchDom();
        suscribeEvents();
        mejorasVisuales();
        //validaciones();
    };

    /* Retorna un objeto literal con el método init haciendo referencia a la 
     función initialize. */
    return{
        init:initialize
    };
})();

var formulario = "";
var decimales = 2;

formularioInteresesAsegurados.init();


$(function(){
	//jQuery Daterange
	$("#fecha").datepicker({
		defaultDate: "+1w",
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		numberOfMonths: 1		
	});	

    //jQuery Daterange
    $("#fecha_despacho").datepicker({
        defaultDate: "+1w",
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        numberOfMonths: 1       
    }); 

    //jQuery Daterange
    $("#fecha_arribo").datepicker({
        defaultDate: "+1w",
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        numberOfMonths: 1       
    }); 

    //jQuery Daterange
    $("#fecha_concurso").datepicker({
        defaultDate: "+1w",
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        numberOfMonths: 1       
    }); 

    $(".campodesde").val(desde);
	$(".indcolec").val(indcolec);
    
    var counter = 2;
    //$('#del_file_vehiculo').hide();
    $('#add_file').click(function(){
            
        $('#file_tools').before('<div class="file_upload" id="f'+counter+'"><input name="nombre_documento[]" type="text" style="width: 300px!important; float: left;" class="form-control"><input name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"></div>');
        $('#del_file').fadeIn(0);
    counter++;
    });
    $('#del_file').click(function(){
        /*if(counter==3){
            $('#del_file_vehiculo').hide();
        } */  
        counter--;
        $('#f'+counter).remove();
    });
    
    //Mostrar barra de filtro formulario
        $('.filtro-formularios').removeClass('hide');
        
        //Evento: Cambio de formulario
        $('#formulario').on('change', function(e){
            e.preventDefault();
            var seleccionado = $(this).find('option:selected').val();
            //console.log( seleccionado );
            
            $('.filtro-formularios').find('ul').find('a[href="#'+ seleccionado +'"]').trigger('click');
        });

        //Inicializar Chosen plugin
        if ($().chosen){
            if($(".chosen-filtro").attr("class") != undefined){
                $(".chosen-filtro").chosen({
                    width: '100%',
                    disable_search: true,
                    inherit_select_classes: true
                });
            }
        }

        //Funcion para inicializar plugins
        var actualizar_chosen = function() {
            
            //refresh chosen
            setTimeout(function(){
                $('.filtro-formularios').find('select.chosen-filtro').trigger('chosen:updated');
            }, 50);
        };

        //Verificar si existe variable "formulario_seleccionado"
        if(typeof formulario_seleccionado != "undefined"){
            setTimeout(function(){
                $('.filtro-formularios').find('#formulario').find('option[value*="'+ formulario_seleccionado +'"]').prop("selected", "selected").trigger('change');
                actualizar_chosen();
            }, 800)
            
        }

    
});


$(document).ready(function () {
    $('#crearSolicitudLnk').click(function(e){
        
        var opcionesModal = $('#optionsModal');

        var pantalla = $('#menu_solicitud');
        pantalla.css('display', 'block');
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();        
       
        //Inicializar opciones del Modal
        opcionesModal.modal({
                backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
                show: false
        });
        opcionesModal.find('.modal-title').empty().append('<b>Solicitudes</b>');
        opcionesModal.find('.modal-body').empty().append(pantalla);
        opcionesModal.find('.modal-footer').empty();
        opcionesModal.modal('show');    

    });

    if(regresar_poliza == 'si'){
        $('#cancelar').attr('href', phost()+'polizas/editar/'+uuid_poliza);
    }
});
