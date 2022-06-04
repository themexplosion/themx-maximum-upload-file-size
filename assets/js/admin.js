(function ($) {
	"use strict";

	$( '#hideTmufsNotice' ).on(
		'click',
		function(){

			$.ajax(
				{
					url: tmufs_admin_notice_ajax_object.tmufs_admin_notice_ajax_url,
					type: 'post',
					dataType: 'json',
					data: {
						action: 'tmufs_admin_notice_ajax_object_save', data: 1,
						_ajax_nonce: tmufs_admin_notice_ajax_object.nonce,
					},
					success: function (data) {
						console.log( "success" );
						console.log( data );
						if (data.success == true) {
							$( '.hideTmufsNotice' ).hide( 'fast' );
						}
					},
					error: function (error) {
						console.log( error );
					}
				}
			)
		}
	);

})( jQuery );
