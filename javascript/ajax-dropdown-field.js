jQuery.entwine("select2.ajaxdropdown", function ($) {

	$("select.select2.ajax-dropdown-field").entwine({
		onmatch: function () {
			var self = this;
			self.select2({
				width: '100%',
				minimumInputLength: self.data('suggestMinLength'),
				ajax: {
					url: self.data('suggestUrl'),
					dataType: 'json',
					delay: 250,
					data: function (params) {
						//params.term & params.page
						return params;
					},
					processResults: function (data, params) {
						params.page = params.page || 1;

						return {
							results: data.items,
							pagination: {
								more: data.items.length === self.data('suggestPageLength')
							}
						};
					},
					cache: false
				},
			});
		},
	});
});