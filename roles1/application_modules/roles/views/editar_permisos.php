<div id="wrapper">
<?php
function findKey($keySearch, $array) {
	foreach ($array AS $key => $item){
		if(!empty($item[$keySearch])){
			return $key;
		}
	}
	return false;
}

Template::cargar_vista('sidebar');
?>
    <div id="page-wrapper" class="row">

	    <?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
	    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

	    <div class="col-lg-12">
	        <div class="wrapper-content">
	            <div class="row">

	             <div class="alert alert-success alert-dismissable message-box <?php echo !empty($message) ? 'show' : 'hide'  ?>">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                    <?php echo !empty($message) ? $message : ''  ?>
                </div>

                <!-- White Background -->

					<?php
					$formAttr = array(
						'method'        => 'post',
						'id'            => 'roleForm',
						'autocomplete'  => 'off'
					);
					echo form_open(base_url(uri_string()), $formAttr);
  					?>


					<!--<p>Asigne los permisos al modulo o modulos que le quiera dar acceso a este rol.</p>-->

					<!-- BEGIN ACORDEON -->
					<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
		
        <div id="navbar" ng-controller="navBarMenuCtrlPermisos">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
			<ul class="nav nav-tabs">
				<li ng-repeat="m in menus1 track by $index" class="{{menu_superior_seleccionado1 == m.grupo ? 'active' : ''}}"><a ng-href="#" ng-click="togglemenu1($event)" data-grupo="{{m.grupo}}" role="button" ng-cloak=""><i class="fa {{m.icono}}"></i> {{m.nombre}}</a></li>
			</ul>
	</div>
            <div class="col-md-12">
            <div class="col-md-3 ">
                 <div class="sidebar-collapse ng-cloak" style="overflow: auto; position:relative; clear:both; width: auto; height: 100%;">
		<ul class="nav show ng-cloak" id="side-menu" ng-controller="navBarMenuCtrlPermisos">
			<li ng-repeat="nav in sidemenu1 track by $index" class="{{menu_lateral_navsecond == nav.nombre || menu_lateral_seleccionado == nav.nombre ? 'active' : ''}}">
				<a  class="editarPermRoles" data-id="{{navthird.id}}" data-controlador="{{nav.controlador}}" data-href="{{nav.navsecond && '#' || nav.url}}" ng-click="collapse($event)" ng-cloak><i class="fa fa-sitemap hide"></i> {{nav.nombre}} <span class="fa arrow" ng-show="nav.navsecond"></span></a>
				<ul class="nav nav-second-level {{menu_lateral_navsecond == nav.nombre || menu_lateral_seleccionado == nav.nombre ? 'in' : ''}}" ng-if="nav.navsecond">
					<li ng-repeat="navsecond in nav.navsecond" class="{{menu_lateral_seleccionado == navsecond.nombre ? 'active' : ''}}">
						<a class="editarPermRoles" data-href="{{navsecond.navthird && '#' || navsecond.url}}" ng-click="collapse($event)" ng-cloak data-id="{{navsecond.id}}" data-controlador="{{navsecond.controlador}}">{{navsecond.nombre}} <span class="fa arrow" ng-show="navsecond.navthird"></span></a>
						<ul class="nav nav-third-level  {{menu_lateral_navsecond == navsecond.nombre || menu_lateral_seleccionado == navsecond.nombre ? 'in' : ''}}" ng-show="navsecond.navthird" ng-if="navsecond.navthird">
							<li ng-repeat="navthird in navsecond.navthird"><a data-id="{{navthird.id}}" data-controlador="{{navthird.controlador}}" class="editarPermRoles" data-href="{{navthird.url}}" ng-click="collapse($event)" ng-cloak>{{navthird.nombre}}</a></li>
						</ul>
					</li>
				</ul>
			</li>
		</ul>
	</div>
        <!--
                <div class="sidebar-collapse ng-cloak" style="overflow: auto; position:relative; clear:both; width: auto; height: 100%;">
		<ul class="nav show ng-cloak" id="side-menu" ng-controller="navBarMenuCtrlPermisos">
			<li ng-repeat="nav in sidemenu1 track by $index" class="{{menu_lateral_navsecond == nav.nombre || menu_lateral_seleccionado == nav.nombre ? 'active' : ''}}">
				<a data-id="{{navthird.id}}" data-controlador="{{nav.controlador}}" href="{{nav.navsecond && '#' || nav.url}}" ng-click="collapse($event)" ng-cloak><i class="fa fa-sitemap hide"></i> {{nav.nombre}} <span class="fa arrow" ng-show="nav.navsecond"></span></a>
				<ul class="nav nav-second-level {{menu_lateral_navsecond == nav.nombre || menu_lateral_seleccionado == nav.nombre ? 'in' : ''}}" ng-if="nav.navsecond">
					<li ng-repeat="navsecond in nav.navsecond" class="{{menu_lateral_seleccionado == navsecond.nombre ? 'active' : ''}}">
						<a href="{{navsecond.navthird && '#' || navsecond.url}}" ng-click="collapse($event)" ng-cloak data-id="{{navsecond.id}}" data-controlador="{{navsecond.controlador}}">{{navsecond.nombre}} <span class="fa arrow" ng-show="navsecond.navthird"></span></a>
						<ul class="nav nav-third-level  {{menu_lateral_navsecond == navsecond.nombre || menu_lateral_seleccionado == navsecond.nombre ? 'in' : ''}}" ng-show="navsecond.navthird" ng-if="navsecond.navthird">
							<li ng-repeat="navthird in navsecond.navthird"><a data-id="{{navthird.id}}" data-controlador="{{navthird.controlador}}" href="{{navthird.url}}" ng-click="collapse($event)" ng-cloak>{{navthird.nombre}}</a></li>
						</ul>
					</li>
				</ul>
			</li>
		</ul>
	</div>
        -->
            </div><div class="col-md-9" id="divPermisos">
            <div class="panel-body">
                                                                    <h4><i class="fa fa-info-circle"></i>&nbsp;Permisos generales <small></small></h4>
                                                                    <div class="table-responsive">
                                                                        <table class="table table-bordered">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Listar </th>
                                                                                    <th>Crear </th>
                                                                                    <th>Ver </th>
                                                                                    <th>Editar </th>
                                                                                    <th>Exportar </th>
                                                                                    <th>Imprimir </th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green checked" style="position: relative;"><input type="checkbox" class="i-checks" checked="" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green checked" style="position: relative;"><input type="checkbox" class="i-checks" checked="" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green checked" style="position: relative;"><input type="checkbox" class="i-checks" checked="" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green" style="position: relative;"><input type="checkbox" class="i-checks" id="client_edit_selectall" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green checked" style="position: relative;"><input type="checkbox" class="i-checks" checked="" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green" style="position: relative;"><input type="checkbox" class="i-checks" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                    <br>
                                                                    <h4><i class="fa fa-edit"></i>&nbsp;Campos del módulo <small>Seleccione los campos para ver &amp; editar</small></h4>
                                                                    <div class="table-responsive">
                                                                        <table class="table table-bordered">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Campo </th>
                                                                                    <th>Ver </th>
                                                                                    <th>Editar </th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td>
                                                                                        <h4>Nombre del cliente</h4></td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green checked" style="position: relative;"><input type="checkbox" class="i-checks" checked="" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green" style="position: relative;"><input type="checkbox" class="i-checks edit_icheck" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        <h4>Saldo por cobrar</h4></td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green checked" style="position: relative;"><input type="checkbox" class="i-checks" checked="" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green checked disabled" style="position: relative;"><input type="checkbox" class="i-checks" disabled="" checked="" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        <h4>Crédito a favor</h4></td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green" style="position: relative;"><input type="checkbox" class="i-checks" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green checked disabled" style="position: relative;"><input type="checkbox" class="i-checks" disabled="" checked="" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        <h4>Identificación</h4></td>
                                                                                    <td style="text-align: center; vertical-align: middle;" checked="">
                                                                                        <div class="icheckbox_square-green checked" style="position: relative;"><input type="checkbox" class="i-checks" checked="" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green" style="position: relative;"><input type="checkbox" class="i-checks edit_icheck" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        <h4>Teléfono</h4></td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green checked" style="position: relative;"><input type="checkbox" class="i-checks" checked="" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green" style="position: relative;"><input type="checkbox" class="i-checks edit_icheck" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        <h4>Correo electrónico</h4></td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green checked" style="position: relative;"><input type="checkbox" class="i-checks" checked="" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green" style="position: relative;"><input type="checkbox" class="i-checks edit_icheck" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        <h4>Toma de contacto</h4></td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green checked" style="position: relative;"><input type="checkbox" class="i-checks" checked="" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green" style="position: relative;"><input type="checkbox" class="i-checks edit_icheck" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        <h4>Tipo de cliente</h4></td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green checked" style="position: relative;"><input type="checkbox" class="i-checks" checked="" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green" style="position: relative;"><input type="checkbox" class="i-checks edit_icheck" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        <h4>Categoría de cliente</h4></td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green checked" style="position: relative;"><input type="checkbox" class="i-checks" checked="" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green" style="position: relative;"><input type="checkbox" class="i-checks edit_icheck" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        <h4>Límite de crédito de ventas</h4></td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green" style="position: relative;"><input type="checkbox" class="i-checks" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green" style="position: relative;"><input type="checkbox" class="i-checks edit_icheck" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td><h4>Observaciones</h4></td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green checked" style="position: relative;"><input type="checkbox" class="i-checks" checked="" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green" style="position: relative;"><input type="checkbox" class="i-checks edit_icheck" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        <h4>Exonerado de impuesto</h4></td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green checked" style="position: relative;"><input type="checkbox" class="i-checks" checked="" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green" style="position: relative;"><input type="checkbox" class="i-checks edit_icheck" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        <h4>Retiene impuesto</h4></td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green checked" style="position: relative;"><input type="checkbox" class="i-checks" checked="" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green" style="position: relative;"><input type="checkbox" class="i-checks edit_icheck" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        <h4>Lista de precio de ventas</h4></td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green checked" style="position: relative;"><input type="checkbox" class="i-checks" checked="" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green" style="position: relative;"><input type="checkbox" class="i-checks edit_icheck" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        <h4>Lista de precio de alquiler</h4></td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green" style="position: relative;"><input type="checkbox" class="i-checks" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green" style="position: relative;"><input type="checkbox" class="i-checks edit_icheck" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        <h4>Términos de pago</h4></td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green checked" style="position: relative;"><input type="checkbox" class="i-checks" checked="" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green" style="position: relative;"><input type="checkbox" class="i-checks edit_icheck" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        <h4>Línea de negocio o Centro contable</h4></td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green checked" style="position: relative;"><input type="checkbox" class="i-checks" checked="" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green" style="position: relative;"><input type="checkbox" class="i-checks edit_icheck" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        <h4>Asignado a</h4></td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green checked" style="position: relative;"><input type="checkbox" class="i-checks" checked="" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green" style="position: relative;"><input type="checkbox" class="i-checks edit_icheck" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        <h4>Centro de facturación</h4></td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green checked" style="position: relative;"><input type="checkbox" class="i-checks" checked="" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <div class="icheckbox_square-green" style="position: relative;"><input type="checkbox" class="i-checks edit_icheck" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>    
            </div>
            </div>
		<button id="topSlideMenu" type="button" class="navbar-toggle" data-toggle="offcanvas" data-target="#myNavmenu"><i class="fa fa-tasks"></i></button>
	</div>
					</div>

					<!-- END ACORDEON -->
					<!-- <div class="row">
                    	<div class="col-xs-0 col-sm-6 col-md-8">&nbsp;</div>
                    	<div class="form-group col-xs-12 col-sm-3 col-md-2">
                    		<a href="<?php echo base_url("roles/listar-roles") ?>" class="btn btn-default btn-block">Cancelar</a>
                    	</div>
                    	<div class="form-group col-xs-12 col-sm-3 col-md-2">
                        	<button type="submit" class="btn btn-primary btn-block">&nbsp;Guardar</button>
                    	</div>
                    </div> -->


                     <div class="row">
                            <div class="col-xs-0 col-sm-6 col-md-6">&nbsp;</div>

                             <div class="form-group col-xs-12 col-sm-6 col-md-6"  style="text-align: right;">
                             	 <a href="<?php echo base_url("roles/listar") ?>" class="btn btn-w-m btn-default">Cancelar</a>
                                <button type="submit" class="btn btn-w-m btn-primary">&nbsp;Guardar</button>
                            </div>
                        </div>




					<input type="hidden" name="role_id" value="<?php echo !empty($rol_id) ? $rol_id : "";  ?>" />
					<?php echo form_close(); ?>

					<?php
					$formAttr = array(
						'method' => 'POST',
						'id'     => 'deletePermisoForm',
						'class'  => 'hide'
					);
					echo form_open(base_url(uri_string()), $formAttr);
					?>
                    <input type="text" id="permiso" name="" />
                    <input type="hidden" name="id_rol" value="<?php echo !empty($rol_id) ? $rol_id : "";  ?>" />
                    <?php echo form_close(); ?>

                <!-- White Background -->
                </div>
            </div>
        </div>

    </div>
</div>
