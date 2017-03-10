var sumcob=1;
var sumded=1;
var sumcomi=1;
var pramohijo=0;
var x=0;
var xant=0;
var y=0;
var eli=0;
var config = {

    checks: {
        color: "#023859",
        size: 'small'

    },
    chosen: {
        width: '100%'
    }
};
var planesCrear,

    planes = {

        settings: {
            siguiente1: $("#siguiente1"),
            siguientedos: $("#siguientedos"),
            siguientetres: $("#siguientetres"),
            anterior1: $("#anterior1"),
            anterior2: $("#anterior2"),
            anterior3: $("#anterior3"),
            segundotab: $("#segundotab"),
            tercertab: $("#tercertab"),
            cuartotab: $("#cuartotab"),
            primertab: $("#primertab"),
            ramo_nombre: $("#nombre_plan"),
            aseguradora: $("#aseguradora"),
            impuesto: $("#impuesto2"),
            coberturas: $(".coberturas"),
            iAcordeon: "#accordion",
            iCheckComision: "#ch_comision",
            comision: $("#comision"),
            sobre_comision: $("#sobre_comision"),
            ch_comision: $("#ch_comision"),
            codigo_ramo: $("#codigo_ramo"),
            treeRamos: $("#treeRamos"),
            anio_inicio: $("#anio_inicio"),
            anio_fin: $(".anio_fin")
        },

        init: function () {
            planesCrear = this.settings;
            this.eventos();
            $("#ramoplanes-error").hide();
            $(".comisiones").inputmask('Regex', {regex: "^[0-9]{1,20}(\\,\\d{1,2})?$"});
            $(".sobrecomisiones").inputmask('Regex', {regex: "^[0-9]{1,20}(\\,\\d{1,2})?$"});
            $("#coberturasmonet").inputmask('Regex', {regex: "^[0-9]{1,20}(\\.\\d{1,2})?$"});
            $("#deduciblesmonet").inputmask('Regex', {regex: "^[0-9]{1,20}(\\.\\d{1,2})?$"});
            $("#primaneta").inputmask('Regex', {regex: "^[0-9]{1,20}(\\.\\d{1,2})?$"});
        },
        suscribeEvents: function () {
            //checkbox
            planesCrear.switcheryComision = new Switchery(planesCrear.iCheckComision[0], config.checks);

            // var elem = document.querySelector('.js-switch');
            // planesCrear.switcheryComision = new Switchery(elem, config.checks);

        },
        agregarfila: function (evt,tabla) {
            var idco="";
            var idde="";
            
            var $tr = $('#'+tabla).find("tbody tr:last").clone();
            //var $tr = $('#'+tabla).find("tbody tr:last").clone();
            $(evt).parent().parent().find("#agregarbtn").attr('style', 'margin-top: 5px;display: none');
            $(evt).parent().parent().find("#eliminarbtn").attr('style', 'margin-top: 5px');
            $tr.attr('style', '');
            $tr.find("input:text").val("");
            $tr.find("input:hidden").val("");
            $tr.find("input,select").attr("name", function () {
                var name = this.name;
                return name;
            }).attr("id", function () {
                if (tabla=="tabla_fact") {
                    var id = this.id+sumcob;
                    sumcob++;
                    idco=id;
                    //                  
                }else if (tabla=="tabla_deduc") {
                    var id = this.id+sumded;
                    sumded++;
                    idde=id;
                    //
                }else{
                    var id = this.id;
                }                
                return id;
            });
            
            
            if (idco!="") { $tr.find("#"+idco+"").inputmask('Regex', {regex: "^[0-9]{1,20}(\\.\\d{1,2})?$"}); }
            if (idde!="") { $tr.find("#"+idde+"").inputmask('Regex', {regex: "^[0-9]{1,20}(\\.\\d{1,2})?$"}); }


            //.removeAttr()
            if(tabla == 'tabla_comisiones'){
                //console.log("filaa="+fila);
                var fila=0;
                $("#tabla_comisiones tbody tr").each(function (index){
                    fila++;
                    //console.log("filad="+fila);
                });
                var fi=fila-1;
                //console.log("fi="+fi);
                //console.log("fila="+fila);
                $tr.find("#anio_inicio").attr('name', 'anio_inicio['+fi+']');
                $tr.find("#anio_fin").attr('name', 'anio_fin['+fi+']');
                
                $tr.find("#p_comision").attr('name', 'p_comision['+fi+']');
                $tr.find("#p_sobre_comision").attr('name', 'p_sobre_comision['+fi+']');
                
                var inicio = $(evt).parent().parent().parent().find("#anio_fin").val();
                $tr.find("#anio_final").val(Number(inicio)+1);
                $tr.find("#anio_inicio option").remove();
                $tr.find("#anio_inicio option").remove();
                for (i = (Number(inicio)+1); i < 21; i++) {
                    $tr.find('#anio_inicio').append($('<option>', {
                        value: i,
                        text: i
                    }));
                }
                //$(evt).parent().parent().parent().find("#anio_fin").removeAttr('onchange');
                $tr.attr('style', 'padding-top: 10px;');
                $tr.attr('data-num', fila);                
                $tr.find("#p_comision").inputmask('Regex', {regex: "^[0-9]{1,20}(\\,\\d{1,2})?$"});
                $tr.find("#p_sobre_comision").inputmask('Regex', {regex: "^[0-9]{1,20}(\\,\\d{1,2})?$"});
            }
            $('#'+tabla).find("tbody tr:last").after($tr);
            
            
            //$('select option').remove();
        },
        copyTabla: function (tabla1,tabla2) {

            //console.log("tabla1="+tabla1);
            //console.log("tabla2="+tabla2);

            var Clonedtable = $("#"+tabla1).clone();
            Clonedtable.find("input,select").attr("disabled", true);
            Clonedtable.find("a").remove();
            if(tabla1 == 'tabla_comis'){
                Clonedtable.find("select,.th_inicio,.th_fin").remove();
                Clonedtable.find(".th_anio").attr('style', 'width: 20%;');
                /*Clonedtable.find("select").remove();
                Clonedtable.find("select").remove();*/
            }
            $("#"+tabla2).html("");
            $("#"+tabla2).append(Clonedtable);
            $("#mensaje").html("");
            $("#"+tabla2).find("#"+tabla1).removeClass( "col-lg-10" );
            $("#"+tabla2).find("#"+tabla1).addClass( "col-lg-12" );
            $("#"+tabla2).find(".form-group").attr("style", 'margin-bottom: 0px ! important; padding-left: 0px; padding-right: 0px;padding-bottom: 5px;');
            
            if (tabla2=="tabla_final_comisiones") {
                //console.log("aqui");
                var con=0;
                $("#"+tabla2+" tbody tr").each(function (index){
                    con++;
                    $(this).find("#p_comision").inputmask('Regex', {regex: "^[0-9]{1,20}(\\,\\d{1,2})?$"});
                    $(this).find("#p_sobre_comision").inputmask('Regex', {regex: "^[0-9]{1,20}(\\,\\d{1,2})?$"});
                });
            }                     
            
        },
        eliminarfila: function (evt) {
            $(evt).parent().parent().parent().remove();
            var con=0;
            
            $("#tabla_comisiones tbody tr").each(function (index){   
                con++;
                $(this).find("#anio_inicio").attr('name', 'anio_inicio['+con+']');
                $(this).find("#anio_fin").attr('name', 'anio_fin['+con+']');
                $(this).find("#p_comision").attr('name', 'p_comision['+con+']');
                $(this).find("#p_sobre_comision").attr('name', 'p_sobre_comision['+con+']');
                $(this).attr('data-num', (Number(con)));
            });


        },
        anioFin: function (evt) {
            if($(evt).val() == '+'){
                $(".btn-tabla-agregar").hide();
                $(".btn-tabla-eliminar").hide();
                var inicio = $(evt).parent().parent().parent().find("#anio_inicio").val();
                $(evt).parent().parent().parent().find("#anio_final").val(inicio+$(evt).val());
                var indice = $(evt).parent().parent().parent().data("num");
                $("#tabla_comisiones tbody tr").each(function (index){
                    if(($(this).data("num"))>indice){
                        this.remove();
                    }
                });
            }else{
                $(evt).parent().find("#anio_fin-error").remove();;
                var indice = $(evt).parent().parent().parent().data("num");
                var indece2 = $('#tabla_comisiones').find("tbody tr:last").data("num");
                if(indice == indece2){
                    $(".btn-tabla-eliminar").show();
                    $('#tabla_comisiones').find("tbody tr:last").find(".btn-tabla-agregar").show();
                    $('#tabla_comisiones').find("tbody tr:last").find(".btn-tabla-eliminar").hide();
                }else{
                    var indice = $(evt).parent().parent().parent().data("num");
                    var acti = 1;
                    var inicio = $(evt).parent().parent().parent().find("#anio_fin").val();
                    $("#tabla_comisiones tbody tr").each(function (index){
                        
                        if(($(this).data("num"))>indice && acti==1){
                            $(this).find("#anio_inicio option").remove();
                            acti=0;
                            $(this).find("#anio_final").val(Number(inicio)+1);
                            for (i = (Number(inicio)+1); i < 21; i++) {
                                $(this).find('#anio_inicio').append($('<option>', {
                                    value: i,
                                    text: i
                                }));
                            }
                        }
                        
                    });
                }
                
               
            }
            planes.copyTabla('tabla_comis','tabla_final_comisiones');
        },
        anioInicio: function (evt) {
            $(evt).parent().parent().parent().find("#anio_final").val($(evt).val());
            planes.copyTabla('tabla_comis','tabla_final_comisiones');
        },
        eventos: function () {//treeRamos
            planesCrear.codigo_ramo.on("keyup", function (e) {
                var valor = $(this).val();
                var nuevo = valor.toUpperCase();
                $(this).val(nuevo);
            });
            planesCrear.treeRamos.on("click", function (e) {
                e.preventDefault();
                $(".requerido").html("*");
                $('#tipo_interes_ramo').attr('required', true);
                $('#tipo_poliza_ramo').attr('required', true);
            });
            planesCrear.siguiente1.on("click", function (e) {
                e.preventDefault();

                var validaf = $( "#crearplanesForm" ).validate();
               /* $('input[name="nombre_plan').rules(
                   "add",{ required: true, 
                    regex:'^[a-zA-Z0-9áéíóúñ ]+$',
                    //message: "Campo es Alfanumerico"
                });*/
                var n1 = validaf.element( "#nombre_plan" );
                var n2 = validaf.element( "#aseguradora" );
                var n3 = validaf.element( "#impuesto2" );
                var n4 = $("#ramo_plan_final").val();                

                if (n4=="") {
                    $("#ramoplanes-error").show();
                }else{
                    $("#ramoplanes-error").hide();
                }
                
                if(n1==true && n2==true && n3==true && n4!=""){
                    $(segundotab).trigger('click');
                    $( "#tab2-1" ).removeClass( "active" );
                    $( "#tab_aseguradora" ).removeClass( "active" );
                    $( "#tab2-2" ).addClass( "active" );
                    $( "#tab_coberturas" ).addClass( "active" );

                }
                
            });
            planesCrear.siguientedos.on("click", function (e) {
                e.preventDefault();
                $(tercertab).trigger('click');
                $( "#tab2-2" ).removeClass( "active" );
                $( "#tab_coberturas" ).removeClass( "active" );
                $( "#tab2-3" ).addClass( "active" );
                $( "#tab_comision" ).addClass( "active" );
            });



            planesCrear.siguientetres.on("click", function (e) {

                //-----------------------
                //Verificar Cero en Comisiones
                var con = 0;
                var c = 0;
                var arreglo = [];
                var res = "";
                var v = 0;
                var mensaj = "";
                var vacio = 0;
                while(con==0){
                    if (typeof $('input[name="p_comision['+c+']').val() != "undefined") {
                        console.log($('input[name="p_comision['+c+']').val());
                        arreglo.push($('input[name="p_comision['+c+']').val());                        
                    }else{ 
                        if (c>=20) 
                            con=1;                                          
                    }  
                    c++;                  
                }
                console.log(arreglo.length);
                if (arreglo.length>0) {
                    for (var i = 0; i <= arreglo.length - 1; i++) {
                        if (arreglo[i]!="") {
                            if (parseFloat(arreglo[i])>0) 
                                v=1;
                        }else{
                            vacio=1;
                        }                                                
                    }
                    if (vacio==0) {
                        if (v==1) { 
                            res = true; 
                        }else{ 
                            res = false; mensaj = "Alguno de los campos de comision debe ser mayor a cero." ;
                        }
                    }else{
                        res = false;
                        mensaj = "Los campos de comisiones no deben estar vacios";
                    }
                }else{
                    if (arreglo[i]!="") {
                        if (parseFloat(arreglo[0])>0) {
                            mensaj = "El campo de la comision debe ser mayor a cero.";
                            res = false;
                        }else{ res = true; }
                    }else{
                        vacio=1;
                        mensaj = "Los campos de comisiones no deben estar vacios";
                        res = false;
                    }
                    
                }
                //-------------------------------


                $("#p_comision-error").remove();
                e.preventDefault();
                var formValidado = $('#crearplanesForm').validate({
                    focusInvalid: true,
                    ignore: '',
                    wrapper: ''
                });


                /*$('input[name="nombre_plan').rules(
                   "add",{ required: true, 
                    regex:'^[a-zA-Z0-9áéíóúñ ]+$',
                    //message: "Campo es Alfanumerico"
                });*/
                $('input[name="primaneta').rules(
                   "add",{ required: false, 
                    regex2:'^[0-9.]+$',
                    //message: "Campo es Numerico"
                });
                $('input[name="coberturas[]').rules(
                   "add",{ required: false, 
                    regex:'^[a-zA-Z0-9áéíóúñ ]+$',
                    //message: "Campo es Alfanumerico"
                });
                $('input[name="deducibles[]').rules(
                   "add",{ required: false, 
                    regex:'^[a-zA-Z0-9áéíóúñ ]+$',
                   // message: "Campo es Alfanumerico"
                });
                /*$('input[name="coberturasmonet[]').rules(
                   "add",{ required: false, 
                    regex2:'^[0-9][.][0-9]+$',
                    //message: "Campo es Numerico"
                });
                $('input[name="deduciblesmonet[]').rules(
                   "add",{ required: false, 
                    regex2:'^[0-9.]+$',
                    //message: "Campo es Numerico"
                });*/
                console.log(res);
                if (formValidado.form() === true) {
                    if (res==true) {
                        $(cuartotab).trigger('click');
                        $( "#tab2-3" ).removeClass( "active" );
                        $( "#tab_comision" ).removeClass( "active" );
                        $( "#tab2-4" ).addClass( "active" );
                        $( "#tab_confirmar" ).addClass( "active" );
                    }else{
                        toastr.error(mensaj);
                    }                    
                }
            });


            planesCrear.anterior1.on("click", function (e) {
                e.preventDefault();
                $(primertab).trigger('click');
                $( "#tab2-2" ).removeClass( "active" );
                $( "#tab_coberturas" ).removeClass( "active" );
                $( "#tab2-1" ).addClass( "active" );
                $( "#tab_aseguradora" ).addClass( "active" );
            });
            planesCrear.anterior2.on("click", function (e) {
                e.preventDefault();
                $(segundotab).trigger('click');
                $( "#tab2-3" ).removeClass( "active" );
                $( "#tab_comision" ).removeClass( "active" );
                $( "#tab2-2" ).addClass( "active" );
                $( "#tab_coberturas" ).addClass( "active" );
            });
            planesCrear.anterior3.on("click", function (e) {
                e.preventDefault();
                $(tercertab).trigger('click');
                $( "#tab2-4" ).removeClass( "active" );
                $( "#tab_confirmar" ).removeClass( "active" );
                $( "#tab2-3" ).addClass( "active" );
                $( "#tab_comision" ).addClass( "active" );
            });
            planesCrear.ramo_nombre.on("keyup", function (e) {
                $("#nombre_plan_final").val($(this).val());
                $("#mensaje").html("");
            });
            planesCrear.comision.on("keyup", function (e) {
                $("#comision_final").val($(this).val());
            });
            planesCrear.sobre_comision.on("keyup", function (e) {
                $("#sobre_comision_final").val($(this).val());
            });
            planesCrear.aseguradora.on("change", function (e) {//aseguradora_final
                $('#aseguradora_final > option[value="' + $(this).val() + '"]').attr('selected', 'selected');
                $("#mensaje").html("");
            });
            planesCrear.impuesto.on("change", function (e) {
                $('#impuesto_final > option[value="' + $(this).val() + '"]').attr('selected', 'selected');
            });
            planesCrear.ch_comision.on("change", function (e) {//
                $('#ch_comision_final').trigger('click');
                var Cloned = $("#ch_comision_copy").clone();
                $("#ch_comision_paste").html("");
                $("#ch_comision_paste").append(Cloned);
                
            });
        }

    };
    
$(document).ready(function () {
    planes.init();

    //AddValidator
    $.validator.addMethod(
        "regex",
        function(value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Campo es alfanumerico."
    );
    //AddValidator
    $.validator.addMethod(
        "regex2",
        function(value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Campo es Numerico."
    );

    /*$(".jstree-children").click(function(){
        pramohijo=1;
        console.log("click ramo hijo");
    });*/

    $("#tab_ramos").click(function(){
        console.log("click ramos");
        $("#moduloOpciones").show();
    });
    $("#tab_planes").click(function(){
        console.log("click planes");
        $("#moduloOpciones").show();
    });

    $('#cancelarcrearplan').on("click",function() {
     var id_aseguradora=$('#uuid_a').val();
	 var regreso=$('#regreso').val();
     
	 if(regreso=='aseg')
	 {
		 window.location.href = phost()+'aseguradoras/editar/'+id_aseguradora+'';   
	 }
     else
	 {
		 window.location.href = phost()+'catalogos/ver/planes'; 
	 }
				 
 });
    
    $("#volver").click(function(){
        var uuid_asegura = $("#uuid_aseguradora").val();
		var regreso=$('#regreso').val();
		if(regreso=='aseg')
		{
			window.location.href =(phost() + "aseguradoras/editar/"+uuid_asegura+"");
		}
		else
		{
			window.location.href = phost()+'catalogos/ver/planes'; 	
		}
			
    });
    
    //popular desde asegurados
    if(typeof vista !== 'undefined'){
        if(vista == 'planes-editar' || vista == 'planes-ver' ) {
                
              var data_plan = JSON.parse(data_planes);
              $('#tab_ramos').removeClass( "active" );  
              $('#tab_planes').addClass( "active" );
              $('#tab-1').removeClass( "active" );  
              $('#tab-3').addClass( "active" ); 
              if(data_plan.desc_comision =='SI'){
                  $('#ch_comision').trigger('click') ;
                  $('#ch_comision_final').trigger('click') ;
              }
              $("#treeRamos2").bind("loaded.jstree",function(e,data){

                data.instance.select_node(data_plan.id_ramo);
              });
              new Vue({
                el: '#crearplanesForm',
                data: {
                    nombre_plan: data_plan.nombre,
                    aseguradora: data_plan.id_aseguradora,
                    impuesto: data_plan.id_impuesto,
                    ch_comision: (data_plan.desc_comision =='SI')? true : false,
                    nombre_plan_final:data_plan.nombre,
                    aseguradora_final: data_plan.id_aseguradora,
                    impuesto_final: data_plan.id_impuesto,
                    comision_final: data_plan.comision,
                    sobre_comision_final: data_plan.sobre_comision,
                    id_planes: id_planes
                }
              });
              planes.copyTabla("tabla_planes","tabla_final");
              planes.copyTabla('tabla_comis','tabla_final_comisiones');
              if(vista == 'planes-ver'){
                    $(cuartotab).trigger('click');
                    $( "#tab2-1" ).removeClass( "active" );
                    $( "#tab_aseguradora" ).removeClass( "active" );
                    $( "#tab2-4" ).addClass( "active" );
                    $( "#tab_confirmar" ).addClass( "active" );
                    planesCrear.anterior3.remove();
                    
              }

        }else if(vista == 'planes-crear' ){
            $('#tab_ramos').removeClass( "active" );  
            $('#tab_planes').addClass( "active" );
            $('#tab-1').removeClass( "active" );  
            $('#tab-3').addClass( "active" );
            new Vue({
                el: '#crearplanesForm',
                data: {
                    aseguradora: id_aseguradora,
                    aseguradora_final: id_aseguradora,
                    vista:vista
                }
              });
        }
    }

    planes.copyTabla('tabla_comis','tabla_final_comisiones');
    planes.copyTabla('tabla_planes','tabla_final');

});

    

bluapp.controller("configPlanesController", function ($scope, $http) {
    var vista = {
        formPlanes: $('#crearplanesForm')
    };

    $scope.cargar_tree_planes = function () {
        
        var cuentas = moduloAseguradora.listarRamosTree();
        cuentas.success(function () {
            $("#treeRamosP").jstree("destroy");
        });
        cuentas.done(function (data) {
            var arbol = jQuery.parseJSON(data);
            var hijos = [];


            $('#treeRamosP').jstree(arbol).bind("select_node.jstree", function (e, data) {  

                    var nodo = data.node;
                    var nodo_id = nodo.id;
                    $('#idRamo').val(nodo_id);
                    var i = 0;
                    var nombre = []
                    for (i in data.node.parents) {
                        
                        if (data.node.parents[i] != "#") {
                            //console.log("n="+$('#treeRamosP').find("#" + data.node.parents[i] + "_anchor").find("#labelramo")[0].lastChild.nodeValue);
                            nombre.push($('#treeRamosP').find("#" + data.node.parents[i] + "_anchor").find("#labelramo")[0].lastChild.nodeValue);
                        }
                    }
                    console.log("hijos="+nombre);
                    var labelNombre = "";
                    for (i = nombre.length - 1; i >= 0; i = i - 1) {
                        labelNombre += nombre[i] + "/";
                        //console.log(nombre[i]);
                    }
                    
                    labelNombre += " "+$(data.node.text).find("#labelramo").prevObject[0].innerText;
                    console.log(labelNombre);
                    if(labelNombre.indexOf('/') != -1){
                        $("#ramo_plan_final").val(labelNombre);
                    }else{
                        $("#ramo_plan_final").val("");
                    }                    
                });
            $('#treeRamosP').jstree(true).redraw(true);

            $("#treeRamosP").bind("loaded.jstree",function(e,data){
                if (typeof id_ramo_plan!="undefined") {
                    console.log(id_ramo_plan);
                    data.instance.select_node(id_ramo_plan);
                }               
            });
            
        }); // fin del done
    };

    $scope.guardarPlanes = function () {

        var formValidado = vista.formPlanes.validate();
        if (formValidado.form() === true) {
            vista.formPlanes.submit();
        } else {
            //console.log("ERROOOOOR");
            $("#mensaje").html("");
            $("#mensaje").append('<div class="alert alert-dismissable alert-danger" >\n\
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>\n\
            Debe completar el formulario </div>');
        }

    };


    $scope.inicializar_plugin = function () {
        vista.formPlanes.validate({
            focusInvalid: true,
            ignore: ".ignore",
            wrapper: '',
        });
    };
    $scope.inicializar_plugin();
    $scope.cargar_tree_planes();
});

