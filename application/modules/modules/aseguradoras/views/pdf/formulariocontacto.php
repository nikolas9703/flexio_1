<style type="text/css">
    .titulo1{
        font-weight:bold;
        font-size: 18px;
        text-align: center;
    }
	.columnasnombres{
		   width: 25%;
	}
</style>
<div id="container">
  <table style="width: 100%;margin-left: 20%;">
        <!--seccion de cabecera-->
        <tr>
            <td> <img id="logo" src="<?php $logo = !empty($contacto->nombreAseguradora->datosEmpresa->logo)?$contacto->nombreAseguradora->datosEmpresa->logo:'default.jpg'; echo $this->config->item('logo_path').$logo;?>" height="56.69px" alt="Logo" border="0" /></td>
            <td class="titulo1">Aseguradora: <?php echo $contacto->nombreAseguradora->nombre?></td>
        </tr>                                  
        <tr>
            <td colspan=2 class="titulo1"><br><br></td>
        </tr>
        
        <!--datos de la empresa-->
        <tr>
            <td class='columnasnombres'>Nombre contacto:</td>
			<td><?php echo strtoupper($contacto->nombre);?></td>
        </tr>
        <tr>
             <td class='columnasnombres'>Correo electr&oacute;nico: </td>
             <td><?php echo $contacto->email;?></td>
        </tr>
        <tr>
             <td class='columnasnombres'>Celular:</td>
            <td><?php echo $contacto->celular?></td>
        </tr>
        <tr>
             <td class='columnasnombres'>Tel&eacute;fono:</td>
            <td><?php echo $contacto->telefono?></td>
        </tr>
        <tr>
           <td class='columnasnombres'>Cargo:</td>
            <td><?php echo $contacto->cargo?></td>
        </tr>
        <tr>
           <td class='columnasnombres'>Direcci&oacute;n:</td>
            <td><?php echo $contacto->direccion?></td>
        </tr>
		<tr>
           <td class='columnasnombres'>Comentarios:</td>
            <td><?php echo $contacto->comentarios?></td>
        </tr>
		<tr>
           <td class='columnasnombres'>Estado:</td>
            <td><?php echo $contacto->estado?></td>
        </tr>
         
    </table>
</div>
