<a href="#" class="btn btn-block btn-outline btn-warning state-btn" data-estado-id="13" data-id="<?php echo $factura_id; ?>">Por aprobar</a>
<a href="#" class="btn btn-block btn-outline btn-warning state-btn" data-estado-id="14" data-id="<?php echo $factura_id; ?>">Por pagar</a>
<a href="#" class="btn btn-block btn-outline btn-danger state-btn" data-estado-id="20" data-id="<?php echo $factura_id; ?>">Suspendida</a>
<a href="#" class="btn btn-block btn-outline btn-aux state-btn" data-estado-id="17" data-id="<?php echo $factura_id; ?>">Anulada</a>

<style>
.btn-aux {
    color: inherit;
    background: white;
    border: 1px solid #e7eaec;
}
.btn-aux:hover,
.btn-aux:focus,
.btn-aux:active,
.btn-aux.active,
.open .dropdown-toggle.btn-aux,
.btn-aux:active:focus,
.btn-aux:active:hover,
.btn-aux.active:hover,
.btn-aux.active:focus {
    color: inherit;
    background: #D2D2D2;
    border: 1px solid #d2d2d2;
}
.btn-aux:active,
.btn-aux.active,
.open .dropdown-toggle.btn-aux {
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15) inset;
}
.btn-aux.disabled,
.btn-aux.disabled:hover,
.btn-aux.disabled:focus,
.btn-aux.disabled:active,
.btn-aux.disabled.active,
.btn-aux[disabled],
.btn-aux[disabled]:hover,
.btn-aux[disabled]:focus,
.btn-aux[disabled]:active,
.btn-aux.active[disabled],
fieldset[disabled] .btn-aux,
fieldset[disabled] .btn-aux:hover,
fieldset[disabled] .btn-aux:focus,
fieldset[disabled] .btn-aux:active,
fieldset[disabled] .btn-aux.active {
    color: #cacaca;
}
</style>
