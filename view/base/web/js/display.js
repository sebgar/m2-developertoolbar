define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('mage.developertoolbar_display', {
        options: {},

        _create: function ()
        {
            this.bindEvents();
        },

        bindEvents: function()
        {
            // show toolbar
            $(this.options.elements.show).on('click', function(){
                $(this.options.elements.show).hide();
                $(this.options.elements.hide).show();
                $(this.options.elements.toolbars).show();

                this.saveCookie(this.options.cookiename, 'show');
            }.bind(this));

            // hide toolbar
            $(this.options.elements.hide).on('click', function(){
                $(this.options.elements.show).show();
                $(this.options.elements.hide).hide();
                $(this.options.elements.toolbars).hide();

                this.saveCookie(this.options.cookiename, 'hide');
            }.bind(this));
        },

        saveCookie: function (cookieName, cookieValue)
        {
            //Cookie.write(cookieName, cookieValue, 30 * 24 * 60 * 60);
        }
    });

    return $.mage.developertoolbar_display;
});
