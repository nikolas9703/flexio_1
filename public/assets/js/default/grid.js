$(function(){
	
 	//-------------------------
	// Boton de opciones
	//-------------------------
	$("#iconGrid").on("click", ".viewOptionsGrid", function(e){
 		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		var titulo = $(this).attr("data-nombre");
                console.log("titulo"+titulo);
		var options = $('#menu'+this.id).html();

 	    //Init boton de opciones
		$('#opcionesModal').find('.modal-title').empty().append('Opciones: '+ titulo +'');
		$('#opcionesModal').find('.modal-body').empty().append(options);
		$('#opcionesModal').find('.modal-footer').empty();
		$('#opcionesModal').modal('show');
	});
	
});


//-------------------------------------
// Inicializar Tags Input de búsqueda del Grid
//-------------------------------------
var TagInput = $('[data-role="tags"]').tagsinput({
	tagClass: 'label label-grey'	
});

/* Agregar un atributo custom a los option que se
 * van agregando, para diferenciar la data que se agregan
 * inline desde el formulario de la data que viene
 * de la DB.
 */
var band=0;
$('.bootstrap-tagsinput').on('itemAdded', function(event) {
    var campo = $("#selectBuscar").val();
    var string = event.item.toLowerCase();
    var muestra=0;
    
    $(".vcard").each(function() {
        var outer = $(this);
        var encontrado = 0;
        
        outer.find("."+campo).each(function(){
            encontrado+= 1;
            
        });
        
        if(encontrado < 1 || $(this).find("."+campo).attr("data-value").search(string)<0){
            if(band==0 ){
                outer.fadeOut().addClass("hidden");
            }
            
        }
        else
        {
            muestra++;
            outer.fadeIn().removeClass("hidden");
            $(this).parent().show();
        } 
    });
    if(muestra>0){
        band++;
    }

});

$('.bootstrap-tagsinput').on('itemRemoved', function(event) {
    
    
    var campo=$("#selectBuscar").val();
    var string=event.item.toLowerCase();

    $(".vcard").each(function() {
        var outer = $(this);
        var encontrado = 0;
        
        outer.find("."+campo).each(function(){
            encontrado+= 1;
        });
        
        if(encontrado < 1 || $(this).find("."+campo).attr("data-value").search(string)<0){
            if(band==1 || band== 0){
                outer.fadeIn().removeClass("hidden");
            }
        }else{
            if(band>1){
                outer.fadeOut().addClass("hidden");
            }
        }
    });
    if(band>0){
        band--;
    }

});


$('#selectBuscar').on('change', function(event) {
    
    //Esta funcion genera error
    //$('.bootstrap-tagsinput').tagsinput('removeAll');
    $(".tag").find("span").each(function(){
        $(this).click();
        
    });
    
    $(".vcard").each(function() {
        $(this).fadeIn().removeClass("hidden");

    });

});

size_li = $("#myList li").size();
x=3;
$('#myList li:lt('+x+')').show();
$('#loadMore').click(function () {
    x= (x+5 <= size_li) ? x+5 : size_li;
    $('#myList li:lt('+x+')').show();
});
$('#showLess').click(function () {
    x=(x-5<0) ? 3 : x-5;
    $('#myList li').not(':lt('+x+')').hide();
});
