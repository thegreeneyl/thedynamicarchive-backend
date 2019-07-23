(function ($, Drupal, debounce) {
    Drupal.behaviors.workflow = {
        attach() {
            $('#edit-field-embeddedvideo-wrapper').show();
        },
    };
}(jQuery, Drupal, Drupal.debounce));
