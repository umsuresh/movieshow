(function ($, Drupal, once) {
Drupal.behaviors.myBehavior = {
    attach: function (context, settings) {
      once('body', 'html').forEach(function (element) {
        //alert("test document");
        //$("#edit-granularity").hide();
      })
    }
  }
})(jQuery,Drupal,once)

