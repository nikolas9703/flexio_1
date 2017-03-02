<div  d="wrapper">
    <?php
    Template::cargar_vista('sidebar');
    ?>
    <div id="page-wrapper" class="gray-bg row">

        <?php Template::cargar_vista('navbar'); ?>
        <div class="row border-bottom"></div>
        <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>
        <div class="col-lg-12">
            <div class="wrapper-content">
                <div class="row">
                    <label>Historial de solicitud No. <?php echo $campos['campos']['solicitud_n'] ?></label>
                </div>
                <hr style="margin-bottom: 5px!important; color: gray; background-color: gray; height: 2px; width: 100%;"> 
                <div class="filtro-formularios" style="background-color: white; padding:6px 0 39px 10px">
                    <div class="row">
                        <div class="alert alert-dismissable <?php echo!empty($mensaje) ? 'show ' . $mensaje["clase"] : 'hide' ?>">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                            <?php echo!empty($mensaje) ? $mensaje["contenido"] : '' ?>
                        </div>
                    </div>
                    <div class="row">
                        <label for="comentario">Historial</label>
                        <div id="historial_comentario">
                            <div class="ibox-content" >
                                <div class="vertical-container dark-timeline center-orientation">
                                    <?php
                                    foreach ($campos['campos']['historial'] as $item) {
                                        $item = (object) $item;
                                        switch ($item->comentable_type) {
                                            case 'Comentario':
                                                $titBit = "Comentario";
                                                $logoBit = "fa-comment";
                                                break;
                                            case 'Cambio de Estado':
                                                $titBit = "Cambio de Estado";
                                                $logoBit = "fa-refresh";
                                                break;
                                            case 'Creacion':
                                                $titBit = "Creación de solicitud";
                                                $logoBit = "fa-building";
                                                break;
                                            case 'Documentos':
                                                $titBit = "Documentos";
                                                $logoBit = "fa-book";
                                                break;
											case 'Creacion_prima':
                                                $titBit = "Prima e Información de Cobros";
                                                $logoBit = "fa-usd";
                                                break;
											case 'Creacion_vigencia':
                                                $titBit = "Vigencia y detalle de solicitud";
                                                $logoBit = "fa-calendar";
                                                break;
											case 'actualizacion_comision_principal':
                                                $titBit = "Ajuste Datos de Comisión Agente Principal";
                                                $logoBit = "fa-usd";
                                                break;
											case 'actualizacion_comision':
                                                $titBit = "Ajuste Datos de Comisión";
                                                $logoBit = "fa-usd";
                                                break;
											case 'creacion_comision':
                                                $titBit = "Ingreso Datos de Comisión";
                                                $logoBit = "fa-usd";
                                                break;
											case 'Datos_Generales':
                                                $titBit = "Modificación Datos Generales";
                                                $logoBit = "fa-building";
                                                break;
											case 'Plan':
                                                $titBit = "Modificación Plan";
                                                $logoBit = "fa-building";
                                                break;
											case 'Creacion_interes_solicitudes':
                                                $titBit = "Creación Interes Solicitud";
                                                $logoBit = "fa-child";
                                                break;
											case 'Actualizacion_interes_solicitudes':
                                                $titBit = "Actualización Interes Solicitud";
                                                $logoBit = "fa-child";
                                                break;
                                            case 'Solicitud_aprobada':
                                                $titBit = "Solicitud Aprobada";
                                                $logoBit = "fa-check";
                                                break;
                                        }
                                        ?>
                                        <div class="vertical-timeline-block">
                                            <div class="vertical-timeline-icon blue-bg">
                                                <i class="fa <?= $logoBit; ?>"></i>
                                            </div>
                                            <div class="vertical-timeline-content" >
                                                <h2 style="color: #1f70ba;"><?= $titBit ?></h2>
                                                <div>
                                                    <?= $item->comentario ?>
                                                </div>

                                                <span class="vertical-date">
                                                    <?= $Fecha->getCuantoTiempo($item->created_at) ?> <br>
                                                    <small><?= $item->created_at ?></small>
                                                    <div><small> <?= $item->nombre . " " . $item->apellido ?>  <?= $Fecha->getHora($item->created_at) ?></small></div>
                                                </span>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>

    </div>
</div>