(function ($, Drupal, drupalSettings) {
    'use strict';
    // Put here your custom JavaScript code.
    console.log ("Hello World");
    alert();
    Drupal.behaviors.Elementnew = {
      attach : function(context, settings){
        $("body").addClass('suresh');
       
      }
    }
  })();