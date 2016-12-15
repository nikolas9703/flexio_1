<div class="col-lg-12">
    <div class="ibox float-e-margins">
        <div class="" id="ibox-content">
            <div id="vertical-timeline" class="vertical-container center-orientation light-timeline">
                <?php foreach($serial->seriales_lineas->sortByDesc("id")->values() as $t):?>
                <!--inicio de elemento-->
                <div class="vertical-timeline-block">
                    <div class="vertical-timeline-icon navy-bg">
                        <?php echo $t->line_item->tipoable->tipo_fa;?>
                    </div>
                    <div class="vertical-timeline-content">
                        <h2>
                            N&uacute;mero: <?php echo $t->line_item->tipoable->numero_documento;?> 
                            <span style="float: right;"><?php echo $t->line_item->tipoable->tipo_span?></span>
                        </h2>
                        <p>
                        <?php foreach($t->line_item->tipoable->timeline as $row):?>
                        <?php echo $row."<br>";?>
                        <?php endforeach;?>
                        </p>
                        <span class="vertical-date">
                        <?php echo $t->line_item->tipoable->time_ago;?> <br>
                        <small><?php echo $t->line_item->tipoable->dia_mes;?></small>
                        </span>
                    </div>
                </div>
                <!--fin de elemento-->
                <?php endforeach;?>
            </div>
        </div>
    </div>
</div>