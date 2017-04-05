$(function () {

    //Ejecutar al remover un permiso ya existente.
    $('#roleForm').on('change', '.remove_perm', function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        //Set delete form
        $('#deletePermisoForm').find('#permiso').prop("name", this.name).prop("value", this.value);

        $.ajax({
            url: phost() + 'roles/ajax-eliminar-permiso',
            data: $('#deletePermisoForm').serialize(),
            type: "POST",
            dataType: "json",
            cache: false,
        }).done(function (json) {

            //Check Session
            if ($.isEmptyObject(json.session) == false) {
                window.location = phost() + "login?expired";
            }

            //If json object is not empty.
            if ($.isEmptyObject(json.results[0]) == false) {
            } else {
            }

            $('#deletePermisoForm').find('#permiso').prop("name", '').prop("value", '');
        });

    });
});

bluapp.controller("navBarMenuCtrlPermisos", function ($scope, $cookies, $localstorage) {

    $scope.menu_lateral_seleccionado = $localstorage.get('ml-selected') !== '' ? $localstorage.get('ml-selected') : $cookies.get('ml-selected');
    $scope.menu_lateral_navsecond = $localstorage.get('ml-parent-selected') !== '' ? $localstorage.get('ml-parent-selected') : $cookies.get('ml-parent-selected');

    /**
     * Abrir/Cerrar sub menus del Menu Lateral
     */
    $scope.collapse = function (e) {
        e.preventDefault();
        var href = $(e.target).attr('href') !== undefined ? $(e.target).attr('href') : '';

        //Verificar si el enlace seleccionado contiene un sub-menu
        //o es un enlace directo.
        if (href.match(/http:/g)) {

            //----------------------------------------
            // Guardar seleccion de menu en cookies
            // y localStorage
            //----------------------------------------

            //Si la seleccion del menu superior marca undefined
            //Verificar la seleccion dentro del dropdown "tabdrop"
            if ($('.navtop-menu').find('li.active').find('a').attr('data-grupo') === undefined) {

                //Establecer $cookie seleccion de menu superior
                $cookies.put('ms-selected', $('.navtop-menu').find('li.tabdrop.active').find('ul').find('li.active').find('a').attr('data-grupo'));
                $localstorage.set('ms-selected', $('.navtop-menu').find('li.tabdrop.active').find('ul').find('li.active').find('a').attr('data-grupo'));
            } else {
                //Establecer $cookie seleccion de menu superior
                $cookies.put('ms-selected', $('.navtop-menu').find('li.active').find('a').attr('data-grupo'));
                $localstorage.set('ms-selected', $('.navtop-menu').find('li.active').find('a').attr('data-grupo'));
            }

            //Establecer $cookie seleccion de menu lateral
            $cookies.put('ml-selected', $.trim($(e.target).text()));
            $cookies.put('ml-parent-selected', $.trim($(e.target).closest('ul.nav-second-level').closest('li').find('a').first().text()));

            $localstorage.set('ml-selected', $.trim($(e.target).text()));
            $localstorage.set('ml-parent-selected', $.trim($(e.target).closest('ul.nav-second-level').closest('li').find('a').first().text()));

            //Redireccionar hachia el url
            setTimeout(function () {
                window.location.href = href;
            }, 100);
        }

        if ($(e.target).closest('li').has('ul').children('a')) {
            $(e.target).closest('li').toggleClass('active').children('ul').collapse('toggle');
        }
    };

});
/*
 * Menu de Permisos
 */
/**
 * Controlador Menu Superior
 */
bluapp.controller("navBarMenuCtrlPermisos", function ($scope, $http, $rootScope, $cookies, $localstorage) {

    var url = window.phost() + "roles/navbar";
    /**
     * Desplegar Menu Superior
     */

    $http({
        method: 'POST',
        url: url,
        data: $.param({
            erptkn: tkn,
        }), // pass in data as strings
        cache: false,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function (results) {
        if (results.data) {
            $scope.menus1 = results.data;
        }
    });


    $scope.menu_superior_seleccionado1 = $localstorage.get('ms-selected') !== '' ? $localstorage.get('ms-selected') : $cookies.get('ms-selected');

    //Si existe el cookie del menu superior seleccionado
    //Desplegar menu lateral.

    if ($scope.menu_superior_seleccionado1 !== "") {
        setTimeout(function () {
            $scope.menu_lateral();
        }, 100);
    }

    /**
     * Buscar en DB el menu lateral, segun menu superior seleccionado.
     */
    $scope.menu_lateral = function (e) {

        var url = window.phost() + "roles/sidebar";
        $http({
            method: 'POST',
            url: url,
            data: $.param({
                erptkn: tkn,
                grupo: e !== undefined && $(e.target).attr('data-grupo') !== "" ? $(e.target).attr('data-grupo') : $scope.menu_superior_seleccionado

            }),
            cache: false,
            xsrfCookieName: 'erptknckie_secure',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (results) {
            $rootScope.sidemenu1 = results.data;

        });

    };


    /**
     * Cambiar opciones del menu lateral.
     */

    $scope.togglemenu1 = function (e) {
        e.preventDefault();

        //Primero desmarxar todos las lista
        $(e.target).closest('ul').find('li').removeClass('active');

        //Luego marcar la que acaba de ser seleccionada.
        $(e.target).closest('li').addClass('active');

        $scope.menu_lateral(e);
    };


    $scope.listarPermisos = function (id, controlador) {

        var url = window.phost() + "roles/listarPermisos";
        $http({
            method: 'POST',
            url: url,
            data: $.param({
                erptkn: tkn,
                grupo: ""

            }),
            cache: false,
            xsrfCookieName: 'erptknckie_secure',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (results) {
            console.log(results);

        });

    };

    /**
     * Logout Event
     */

    $('.navtop-menu').tabdrop();
});
$("body").on("click", ".editarPermRoles", function () {
    var href = $(this).data('href');
    var id = $(this).data('id');
    var controlador = $(this).data('controlador');
    if (href != "" && controlador != "") {
        $.ajax({
            url: window.phost() + "roles/listarPermisos",
            type: 'post',
            dataType: 'json',
            data: {
                controlador: controlador,
                 erptkn: tkn,
                modulo_id: id
            },
            success: function (datos) {
                console.log(datos);
            }
        });
    }
})
