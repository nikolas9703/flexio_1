$(function(){
  "use strict";
  //Init Bootstrap Calendar Plugin
  //*********************************

  if(vista == "crear"){
    $('#fecha_reclamo,#fecha_siniestro,#fecha_notificacion, #fecha_cheque, #fecha_juicio, #fecha_salud, #fecha_seguimiento').daterangepicker({ //
      locale: { format: 'YYYY-MM-DD' },
      showDropdowns: true,
      defaultDate: '',
      singleDatePicker: true
    }).val('');
  }else{
    var fechareclamo = "";
    var fechasiniestro = "";
    var fechanotificacion = "";
    var fechacheque = "";
    var fechajuicio = "";
    var fechasalud = "";
    var fechaseguimiento = "";
    if (typeof formularioCrear.reclamoInfo.fecha != "undefined") { fechareclamo = formularioCrear.reclamoInfo.fecha; }
    if (typeof formularioCrear.reclamoInfo.fecha_siniestro != "undefined") { fechasiniestro = formularioCrear.reclamoInfo.fecha_siniestro; }
    if (typeof formularioCrear.reclamoInfo.fecha_notificacion != "undefined") { fechanotificacion = formularioCrear.reclamoInfo.fecha_notificacion; }
    if (typeof formularioCrear.reclamoInfo.fecha_cheque != "undefined") { fechacheque = formularioCrear.reclamoInfo.fecha_cheque; }
    if (typeof formularioCrear.reclamoInfo.fecha_juicio != "undefined") { fechajuicio = formularioCrear.reclamoInfo.fecha_juicio; }
    if (typeof formularioCrear.reclamoInfo.fecha_salud != "undefined") { fechasalud = formularioCrear.reclamoInfo.fecha_salud; }
    if (typeof formularioCrear.reclamoInfo.fecha_seguimiento != "undefined") { fechaseguimiento = formularioCrear.reclamoInfo.fecha_seguimiento; }
    $('#fecha_reclamo').daterangepicker({ locale: { format: 'YYYY-MM-DD' }, showDropdowns: true, defaultDate: '', singleDatePicker: true }).val(fechareclamo);
    $('#fecha_siniesro').daterangepicker({ locale: { format: 'YYYY-MM-DD' }, showDropdowns: true, defaultDate: '', singleDatePicker: true }).val(fechasiniestro);
    $('#fecha_notificacion').daterangepicker({ locale: { format: 'YYYY-MM-DD' }, showDropdowns: true, defaultDate: '', singleDatePicker: true }).val(fechanotificacion);
    $('#fecha_cheque').daterangepicker({ locale: { format: 'YYYY-MM-DD' }, showDropdowns: true, defaultDate: '', singleDatePicker: true }).val(fechacheque);
    $('#fecha_juicio').daterangepicker({ locale: { format: 'YYYY-MM-DD' }, showDropdowns: true, defaultDate: '', singleDatePicker: true }).val(fechajuicio);
    $('#fecha_salud').daterangepicker({ locale: { format: 'YYYY-MM-DD' }, showDropdowns: true, defaultDate: '', singleDatePicker: true }).val(fechasalud);
    $('#fecha_seguimiento').daterangepicker({ locale: { format: 'YYYY-MM-DD' }, showDropdowns: true, defaultDate: '', singleDatePicker: true }).val(fechaseguimiento);
  }


  if (vista=="crear") {
    var hoy = new Date();
    var year = hoy.getFullYear();
    var month = hoy.getMonth()+1;
    var day  = hoy.getDate();
    if (month<10) {month="0"+month;}
    if (day<10) {day="0"+day;}
    var today = year + '-' + month + '-' + day ;
    $('#fecha_reclamo').val( today );
  }
         
  $("#total_reclamar, #pago_asegurado, #pago_deducible, #gastos_no_cubiertos, #monto").inputmask('currency',{
    prefix: "",
    autoUnmask : true,
    removeMaskOnSubmit: true
  });         
});

$(document).ready(function() {
  var stickyNavTop = $('.tab-principal').offset().top;

  var stickyNav = function(){
    var scrollTop = $(window).scrollTop();
    
    if (scrollTop > stickyNavTop) { 
      $('.tab-principal').addClass('sticky');
    } else {
      $('.tab-principal').removeClass('sticky'); 
    }
  };

  stickyNav();

  $(window).scroll(function() {
    stickyNav();
  });

   
});