jQuery(document).ready(function($) {
    var sortList = $( "#product-list-wrapper" );  // Replace this with your selector
    sortList.sortable({
        update: function( event, ui ) {
            $.ajax({
                url: MySortable.ajax_url,
                type: 'post',
                dataType: 'json',
                data: {
                    action: 'save_sort',
                    order: sortList.sortable( 'toArray' ),
                    security: wpApiSettings.nonce
                }
            });
        }
    });
});