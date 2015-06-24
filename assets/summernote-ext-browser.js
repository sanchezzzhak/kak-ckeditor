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


    var onEventItemClick = function(event) {
        
        var $browserDialog = $(this).closest('.note-browser-dialog');
        $(this).addClass('selected').siblings().removeClass();
        $browserDialog.find('.note-browserDone-btn').removeClass('disabled');

    }, showBrowserDialog = function ($editable, $dialog, text , layoutInfo) {
        return $.Deferred(function (deferred) {

            var $browserDialog = $dialog.find('.note-browser-dialog'),
            $browserDoneBtn    = $browserDialog.find('.note-browserDone-btn'),
            $browserBody       = $browserDialog.find('.modal-body'),
            $url = layoutInfo.holder().data('browser-url');

            $dialog.find('.modal-dialog').addClass('modal-lg');

            $browserDialog.one('shown.bs.modal', function () {

                $.get($url,{}).done(function(data){

                    $browserBody.html(data);
                    $browserDialog.find('.browsers li').off().on('click', onEventItemClick);
                });

                $browserDoneBtn.on('click',function (event) {
                    event.preventDefault();
                    var file = 'value complete';
                    deferred.resolve(file);
                    $browserDialog.modal('hide');
                });


            }).one('hidden.bs.modal', function () {
                //$videoUrl.off('input');

                $browserDialog.find('.browsers li').off('click');

                $browserDoneBtn.off('click');

                if (deferred.state() === 'pending') {
                    deferred.reject();
                }
            }).modal('show');
        });

    }, createObjectNode = function(){
        var $object = $('<div></div>div>');
        return $object[0];
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
                return tmpl.dialog('note-browser-dialog', 'lang insert video' , body, footer);
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
                    // insert video node
                    editor.insertNode($editable, createObjectNode(url));
                }).fail(function () {
                    // when cancel button clicked
                    editor.restoreRange($editable);
                });

            }
        }

    });


}));
