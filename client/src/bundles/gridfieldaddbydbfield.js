import jQuery from 'jquery';

jQuery.entwine('ss', ($) => {
    /**
     * Prevent the CMS hijacking the return key
     */
    $('.add-existing-autocompleter input.text').entwine({
        'onkeydown': function (e) {
            if (e.which === 13) {
                const $parent = $(this).parents('.add-existing-autocompleter');
                $parent.find('button[type="submit"]').click();
                e.preventDefault();
                return false;
            }
        }
    });
});
