// Javascript for Jayj Quicktag
jQuery(document).ready( function($) {

	/* Open/close metaboxes */
	$( '.if-js-closed' ).removeClass( 'if-js-closed' ).addClass( 'closed' );
	postboxes.add_postbox_toggles( 'settings_page_jayj-quicktag/jayj-quicktag' );

	/**
	 * Credits to the Advanced Custom Fields plugin for this code
	 */

	// Update Order Numbers
	function update_order_numbers(div) {
		div.children('tbody').children('tr.jayj-row').each(function(i) {
			$(this).children('td.jayj-order').html(i+1);
		});
	}
	
	// Make Sortable
	function make_sortable(div){
		var fixHelper = function(e, ui) {
			ui.children().each(function() {
				$(this).width($(this).width());
			});
			return ui;
		};

		div.children('tbody').unbind('sortable').sortable({
			update: function(event, ui){
				update_order_numbers(div);
			},
			handle: 'td.jayj-order',
			helper: fixHelper
		});
	}

	var div = $('.jayj-quicktags-table'),
		row_count = div.children('tbody').children('tr.jayj-row').length;

	// Make the table sortable
	make_sortable(div);

	// Add button
	$('#jayj-add-button').live('click', function(){

		var div = $('.jayj-quicktags-table'),			
			row_count = div.children('tbody').children('tr.jayj-row').length,
			new_field = div.children('tbody').children('tr.jayj-clone').clone(false); // Create and add the new field

		new_field.attr( 'class', 'jayj-row' );

		// Update names
		new_field.find('[name]').each(function(){
			var count = parseInt(row_count);
			var name = $(this).attr('name').replace('[999]','[' + count + ']');
			$(this).attr('name', name);
		});

		// Add row
		div.children('tbody').append(new_field); 
		update_order_numbers(div);

		// There is now 1 more row
		row_count ++;

		return false;	
	});

	// Remove button
	$('.jayj-quicktags-table .jayj-remove-button').live('click', function(){
		var div = $('.jayj-quicktags-table'),
			tr = $(this).closest('tr');

		tr.animate({'left' : '50px', 'opacity' : 0}, 250, function(){
			tr.remove();
			update_order_numbers(div);
		});

		return false;
	});
});