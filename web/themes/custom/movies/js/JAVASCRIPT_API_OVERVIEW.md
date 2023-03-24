# Drupal Javascript API

## Points to Remember - 

- In Drupal, assets are loaded only if you tell drupal to load them.
- Compared to Drupal 7, the javascript files in drupal 8+ are loaded using libraries - libraries are defined in libraries.yml file and these libraries can then be loaded using .info.yml files or hooks based upon the requirements.
- Configurable Javascript (sending data from server to browser) can be done using drupalSettings (in drupal 7 this was drupal.settings) but it needs to be declared as a dependency in libraries.yml file.

## Why do we have an API for javascript in drupal?

- We can use vanilla js code in drupal but in drupal JS code has to follow drupal coding standards that enforces developers to use Drupal's JAVASCRIPT API to write the custom js code.

- This ensures -
1) There is standard that will be followed while writing the JS code in drupal
2) There are no JS errors or performance impact on the frontend of the website that is caused by Javascript.

## What is JAVASCRIPT API?

- Drupal's JAVASCRIPT API is mostly about - Drupal.behaviors
- Drupal.behaviors is the alternative to jQuery(document).ready for writing the JS code in drupal.

- Example JS (with drupal behaviors) snippet -
```
Drupal.behaviors.myBehavior = {
  attach: function (context, settings) {
    // Use context to filter the DOM to only the elements of interest,
    // and use once() to guarantee that our callback function processes
    // any given element one time at most, regardless of how many times
    // the behaviour itself is called (it is not sufficient in general
    // to assume an element will only ever appear in a single context).
    once('myCustomBehavior', 'input.myCustomBehavior', context).forEach(
      function (element) {
        element.classList.add('processed');
      }
    );
  }
};
```

- Whenever we wrap the code in jQuery(document).ready example -

```
jQuery(document).ready(function ($) {
  // Do some fancy stuff.
});
```
- This ensures that our code will only run after the DOM (document object model) has loaded and all elements are available.

- However, Drupal provides a better approach of wrapping the code in Drupal.behaviors (as shown in example above).
- This ensures that our JS code is exceuted both on normal page load & ajax operation.

### Important note from the drupal doc.

```
Any object defined as a property of Drupal.behaviors will get its attach() method called when the DOM has loaded both initially and after any AJAX calls. drupal.js has a $(document).ready() function which calls the Drupal.attachBehaviors() function, which in turn cycles through the Drupal.behaviors object calling every one of its properties, these all being functions declared by various modules as above, and passing in the document as the context.
```

## Writing Secure code in Javascript in drupal

- In Drupal you can use the Drupal.checkPlain() to escape basic characters and prevent malicious elements being introduced into the DOM, avoiding some basic Clickjacking techniques.

- If the JS code that you've written accepts the input from the user, sanitize the data captured using the drupal.checkPlain() method.

- Refer to following example for better understanding - 

```
var rawInputText     = $('#form-input').text();
var escapedInputText = Drupal.checkPlain(rawInputText);
```

## String translation in drupal JS - 

- Translation can be achieved by using the following method = Drupal.t()
- Add all the text that should be translated inside the t() method example - 
- Drupal.t("How are you?");

## Full JS snippet (example) 

```
/**
 * @file
 * Contains client-side support code for Web Accessibility.
 */

(function ($, Drupal, drupalSettings, once) {
    Drupal.behaviors.webAccessibility = {
      attach: function (context, settings) {
        $('.path-frontpage', context).once('webAccessibility').each(function () {
          Drupal.announce(
            Drupal.t('This is the front page.'), 'assertive'
          );
        })
      }
    }
  } (jQuery, Drupal, drupalSettings, once));
```

- This JS snippet implements the web accessibility by adding the page annoucement feature to the page. (you can verify this using the google chrome announce extension)
- The snippet uses drupal.behaviors for complete implementation.
- Drupal.announce is the drupal's javascript method for announcing the content on the page.