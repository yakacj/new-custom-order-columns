/* Admin scripts */

(function( $ ){
	$( 'document' ).ready( function(){
		$( 'body' ).on( 'click', '.ycnb', function( e ){
		    e.preventDefault();
			$( this ).closest( 'tr' ).removeClass( 'active' );
			trCloned = $( this ).closest( 'tr' ).clone();
		
			trCloned.find( 'input' ).each( function(){
				$( this ).val( '' );
			});
			$( this ).closest( 'tbody' ).append( trCloned );
				trCloned.addClass( 'active' );
		});
		
		$( 'body' ).on( 'click', '.ycrb', function( e ){
		    e.preventDefault();
			$( this ).closest( 'tr' ).remove();
		});
	});
})(jQuery);