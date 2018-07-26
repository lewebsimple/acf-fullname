(function ($) {

  function initialize_field ($el) {
  }

  if (typeof acf.add_action !== 'undefined') {
    // ACF 5
    acf.add_action('ready append', function ($el) {
      acf.get_fields({type: 'fullname'}, $el).each(function () {
        initialize_field($(this));
      });
    });
  } else {
    // ACF 4
    $(document).on('acf/setup_fields', function (e, postbox) {
      $(postbox).find('.field[data-field_type="fullname"]').each(function () {
        initialize_field($(this));
      });
    });
  }

})(jQuery);
