<!-- template for the modal component -->
<script type="x/template" id="modal-template">
  <div class="modal-mask" v-show="show" transition="modal">
    <div class="modal-wrapper">
      <div class="modal-container">
        <div class="modal-header"><h3><i class="fa fa-envelope-o"></i> Enviar:{{orden_compra_id}}</h3>
      </div>

        <div class="modal-body">

          <div class="row">
              <div class="col-lg-12">
                  <label>  Proveedor: Industrias Panamá, S.A. </label>
               </div>
              <div class="col-lg-12">
                  <div class="input-group m-b"><span class="input-group-addon">@</span> <input type="text" placeholder="Correo Electrónico" class="form-control"></div>
               </div>


          </div>


         </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" href="#" @click="show = false" data-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" data-dismiss="modal">Enviar</button>

        </div>
      </div>
    </div>
  </div>
</script>
