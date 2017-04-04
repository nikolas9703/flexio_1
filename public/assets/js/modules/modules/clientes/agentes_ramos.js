function remover_ramos(index, i){
	$("#ramosagentes_"+index+"_"+i).remove();

	var x = 0;
	$(".ramosagentes_"+index+"").each(function(){
		$(this).attr("id", "ramosagentes_"+index+"_"+x);
		x = x + 1;
	});

	var m = 0;
	$(".select_ramo_"+index+"").each(function(){
		$(this).attr("id", "ramos_"+index+"_"+m);
		$(this).attr("name", "ramos_agentes["+index+"]["+m+"]");
		m = m + 1;
	});

	var j = 0;
	$(".select_ramo_h_"+index+"").each(function(){
		$(this).attr("id", "ramos_h_"+index+"_"+j);
		$(this).attr("name", "ramos_agentes_h["+index+"]["+j+"]");
		j = j + 1;
	});

	var n = 0;
	$(".input_participacion_"+index+"").each(function(){
		$(this).attr("id", "porcentajes_"+index+"_"+n);
		$(this).attr("name", "porcentajes_agentes["+index+"]["+n+"]");
		n = n + 1;
	});

	var y = 1;
	$(".btnramos_"+index+"").each(function(){
		console.log("btnramos");
		var n = $(this).attr("onclick", "remover_ramos("+index+", "+y+")");
		console.log(n);
		y = y + 1;
	});

	deshabilita2(index);
}

function deshabilita2 (index){
	var num = [];
    $(".select_ramo_"+index+"").each(function(){
        if ($(this).val() != "" && $(this).val() != null) {
            $.each( $(this).val(), function(key, value){
                num.push(value);                
            });
        }     
    });

    console.log(index);
    console.log(num);

    $(".select_ramo_"+index+"").each(function(){
        var valor = $(this).val();
        $("option", this).each(function(){
            $(this).removeAttr("disabled");
            if ($.inArray($(this).attr('value'), num)>=0) {   
                //console.log($(this).attr('value')); 
                if ($.inArray($(this).attr('value'), valor)<0) {
                    $(this).attr("disabled", "disabled");
                    var y = $(this).attr('data-index');
                }            
            }
        });        
    });

}

$(document).ready(function(){
	$(".iniramo").select2();

	$(".agtramosdiv").each(function(){
		$(this).find(".select2-container").each(function(){
			$(this).find(".select2-selection").css({"overflow": "scroll", "height": "100px", "overflow-x": "hidden"}); 
		});
	});
	


	if (localStorage.getItem("ms-selected") == "seguros") {

		if (vista == "crear") {
			$("#campo_estado").val("activo").trigger("change");
			$("#centro_fact_dir").text("Direcciones");
			$("#centro_fact_small").text("");
			$("#centro_fact_label").text("Nombre de referencia");
		}else if(vista == "ver"){
			$("#tab_Clientes").text("Direcciones");
			$("#agregarCentroFacturacionBtn").text("Agregar Direcciones");
			$("#centro_fact_dir").text("Direcciones");
			$("#centro_fact_small").text("");
			$("#centro_fact_label").text("Nombre de referencia");
			$("#tab_Oportunidades").hide();
			$("#tab_Cotizaciones").hide();
			$("#tab_Anticipos").hide();
		}

		$("#correo_cliente").removeAttr("data-rule-required");
        $("#tipo_correo_cliente").removeAttr("data-rule-required");
        $("#span_correo").remove();

		$("#div_lista_precio_venta").hide();
		$("#div_lista_precio_alquiler").hide();
		$("#div_lista_terminos_pago").hide();
		$("#info_pago_small").hide();
		$("#div_limite_ventas").hide();
		
	}

	$("#coll_agt").click(function(){
		$('.iniramo').select2();
		$(".agtramosdiv").each(function(){
			$(this).find(".select2-container").each(function(){
				$(this).find(".select2-selection").css({"overflow": "scroll", "height": "100px", "overflow-x": "hidden"}); 
			});
		});
		
	});

});

