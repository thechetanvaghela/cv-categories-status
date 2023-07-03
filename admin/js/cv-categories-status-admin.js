jQuery(document).ready(function(){

	if(jQuery('.toplevel_page_cvcs_admin_menu_settings_page #message').length > 0)
	{
		jQuery('body').addClass('cvcs-bg-overlay');
	}
   	jQuery(document).on("click",".toplevel_page_cvcs_admin_menu_settings_page #message button.notice-dismiss",function() {
    	jQuery('body').removeClass('cvcs-bg-overlay');
    });

	/*check box*/
    jQuery('#cvcs-setting-container .toggle input[type="checkbox"]').click(function(){
        jQuery(this).parent().toggleClass('on');

        if (jQuery(this).parent().hasClass('on')) {
            jQuery(this).parent().children('.label').text('On')
        } else {
            jQuery(this).parent().children('.label').text('Off')
        }
    });


    jQuery('#cvcs-setting-container input').focusin (function() {
        jQuery(this).parent().addClass('focus');
    });
    jQuery('#cvcs-setting-container input').focusout (function() {
        jQuery(this).parent().removeClass('focus');
    });

    /* check all plugins */
    jQuery(".checkedAllTaxo").change(function(){
	    if(this.checked){
	      jQuery(".checkSingleptaxo").each(function(){
	        this.checked=true;
	      	jQuery(this).parent().children('.label').text('On');
	        jQuery(this).parent().addClass('on');
	        
	      })              
	    }else{
	      jQuery(".checkSingleptaxo").each(function(){
	        this.checked=false;
	        jQuery(this).parent().children('.label').text('Off');
	        jQuery(this).parent().removeClass('on');
	      })              
	    }
  });
  jQuery(".checkSingleptaxo").click(function () {
    if (jQuery(this).is(":checked")){
      var isAllChecked = 0;
      jQuery(".checkSingleptaxo").each(function(){
        if(!this.checked)
           isAllChecked = 1;
      })              
      if(isAllChecked == 0){ 
      	jQuery(".checkedAllTaxo").prop("checked", true); 
      	jQuery(".checkedAllTaxo").parent().children('.label').text('On');
      	jQuery(".checkedAllTaxo").parent().addClass('on');
      }     
    }else {
      jQuery(".checkedAllTaxo").prop("checked", false);
      jQuery(".checkedAllTaxo").parent().removeClass('on');
      jQuery(".checkedAllTaxo").parent().children('.label').text('Off');
    }
  });


});