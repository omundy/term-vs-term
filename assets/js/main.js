$(document).ready(function() {

	let defaultStr = 'search term';

	$('input:text').each(function() {
		if ($(this).val() == '' || $(this).val() == defaultStr) {
			$(this).val(defaultStr).css('color', '#aaa');
		}
	});

	$('input:text').focus(function() {
		if ($(this).val() == defaultStr) {
			$(this).val('').css('color', '#555');
		}
	});

	$('input:text').blur(function() {
		if ($(this).val() == "") {
			$(this).val(defaultStr).css('color', '#aaa');
		}
	});

	$('.compare-btn').on('click', submitForm);

	$("input:text").keypress(function(e) {
		if (e.which == 13) {
			submitForm();
		}
	});
});

function submitForm(){
	let url = `?q1=${$('.q1').val()}&q2=${$('.q2').val()}`;
	window.location.href = url;
}
