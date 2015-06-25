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

    var $cssSelectors = {
        storage : '.node-browser-storage',
        breadcrumb : '.node-breadcrumb a',
        items : '.node-browser-listing li',
        btnDone: '.note-browser-done'
    };

    var listingDir = function(path) {

        var $browserBody  = $browserDialog.find('.modal-body')

        $.get($url,{ storage :  getStorageValue() , path: path  }).done(function(data){

            $browserBody.html(data);
            $browserDialog.find($cssSelectors.storage).off().on('change', onEventStorageChange);
            $browserDialog.find($cssSelectors.breadcrumb).off().on('click', onEventBreadcrumbClick);
            $browserDialog.find($cssSelectors.items).off().on('click', onEventItemClick);
        });

    },getStorageValue = function(){
        return $browserDialog.find($cssSelectors.storage).val();
    }, onEventBreadcrumbClick = function(event) {
        event.preventDefault();
        listingDir($(this).data('path')  );

    },onEventStorageChange = function(event){
        event.preventDefault();
        listingDir(null);

    }, onEventItemClick = function(event) {
        event.preventDefault();

        if($(this).data('type') == 'dir') {
            listingDir($(this).data('path')  );
            return false;
        }
        if(event.ctrlKey == false) {
            $(this).addClass('selected').siblings().removeClass();
        }
        if(event.ctrlKey == true) {
            $(this).toggleClass('selected');
        }

        $browserDialog.find($cssSelectors.btnDone).removeClass('disabled');

    }, showBrowserDialog = function ($editable, $dialog, text , layoutInfo) {

        return $.Deferred(function (deferred) {

            // init
            $browserDialog = $dialog.find('.note-browser-dialog');
            $url = layoutInfo.holder().data('browser-url');
            $dialog.find('.modal-dialog').addClass('modal-lg');

            var $browserDoneBtn = $browserDialog.find($cssSelectors.btnDone);

            $browserDialog.one('shown.bs.modal', function () {

                listingDir();

                $browserDoneBtn.on('click',function (event) {
                    event.preventDefault();
                    var files = [];
                    $.each($browserDialog.find( $cssSelectors.items + '.selected'), function(k,item){
                        files.push( $(item).data('path'));
                    })
                    deferred.resolve(files);
                    $browserDialog.modal('hide');
                });

            }).one('hidden.bs.modal', function () {

                $browserDialog.find($cssSelectors.items).off('click');
                $browserDoneBtn.off('click');

                if (deferred.state() === 'pending') {
                    deferred.reject();
                }
            }).modal('show');
        });

    }, createObjectNode = function(files){

        var result = $('<p></p>');
        for(var i= 0, l=files.length; i < l; i++) {
            var url = files[i];
            if (url && (/\.(gif|jpg|jpeg|tiff|png)$/i).test(url)) {
                var $img = $('<img />').attr('src',url);
                result.append($img);
            }else{
                var $a = $('<a></a>',{ href: url});
                $a.text(url);
                result.append($a);
            }
        }
        return result[0];
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
                var footer = '<a href="#" class="btn btn-primary note-browser-done disabled">' + 'select' + '</a>';
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
