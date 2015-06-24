(function (factory) {
    /* global define */
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else {
        // Browser globals: jQuery
        factory(window.jQuery);
    }
}(function ($) {
    // template, editor
    var tmpl = $.summernote.renderer.getTemplate();
    var editor = $.summernote.eventHandler.getEditor;

    // core functions: range, dom
    var range = $.summernote.core.range;
    var dom = $.summernote.core.dom;
    var $url;
    var $browserDialog;



    var ctrlMode = false;

    var listingDir = function(storage,path) {

        var $browserBody  = $browserDialog.find('.modal-body')

        $.get($url,{ storage : storage, path: path  }).done(function(data){
            $browserBody.html(data);
            $browserDialog.find('.node-browser-storage').off().on('change', onEventStorageChange);
            $browserDialog.find('.browsers li').off().on('click', onEventItemClick);
        });
    }




    var onEventBreadcrumbClick = function(event) {



    },onEventStorageChange = function(event){

        listingDir( $(this).val(),null);

    }, onEventItemClick = function(event) {

        var $browserDialog = $(this).closest('.note-browser-dialog');

        if($(this).data('type') == 'dir') {
            listingDir( $browserDialog.find('.node-browser-storage').val(), $(this).data('path')  );
            return false;
        }

        if(event.ctrlKey == false) {
            $(this).addClass('selected').siblings().removeClass();
        }
        if(event.ctrlKey == true) {
            $(this).toggleClass('selected');
        }

        $browserDialog.find('.note-browserDone-btn').removeClass('disabled');

    }, showBrowserDialog = function ($editable, $dialog, text , layoutInfo) {
        return $.Deferred(function (deferred) {

            $browserDialog = $dialog.find('.note-browser-dialog');

            var $browserDoneBtn    = $browserDialog.find('.note-browserDone-btn');

            $url = layoutInfo.holder().data('browser-url');

            $dialog.find('.modal-dialog').addClass('modal-lg');

            $browserDialog.one('shown.bs.modal', function () {

                listingDir();

                $browserDoneBtn.on('click',function (event) {
                    event.preventDefault();

                    var file = $browserDialog.find('.browsers li.selected').data('path');

                    deferred.resolve(file);
                    $browserDialog.modal('hide');
                });


            }).one('hidden.bs.modal', function () {


                $browserDialog.find('.browsers li').off('click');
                $browserDoneBtn.off('click');

                if (deferred.state() === 'pending') {
                    deferred.reject();
                }
            }).modal('show');
        });

    }, createObjectNode = function(url){

        if (url && (/\.(gif|jpg|jpeg|tiff|png)$/i).test(url)) {
            var $img = $('<img />').attr('src', url);
            return $img[0];
        }
        var $a = $('<a></a>',{ href: url});
            $a.text(url);
        return $a[0];
    };

    /**
     * @member plugin.browser
     * @private
     * @param {jQuery} $editable
     * @return {String}
     */
    var getTextOnRange = function ($editable) {
        $editable.focus();
        var rng = range.create();
        // if range on anchor, expand range with anchor
        if (rng.isOnAnchor()) {
            var anchor = dom.ancestor(rng.sc, dom.isAnchor);
            rng = range.createFromNode(anchor);
        }

        return rng.toString();
    };

    /**
     * @class plugin.browser
     * Browser Plugin
     */
    $.summernote.addPlugin({
        name : 'browser',

        /**
         * @property {Object} dialogs
         * @property {function(object, object): string} dialogs.video
         */
        dialogs: {

            browser: function (lang) {
                var body = '';
                var footer = '<a href="#" class="btn btn-primary note-browserDone-btn disabled">' + 'select' + '</a>';
                return tmpl.dialog('note-browser-dialog', 'Browser Manager' , body, footer);
            }
        },

        buttons : {
            /**
             *
             * @param lang
             * @param options
             * @returns {*}
             */
            browser: function (lang, options) {
                return tmpl.iconButton(options.iconPrefix + 'th', {
                    event : 'browserShow',
                    title: 'browser',
                    hide: true
                });
            }
        },
        events: {

            browserShow: function(event, editor, layoutInfo){
                var $dialog = layoutInfo.dialog(),
                $editable = layoutInfo.editable(),
                text = getTextOnRange($editable);

                // save current range
                editor.saveRange($editable);
                showBrowserDialog($editable, $dialog, text , layoutInfo ).then(function (url) {
                    // when ok button clicked
                    // restore range
                    editor.restoreRange($editable);

                    editor.insertNode($editable, createObjectNode(url));
                }).fail(function () {
                    // when cancel button clicked
                    editor.restoreRange($editable);
                });

            }
        }

    });


}));
