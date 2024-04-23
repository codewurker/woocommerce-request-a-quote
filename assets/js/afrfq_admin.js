jQuery(function($) {

	"use strict";

	 $('.af-rfq-live-search').select2();

	$(document).on('click', '#af_rfq_download_pdf_with_qoute_id_admin_qoute_attributes', function(){

		var current_button = document.getElementById("af_rfq_download_pdf_with_qoute_id_admin_qoute_attributes").value;
		
		jQuery.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action     : 'afrfq_admin_qoute_attribute_download_quote_pdf',
				nonce      : nonce,
				post_id    : current_button,
			},
			success: function (response) {
				console.log(response.data);
				var link = document.createElement("a");
                  link.href = response.data.file_to_save;

               // Set the download attribute to specify the desired filename
                 link.download = "Quotes.pdf";

            // Simulate a click on the link to trigger the download
                   link.click();
			
			},
			error: function (response) {
			
			//	console.log( response );
				
			}
		});
	});


	$('.afrfq_upload_button').click(function (e) {
		e.preventDefault();
		var image = wp.media({
			title: 'Upload Image',
			multiple: false
		}).open().on('select', function (e) {
			var uploadedImage = image.state().get('selection').first();
			var imageURL = uploadedImage.toJSON().url;
			$('#afrfq_company_logo').val(imageURL);
			$('#afrfq_company_logo_preview img').attr('src', imageURL);
		});
	});

	if ( jQuery('input#afrfq_redirect_after_submission').is(':checked') ) {
		jQuery('input#afrfq_redirect_url').closest('tr').show();
	} else {
		jQuery('input#afrfq_redirect_url').closest('tr').hide();
	}

	jQuery('input#afrfq_redirect_after_submission').change(function(){

		if ( jQuery(this).is(':checked') ) {
			jQuery('input#afrfq_redirect_url').closest('tr').show();
		} else {
			jQuery('input#afrfq_redirect_url').closest('tr').hide();
		}

	});
	
	var ajaxurl = afrfq_php_vars.admin_url;
	var nonce   = afrfq_php_vars.nonce;

	$('.multi-select').select2({
	});

	$(document).on('click', 'a.delete-quote-item', function(event){
		event.preventDefault();
		var current_button = $(this);
		console.log($('input#post_ID').val());
		$(this).closest('tr').css('opacity', '0.4');
		jQuery.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action     : 'afrfq_delete_quote_item',
				nonce      : nonce,
				quote_key  : current_button.data( 'quote_item_id' ),
				post_id    : $('input#post_ID').val(),
			},
			success: function (response) {
				// response = JSON.parse( response );
				$('#addify_quote_items_container').replaceWith( response['quote-details-table'] );

			  console.log(response.data.qoute-contents-array);
				
			},
			error: function (response) {
				jQuery(this).removeClass('loading');
				console.log( response );
				
			}
		});
	});

	$(document).on('click', '.add_option_button', function(event){
		event.preventDefault();

		var html = '<div class="option_row"><input type="text" name="afrfq_field_options[]" value=""><span type="button" title="Add Option" id="afrfq_field_add_option" class="dashicons dashicons-plus-alt2 add_option_button"></span><span type="button" title="Remove Option" class="dashicons dashicons-no-alt remove_option_button"></span></div>';
		$( html ).insertAfter( $(this).closest('div.option_row') );
	});

	$(document).on('click', '.remove_option_button', function(event){
		event.preventDefault();

		if ( $(document).find( 'div.option_row' ).length > 1 ) {
			$(this).closest( 'div.option_row').remove();
		}
		
	});
	
	$(document).ready( function(event) {
		var value = $('select[name="afrfq_field_type"]').val();
		$('select[name="afrfq_field_value"]').closest('tr').show();
		$('input[name="afrfq_field_placeholder"]').closest('tr').show();

		if ( 'select' == value || 'multiselect' == value || 'radio' == value ) {
			$('tr.options-field').show();
			$('input[name="afrfq_file_types"]').closest('tr').hide();
			$('input[name="afrfq_file_size"]').closest('tr').hide();		
			$('textarea[name="afrfq_field_terms"]').closest('tr').hide();
		} else if ( 'file' == value ) {
			$('textarea[name="afrfq_field_terms"]').closest('tr').hide();
			$('input[name="afrfq_file_types"]').closest('tr').show();
			$('input[name="afrfq_file_size"]').closest('tr').show();
			$('tr.options-field').hide();

		} else if ( 'terms_cond' == value ) {
			$('textarea[name="afrfq_field_terms"]').closest('tr').show();
			$('tr.options-field').hide();
			$('input[name="afrfq_file_types"]').closest('tr').hide();
			$('input[name="afrfq_file_size"]').closest('tr').hide();
			$('select[name="afrfq_field_value"]').closest('tr').hide();
			$('input[name="afrfq_field_placeholder"]').closest('tr').hide();
		} else {
			$('textarea[name="afrfq_field_terms"]').closest('tr').hide();
			$('tr.options-field').hide();
			$('input[name="afrfq_file_types"]').closest('tr').hide();
			$('input[name="afrfq_file_size"]').closest('tr').hide();
		}
	});

	$(document).on('change', 'select[name="afrfq_field_type"]', function(event){
		var value = $(this).val();

		$('select[name="afrfq_field_value"]').closest('tr').show();
		$('input[name="afrfq_field_placeholder"]').closest('tr').show();

		if ( 'select' == value || 'multiselect' == value || 'radio' == value ) {
			$('tr.options-field').show();
			$('input[name="afrfq_file_types"]').closest('tr').hide();
			$('input[name="afrfq_file_size"]').closest('tr').hide();		
			$('textarea[name="afrfq_field_terms"]').closest('tr').hide();
		} else if ( 'file' == value ) {
			$('textarea[name="afrfq_field_terms"]').closest('tr').hide();
			$('input[name="afrfq_file_types"]').closest('tr').show();
			$('input[name="afrfq_file_size"]').closest('tr').show();
			$('tr.options-field').hide();

		} else if ( 'terms_cond' == value ) {
			$('textarea[name="afrfq_field_terms"]').closest('tr').show();
			$('tr.options-field').hide();
			
			$('input[name="afrfq_file_types"]').closest('tr').hide();
			$('input[name="afrfq_file_size"]').closest('tr').hide();
			$('select[name="afrfq_field_value"]').closest('tr').hide();
			$('input[name="afrfq_field_placeholder"]').closest('tr').hide();
		} else {
			$('textarea[name="afrfq_field_terms"]').closest('tr').hide();
			$('tr.options-field').hide();
			$('input[name="afrfq_file_types"]').closest('tr').hide();
			$('input[name="afrfq_file_size"]').closest('tr').hide();
		}
		
	});

	$('#addify_add_item').click( function(){
		$('div#af-backbone-add-product-modal').show();
		$('.af-single_select-product').select2({

			ajax: {
				url: ajaxurl, // AJAX URL is predefined in WordPress admin
				dataType: 'json',
				type: 'POST',
				delay: 250, // delay in ms while typing when to perform a AJAX search
				data: function (params) {
					return {
						q: params.term, // search query
						action: 'afrfqsearch_product_and_variation', // AJAX action for admin-ajax.php
						nonce: nonce // AJAX nonce for admin-ajax.php
					};
				},
				processResults: function( data ) {

					var options = [];
					if ( data ) {
	   
						// data is the array of arrays, and each of them contains ID and the Label of the option
						$.each( data, function( index, text ) { // do not forget that "index" is just auto incremented value
							options.push( { id: text[0], text: text[1]  } );
						});
	   
					}
					return {
						results: options
					};
				},
				success: function( $data ){
					$('p.af-backbone-message').remove();
					$('div#af-backbone-add-product-modal button#btn-ok').removeClass('loading');
					$('div#af-backbone-add-product-modal button#btn-ok').css('opacity', '1');
				},
				cache: true
			},
			multiple: false,
			placeholder: 'Choose Product',
			minimumInputLength: 3 // the minimum of symbols to input before perform a search
			
		});		
	});

	$('div#af-backbone-add-product-modal button#btn-ok').click( function(event){

		event.preventDefault();
		if ( $(this).css('opacity') == 0.2 ) {
			return;
		}
		var current_button = $(this);
		$(this).css('opacity' ,'0.2' );
		$('p.af-backbone-message').remove();

		jQuery.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action     : 'afrfq_insert_product_row',
				nonce      : nonce,
				post_id    : current_button.val(),
				product_id : $('div#af-backbone-add-product-modal select.af-single_select-product').val(),
				quantity   : $('div#af-backbone-add-product-modal input[name="afacr_product_quantity"]').val(),
			},
			success: function (response) {

				if ( response['success'] ) {

					$('div#af-backbone-add-product-modal').hide();
					current_button.removeClass('loading');
					current_button.css('opacity', '1');
					$('#addify_quote_items_container').replaceWith( response['quote-details-table'] );

				} else {
					
					$('div#af-backbone-add-product-modal table.widefat').after("<p class='af-backbone-message'>" + response['message'] + "</p>");
				}
			},
			error: function (response) {
				jQuery(this).removeClass('loading');
				console.log( response );	
			}
		});
	});

	$('span.af-backbone-close').click( function(){
		$('div#af-backbone-add-product-modal').hide();
	});

	$(".accordion").accordion({
		active: 'none',
		collapsible: true
	});
	
	$('.ajax_customer_search').select2({
		ajax: {
			url: ajaxurl, // AJAX URL is predefined in WordPress admin.
			dataType: 'json',
			type: 'POST',
			delay: 250, // Delay in ms while typing when to perform a AJAX search.
			data: function (params) {
				return {
					q: params.term, // Search query.
					action: 'afrfq_search_users', // AJAX action for admin-ajax.php.
					nonce: nonce // AJAX nonce for admin-ajax.php.
				};
			},
			processResults: function ( data ) {
				var options = [];
				if (data ) {

					// Data is the array of arrays, and each of them contains ID and the Label of the option.
					$.each(
						data, function ( index, text ) {
							// Do not forget that "index" is just auto incremented value.
							options.push({ id: text[0], text: text[1]  });
						}
					);

				}
				return {
					results: options
				};
			},
			cache: true
		},
		multiple: false,
		placeholder: 'Choose User',
		minimumInputLength: 3 // The minimum of symbols to input before perform a search.

	});

	$('.afrfq_hide_products').select2({

		ajax: {
			url: ajaxurl, // AJAX URL is predefined in WordPress admin
			dataType: 'json',
			type: 'POST',
			delay: 250, // delay in ms while typing when to perform a AJAX search
			data: function (params) {
				return {
					q: params.term, // search query
					action: 'af_r_f_q_search_products', // AJAX action for admin-ajax.php
					nonce: nonce // AJAX nonce for admin-ajax.php
				};
			},
			processResults: function( data ) {
				var options = [];
				if ( data ) {
   
					// data is the array of arrays, and each of them contains ID and the Label of the option
					$.each( data, function( index, text ) { // do not forget that "index" is just auto incremented value
						options.push( { id: text[0], text: text[1]  } );
					});
   
				}
				return {
					results: options
				};
			},
			cache: true
		},
		multiple: true,
		placeholder: 'Choose Products',
		minimumInputLength: 3 // the minimum of symbols to input before perform a search
		
	});

	$(".namediv").click(function(){
		$(".fieldsdiv").toggle();
	});

	$(".emaildiv").click(function(){
		$(".emailfieldsdiv").toggle();
	});

	$(".companydiv").click(function(){
		$(".companyfieldsdiv").toggle();
	});

	$(".phonediv").click(function(){
		$(".phonefieldsdiv").toggle();
	});

	$(".filediv").click(function(){
		$(".filefieldsdiv").toggle();
	});

	$(".messagediv").click(function(){
		$(".messagefieldsdiv").toggle();
	});

	$(".field1div").click(function(){
		$(".field1fieldsdiv").toggle();
	});

	$(".field2div").click(function(){
		$(".field2fieldsdiv").toggle();
	});

	$(".field3div").click(function(){
		$(".field3fieldsdiv").toggle();
	});

	$('.afrfq_hide_urole').select2();

	$('#afrfq_apply_on_all_products').change(function () {
		if (this.checked) { 
			//  ^
			$('.hide_all_pro').fadeOut('fast');
		} else {
			$('.hide_all_pro').fadeIn('fast');
		}
	});

	if ($("#afrfq_apply_on_all_products").is(':checked')) {
		$(".hide_all_pro").hide();  // checked
	} else {
		$(".hide_all_pro").show();
	}

	$(".child").on("click",function() {
		$parent = $(this).prevAll(".parent");
		if ($(this).is(":checked")) {
			$parent.prop("checked",true);
		} else {
			var len = $(this).parent().find(".child:checked").length;
			$parent.prop("checked",len>0);
		}
	});
	$(".parent").on("click",function() {
		$(this).parent().find(".child").prop("checked",this.checked);
	});

	var value = $("#afrfq_rule_type option:selected").val();
	if (value == 'afrfq_for_registered_users') {
		$('#quteurr').show();
	} else {
		$('#quteurr').hide();
	}

	var value1 = $("#afrfq_is_hide_price option:selected").val();
	if (value1 == 'yes') {
		$('#hpircetext').show();
	} else {
		$('#hpircetext').hide();
	}

	var value2 = $("#afrfq_is_hide_addtocart option:selected").val();
	if (value2 == 'replace_custom' || value2 == 'addnewbutton_custom') {
		jQuery('#afcustom_link').show();
	} else {
		jQuery('#afcustom_link').hide();
	}



	afrfq_get_templete()

		function afrfq_get_templete(){
	
			var afrfq_pdf_select_layout = $( '#afrfq_pdf_select_layout' ).val();			
			
			if (afrfq_pdf_select_layout == "afrfq_template1") {
							
				$( '#afrfq_template1' ).show();
				$( '#afrfq_template2' ).hide();
				$( '#afrfq_template3' ).hide();

			}

			if (afrfq_pdf_select_layout == "afrfq_template2") {

				$( '#afrfq_template1' ).hide();
				$( '#afrfq_template2' ).show();
				$( '#afrfq_template3' ).hide();

			}

			if (afrfq_pdf_select_layout == "afrfq_template3") {

				$( '#afrfq_template1' ).hide();
				$( '#afrfq_template2' ).hide();
				$( '#afrfq_template3' ).show();

			}
		}

		$( "#afrfq_pdf_select_layout" ).on(
			"change",
			function() {
			
				afrfq_get_templete()
			}
		);
		

});

jQuery(document).ready(function($){
	
	if ( $("#afrfq_redirect_after_submission").is(':checked') ) {
		$(".URL_Quote_Submitted").show();  // checked
	} else {
		$(".URL_Quote_Submitted").hide();
	}

	$("#afrfq_redirect_after_submission").on('click' , function(){
		console.log('clicked');
		if ( $(this).is(':checked') ) {
			$(".URL_Quote_Submitted").show();  // checked
		} else {
			$(".URL_Quote_Submitted").hide();
		}
	});

});

function afrfq_getUserRole(value) {

	"use strict";
	if (value == 'afrfq_for_registered_users') {
		jQuery('#quteurr').show();
	} else {
		jQuery('#quteurr').hide();
	}
}

function afrfq_HidePrice(value) {

	"use strict";
	if (value == 'yes') {
		jQuery('#hpircetext').show();
	} else {
		jQuery('#hpircetext').hide();
	}
}

function getCustomURL(value) {

	"use strict";
	if (value == 'replace_custom' || value == 'addnewbutton_custom') {
		jQuery('#afcustom_link').show();
	} else {
		jQuery('#afcustom_link').hide();
	}

}

jQuery( function() {
	"use strict";
	jQuery( "#addify_settings_tabs" ).tabs().addClass('ui-tabs-vertical ui-helper-clearfix');
});
