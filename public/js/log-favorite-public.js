jQuery(document).ready(function () {
	jQuery('#add-favorite').submit(function (e) {
	  e.preventDefault();

	  var ContactForm = jQuery('#add-favorite').serialize();
	  jQuery.ajax({
	    type:    'POST',
	    url:     mainajax.ajaxurl,
	    data:    ContactForm,
	    success: function(data) {
	       alert(data);
	       location.reload();
	    }
	  });
	  return false;  
	});
});

