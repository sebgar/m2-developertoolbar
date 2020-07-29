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
            var toolbarPrefix = this.options.elements.toolbar_prefix+this.options.requestKey;

            // container close
            $(toolbarPrefix+' '+this.options.elements.close).on('click', function(event){
                var element = $(event.currentTarget);
                var requestKey = element.closest('.dtt').data('key');
                var content = $(element.parents(this.options.elements.item_content)[0]);

                // hide content
                content.hide();

                // remove active on item
                $(this.options.elements.item_id+content.data('code')+requestKey).removeClass('active');
            }.bind(this));

            // toolbar items
            $(toolbarPrefix+' '+this.options.elements.item).on('click', function(event){
                var element = $(event.currentTarget);
                var requestKey = element.closest('.dtt').data('key');

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
                        $(this.options.elements.item_content_id+element.data('code')+requestKey).show();
                    }
                }
            }.bind(this));

            // tabs
            $(toolbarPrefix+' .dtt-tab').on('click', function (event) {
                var element = $(event.currentTarget);

                // enable / disable tab
                element.parents('.dtt-tabs').find('.dtt-tab').removeClass('active');
                element.addClass('active');

                // enable / disable container
                element.parents('.dtt-tabs').find('.dtt-tab-container').hide();
                $(element.data('tab')).show();
            });
            $(toolbarPrefix+' .dtt-tabs').each(function(index, element){
                $(element).find('.dtt-tab').first().click();
            });

            // tree
            $(toolbarPrefix+' .dtt-tree li > .icon').on('click', function(event){
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
            $(toolbarPrefix+' .dtt-tree li > .name').on('click', function(event){
                var element = $(event.currentTarget);
                element.siblings('.detail').toggle();
            });

            // open children
            $(toolbarPrefix+' .open-children').on('click', function(event){
                var element = $(event.currentTarget);
                element.siblings('.children').toggle();
            });

            // open tree
            $(toolbarPrefix+' .open-tree').on('click', function(event){
                var element = $(event.currentTarget);
                element.parents('.dtt-tree').find('.tree-root ul').show();
            });

            // close tree
            $(toolbarPrefix+' .close-tree').on('click', function(event){
                var element = $(event.currentTarget);
                element.parents('.dtt-tree').find('.tree-root ul').hide();
            });
        }
    });

    return $.mage.developertoolbar_toolbar;
});
