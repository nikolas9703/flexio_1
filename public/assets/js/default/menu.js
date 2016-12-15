function vhost()
{
    return window.phost();

}

$(function(){

	$.post( vhost() + "menu/navbar",
		$.extend({erptkn: tkn}, {}
	)).done(function(data){
        var menus = $.parseJSON(data);
        $('.navbar-top-menu').empty();
        $.each(menus, function(i, menu){
			$('.navbar-top-menu').append('<li class="dropdown"><a data-toggle="dropdown" class="dropdown-toggle" href="#" role="button" aria-expanded="false" data-grupo="'+ menu["grupo"] +'"> '+ menu["nombre"] +'</a></li>');
		});
    });

	$('.navbar-top-menu').on('click', 'a', function(e){

		$.post( vhost() + "menu/sidebar",
			$.extend({erptkn: tkn}, {}
		)).done(function(data){

			var menus = $.parseJSON(data);
		    $('.sidebar').empty();
		    $.each(menus, function(i, menu){
				$('.navbar-top-menu').append('<li class="dropdown"><a data-toggle="dropdown" class="dropdown-toggle" href="#" role="button" aria-expanded="false" data-grupo="'+ menu["grupo"] +'"> '+ menu["nombre"] +'</a></li>');
			});

		});
	});

});
