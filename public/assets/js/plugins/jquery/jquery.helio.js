/*
 *  jQuery Helio v1.0.1
 *
 * @summary     Helio Menu
 * @description Responsive bootstrap menu
 * @version     1.0.1
 * @file        jquery.helio.js
 * @author      @jluispinilla
 * @contact     
 * @copyright   Copyright 2014 PensanomicaTeam
 *
 * This source file is free software, available under the following license:
 *   
 */

if (typeof Object.create !== "function") {
    Object.create = function (obj) {
        function F() {}
        F.prototype = obj;
        return new F();
    };
}
(function ($, window, document) {

    var helioApp = {
      init : function (options, el) {
          this.$elem = $(el);
          this.options = $.extend({}, $.fn.helio.options, this.$elem.data(), options);
          this.userOptions = options;
		
          //Trigger beforeLoad
          if (typeof this.options.beforeLoad === "function") {
            this.options.beforeLoadGame.apply(this, [this.$elem]);
          }
		
          this.startMenu();

          this.setActiveMenu();
      },
      startMenu : function () {
          var base = this,
          $toggle = this.options.toggle;

          this.$elem.find('li.active').has('ul').children('ul').addClass('collapse in');
          this.$elem.find('li').not('.active').has('ul').children('ul').addClass('collapse');

          this.$elem.find('li').has('ul').children('a').on('click', function (e) {
              e.preventDefault();

              $(this).parent('li').toggleClass('active').children('ul').collapse('toggle');

              if ($toggle) {
                  $(this).parent('li').siblings().removeClass('active').children('ul.in').collapse('hide');
              }
          });
      },
     setActiveMenu : function () {
          var path = window.location.pathname,
              parent = '';
          
          this.$elem.find('li > a')
          .filter(function(){ 
              /*if($.isEmptyObject($(this).closest('li').closest('ul').closest('li[class!="nav-header"]')) == false){
            	  return false;
              }*/
        	  
              return $(this).attr('href') != '#' ? true : false; 
          })
          .filter(function(){
              
              path = path.replace(/(\/\d+)/g, '');

              var pattern = new RegExp('\\b(' + path + ')\\b', 'i');
              var check = pattern.test($(this).attr('href'));
              if(check){
                //var parent = $(this).attr('data-parent');
                return check;
              }
          })
          .closest('li')
          .addClass('active')
          .closest('ul')
          .addClass('collapse in')
          .closest('li[class!="nav-header"]')
          .addClass('active');
      },
      escapeRegExp:  function(string) {
          //Escaping user input to be treated as a literal string within a regular expression can be accomplished by simple replacement:
          return string.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
      },
  		getQueryString : function () {
  			var url = window.location.href;
  			KeysValues = url.split(/[\?&]+/);
  			for (i = 0; i < KeysValues.length; i++) {
  				KeyValue = KeysValues[i].split("=");
  				if(KeyValue[0] == this.options.languageQueryStringParam) {
  					return KeyValue[1];
  				}
  			}
      }
  };

  $.fn.helio = function (options) {
      return this.each(function () {
          var app = Object.create(helioApp);
          app.init(options, this);
          $.data(this, "helio", app);
      });
  };

  $.fn.helio.options = {
	    toggle: true,
      beforeLoad: false,
      afterLoad: false
  };
}(jQuery, window, document));
