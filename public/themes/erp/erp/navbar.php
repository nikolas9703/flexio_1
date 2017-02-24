<nav id="myNavmenu" class="navmenu-fixed-left offcanvas" role="navigation">
	<ul class="nav navmenu-nav">
		<li><strong class="font-bold"><?php echo self::$ci->session->userdata ( 'nombre' ) . " " . self::$ci->session->userdata ( 'apellido' ); ?></strong></li>
		<?php  if(Auth::has_permission ( 'acceso',"usuarios/ver-perfil/(:num)" )){?>
		<li><a href="<?php echo base_url("usuarios/ver-perfil/". self::$ci->session->userdata('id_usuario')); ?>">Perfils</a></li>
		<li class="divider"></li>
		<?php  }?>
                <?php if(in_array(3,self::$ci->session->userdata ( 'roles' ))):?>
		<li><a href="<?php echo base_url("usuarios/empresas_usuario"); ?>">Empresas</a></li>
                <?php endif;?>
		<li class="divider"></li>
		<li><a href="<?php echo base_url("login/logout"); ?>" ng-click="logout($event)">Salir</a></li>
	<ul>
</nav>

<nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
    <div class="navbar-header">
        <a href="#" class="navbar-minimalize minimalize-styl-2 btn btn-primary"><i class="fa fa-bars"></i> </a>
    </div>

    <div id="navbar" ng-controller="navBarMenuCtrl">
		<div class="col-xs-4 col-sm-8 col-md-6 col-lg-7">
			<ul class="nav navbar-nav navtop-menu navbar-left ng-cloak">
				<li ng-repeat="m in menus track by $index" class="{{menu_superior_seleccionado == m.grupo ? 'active' : ''}}"><a ng-href="#" ng-click="togglemenu($event)" data-grupo="{{m.grupo}}" role="button" ng-cloak=""> {{m.nombre}}</a></li>
			</ul>
		</div>
		<div class="col-xs-0 col-sm-2 col-md-5 col-lg-4 hidden-xs">
		    <ul class="nav navbar-top-links navbar-right">
		        <li class="dropdown hidden-xs hidden-sm">
		            <a href="#" data-toggle="dropdown" class="dropdown-toggle count-info">
		                <i class="fa fa-bell"></i>
		                <span id="count_user" class="label label-warning">0</span>
		            </a>
		            <ul id="lista_notificaciones" class="dropdown-menu dropdown-alerts content mCustomScrollbar minimal-dark"></ul>
		        </li>
				<li>
					<a href="<?php echo base_url()?>" class="dropdown-toggle count-info">
							<i class="fa fa-comments"></i>
							<span id="count_user" class="label label-warning">0</span>
					</a>
				</li>
				<li class="dropdown">
		  			<a href="#" class="dropdown-toggle hidden-xs hidden-sm" data-toggle="dropdown" >
						<span class="clear">
							<span class="block m-t-xs">
								<strong class="font-bold">
								<?php echo self::$ci->session->userdata ( 'nombre' ) . " " . self::$ci->session->userdata ( 'apellido' ); ?>
								</strong>
								<span class="">&nbsp;</span>
								<b class="caret"></b>
							</span>
						</span>
					</a>
					<ul class="dropdown-menu animated fadeInRight m-t-xs">
						<?php if(in_array(3,self::$ci->session->userdata('roles'))):?>
             <li><a href="<?php echo base_url("usuarios/empresas_usuario"); ?>">Empresas</a></li>
						<?php endif;?>
						<?php if(in_array(2,self::$ci->session->userdata('roles'))):
						?>
							<li><a href="<?php echo base_url("usuarios/listar_empresa"); ?>">Empresas</a></li>
						<?php endif;?>
						<?php  //if(Auth::has_permission ( 'acceso',"usuarios/ver-perfil/(:num)" )){?>
							<li><a href="<?php echo base_url("administracion/perfil"); ?>">Perfil</a></li>
						<?php  //}?>

						<li class="divider"></li>
						<li><a href="<?php echo base_url("login/logout"); ?>" ng-click="logout($event)">Salir</a></li>
					</ul>
	 			</li>
		    </ul>
		</div>
		<button id="topSlideMenu" type="button" class="navbar-toggle" data-toggle="offcanvas" data-target="#myNavmenu"><i class="fa fa-tasks"></i></button>
	</div>

</nav>
