# D8 - legal


********************************************************************
DRUPAL MODULE
********************************************************************
  Name: Legal Module   
  Author: Robert Castelo  
  Sponsor: Code Positive [www.codepositive.com]  
  Drupal: 8.0.x
********************************************************************
DESCRIPTION:

A module which displays your Terms & Conditions to users who want to
register, and makes sure they accept the T&C before their registration
is accepted.

Note: No T&C will be displayed until the T&C text has been input by
      the administrator.

Each time a new version of the T&C is created all users will be required to 
accept the new version.

Note: T&C text should only be entered by administrators or other highly trusted users.
          filter_xss_admin() is used to filter content for display, this is a very permissive 
          XSS/HTML filter intended for admin-only use.

Integrates with Views, and ships with 2 default views that display T&C history, and user 
acceptance of T&Cs.

********************************************************************
INSTALLATION:

  Note: It is assumed that you have Drupal up and running.  Be sure to
  check the Drupal web site if you need assistance.  If you run into
  problems, you should always read the INSTALL.txt that comes with the
  Drupal package and read the online documentation.

  1. Place the entire legal directory into your Drupal
        modules/directory.

  2. Enable the legal module by navigating to:

     Administration > Extend

  Click the 'Save configuration' button at the bottom to commit your
    changes.
    


********************************************************************
CONFIGURATION:

  1. Go to Administer > User management > Access control
      
      Set which roles can "view Terms and Conditions"
      Set which roles can "administer Terms and Conditions"
  
  2. Go to Administer > Configuration > Legal

     Input your terms & conditions text, set how you would like it
     displayed:

  - Scroll Box - Standard form text box (read only) Text is entered
    and displayed as text only

  - Scroll Box (CSS) - Scrollable text box created in CSS Text should
      be entered with HTML formatting. 
      (less accessible than a standard scroll box)

  - HTML Text - Terms & conditions displayed as HTML formatted text
       Text should be entered with HTML formatting
       
  - Page Link - Label of Accept checkbox contains link to T&Cs.

    Note: When displayed on the page /legal your T&Cs will be automatically 
            reformatted to HTML Text if entered as a Scroll Box or Scroll Box (CSS)
                
********************************************************************
ADDITIONAL CONFIGURATION:

ADDITIONAL CHECKBOXES

Each field that contains text will be shown as a checkbox which the user must tick to register.
For example, if you enter "I am at least 18 years of age" in the text area, this will display as an additional checkbox,
which must be ticked in order to proceed.

EXPLAIN CHANGES

Explain what changes were made to the T&C since the last version.
This will only be shown to users who accepted a previous version (authenticated users).
Each line will automatically be shown as a bullet point.

FACEBOOK CONNECT

In facebook applications, click edit, click on Facebook User Settings.
Click on "Do not create accounts Automatically". Then, when user DOES
create account, it runs them through the Legal agreement.

VARNISH

Add Legal module's cookies to the Varnish whitelist.

********************************************************************
 
INSERTING NEW TERMS AND CONDITIONS VIA INSTALL SCRIPT

Conditions are content, so if you'd like to deploy conditions programatically,
you'll have to create an install function, as in this barebones example:


    use Drupal\legal\Entity\Conditions;

    /**
    * Implements hook_update_n.
    * Insert terms and conditions.
    */
    function example_module_update_8001() {
      $language = 'en';
      $version = legal_version('version', $language);

      // Insert terms here in the 'value' value.
      // Change `full_html` format to a valid format as needed.
      $terms = [
        'value => '..Insert terms here (HTML allowed)...',
        'format' => 'full_html',
      ];

      // To add extra checkboxes, use and uncomment the extras here.
      $extras = [
        // 'extras-1' => '...Checkbox 1 (no HTML)...',
        // 'extras-2' => '...Checkbox 2 (no HTML)...',
        // 'extras-3' => '...Checkbox 3 (no HTML)...',
        // 'extras-4' => '...Checkbox 4 (no HTML)...',
        // 'extras-5' => '...Checkbox 5 (no HTML)...',
        // 'extras-6' => '...Checkbox 6 (no HTML)...',
        // 'extras-7' => '...Checkbox 7 (no HTML)...',
        // 'extras-8' => '...Checkbox 8 (no HTML)...',
        // 'extras-9' => '...Checkbox 9 (no HTML)...',
        // 'extras-10' => '...Checkbox 10 (no HTML)...',
      ];

      $changes = '...Insert changes here (no HTML)...';

      Conditions::create(array(
        'version'    => $version['version'],
        'revision'   => $version['revision'],
        'language'   => $language,
        'conditions' => $terms['value'],
        'format'     => $terms['format'],
        'date'       => time(),
        'extras'     => serialize($extras),
        'changes'    => $changes,
      ))->save();
    }   
       
********************************************************************
ACKNOWLEDGEMENTS

  * Drupal 5 update sponsorship  
  Lullabot (http://www.lullabot.com)
  
  * User data variables clean up  
  Steven Wittens (Steven)
  
  * T&C Page formatting  
  Bryant Mairs (Susurrus)
