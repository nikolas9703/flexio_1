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
		<div class="col-xs-0 col-sm-2 col-md-5 col-lg-4 hidden-xs" id="notifications_div">
		    <ul class="nav navbar-top-links navbar-right">

				<notifications :notifications.sync="notifications"></notifications>

				<li>

					<a href="<?php echo base_url()?>" class="dropdown-toggle count-info">
							<i class="fa fa-comments"></i>
							<span id="count_user" class="label label-warning">0</span>
							<!--Start of Zendesk Chat Script-->
							<script type="text/javascript">
							window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
							d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
							_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute("charset","utf-8");
							$.src="https://v2.zopim.com/?4QquKU72AMjdFucb2leXjTiJOrjnRY23";z.t=+new Date;$.
							type="text/javascript";e.parentNode.insertBefore($,e)})(document,"script");

							$zopim(function() {
								 $zopim.livechat.setName("<?php echo self::$ci->session->userdata('nombre').' '.self::$ci->session->userdata('apellido').' / '.self::$ci->session->userdata('nombre_empresa'); ?>");
								 $zopim.livechat.setEmail("<?php echo self::$ci->session->userdata('correo_electronico'); ?>");
							 });
							</script>
							<!--End of Zendesk Chat Script-->​
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
