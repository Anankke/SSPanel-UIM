// ui-picker.html
	$('#ui_datepicker_example_1').pickdate();

	$('#ui_datepicker_example_2').pickdate({
		cancel: 'Clear',
		closeOnCancel: false,
		closeOnSelect: true,
		container: '',
		firstDay: 1,
		format: 'You selecte!d: dddd, d mm, yy',
		formatSubmit: 'dd/mmmm/yyyy',
		ok: 'Close',
		onClose: function () {
			$('body').snackbar({
				content: 'Datepicker closes'
			});
		},
		onOpen: function () {
			$('body').snackbar({
				content: 'Datepicker opens'
			});
		},
		selectMonths: true,
		selectYears: 10,
		today: ''
	});

	$('#ui_datepicker_example_3').pickdate({
		disable: [
			[2016,0,12],
			[2016,0,13],
			[2016,0,14]
		],
		today: ''
	});

	$('#ui_datepicker_example_4').pickdate({
		disable: [
			new Date(2016,0,12),
			new Date(2016,0,13),
			new Date(2016,0,14)
		],
		today: ''
	});

	$('#ui_datepicker_example_5').pickdate({
		disable: [
			2, 4, 6
		],
		today: ''
	});

	$('#ui_datepicker_example_6').pickdate({
		disable: [
			{
				from: [2016,0,12],
				to: 2
			}
		],
		today: ''
	});

	$('#ui_datepicker_example_7').pickdate({
		disable: [
			true,
			3,
			[2016,0,13],
			new Date(2016,0,14)
		],
		today: ''
	});

	$('#ui_datepicker_example_8').pickdate({
		disable: [
			{
				from: [2016,0,10],
				to: [2016,0,30]
			},
			[2016,0,13, 'inverted'],
			{
				from: [2016,0,19],
				to: [2016,0,21],
				inverted: true
			}
		],
		today: ''
	});

	$('#ui_datepicker_example_9').pickdate({
		max: [2016,0,30],
		min: [2016,0,10],
		today: ''
	});

	$('#ui_datepicker_example_10').pickdate({
		max: new Date(2016,0,30),
		min: new Date(2016,0,10),
		today: ''
	});

	$('#ui_datepicker_example_11').pickdate({
		max: true,
		min: -10,
		today: ''
	});
