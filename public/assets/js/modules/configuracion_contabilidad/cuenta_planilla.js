var cuentaPlanilla = {
    rutas:{
        cuentaActivo: phost() +'contabilidad/ajax-cuenta-activo',
        guardarcuentaCobro: phost() + 'configuracion_contabilidad/ajax-cuenta-planilla'
    },
    vista:{
        guardarCuenta: $('#planilla').find('button#btnGuardar'),
        botonCancelar: $('table#cuentas_planilla').find('button#btnCancelar'),
        divCuentas: $('table#cuentas_planilla').find('div#cuentas_activo'),
        eliminarCuenta: 'a#close_cuenta'
    },
    init:function(){
        this.cargar_cuenta();
        /*$('#configuracionTabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            e.target // newly activated tab
            e.relatedTarget // previous active tab
            var planilla = e.target;
          //  var palntilla2 = planilla.split('#');
            this.cargar_cuenta();
            //$('#plantilla').tab('show');
            console.log("entre"+planilla);
        });*/
    },
    mensaje:function(data){
        if(data.tipo ==='success'){
            toastr.success(data.mensaje);
        }else if(data.tipo ==='error'){
            toastr.error(data.mensaje);
        }else if(data.tipo ==='info'){
            toastr.info(data.mensaje);
        }
    },
    cargar_cuenta:function(){
        var self = this;
        var cuentas = moduloConfiguracionContabilidad.getCuentaActivos();
        cuentas.done(function(data){
            self.treeRender(data);
        });
       /* $("#planilla").on( "click", function( event, ui ) {
            //control comes here when you clicked on tab 1
            self.treeRender(cuentas);
        });*/
        self.vista.guardarCuenta.click(function(){
            console.log("guardar");
            var cuenta = $('table#cuentas_planilla').find("#id_seleccion").val();
            if(cuenta.length === 0){
                console.log("error");
                return false;
            }

            var parametros = {id:cuenta};
            var guardar = moduloConfiguracionContabilidad.guardarCuentaPlanilla(parametros);
            guardar.done(function(data){
                $('table#cuentas_planilla').find("#id_seleccion").val("");
                self.mensaje(data);
                if(data.tipo ==='success'){
                    var arbol_cuenta = $.jstree.reference($('table#cuentas_planilla').find('div#cuentas_activo'))._model.data;//.disable=true;
                    $.each(arbol_cuenta,function(i, elem){
                        if(elem.id != cuenta){
                            $('table#cuentas_planilla').find('div#cuentas_activo').jstree('disable_node',elem.id);
                        }
                    });
                }
            });
        });
        $('table#cuentas_planilla div#cuenta_seleccionada').on('click',self.vista.eliminarCuenta,function(e){
            //el id
            var cuenta_id = $(this).data('item');
           // console.log(cuenta_id);
            var parametros = {cuenta_id:cuenta_id};
            var cuentaEliminada = moduloConfiguracionContabilidad.eliminarCuentaPlanilla(parametros);
            cuentaEliminada.done(function(data){
                if(!data.puede_eliminar){
                    swal("Cuenta Planilla", "La cuenta no se puede eliminar porque tiene transacciones", "warning");
                }else{
                    self.mensaje(data);
                    $('table#cuentas_planilla').find('div.item-cuenta').fadeIn('slow');
                    $('table#cuentas_planilla').find('div.item-cuenta').remove();
                    var arbol_cuenta = $.jstree.reference($('div#cuentas_activo'))._model.data;
                    $.each(arbol_cuenta,function(i, elem){
                        if(elem.es_padre){
                            $('table#cuentas_planilla').find('div#cuentas_activo').jstree('disable_node',elem.id);
                        }else{
                            $('table#cuentas_planilla').find('div#cuentas_activo').jstree('enable_node',elem.id);
                        }
                    });
                    $('table#cuentas_planilla').find('li#'+cuenta_id+' a').removeClass('jstree-clicked');
                    $('table#cuentas_planilla').find('li#'+cuenta_id+' div').removeClass('jstree-wholerow-clicked');
                }
            });
        });
        self.vista.botonCancelar.click(function(){
            console.log('log()');
        });
    },
    treeRender:function(data){
        var arbol = data;
        $('div#cuentas_activo').jstree(arbol);
        $('div#cuentas_activo').jstree(true).redraw(true);
        $('div#cuentas_activo').bind("loaded.jstree",
            function (event, data){
                $('table#cuentas_planilla').find("a:contains('1. Activos')").css("visibility","hidden");
                $('table#cuentas_planilla').find(".jstree-last .jstree-icon").first().hide();
                //$('div#cuentas_activo').jstree('select_node', 11);
                //busca el selecionado
                var selecionado = moduloConfiguracionContabilidad.getCuentaPlanilla();
               // console.log(selecionado);
                selecionado.done(function(data){
                    var obj =data;
                    if(!_.isEmpty(obj)){
                       // console.log(obj[0].cuenta_id);
                        $('table#cuentas_planilla').find('div#cuentas_activo').jstree('select_node',obj[0].cuenta_id);
                        //$.jstree.reference($('div#cuentas_activo'))._model.default_state.disabled=true;
                        var arbol_cuenta = $.jstree.reference($('div#cuentas_activo'))._model.data;//.disable=true;
                        $.each(arbol_cuenta,function(i, elem){
                            if(elem.id != obj[0].cuenta_id){
                                $('table#cuentas_planilla').find('div#cuentas_activo').jstree('disable_node',elem.id);
                            }
                        });
                    }
                });
            });
        $('table#cuentas_planilla').find('div#cuentas_activo').bind("select_node.jstree", function(e, data) {

            var cuenta = data.node;
            var contenido = '';

            //console.log(cuenta);

            $('table#cuentas_planilla').find('#id_seleccion').val(cuenta.id);
            contenido = '<div class="item-cuenta"><div class="pull-left icono-cerrar"><a id="close_cuenta" data-item="'+cuenta.id+'"><i class="fa fa-close fa-lg fa-border "></i></a></div><div class="pull-left text-right cuenta-texto">'+cuenta.text+'</div></div>';
            $('table#cuentas_planilla').find('#cuenta_seleccionada').html(contenido);

            //console.log($('ul.jstree-children').find('li'));
        });
        /*$('div#cuentas_activo').bind('hover_node.jstree', function (e, data) {
         console.log("changed",data);
         });*/
    }
};

(function(){
    cuentaPlanilla.init();
})();

