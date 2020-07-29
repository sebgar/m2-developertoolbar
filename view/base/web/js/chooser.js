define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('mage.developertoolbar_chooser', {
        options: {},

        _create: function ()
        {
            this.bindEvents();
        },

        bindEvents: function()
        {
            // show toolbar
            $(this.options.elements.requests_item).on('click', function(event){
                var element = $(event.currentTarget);

                // enable right request
                $(this.options.elements.requests_item).removeClass('active');
                element.addClass('active');

                // show right toolbar
                $(this.options.elements.container_requests).hide();
                $(this.options.elements.container_request+element.data('key')).show();
            }.bind(this));
        }
    });

    return $.mage.developertoolbar_chooser;
});
