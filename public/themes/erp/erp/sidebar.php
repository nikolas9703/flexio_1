<nav role="navigation" class="navbar-default navbar-static-side">
	<!-- componente de  listado de empresa -->
	<div id="empresaMenu" class="show">
		<div id="crop-logo" class="pull-left" v-show="lista_empresa.length > 0">
			<img class="img-responsive" :src="ver_logo(empresa_seleccionada.logo)"  alt="image">
		</div>
		<div id="menu_dropdown" class="pull-left dropdown" v-show="lista_empresa.length > 0">
		    <button class="btn btn-white dropdown-toggle ng-cloak" type="button" id="dropdownMenu2" data-toggle="dropdown">
		    	<span data-bind="label">{{empresa_seleccionada.nombre}}</span>&nbsp;<span class="caret"></span>
		    </button>
		    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu2" style="z-index:9999;">
		    	<li v-for="empresa in lista_empresa">
		    		<a href="javascript:" data-logo="{{empresa.logo}}" data-nombre="{{empresa.nombre}}" data-item="{{empresa.uuid_empresa}}" @click="seleccionar($event,empresa.uuid_empresa)">
						<span v-text="empresa.nombre"></span>
						<i class="fa fa-check pull-right" v-show="empresa_seleccionada.uuid_empresa == empresa.uuid_empresa"></i>
					</a>
				</li>
		    </ul>
	  	</div>
	</div>
	<!--  -->
	<div class="sidebar-collapse ng-cloak" style="overflow: auto; position:relative; clear:both; width: auto; height: 100%;">
		<ul class="nav show ng-cloak" id="side-menu" ng-controller="sideBarMenuCtrl">
			<li ng-repeat="nav in sidemenu track by $index" class="{{menu_lateral_navsecond == nav.nombre || menu_lateral_seleccionado == nav.nombre ? 'active' : ''}}">
				<a href="{{nav.navsecond && '#' || nav.url}}" ng-click="collapse($event)" ng-cloak><i class="fa fa-sitemap hide"></i> {{nav.nombre}} <span class="fa arrow" ng-show="nav.navsecond"></span></a>
				<ul class="nav nav-second-level collapse {{menu_lateral_navsecond == nav.nombre || menu_lateral_seleccionado == nav.nombre ? 'in' : ''}}" ng-if="nav.navsecond">
					<li ng-repeat="navsecond in nav.navsecond" class="{{menu_lateral_seleccionado == navsecond.nombre ? 'active' : ''}}">
						<a href="{{navsecond.navthird && '#' || navsecond.url}}" ng-click="collapse($event)" ng-cloak>{{navsecond.nombre}} <span class="fa arrow" ng-show="navsecond.navthird"></span></a>
						<ul class="nav nav-third-level collapse {{menu_lateral_navsecond == navsecond.nombre || menu_lateral_seleccionado == navsecond.nombre ? 'in' : ''}}" ng-show="navsecond.navthird" ng-if="navsecond.navthird">
							<li ng-repeat="navthird in navsecond.navthird"><a href="{{navthird.url}}" ng-click="collapse($event)" ng-cloak>{{navthird.nombre}}</a></li>
						</ul>
					</li>
				</ul>
			</li>
		</ul>
	</div>

</nav>
