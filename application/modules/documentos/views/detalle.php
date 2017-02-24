<template id="detalle">
        <div class="col-lg-9 animated fadeInRight">
            <div class="row">
                <div v-for="line in detalle">
                    <div class="file-box">
                        <div class="file">
                            <a href="{{line.ruta}}" target="_blank">
                                <span class="corner"></span>
                                <div class="icon">
                                    <i class="{{line.extension}}"></i>
                                </div>
                            </a>
                                <div class="file-name">
                                    <p v-html="line.nombre"></p>
                                    <small> Tipo de documento:<strong v-text="line.tipo"></strong></small>
                                    <br>
                                    <small>Fecha de carga: <strong v-text="line.fecha_carga"></strong></small>
                                    <br>
                                    <small>Fecha de documento: <strong v-text="line.fecha_documento"></strong></small>
                                    <br>
                                    <small>Usuario: <strong v-text="line.usuario"></strong></small>
                                    <br>
                                    <small>Estado: <span v-html="line.estado"></span>
                                        <div class="tooltip-demo pull-right">
                                            <button type="button" class="btn btn-default btn-xs pull-right"
                                                    data-toggle="tooltip" data-placement="bottom" title="Descargar" v-on:click="descarga(line.id)">
                                                <span class="fa fa-download"></span></button>
                                        </div>
                                    </small>
                                </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</template>