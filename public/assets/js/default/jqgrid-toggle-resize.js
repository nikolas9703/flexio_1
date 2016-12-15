$(function(){
	$('body').on('click','a.navbar-minimalize',function(){
      if ($(".ui-jqgrid").length > 0){
        setTimeout(function(){
              $(".ui-jqgrid").each(function(){
               var w = parseInt( $(this).parent().width()) - 6;
               var tmpId = $(this).attr("id");
               var gId = tmpId.replace("gbox_","");
               $("#"+gId).setGridWidth(w);
             });
        }, 500);
      }
  });
});
