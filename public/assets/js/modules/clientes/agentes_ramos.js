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


});

