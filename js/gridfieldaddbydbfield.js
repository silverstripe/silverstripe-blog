(function ($) {

    $.entwine('ss', function ($) {

        /**
         * Prevent the CMS hijacking the return key
         */
        $('.add-existing-autocompleter input.text').entwine({
            'onkeydown': function (e) {
                if(e.which == 13) {
                    $parent = $(this).parents('.add-existing-autocompleter');
                    $parent.find('button[type="submit"]').click();
                    return false;
                }
            }
        });

    });

})(jQuery);
