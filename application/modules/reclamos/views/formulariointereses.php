    <h5 style="font-size:14px">Bienes Asegurados</h5>
     
    
    <div class="row" style="margin-right: 0px !important" >
        <?php 
        if (!isset($campos)) {  $campos= array(); }
        if (isset($tipo_interes)) {
            if ($tipo_interes == 1) {                
                echo modules::run("intereses_asegurados/articuloformularioparcial",$campos); 
                $tabla = "reclamos/ocultotablaarticulo";  
                $busqueda = '<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                <label>Nombre</label>  
                                <input type="text" id="modal_nombre_articulo" class="form-control">                         
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                <label>Modelo</label>  
                                <input type="text" id="modal_modelo_articulo" class="form-control">                         
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                <label>No. Serie</label>  
                                <input type="text" id="modal_serie_articulo" class="form-control">                         
                            </div>';             
            }else if ($tipo_interes == 2) {
                echo modules::run("intereses_asegurados/cargaformularioparcial",$campos);
                $tabla = "reclamos/ocultotablacarga"; 
                $busqueda = '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                <label>No. Liquidación</label>  
                                <input type="text" id="modal_liquidacion_carga" class="form-control">                         
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-5 col-lg-5">
                                <label>Medio de Transporte</label>  
                                <input type="text" id="modal_medio_carga" class="form-control">                         
                            </div>';      
            }else if ($tipo_interes == 3) {
                echo modules::run("intereses_asegurados/casco_aereoformularioparcial",$campos);
                $tabla = "reclamos/ocultotablaaereo"; 
                $busqueda = '<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                <label>No. Serie</label>  
                                <input type="text" id="modal_serie_aereo" class="form-control">                         
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                <label>Modelo</label>  
                                <input type="text" id="modal_modelo_aereo" class="form-control">                         
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                <label>Matricula</label>  
                                <input type="text" id="modal_matricula_aereo" class="form-control">                         
                            </div>';  
            }else if ($tipo_interes == 4) {
                echo modules::run("intereses_asegurados/casco_maritimoformularioparcial",$campos);
                $tabla = "reclamos/ocultotablamaritimo"; 
                $busqueda = '<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                <label>No. Serie</label>  
                                <input type="text" id="modal_serie_maritimo" class="form-control">                         
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                <label>Nombre</label>  
                                <input type="text" id="modal_nombre_maritimo" class="form-control">                         
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                <label>Marca</label>  
                                <input type="text" id="modal_marca_maritimo" class="form-control">                         
                            </div>';  
            }else if ($tipo_interes == 5) {
               echo modules::run("intereses_asegurados/personaformularioparcial",$campos);
               $tabla = "reclamos/ocultotablapersonas"; 
               $busqueda = '<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                <label>Nombre</label>  
                                <input type="text" id="modal_nombre_persona" class="form-control">                         
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                <label>Cedula</label>  
                                <input type="text" id="modal_cedula_persona" class="form-control">                         
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                <label>No. Certificado</label>  
                                <input type="text" id="modal_certificado_persona" class="form-control">                         
                            </div>';
            }else if ($tipo_interes == 6) {
               echo modules::run("intereses_asegurados/proyecto_actividadformularioparcial",$campos);
               $tabla = "reclamos/ocultotablaproyecto"; 
               $busqueda = '<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                <label>Nombre</label>  
                                <input type="text" id="modal_nombre_proyecto" class="form-control">                         
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                <label>Orden</label>  
                                <input type="text" id="modal_orden_proyecto" class="form-control">                         
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                <label>Ubicación</label>  
                                <input type="text" id="modal_ubicacion_proyecto" class="form-control">                         
                            </div>';
            }else if ($tipo_interes == 7) {
                echo modules::run("intereses_asegurados/ubicacionformularioparcial",$campos);
                $tabla = "reclamos/ocultotablaubicacion"; 
                $busqueda = '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                <label>Nombre</label>  
                                <input type="text" id="modal_nombre_ubicacion" class="form-control">                         
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-5 col-lg-5">
                                <label>Dirección</label>  
                                <input type="text" id="modal_direccion_ubicacion" class="form-control">                         
                            </div>';     
            }else if ($tipo_interes == 8) {
                echo modules::run("intereses_asegurados/vehiculoformularioparcial",$campos);
                $tabla = "reclamos/ocultotablavehiculo"; 
                $busqueda = '<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                <label>Motor</label>  
                                <input type="text" id="modal_chasis_vehiculo" class="form-control">                         
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                <label>Placa</label>  
                                <input type="text" id="modal_placa_vehiculo" class="form-control">                         
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                <label>Operador</label>  
                                <input type="text" id="modal_operador_vehiculo" class="form-control">                         
                            </div>';
            }     
        } 

        ?>       
        
        <div id="detallereclamo"></div>
    </div>   

    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 intereses_modal" style="display:none;">
        <div class="row">
            <?php echo $busqueda; ?>
            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                <label style="color:white">Botones</label> 
                <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <button id="modal_filtrar" class="btn btn-default" style="width: 100%">Filtrar</button>
                    </div> 
                    <div class="col-md-6 col-lg-6">
                        <button id="modal_limpiar" class="btn btn-default" style="width: 100%">Limpiar</button>
                    </div> 
                </div>                                         
            </div>
        </div>
        <?php echo modules::run("$tabla"); ?>
    </div>

    <div class="form-group--range">
        
    </div>