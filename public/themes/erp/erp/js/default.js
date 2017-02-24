$(function() {

    //Helio Menu
	if($().helio) {
		$('#side-menu').helio();
	}

	// Add body-small class if window less than 768px
    if ($(this).width() < 769) {
        $('body').addClass('body-small')
    } else {
        $('body').removeClass('body-small')
    }


    // Collapse ibox function
    $('.collapse-link').click( function() {
        var ibox = $(this).closest('div.ibox');
        var button = $(this).find('i');
        var content = ibox.find('div.ibox-content');
        content.slideToggle(200);
        button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
        ibox.toggleClass('').toggleClass('border-bottom');
        setTimeout(function () {
            ibox.resize();
            ibox.find('[id^=map-]').resize();
        }, 50);
    });

    //Botones del Breadcrumb
    if($("#breadcrumbBtns").attr("id") != undefined)
    {
    	$("#breadcrumbBtns a").on('click', function(e) {
    		$("#breadcrumbBtns").find("a").removeClass("active");
    	    $(this).addClass("active");

    	    //Redimensionar vista de tabla
    	    if($(this).attr('href') == '#tabla'){

    	    	setTimeout(function(){
	    	    	$(".ui-jqgrid").each(function(){
	    				var w = parseInt( $(this).parent().width()) - 6;
	    				var tmpId = $(this).attr("id");
	    				var gId = tmpId.replace("gbox_","");
	    				$("#"+gId).setGridWidth(w);
	    			});
    	    	}, 500);
    	    }
    	});
    }

    // Close ibox function
    $('.close-link').click( function() {
        var content = $(this).closest('div.ibox');
        content.remove();
    });

    // Small todo handler
    $('.check-link').click( function(){
        var button = $(this).find('i');
        var label = $(this).next('span');
        button.toggleClass('fa-check-square').toggleClass('fa-square-o');
        label.toggleClass('todo-completed');
        return false;
    });

    // minimalize menu
    $('.navbar-minimalize').click(function () {
    	$("body").toggleClass("mini-navbar");
        SmoothlyMenu();
    });

    // tooltips
    $('.tooltip-demo').tooltip({
        selector: "[data-toggle=tooltip]",
        container: "body"
    });


    //$('.wrapper-content').after('<div class="content-loading"><img src="'+ phost() +'public/themes/erp/images/preloader.gif" border="0" alt="Cargando..." /></div>');
    //$('.content-loading').center();

    //Cuando el plugin "pace"
    //termine de cargar
    Pace.on('hide', function(){

		// - Mostrar Sidebar
		// - Mostrar contenido
		$('nav.navbar-static-side, .wrapper-content').removeClass('hide');
	});

    // Move modal to body
    // Fix Bootstrap backdrop issu with animation.css
    $('.modal').appendTo("body");

    // Full height of sidebar
    function fix_height() {
        var heightWithoutNavbar = $("body > #wrapper").height() - 61;
        $(".sidebard-panel").css("min-height", heightWithoutNavbar + "px");
    }
    fix_height();

    // Fixed Sidebar
    // unComment this only whe you have a fixed-sidebar
            //    $(window).bind("load", function() {
            //        if($("body").hasClass('fixed-sidebar')) {
            //            $('.sidebar-collapse').slimScroll({
            //                height: '100%',
            //                railOpacity: 0.9,
            //            });
            //        }
            //    })

    $(window).bind("load resize scroll", function () {
        if (!$("body").hasClass('body-small')) {
            fix_height();
        }
    });

    $("[data-toggle=popover]").popover();

    //------------------------------------------------------------
    // jQuery Idle Timer Plugin
    // Provides you a way to monitor user activity with a page.
    //------------------------------------------------------------
   /* pTitle = $(document).attr('title');
    var inacSess = {
        //Logout Settings
        inactiveTimeout: 1800000,   //(ms) The time until we display a warning message. 30 min)
        warningTimeout: 60000,    //(ms) The time until we log them out (1min)
        minWarning: 60000,        //(ms) If they come back to page (on mobile), The minumum amount, before we just log them out
        warningStart: null,       //Date time the warning was started
        warningTimer: null,       //Timer running every second to countdown to logout
        logout: function () {     //Logout function once warningTimeout has expired
            window.location = phost() + 'login/logout';
        },

        //Keepalive Settings
        keepaliveUrl: phost() + 'login/checksess',
        keepaliveTimer: null,
        keepaliveInterval: 1200000, //(ms) the interval to call said url. 20 min
        keepAlive: function () {
             $.ajax({
                url: inacSess.keepaliveUrl,
                data: {erptkn: tkn},
                type: "POST",
                dataType: "json",
                cache: false,

            });
        }
    };*/

    /*$(document).on("idle.idleTimer", function (event, elem, obj) {

        //Get time when user was last active
        var diff = (+new Date()) - obj.lastActive - obj.timeout,
        warning = (+new Date()) - diff;

        //On mobile js is paused, so see if this was triggered while we were sleeping
        if (diff >= inacSess.warningTimeout || warning <= inacSess.minWarning) {
            window.location = phost() + 'login/logout';
        }else {
            //Show dialog, and note the time
            $('#sessionSecondsRemaining').html(Math.round((inacSess.warningTimeout - diff) / 1000));

            $("#inacModal").modal({
				backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
				show: false
			}).modal("show");

            inacSess.warningStart = (+new Date()) - diff;

            //Update counter downer every second
            inacSess.warningTimer = setInterval(function () {
                var remaining = Math.round((inacSess.warningTimeout / 1000) - (((+new Date()) - inacSess.warningStart) / 1000));
                if (remaining >= 0) {
                    $('#sessionSecondsRemaining').html(remaining);
                    if(remaining % 2 == 0) {
                        $(document).attr('title', remaining +' segundos para desconectarte');
                    }else{
                        $(document).attr('title', '...');
                    }
                } else {
                    inacSess.logout();
                }
            }, 1000);
        }
    });

    inacSess.keepaliveTimer = setInterval(function () {
        inacSess.keepAlive();
    }, inacSess.keepaliveInterval);*/

    $("#extendSession").click(function () {
        clearTimeout(inacSess.warningTimer);
        $(document).attr('title',pTitle);
    });
    $("#logoutSession").click(function () {
        inacSess.logout();
    });

    /*if($().idleTimer) {
    	$(document).idleTimer(inacSess.inactiveTimeout);
    }*/
});

function phost()
{
    if(window.flexio.serveUrl ==='undefined'){
        return console.log('debe de crear el archivo config.js');
    }

    return window.flexio.serveUrl;
}

// For demo purpose - animation css script
function animationHover(element, animation){
    element = $(element);
    element.hover(
        function() {
            element.addClass('animated ' + animation);
        },
        function(){
            //wait for animation to finish before removing classes
            window.setTimeout( function(){
                element.removeClass('animated ' + animation);
            }, 2000);
        });
}

//Minimalize menu when screen is less than 768px
$(window).bind("resize", function () {
    if ($(this).width() < 769) {
        $('body').addClass('body-small')
    } else {
        $('body').removeClass('body-small')
    }
});

function SmoothlyMenu() {

	if (!$('body').hasClass('mini-navbar') || $('body').hasClass('body-small')) {
        // Hide menu in order to smoothly turn on when maximize menu
        $('#side-menu, #empresaMenu').hide();
        // For smoothly turn on menu
        setTimeout(function () {
                $('#side-menu').fadeIn(500);
        }, 100);
    } else if ($('body').hasClass('fixed-sidebar')) {
        $('#side-menu, #empresaMenu').hide();
        setTimeout(function(){
             $('#side-menu, #empresaMenu').fadeIn(500);
        }, 300);
    } else {
        // Remove all inline style from jquery fadeIn function to reset menu state
        $('#side-menu, #empresaMenu').removeAttr('style');
    }
}

// Dragable panels
function WinMove() {
    var element = "[class*=col]";
    var handle = ".ibox-title";
    var connect = "[class*=col]";
    $(element).sortable(
        {
            handle: handle,
            connectWith: connect,
            tolerance: 'pointer',
            forcePlaceholderSize: true,
            opacity: 0.8,
        })
        .disableSelection();
};
