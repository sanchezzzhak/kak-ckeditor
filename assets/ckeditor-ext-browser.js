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

    var $cssSelectors = {
        browser: '.node-browser',
        storage : '.node-browser-storage',
        breadcrumb : '.node-breadcrumb a',
        items : '.node-browser-listing li',
        btnDone: '.note-browser-done'
    };

    var onEventBtnDoneClick = function(event){

        function getUrlParam( paramName ) {
            var reParam = new RegExp( '(?:[\?&]|&)' + paramName + '=([^&]+)', 'i' ) ;
            var match = window.location.search.match(reParam) ;

            return ( match && match.length > 1 ) ? match[ 1 ] : null ;
        }
        var funcNum = getUrlParam( 'CKEditorFuncNum');
        var fileUrl = $($cssSelectors.items + '.selected').data('path');
        window.opener.CKEDITOR.tools.callFunction( 0, fileUrl );
        if (window.opener) {
            window.close();
        }
    };


    var onEventItemClick = function(event) {
        if($(this).data('type') == 'dir') {
            return true;
        }

       // if(event.ctrlKey == false) {
            $(this).addClass('selected').siblings().removeClass();
        //}
       /* if(event.ctrlKey == true) {
            $(this).toggleClass('selected');
        }*/
        return false;
    };

    var $browserDialog = $($cssSelectors.browser);
    $browserDialog.on('click', $cssSelectors.items, onEventItemClick);
    $browserDialog.on('click', $cssSelectors.btnDone, onEventBtnDoneClick);

}));
