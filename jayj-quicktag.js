/* global postboxes */
jQuery(document).ready( function($) {

	/* Open/close metaboxes */
	$( '.if-js-closed' ).removeClass( 'if-js-closed' ).addClass( 'closed' );
	postboxes.add_postbox_toggles( 'settings_page_jayj-quicktag/jayj-quicktag' );

	/**
	 * Add new and reordering functionality
	 *
	 * Credits to the Advanced Custom Fields plugin for initial code
	 */

	// Update the order numbers
	function update_order_numbers( table ) {
		table.find( '.jayj-quicktag-row' ).each(function( i ) {
			$(this).children( '.jayj-quicktag-order' ).html( i + 1 );
		});
	}

	// Make the rows sortable
	function make_sortable( table ) {
		var fixHelper = function(e, ui) {
			ui.children().each(function() {
				$(this).width( $(this).width() );
			});
			return ui;
		};

		table.children( 'tbody' ).sortable({
			update: function() {
				update_order_numbers( table );
			},
			helper: fixHelper,
			handle: '.jayj-quicktag-order',
			placeholder:"jayj-quicktag-row-placeholder",
		});
	}

	// Make the table sortable
	make_sortable( $( '.jayj-quicktag-table' ) );

	// Add new button
	$( '.jayj-quicktag-add-quicktag' ).on( 'click', function() {

		console.log('clicked new');



		var table = $( '.jayj-quicktag-table' ),
			tbody = table.children( 'tbody' ),
			count = tbody.children( '.jayj-quicktag-row' ).length,
			new_row = tbody.children( '.jayj-quicktag-clone' ).clone( false ); // Create and add the new field

		new_row.attr( 'class', 'jayj-quicktag-row' );

		// Update the name attributes
		new_row.find( '[name]' ).each( function() {
			var name = $(this).attr( 'name' ).replace( '[9999]', '[' + count + ']' );

			$(this).attr( 'name', name );
		});

		var new_id = new_row.data( 'id' ).replace( '9999', count );
		new_row.attr('data-id', new_id);

		QTags.addButton('jayj_qtag_' + count, '&nbsp;', '', '');

		// Add the new row
		table.children( 'tbody' ).append( new_row );

		update_order_numbers( table );

		// There is now 1 more row
		count ++;

		$( ".jayj-quicktag-row .jayj-quicktag-name-input" ).on('input', test);


		return false;
	});

	$( ".jayj-quicktag-row .jayj-quicktag-name-input" ).on('input', test);

	function test() {
		var row = $(this).parents('.jayj-quicktag-row'),
			quicktag = $('.quicktags-toolbar .button[id$=' + row.attr('data-id') + ']');

			quicktag.val($(this).val());
		console.log(quicktag, quicktag.val(), $(this).val());
	}

	// Remove quicktag
	$( '.jayj-quicktag-table' ).delegate( '.jayj-quicktag-remove-button', 'click', function( e ) {
		var table = $( '.jayj-quicktag-table' ),
			tr = $(this).closest( 'tr' );

		tr.animate({
			'left' : '50px',
			'opacity' : 0
		}, 250, function() {
			tr.remove();
			update_order_numbers( table );
		});

		e.preventDefault();
	});
});
