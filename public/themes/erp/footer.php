<div class="footer fixed <?php echo session_isset() == "" ? "hide" : "" ?>">

<?php
if(session_isset()){
//si la session existe incluir en DOM
//modal para notificacion de inactividad
echo Modal::config(array(
	"id" => "inacModal",
	"size" => "md",
	"titulo" => "Alerta Expiracion de Sesi&oacute;n",
	"contenido" => '<p>Usted ha estado inactivo por un tiempo. Por su seguridad, le cerraremos la sesi&oacute;n autom&aacute;ticamente. Haga clic en "Seguir Conectado" para continuar con la sesi&oacute;n. </p>
	<p>Tu sesi&oacute;n expirar&aacute; en <span class="bold" id="sessionSecondsRemaining">120</span> segundos.</p>',
	"footer" => '<button id="extendSession" type="button" class="btn btn-info" data-dismiss="modal">Seguir Conectado</button>
	<button id="logoutSession" type="button" class="btn btn-default" data-dismiss="modal">Logout</button>',
))->html();
}
?>

<!-- Este div se usa para mostrar las alertas del sistema flexio -->
<div id="z_flexio_div">
	<toast_v2 :mensaje.sync="mensaje"></toast_v2>
</div>

<?php $commit = exec('git log --pretty="%H" -n1 HEAD');?>

<input type="hidden" id="alert_expiracion" name="alert_expiracion" value="<?php echo (isset($this->ci->session->userdata['por_vencer']) && $this->ci->session->userdata['por_vencer'] !='')?1 :0; ?>"  >
<!-- <script src="<?php echo base_url('public/assets/js/default/jquery-1.11.2.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/assets/js/default/bootstrap.min.js') ?>" type="text/javascript"></script> -->
<script src="<?php echo base_url('public/resources/compile/js/flexio.min.js') ?>" type="text/javascript"></script>

<?php if(session_isset()): ?>
<script src="<?php echo base_url('public/assets/js/default/config.js') ?>" type="text/javascript"></script>
<!-- <script src="<?php echo base_url('public/assets/js/default/lodash.min.js') ?>" type="text/javascript"></script> -->
<?php
if(getenv('APP_ENV') =='local'){?>
<script src="<?php echo base_url('public/assets/js/default/vue.js') ?>" type="text/javascript"></script>
<?php }else{?>
	<script src="<?php echo base_url('public/assets/js/default/vue.min.js') ?>" type="text/javascript"></script>
<?php }?>
<script src="<?php echo base_url('public/assets/js/default/vue-resource.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/assets/js/plugins/jquery/idle-timer.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/assets/js/default/pusher.min.js') ?>" type="text/javascript"></script>
<!-- <script src="<?php echo base_url('public/assets/js/default/postal.min.js') ?>" type="text/javascript"></script> -->
<script src="<?php echo base_url('public/themes/'. Template::$theme_default .'/js/default.js') ?>" type="text/javascript"></script>
<!-- <script src="<?php echo base_url('public/assets/js/plugins/jquery/pace.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/assets/js/default/modernizr-custom.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/assets/js/plugins/jquery/jquery.helio.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/assets/js/plugins/jquery/jquery.nailthumb.1.1.js') ?>" type="text/javascript"></script> -->
<script src="<?php echo base_url('public/assets/js/default/angular.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/assets/js/plugins/angularjs/ng-infinite-scroll.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/assets/js/plugins/angularjs/ngCookies/cookies.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/assets/js/plugins/angularjs/ngCookies/cookieStore.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/assets/js/plugins/angularjs/ngCookies/cookieWriter.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/assets/js/plugins/angularjs/ngSanitize/sanitize.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/assets/js/plugins/angularjs/ngFlow/flow.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/assets/js/plugins/angularjs/ngFlow/fusty-flow.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/assets/js/plugins/angularjs/ngFlow/fusty-flow-factory.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/assets/js/plugins/angularjs/ngFlow/ng-flow.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/assets/js/default/angular-animate.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/assets/js/default/angular-relative-date.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/assets/js/default/menu.angular.js') ?>" type="text/javascript"></script>
<!-- <script src="<?php echo base_url('public/assets/js/plugins/jquery/toastr.js') ?>" type="text/javascript"></script>-->
<script src="<?php echo base_url('public/assets/js/plugins/bootstrap/bootstrap-tabdrop.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/assets/js/plugins/bootstrap/jasny-bootstrap.js') ?>" type="text/javascript"></script>
<!--<script src="<?php echo base_url('public/assets/js/plugins/jquery/jQuery.resizeEnd.js') ?>" type="text/javascript"></script> -->
<script src="<?php echo base_url('public/assets/js/default/util.js') ?>" type="text/javascript"></script>
<!-- <script src="<?php echo base_url('public/assets/js/default/jqgrid-toggle-resize.js') ?>" type="text/javascript"></script> -->

<?php Assets::js_vars(); ?>
<script src="<?php echo base_url('public/resources/compile/modulos/zflexio/zflexio.js').'?rev='.$commit ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/resources/compile/modulos/notifications/notifications.js').'?rev='.$commit ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/assets/js/default/vue.empresa-switch.js').'?rev='.$commit ?>" type="text/javascript"></script>
<!-- <script src="<?php echo base_url('public/assets/js/default/subcripcion.js') ?>" type="text/javascript"></script> -->
<?php Assets::js(); ?>
<?php endif; ?>
<?php
if(getenv('VER_BRANCH') =='SI'){
	$stringfromfile = file('.git/HEAD', FILE_USE_INCLUDE_PATH);
	$firstLine = $stringfromfile[0];
	$explodedstring = explode("/", $firstLine, 3);
	$branchname = $explodedstring[2];
	echo "<div style='clear: both; width: 100%; font-size: 12px; font-family: Helvetica; color: #000; background: #fff; text-align: right; '>branch: <span style='color:#000; font-weight: bold; text-transform: uppercase;'>" . $branchname . "</span></div>";
 }?>
</body>
</html>
