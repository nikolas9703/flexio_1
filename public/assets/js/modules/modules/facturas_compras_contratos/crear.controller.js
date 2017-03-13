(function() {bluapp.controller("facturasController", function($scope, $timeout,serviceFactura, $document){
    var model = this;
    var items;
    
    var st = {
        //cabecera
        iTipo:"#tipo",
        iUuidTipo:"#uuid_tipo",
        //formulario
        form:"form",
        iFormularioCrear:"#form_crear_facturas",
        iFormularioEditar:"#form_editar_facturas",
        iFormularioCancelarBtn: "#cancelarFormBtn",
        iFormularioGuardarBtn: "#guardarBtn",
        //formulario -> campos generales
        iProveedor:"#proveedor_id",
        iBodega:"#bodega_id",
        iSaldo:"#campo\\[saldo\\]",
        iCredito:"campo\\[credito\\]",
        iFechaDesde:"#fecha_desde",
        iFacturaProveedor:"#factura_proveedor",
        iFechaHasta:"#fecha_hasta",
        iComprador:"#comprador",
        iCentroContable:"#centro_contable_id",
        iTerminoPago:"#termino_pago",
        iComentario:"#comentario",
        iEstado:"#estado",
        //formulario -> articulos
        cCategoria:".categoria",
        cItem:".item",
        cCantidad:".cantidad",
        cUnidad:".unidad",
        cPrecioUnidad:".precio_unidad",
        cImpuesto:".impuesto",
        cDescuento:".descuento",
        cCuenta:".cuenta",
        cTotal:".total",
        cSubtotal:".subtotal",
        cDescuentos:".descuentos",
        cImpuestos:".impuestos",
        //agrupadores
        cChosen:".chosen-select"
    };
    
    var config = {
        chosen: {
            width: '100%'
        },
        datepicker: {
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            numberOfMonths: 1,
            onClose: function( selectedDate ) {
                var self = $(this);
                
                if(self.prop("id") === "fecha_desde")
                {
                    dom.iFechaHasta.datepicker( "option", "minDate", selectedDate );
                }
                else if(self.prop("id") === "fecha_hasta")
                {
                    dom.iFechaDesde.datepicker( "option", "maxDate", selectedDate );
                }
            }
        }
    };
    
    var dom = {};
    
    var catchDom = function(){
        //cabecera
        dom.iTipo = $(st.iTipo);
        dom.iUuidTipo = $(st.iUuidTipo);
        //formulario
        dom.form = $(st.form);
        dom.iFormularioCrear = $(st.iFormularioCrear);
        dom.iFormularioEditar = $(st.iFormularioEditar);
        dom.iFormularioCancelarBtn = $(st.iFormularioCancerlarBtn);
        dom.iFormularioGuardarBtn = $(st.iFormularioGuardarBtn);
        //formulario -> campos generales
        dom.iProveedor = $(st.iProveedor);
        dom.iBodega = $(st.iBodega);
        dom.iSaldo = $(st.iSaldo);
        dom.iCredito = $(st.iCredito);
        dom.iFechaDesde = $(st.iFechaDesde);
        dom.iFechaHasta = $(st.iFechaHasta);
        dom.iComprador = $(st.iComprador);
        dom.iCentroContable = $(st.iCentroContable);
        dom.iTerminoPago = $(st.iTerminoPago);
        dom.iComentario = $(st.iComentario);
        dom.iEstado = $(st.iEstado);
    };
    
    var suscribeEvents = function(){
        //fechas
        dom.iFechaDesde.datepicker(config.datepicker);
        dom.iFechaHasta.datepicker(config.datepicker);
    };
    
    var validaciones = function(){
        //activo el input mask
        
        dom.form.validate({
            ignore: '',
            wrapper: '',
            errorPlacement:function(error, element){

                if($(element).hasClass("categoria") === true || $(element).hasClass("item") === true || $(element).hasClass("cantidad") === true || $(element).hasClass("unidad") === true || $(element).hasClass("precio_unidad") === true || $(element).hasClass("impuesto") === true || $(element).hasClass("descuento") === true || $(element).hasClass("cuenta") === true) {
                    //error.appendTo( $("tfoot td:nth-child(1)") );
                    $("#tablaError").html(error);
                }
                else
                {
                    error.insertAfter(element);
                }
            },
            submitHandler: function(form) {
                // do other things for a valid form
                dom.form.find("input").prop("disabled", false);
                dom.form.find("select").prop("disabled", false);
                dom.iFormularioGuardarBtn.prop("disabled",true);
                form.submit();
            }
        });
    };
    
    var dibujarDom = function(){
        //scope sumatoria
        $scope.totales = new variablesIniciales.totales;
    

        //elementos que componen la tabla dinamica - articulos
        $scope.articulos = new variablesIniciales.articulos([]);
        
        //activo el jqueryChosen
        setTimeout(function(){
            dom.form.find(st.cChosen).chosen(config.chosen);
            $(':input[data-inputmask]').inputmask();
        },400);
        
    };
    
    //Obtiene el catelogo de items...
    var getItems = function(){
        var itemsInfo = serviceFactura.getItems();
            
        itemsInfo.then(function(data){
            items = data;//Catalogo de items agrupados por categorias
            
            //SI ESTOY DESDE EL FORMULARIO DE EDICION CARGO
            //LOS DATOS QUE NO ME ESTOY TRAYENDO DESDE PHP
            if(dom.iFormularioEditar.length)
            {
                console.log("obtengo los datos de la factura -> formulario de edicion");
                $scope.gets.factura($("#factura_id").val());
            }
            else if(orden_compra_id.length > 0)
            {
                $scope.tipo = 'Ordenes_orm';
                $scope.ngChanged.empezarDesde($scope.tipo);
                $scope.uuid_tipo = orden_compra_id; 
                $scope.ngChanged.empezarDesdeId($scope.tipo, $scope.uuid_tipo);
            }
        });
    };
    
    var actualizarChosens = function(){
        setTimeout(function(){
            dom.form.find(st.cChosen).trigger("chosen:updated");
        },500);
    };

    var getItem = function(item_id, index){
        return _.find($scope.articulos[index].items,function(query){
            return query.id == item_id;
        });
    };
    
    var calcularFactura = function(){
        var articulos = $scope.articulos;
        var subTotalFactura = 0;
        var descuentosFactura = 0;
        var impuestosFactura = 0;
        var totalFactura = 0;
        
        $.each(articulos, function(i, articulo){
            var subTotalFila = 0;
            var descuentosFila = 0;
            var totalFila = 0;
            var impuestosFila = 0;
            
            var porcentajeImpuesto = parseFloat(angular.element('#impuesto_id0').find('option[value="'+$scope.articulos[i].impuesto+'"]').data("impuesto")) || 0;
            
            subTotalFila = parseFloat(articulo.precio_unidad * articulo.cantidad) || 0;
            descuentosFila = parseFloat((subTotalFila * articulo.descuento)/100) || 0;
            totalFila = parseFloat(subTotalFila - descuentosFila) || 0;//el total de la fila no aplica impuestos
            impuestosFila = parseFloat((totalFila * porcentajeImpuesto)/100) || 0;
            
            //Asigno los valores al dom de la fila en la que estoy ubicado
            $scope.set.subTotalFila(subTotalFila, i);
            $scope.set.descuentosFila(descuentosFila, i);
            $scope.set.impuestosFila(impuestosFila, i);
            $scope.set.totalFila(totalFila, i);
            
            //actualizo mis totalizadores globales de la factura
            subTotalFactura += subTotalFila;
            descuentosFactura += descuentosFila;
            impuestosFactura += impuestosFila;
            totalFactura += parseFloat(totalFila + impuestosFila) || 0;//el total de la fila ya tiene el descuento
            
            $scope.set.subTotalFactura(subTotalFactura);
            $scope.set.descuentosFactura(descuentosFactura);
            $scope.set.impuestosFactura(impuestosFactura);
            $scope.set.totalFactura(totalFactura);
        });
    };
    
    //funciones del scope
    $scope.funciones = {
        //limpia todo el formulario de creacion/edicion de facturas
        limpiarData: function(){
            $scope.datosFactura = new variablesIniciales.datosFactura({});
            $scope.totales = new variablesIniciales.totales;
            $scope.articulos = new variablesIniciales.articulos([]);
            
            $scope.uuid_tipo = '';
        
            dom.form.find(st.cChosen).prop("value","").trigger("chosen:updated");
        },
        populateDatosFactura: function(data){
            $scope.datosFactura = new variablesIniciales.datosFactura(data);
            $scope.articulos = new variablesIniciales.articulos(data.items);
            
            setTimeout(function(){
                dom.form.find(st.cChosen).trigger("chosen:updated");
            },400);
        }
    };
    
    $scope.ngClick = {
        addRow: function(index){
            //Cuando se esta facturando desde un contrato
            //o una orden de compra no se pueden agregar
            //mas filas a la factura
            if((_.isEmpty($scope.uuid_tipo) || $scope.uuid_tipo == '0') || $scope.tipo == 'Subcontratos'){
                
                $scope.articulos.push({
                    categoria:'',
                    item:'',
                    descripcion:'',
                    cantidad:1,
                    unidad:'',
                    unidades:{},
                    precio_unidad:'0.00',
                    impuesto:'',
                    descuento:'0.00',
                    cuenta:'',
                    //totalizadores de la fila
                    total:'0.00',
                    subtotal: '0.00',
                    descuentos: '0.00',
                    impuestos: '0.00'
                });
              
                var row = "tr#items" + ($scope.articulos.length - 1);
                $timeout(function(){
                    //activo la mascara
                    $(row).find(':input[data-inputmask]').inputmask();
                    
                    if($(row).find('select.chosen-select').attr('class') !== undefined){

                        $(row).find('.chosen-container').remove();
                        $(row).find('.chosen-container-single').remove();
                        $(row).closest('table').find('select.chosen-select').chosen({
                            width: '100%'
                        }).trigger('chosen:updated').on('chosen:showing_dropdown', function(evt, params) {
                            $(row).closest('div.table-responsive').css("overflow", "visible");
                        }).on('chosen:hiding_dropdown', function(evt, params) {
                            $(row).closest('div.table-responsive').css({'overflow-x':'auto !important'});
                        });
                    }
                },800);
            }
        },
        deleteRow: function(articulo){
            var index = $scope.articulos.indexOf(articulo);
            if(!angular.isUndefined($scope.articulos[index]))
            {
                $scope.articulos.splice(index, 1);
                calcularFactura();
                actualizarChosens();
            }
        }
    };
    
    $scope.ngBlur = {
        itemCantidad: function(valor, index){
            calcularFactura();
        },
        itemPrecioUnidad: function(valor, index){
            calcularFactura();
        },
        itemDescuento: function(valor, index){
            calcularFactura();
        }
    };
    
    $scope.ngChanged = {
        //cabecera
        empezarDesde: function(tipo_orden){

            $scope.tipo = tipo_orden === '' ? '' : tipo_orden;
            $scope.uuid_tipo = '';
            
            if(tipo_orden === '')
            {
                $scope.uuid_tipo = tipo_orden;
                $scope.funciones.limpiarData();
                
                $scope.disabled = false;
                actualizarChosens();
            }
        },
        empezarDesdeId: function(tipo, uuid_tipo){
            if(uuid_tipo !==''){
                var info = serviceFactura.getEmpezarDesde({uuid:uuid_tipo,tipo:tipo});

                info.then(function(data){
                    
                    $scope.uuid_tipo = uuid_tipo;
                    $scope.disabled = ($scope.tipo == 'Ordenes_orm') ? true : false;
                    
                    //populate datos generales de formulario -> incluye los articulos
                    $scope.funciones.populateDatosFactura(data);
                    calcularFactura();
                    
                    setTimeout(function(){
                        dom.form.find(st.cChosen).chosen(config.chosen).trigger("chosen:updated");
                    },700);
                });

            }
            else if(_.isEmpty(uuid_tipo) || _.isEmpty($scope.tipo))
            {
                $scope.funciones.limpiarData();
                calcularFactura();
            }
        },
        //seccion general
        proveedor: function(proveedor_id){
            var info = serviceFactura.getProveedor({"proveedor_id":proveedor_id});
            
            info.then(function(data){
                $scope.set.credito(data.credito);
                $scope.set.saldo(data.saldo);
            });
        },
        //seccion de articulos
        itemCategoria: function(categoria_id, index){
            //populate catalogos
            $scope.populate.items(categoria_id, index);
            $scope.populate.unidades("", index);
            
            //setear valores
            $scope.set.item("", index);
            $scope.set.cantidad(1, index);
            $scope.set.unidad("", index);
            $scope.set.precio_unidad("", index);
            $scope.set.impuesto("", index);
            $scope.set.descuento("0.00", index);
            $scope.set.cuenta("", index);
            $scope.set.totalFila("", index);
            
            //actualizo los chosen
            actualizarChosens();
        },
        itemItem: function(item_id, index){
            //populate unidades
            $scope.populate.unidades(item_id, index);
            
            //intancion mi item
            var aux = getItem(item_id, index);
            
            //setear valores
            $scope.set.cantidad(1, index);
            $scope.set.unidad(aux.unidad_id, index);
            $scope.set.precio_unidad("", index);
            $scope.set.impuesto(aux.impuesto_id, index);
            $scope.set.descuento("0.00", index);
            $scope.set.cuenta(aux.cuenta_id, index);
            $scope.set.totalFila("", index);
            
            //actualizo los chosen
            actualizarChosens();
        },
        itemUnidad: function(){
            calcularFactura();
        },
        itemImpuesto: function(){
            calcularFactura();
        }
    };
    
    $scope.gets = {
        items: function(categoria_id){
            var itemsAux = [];
            if(categoria_id.length > 0)
            {
                var aux = _.find(items,function(query){
                    return query.categoria_id == categoria_id;
                });
                
                itemsAux = aux.items;
            }
            
            return itemsAux;
        },
        item: function(categoria_id, item_id){
            if(categoria_id != '' && item_id != '')
            {
                //obtengo el catalogo de items de la categoria
                var itemsCat = _.find(items,function(query){
                    return query.categoria_id == categoria_id;
                });

                //obtengo el item
                var item = _.find(itemsCat.items,function(query){
                    return query.id == item_id;
                });
                
                return item;
            }
        },
        unidad: function(categoria_id, item_id, unidad_id){
            if(categoria_id != '' && item_id != '')
            {
                //obtengo el catalogo de items de la categoria
                var itemsCat = _.find(items,function(query){
                    return query.categoria_id == categoria_id;
                });

                //obtengo el item
                var item = _.find(itemsCat.items,function(query){
                    return query.id == item_id;
                });
                
                //obtengo la unidad
                var unidad = _.find(item.unidades,function(query){
                    return query.id == unidad_id;
                });
                
                return unidad;
            }
        },
        factura: function(factura_id){
            if(factura_id !==''){
                var info = serviceFactura.getFactura({factura_id:factura_id});

                info.then(function(data){
                    var tipo = '';
                    
                    if(data.operacion_type == "Flexio\\Modulo\\SubContratos\\Models\\SubContrato")
                    {
                        tipo = 'subcontrato';
                    }
                    else if(data.operacion_type == "Ordenes_orm")
                    {
                        tipo = 'orden_compra';
                    }
                    
                    $scope.tipo = data.operacion_type;
                    $scope.uuid_tipo = data.operacion_id + tipo;
                    
                    //populate datos generales de formulario -> incluye los articulos
                    $scope.funciones.populateDatosFactura(data);
                    calcularFactura();
                    
                    setTimeout(function(){
                        dom.form.find(st.cChosen).chosen(config.chosen).trigger("chosen:updated");
                    },700);
                });

            }
        }
    };
    
    $scope.populate = {
        items: function(categoria_id, index){
            var itemsAux = [];
            if(categoria_id.length > 0)
            {
                var aux = _.find(items,function(query){
                    return query.categoria_id == categoria_id;
                });
                
                itemsAux = aux.items;
            }
            
            $scope.articulos[index].items = itemsAux;
        },
        unidades: function(item_id, index){
            var unidadesAux = [];
            if(item_id.length > 0)
            {
                var aux = getItem(item_id, index);
                    
                unidadesAux = aux.unidades;
            }
            
            $scope.articulos[index].unidades = unidadesAux;
        }
    };
    
    $scope.set = {
        //elementos generales del formualrio
        credito: function(valor){
            var aux = "";
            if(valor.length > 0)
            {
                aux = valor;
            }
            $scope.datosFactura.credito = aux;
        },
        saldo: function(valor){
            var aux = "";
            if(valor.length > 0)
            {
                aux = valor;
            }
            $scope.datosFactura.saldo = aux;
        },
        //tabla dinamica -> items
        item: function(valor, index){
            var aux = 1;
            if(valor.length > 0)
            {
                aux = valor;
            }
             [index].item = aux;
        },
        cantidad: function(valor, index){
            var aux = 1;
            if(valor.length > 0)
            {
                aux = valor;
            }
            $scope.articulos[index].cantidad = aux;
        },
        unidad: function(valor, index){
            var aux = "";
            if(valor.length > 0)
            {
                aux = valor;
            }
            $scope.articulos[index].unidad = aux;
        },
        precio_unidad: function(valor, index){
            var aux = "";
            if(valor.length > 0)
            {
                aux = valor;
            }
            
            $scope.articulos[index].precio_unidad = aux;
        },
        impuesto: function(valor, index){
            var aux = "";
            if(valor.length > 0)
            {
                aux = valor;
            }
            
            $scope.articulos[index].impuesto = aux;
        },
        descuento: function(valor, index){
            var aux = "";
            if(valor.length > 0)
            {
                aux = valor;
            }
            
            $scope.articulos[index].descuento = aux;
        },
        cuenta: function(valor, index){
            var aux = "";
            if(valor.length > 0)
            {
                aux = valor;
            }
            
            $scope.articulos[index].cuenta = aux;
        },
        //totales Fila
        subTotalFila: function(valor, index){
            var aux = 0;
            if(valor)
            {
                aux = valor;
            }
            $scope.articulos[index].subtotal = aux;
        },
        descuentosFila: function(valor, index){
            var aux = 0;
            if(valor)
            {
                aux = valor;
            }
            $scope.articulos[index].descuentos = aux;
        },
        impuestosFila: function(valor, index){
            var aux = 0;
            if(valor)
            {
                aux = valor;
            }
            $scope.articulos[index].impuestos = aux;
        },
        totalFila: function(valor, index){
            var aux = 0;
            if(valor)
            {
                aux = valor;
            }
            $scope.articulos[index].total = parseFloat(aux).toFixed(2) || 0;
        },
        //totales Factura
        subTotalFactura: function(valor){
            var aux = 0;
            if(valor)
            {
                aux = valor;
            }
            
            $scope.totales.subtotal = aux;
        },
        descuentosFactura: function(valor){
            var aux = 0;
            if(valor)
            {
                aux = valor;
            }
            
            $scope.totales.descuentos = aux;
        },
        impuestosFactura: function(valor){
            var aux = 0;
            if(valor)
            {
                aux = valor;
            }
            
            $scope.totales.impuesto = aux;
        },
        totalFactura: function(valor){
            var aux = 0;
            if(valor)
            {
                aux = valor;
            }
            
            $scope.totales.total = aux;
        }
    };
    
    
    //DEFINO MIS VARIABLES INICIALES
    var variablesIniciales = {
        //campos generales del formulario
        datosFactura: function(data){
            var vacio = _.isEmpty(data);
            
            //para la edicion -> condicion -> se recibe data sin estas propiedades
            var fecha_desde = (typeof data.fecha_desde === "undefined") ? '' : data.fecha_desde;
            var factura_proveedor = (typeof data.factura_proveedor === "undefined") ? '' : data.factura_proveedor;
            var fecha_hasta = (typeof data.fecha_hasta === "undefined") ? '' : data.fecha_hasta;
            var termino_pago = (typeof data.termino_pago === "undefined") ? '' : data.termino_pago;
            var comentario = (typeof data.comentario === "undefined") ? '' : data.comentario;
            var estado = (typeof data.estado === "undefined") ? 'por_aprobar' : data.estado;
            
            //asignacion al objeto
            this.proveedor = vacio ? proveedor_id : data.proveedor.id;
            this.bodega = vacio ? '' : data.bodega.id;
            this.credito = vacio ? '' : data.proveedor.credito_favor;
            this.saldo = vacio ? '' : data.proveedor.saldo_pendiente;
            this.fecha_desde = vacio ? '' : fecha_desde;
            this.factura_proveedor = vacio ? '' : factura_proveedor;
            this.fecha_hasta = vacio ? '' : fecha_hasta;
            this.comprador = vacio ? '' : data.comprador.id;
            this.centro_contable = vacio ? '' : data.centro_contable.id;
            this.termino_pago = vacio ? '' : termino_pago;
            this.comentario = vacio ? '' : comentario;
            this.estado = vacio ? 'por_aprobar' : estado;//estado por defecto para la creacion
        },
        //valores de la tabla totalizadora
        totales: function(){
            this.subtotal = 0;
            this.descuentos = 0;
            this.impuesto = 0;
            this.total = 0;
        },
        //campos de los articulos que componen a la factura
        articulos: function(articulos){
            var obj = function (articulo){
                var vacio = _.isEmpty(articulo);



                this.categoria = vacio ? '' : articulo.categoria_id;
                this.items = $scope.gets.items(this.categoria);//catalogo de items
                this.item = vacio ? '' : articulo.item_id;
                this.descripcion = vacio ? '' : articulo.descripcion;
                this.cantidad = vacio ? '1' : articulo.cantidad;
                this.unidades = vacio ? [] : $scope.gets.item(this.categoria, this.item).unidades;//catalogo de unidades del item;
                this.unidad = vacio ? '' : articulo.unidad_id;
                this.precio_unidad = vacio ? '0.00' : articulo.precio_unidad;
                this.impuesto = vacio ? '' : articulo.impuesto_id;
                this.descuento = vacio ? '0.00' : articulo.descuento;
                this.factura_item_id = vacio ? '' : articulo.factura_item_id;
                this.cuenta = vacio ? '' : articulo.cuenta;
                //totalizadores de la fila
                this.total = vacio ? '0.00' : articulo.total;
                this.subtotal = vacio ? '0.00' : articulo.subtotal;
                this.descuentos = vacio ? '0.00' : articulo.descuentos;
                this.impuestos = vacio ? '0.00' : articulo.impuestos;
            };
            
            var aux = [];
            
            if(articulos.length > 0)
            {
                $.each(articulos, function(i, articulo){
                    aux.push(new obj(articulo));
                });
            }
            else
            {
                aux.push(new obj({}));
            }
            
            return aux;
        }
    };
    

    if($(document).find(st.iFormularioCrear).length > 0)
    {
        $scope.tipo = ""; //Orden de Compra || Contrado
        $scope.uuid_tipo = "";//identificador de la orden de compra o el contrato
        $scope.datosFactura = new variablesIniciales.datosFactura({});
    }
    else if($(document).find(st.iFormularioEditar).length > 0)
    {
        console.log("logica para el formulario de edicion");
    }
        
    $scope.acceso = acceso === 0 ? false : true;
    $scope.vista = vista;
    $scope.disabled = false;
    
    
    $scope.init = function(){
        catchDom();
        suscribeEvents();
        validaciones();
        
        //carga los catalogos dinamicos
        //lleva dentro la obtencion de la informacio de la factura
        //ya creada cuando se esta desde el formulario de ver/editar
        getItems(); 
        
        //dibujo el dom con los catalogos recien cargados
        dibujarDom(); //->lleva dentro la activacion de la mascara inputmask
    };

    $scope.init();
});})();
