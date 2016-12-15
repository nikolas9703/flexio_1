<?php
if($politicas['contrasena']['configuracion_avanzada'] == 1 ){
	$Exp_regular = '/^(?=.*[A-Za-z]{'.$politicas['contrasena']['minima_cantidad_letras'].',})(?=.*\d{'.$politicas['contrasena']['minima_cantidad_numeros'].',})(?=.*[$@$!%*#?&]{'.$politicas['contrasena']['minima_cantidad_caracteres'].',})[A-Za-z\d$@$!%*#?&]{'.$politicas['contrasena']['long_minima_contrasena'].',}$/i';
	$mensaje = 'Para la contraseña al menos se requiere '.$politicas['contrasena']['long_minima_contrasena'].' Caracteres, '.$politicas['contrasena']['minima_cantidad_letras'].' letras, '.$politicas['contrasena']['minima_cantidad_numeros'].' numeros y '.$politicas['contrasena']['minima_cantidad_caracteres'].' caracteres especiales.';
}else{
	$Exp_regular = '';
	$mensaje='';
}
?>
<div class="middle-box text-center loginscreen animated fadeInDown">
    <div>
        <p>&nbsp;</p>
        <div>
        	<img src="<?php echo base_url('/public/assets/images/logo_flexio_background_transparent_recortado.png') ?>" alt="Bluleaf" border="0" />

        </div>
        <p>&nbsp;</p>

				<?php $message = self::$ci->session->flashdata('mensaje'); ?>
        <div class="alert <?php if (isset($message) && !empty($message)) echo $message["clase"];  ?> <?php echo !empty($message) ? 'show' : 'hide'  ?> alert-dismissable">
            <button class="close" type="button" data-dismiss="alert" aria-hidden="true">x</button>
            <?php if(isset($message) && !empty($message)){ echo $message["contenido"]; } ?>
        </div>

        <?php

        $formAttr = array(
 			"method"        => "post",
        	"name"           => "changePassword",
        	"class" 		=> "form-signin",
        	"autocomplete"  => "off",
        	"id"  			=> "changePassword"
        );
        echo form_open(base_url('login/recover/?usr='.  $username. '&token='. $token), $formAttr);
        ?>
            <h3>Recuperar Contrase&ntilde;a</h3>
            <div class="form-group">
                <input type="password" class="form-control" name="password1" id="password1" placeholder="Nueva Contrase&ntilde;a" autocomplete="off">
                <?php echo form_error('password1'); ?>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password2" id="password2" placeholder="Reingresar Contrase&ntilde;a" autocomplete="off">
                <?php echo form_error('password2'); ?>
            </div>
            <div class="form-group">
                 <button type="submit" class="btn btn-primary block full-width m-b">Cambiar Contrase&ntilde;a</button>
            </div>

            <a href="<?php echo base_url("/login"); ?>"><small>Ir a Pagina de Inicio</small></a>
        <?php echo form_close(); ?>
        <div class="row">
         <input type="hidden" id="expr_regular" value="<?php echo $Exp_regular;?>">
                      <input type="hidden" id="longitud_minima" value="<?php echo $politicas['contrasena']['long_minima_contrasena'];?>">
				 	<?php if($mensaje !=''){?>

							<div class="alert alert-warning alert-dismissable"   >
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                <?php echo $mensaje; ?>.                            </div>
                      <?php } ?>

            </div>
        <p class="m-t"> <small>Develop by Pensanomica &copy; <?php echo date('Y') ?></small> </p>
    </div>
</div>
