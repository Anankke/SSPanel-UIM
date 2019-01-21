// ui-snackbar.html
	var snackbarText = 1;

	$('#ui_snackbar_toggle_1').on('click', function () {
		$('body').snackbar({
			content: 'Simple snackbar ' + snackbarText + ' with some text',
			show: function () {
				snackbarText++;
			}
		});
	});

	$('#ui_snackbar_toggle_2').on('click', function () {
		$('body').snackbar({
			content: '<a data-dismiss="snackbar">Dismiss</a><div class="snackbar-text">Simple snackbar ' + snackbarText + ' with some text and a simple <a href="javascript:void(0)">link</a>.</div>',
			show: function () {
				snackbarText++;
			}
		});
	});
