<div class="row" ng-controller="entradaManualComentarioController">
<div class="col-lg-6 col-md-6 col-xs-12 col-sm-12 Comentario<spanrequired>*</span>">
  <form id="entradaComentarioForm">
    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <label>Comentario<span class="require" required aria-required="true">*</span></label>
      <textarea rows="40" ng-model="comentarios.comentario" name="comentario" id="comentario" data-rule-required="true" ></textarea>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8"></div>
      <div class="form-group col-xs-2 col-sm-2 col-md-2 col-lg-2"><button type="button" ng-click="limpiarCampo($event)" class="btn btn-block btn-default">Limpiar</button></div>
      <div class="form-group col-xs-2 col-sm-2 col-md-2 col-lg-2"><button type="button" ng-click="guardarComentario(comentarios,$target)" class="btn btn-block btn-primary">Comentar</button></div>
    </div>
  </form>
</div>
<div class="col-lg-6 col-md-6 col-xs-12 col-sm-12">
      <div class="ibox-content" ng-hide="historial == ''">
        <div class="vertical-container dark-timeline center-orientation"  infinite-scroll-distance='3'>
           <div class="vertical-timeline-block" ng-repeat="item in historial" ng-if="$even">
                              <div class="vertical-timeline-icon navy-bg">
                                  <i class="fa fa-comments-o"></i>
                              </div>

                              <div class="vertical-timeline-content" >
                                <h2>Coment&oacute;</h2>
                                <div ng-bind-html="renderHtml(item.comentario)"></div>

                                  <span class="vertical-date">
                                      {{item.time_ago}} <br>
                                      <small>{{ item.fecha1}}</small>
                                      <div><small>{{item.usuario}} @ {{ item.hora}}</small></div>
                                  </span>
                              </div>
                </div>


           <div class="vertical-timeline-block" ng-repeat="item in historial" ng-if="$odd">
                              <div class="vertical-timeline-icon blue-bg">
                                  <i class="fa fa-comments-o"></i>
                              </div>

                              <div class="vertical-timeline-content" >
                                <h2>Coment&oacute;</h2>
                                <div ng-bind-html="renderHtml(item.comentario)"></div>

                                  <span class="vertical-date">
                                      {{item.time_ago}} <br>
                                      <small>{{ item.fecha1}}</small>
                                      <div><small>{{item.usuario}} @ {{ item.hora}}</small></div>
                                  </span>
                              </div>
              </div>
        </div>
      </div>
</div>
