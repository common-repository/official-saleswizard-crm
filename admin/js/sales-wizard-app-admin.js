(function( $ ) {
	'use strict';
	$(document).ready(function(){
	// Get the current URL
	var currentUrl = window.location.href;
	var crmFieldsJson 	= sales_wizard_app.crm_fields;
	var wpcf7Text 		= sales_wizard_app.wpcf7;
	var WPFormText 		= sales_wizard_app.WPForm;

	// Use URLSearchParams to extract the value of the "tab" parameter
	var urlParams = new URLSearchParams(currentUrl);
	var tabValue = urlParams.get('tab')?urlParams.get('tab'):'wpcf7';


	$(document).on('click','.password-hidden', function() {
            if ($('.form__password').attr('type') == 'text') {
                $('.form__password').attr('type', 'password');
            } else {
                $('.form__password').attr('type', 'text');
            }
        });


	

	$(document).on('click','#add',function(){
		let crmFields = $("table tbody tr:last");
		
		let lastKey = crmFields.data('last-key');
		let key = lastKey+1;
		
		
		
		var clonedRow = $("table tbody tr:last").clone();
		
		
		clonedRow.find('input[type="hidden"]').attr('name','sales_wizard_app_mapping_field['+key+'][crm_field]');
		clonedRow.find('input[type="text"]').attr('name','sales_wizard_app_mapping_field['+key+'][form_field]');
		
		clonedRow.find('.remove').removeClass('hidden-el');
		
		$(".formulas-table tbody").append(clonedRow);
		clonedRow.attr('data-last-key',key);
		disableSelectCrmNameOption();

		let crmLastFields = $("table tbody tr:last");
		let lastsKey = crmLastFields.data('last-key');
		if(lastsKey >0){
			let removeButton = crmLastFields.find('.remove');
			removeButton.show();
		}
	});
	let crmFields = $("table tbody tr:first");
	let firstKey = crmFields.data('last-key');
	let removeButton = crmFields.find('.remove');
	if(firstKey ==0){
		removeButton.hide();
	}
	$(document).on('click','.remove',function(){
		$(this).parents("tr").remove();
		disableSelectCrmNameOption()
	});
	$(document).on('click','#column-list',function(e){
		e.preventDefault();
		$('#column-id-list').css('display','block');
	});
	$(document).on('click','#close-modal',function(e){
		$('#column-id-list').css('display','none');
	});
	$(document).on('change','.crm-fields',function(e){
		let th 			= $( this ).closest( 'th' );
		let nameField 	= th.find('input[type="hidden"]');
		let fieldValue = $( this ).val();
		nameField.val(fieldValue);
		
		disableSelectCrmNameOption();
	});
	disableSelectCrmNameOption();
	function disableSelectCrmNameOption(){
		let options = $('.crm-fields option');
		options.prop('disabled', false);
		var tableRow 	= $("table tbody tr");
		
		let crmFields 	= tableRow.find('.crm-fields');
	
		for (let i = 0; i < crmFields.length; i++) {
			let optionVal = $(crmFields[i]).val();
			$('.crm-fields option[value="' + optionVal + '"]').prop('disabled', true);
		}
	}
	
	if (typeof $('#sales_wizard_app_webhook_form_type') !== 'undefined' && $('#sales_wizard_app_webhook_form_type').length >0) {
		let formType = $('#sales_wizard_app_webhook_form_type').find(":selected").val();
		
		if (formType =='wpcf7') {
			$('.field-type-text').text(wpcf7Text);
			$(".sales_wizard_app_wpcf7").removeClass('hidden-el');
		}
		if (formType =='wpforms') {
			$('.field-type-text').text(WPFormText);
			$(".sales_wizard_app_wpform").removeClass('hidden-el');
		}

	}
	$(document).on('change','#sales_wizard_app_webhook_form_type',function(){
		let formType = $(this).val();
		
        if (formType =='wpcf7') {
			$('.field-type-text').text(wpcf7Text);
			
			$(".sales_wizard_app_wpcf7").removeClass('hidden-el');
			$(".sales_wizard_app_wpform").addClass('hidden-el');
		}
		if (formType == 'wpforms'){
			
			$('.field-type-text').text(WPFormText);
			$(".sales_wizard_app_wpcf7").addClass('hidden-el');
			$(".sales_wizard_app_wpform").removeClass('hidden-el');
		}
	});
});
})( jQuery );