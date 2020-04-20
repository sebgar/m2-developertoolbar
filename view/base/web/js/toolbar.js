define([
    'jquery',
    'jquery/ui',
    'Magento_Ui/js/modal/confirm',
    "Magento_Ui/js/modal/alert"
], function ($, jqueryUi, modalConfirm, modalAlert) {
    'use strict';

    $.widget('mage.developertoolbar_toolbar', {
        options: {},

        _create: function ()
        {
            this.bindEvents();
        },

        bindEvents: function()
        {
            // show toolbar
            $(this.options.elements.showToolbar).on('click', function(){
                $(this.options.elements.showToolbar).hide();
                $(this.options.elements.hideToolbar).show();
                $(this.options.elements.toolbars).show();

                this.saveCookie(this.options.cookiename, 'show');
            }.bind(this));

            // hide toolbar
            $(this.options.elements.hideToolbar).on('click', function(){
                $(this.options.elements.showToolbar).show();
                $(this.options.elements.hideToolbar).hide();
                $(this.options.elements.toolbars).hide();

                this.saveCookie(this.options.cookiename, 'hide');
            }.bind(this));

            // container close
            $(this.options.elements.close).on('click', function(event){
                var element = $(event.currentTarget);
                var content = $(element.parents(this.options.elements.item_content)[0]);

                // hide content
                content.hide();

                // remove active on item
                $(this.options.elements.item_id+content.data('code')+this.options.request_key).removeClass('active');
            }.bind(this));

            // toolbar items
            $(this.options.elements.item).on('click', function(event){
                var element = $(event.currentTarget);

                if (element.hasClass('dtti-has-content')) {
                    var isOpen = element.hasClass('active');

                    // remove active on items and activate right item
                    $(this.options.elements.item).removeClass('active');
                    if (!isOpen) {
                        element.addClass('active');
                    }

                    // hide content and activate the right content
                    $(this.options.elements.item_content).hide();
                    if (!isOpen) {
                        $(this.options.elements.item_content_id+element.data('code')+this.options.request_key).show();
                    }
                }
            }.bind(this));

            // tabs
            $('.dtt-tab').on('click', function (event) {
                var element = $(event.currentTarget);

                // enable / disable tab
                element.parents('.dtt-tabs').find('.dtt-tab').removeClass('active');
                element.addClass('active');

                // enable / disable container
                element.parents('.dtt-tabs').find('.dtt-tab-container').hide();
                $(element.data('tab')).show();
            });
            $('.dtt-tabs').each(function(index, element){
                $(element).find('.dtt-tab').first().click();
            });

            // tree
            $('.dtt-tree li > .icon').on('click', function(event){
                var element = $(event.currentTarget);
                var container = element.siblings('ul');
                if (container.is(":visible")) {
                    container.hide();
                    element.siblings('.icon').html('+');
                } else {
                    container.show();
                    element.siblings('.icon').html('-');
                }
            });
            $('.dtt-tree li > .name').on('click', function(event){
                var element = $(event.currentTarget);
                element.siblings('.detail').toggle();
            });

            // open children
            $('.open-children').on('click', function(event){
                var element = $(event.currentTarget);
                element.siblings('.children').toggle();
            });

            // open tree
            $('.open-tree').on('click', function(event){
                var element = $(event.currentTarget);
                element.parents('.dtt-tree').find('.tree-root ul').show();
            });

            // close tree
            $('.close-tree').on('click', function(event){
                var element = $(event.currentTarget);
                element.parents('.dtt-tree').find('.tree-root ul').hide();
            });

        },

        saveCookie: function (cookieName, cookieValue)
        {
            //Cookie.write(cookieName, cookieValue, 30 * 24 * 60 * 60);
        }
    });

    return $.mage.developertoolbar_toolbar;
});
