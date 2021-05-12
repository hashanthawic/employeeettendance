$('.has-tooltip').tooltip();

$(function () { 
  $('[data-toggle="tooltip"]').tooltip({trigger: 'manual'}).tooltip('show');
});

$(function() {
	$(".switch-checkbox").bootstrapSwitch();
});

$('.open-page-popover').popover({
	title : '<div>-<a class="close" data-dismiss="alert">&times;</button></a>',
	template: '<div class="popover inline-page" role="tooltip"><div class="arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>',
	html: true,
	container: 'body',
    content: function(){
        var divID =  "tmp-id-" + $.now();
        return loadPopOverContent($(this).attr('href'), divID);
    }
});

function loadPopOverContent(link, divID){
    $.ajax({
        url: link,
        success: function(response){
            $('#' + divID).html(response);
        }
	});
	//var footer = '<div class="card-footer text-right"><a class="btn btn-sm btn-secondary close-btn">&times;</a></div>';
    return '<div class="reset-grids" id="'+ divID +'">' + pageLoadingIndicator + '</div>';// + footer;
}

(function() {
	var forms = document.getElementsByClassName('needs-validation');
	// Loop over them and prevent submission
	var validation = Array.prototype.filter.call(forms, function(form) {
		form.addEventListener('submit', function(event) {
			if (form.checkValidity() === false) {
				event.preventDefault();
				event.stopPropagation();
			}
			form.classList.add('was-validated');
			$("input:required:invalid").parents('.dropzone').css("borderColor", "red");
			$("input:required:invalid").parents('.custom-file').find('.custom-file-label').css("borderColor", "red");
			$("textarea:required:invalid").parents('.form-group').find('.note-editor').css("borderColor", "red");
		}, false);
	});
})();


$.fn.editableform.buttons = '<button type="submit" class="btn btn-sm btn-primary editable-submit">&check;</button><button type="button" class="btn btn-sm btn-secondary editable-cancel">&times;</button>';
$(function(){
	$.fn.editable.defaults.ajaxOptions = {type: "post"};
	$.fn.editable.defaults.params = {csrf_token : csrfToken};
	$.fn.editable.defaults.emptytext = '...';
	$.fn.editable.defaults.textFieldName = 'label';
	
	$('.is-editable').editable();
	
	$(document).on('click', '.inline-edit-btn', function(e){
		e.stopPropagation();
		$(this).closest('td').find('.make-editable').editable('toggle');
	});
});





	
	$('.datepicker').flatpickr({
		altInput: true, 
		allowInput:true,
		onReady: function(dateObj, dateStr, instance) {
			var $cal = $(instance.calendarContainer);
			if ($cal.find('.flatpickr-clear').length < 1) {
				$cal.append('<button class="btn btn-light my-2 flatpickr-clear">Clear</button>');
				$cal.find('.flatpickr-clear').on('click', function() {
					instance.clear();
					instance.close();
				});
			}
		}
	});



