<style type="text/css">
    .titulo1{
        font-weight:bold;
        font-size: 18px;
        text-align: center;
    }
    .columnasnombres{
           width: 25%;
           text-align: left !important;
    }

</style>
<div id="container">
  <table style="width: 100%;margin-left: 40%;">
        <!--seccion de cabecera-->
        <tr>
            <td> <img id="logo" src="<?php $logo = !empty($datos->datosEmpresa->logo)?$datos->datosEmpresa->logo:'default.jpg'; echo $this->config->item('logo_path').$logo;?>" height="56.69px" alt="Logo" border="0" /></td>
            <td class="titulo1"><?php echo $datos->datosEmpresa->nombre?></td>
        </tr>                                  
        <tr>
            <td colspan=2 class="titulo1"><br><br></td>
        </tr>
        <!--datos de la empresa-->
        <tr>
            <th class='columnasnombres'>Nombre persona :</th>
            <td><?php echo ($datos->persona ->nombrePersona);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Identificación :</th>
            <td><?php echo strtoupper($datos->persona ->identificacion);?></td>
        </tr>
        <tr>
             <th class='columnasnombres'>Fecha de Nacimiento: </th>
             <td><?php echo $datos->persona ->fecha_nacimiento;?></td>
        </tr>
        <tr>
             <th class='columnasnombres'>Estado Civil:</th>
            <td><?php echo $datos->persona->datosEstadoCivil->etiqueta;?></td>
        </tr>
        <tr>
             <th class='columnasnombres'>Nacionalidad</th>
            <td><?php echo $datos->persona ->nacionalidad;?></td>
        </tr>
        <tr>
           <th class='columnasnombres'>Sexo:</th>
            <td><?php echo $datos->persona->datosGenero->etiqueta;?></td>
        </tr>
        <tr>
           <th class='columnasnombres'>Estatura</th>
            <td><?php if($datos->persona ->estatura !="")echo $datos->persona ->estatura?></td>
        </tr>
        <tr>
           <th class='columnasnombres'>Peso:</th>
            <td><?php if($datos->persona ->peso !="")echo $datos->persona ->peso?></td>
        </tr>
        <tr>
           <th class='columnasnombres'>Telefono Residencial:</th>
            <td><?php if($datos->persona ->telefono_residencial !="")echo $datos->persona ->telefono_residencial?></td>
        </tr>
        <tr>
           <th class='columnasnombres'>Telefono de Oficina:</th>
            <td><?php echo $datos->persona ->telefono_oficina;?></td>
        </tr>
        <tr>
           <th class='columnasnombres'>Dirección Residencial:</th>
            <td><?php echo $datos->persona ->direccion_residencial;?></td>
        </tr>
        <tr>
           <th class='columnasnombres'>Direccion Laboral:</th>
            <td><?php if($datos->persona ->direccion_laboral !="") echo $datos->persona ->direccion_laboral?></td>
        </tr>      
        <tr>
           <th class='columnasnombres'>Observaciones:</th>
            <td><?php echo $datos->persona ->observaciones;?></td>
        </tr>
        <tr>
           <th class='columnasnombres'>Estado:</th>
            <td><?php echo $datos->estado;?></td>
        </tr>   
    </table>
</div>
