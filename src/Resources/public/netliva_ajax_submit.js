(function ($, window) {

	window.ajaxFormCreate = function()
	{
		$(".be_ajax_form").ajaxForm({
			dataType: 'json',
			success:ajaxFormSuccess
		});
		$(".be_ajax_form").removeClass("be_ajax_form").addClass("ajax_form");
	};

	window.ajaxFormSuccess = function (response, statusText, xhr, $form) {
		$(".has-error").removeClass("has-error has-danger");
		$form.find(".form-control-feedback").remove();
		if (response.situ === 'success')
		{

			$form.before(
				'<div class="alert alert-success" role="alert">'+
				'<h4 class="alert-heading">'+(response.message !== undefined && response.message.title !== undefined?response.message.title:doneText)+'</h4>'+
				'<p>'+(response.message !== undefined && response.message.content !== undefined ? response.message.content:doneText)+'</p>'+
				'</div>'
			);
			if(response.removeForm === false)
			{
				$(".alert").slideUp(1000);
			}

			if(response.removeForm !== false)
			{
				$form.remove();
			}

			if(response.hideModal === true || response.hideModal === undefined)
			{
				$(".modal").modal("hide");
			}

			if(response.noty !== undefined)
			{
				$.notify(response.noty.message, response.noty.class);
			}
		}
		else
		{
			$.each(response.errors, function(name, err) {
				let errorsText = '<div class="help-block form-text with-errors form-control-feedback">' +
					'<ul class="list-unstyled">';

				$.each(err, function(n, errorText) {
					errorsText += "<li>"+errorText+"</li>";
				});
				errorsText += "</ul></div>";
				$("#"+name).parent()
					.addClass("has-error has-danger")
					.find(".form-control").after(errorsText);
			});

		}
		if (response.refresh) window.location.reload();
		else if (response.url) { window.location.href = response.url; }
		else if (response.script) { eval(response.script); }
	};

})(jQuery, window);


$(document).ajaxComplete(ajaxFormCreate);
jQuery(ajaxFormCreate);
