(function ($, window) {

	window.netlivaDependentPrepeare = function (formId, dependId, settings)
	{
		settings = $.extend({
			value: null,
			option_path: null,
		}, settings);

		var $element = $("#"+formId);
		var $dependent = $("#"+dependId);
		var ajx;
		$dependent.change(function(){
			var val = $(this).val();
			console.log(val);
			$element.prop("disabled",true).html("<option>Yükleniyor...</option>");
			if (ajx != undefined)
			{
				ajx.abort();
				setTimeout(function () { $("#loading").hide();},1000);
			}
			// console.log(formId, "- ",val);
			if (val)
			{
				ajx = $.ajax({
					url:settings.option_path,
					data : { val:val }, dataType:"json",
					type:"post",
					success:function(response){
						if (response.options)
						{
							$element.prop("disabled",false).html(response.options);
							setTimeout(function () {
								if($element.find("option[value='"+settings.value+"']").length)
									$element.val(settings.value);
								$element.change();
							},100);
						}
						else
						{
							$element.html("<option>"+ settings.default +"</option>");
							setTimeout(function () { $element.val(settings.default).change().prop("disabled",true);},100);
						}
					}
				});
			}
			else
			{
				$element.html("<option>"+ settings.default +"</option>");
				setTimeout(function () { $element.val(settings.default).change().prop("disabled",true);},100);
			}
		});
		if (settings.value || $dependent.val())
			$dependent.change();
		
	};

})(jQuery, window);
