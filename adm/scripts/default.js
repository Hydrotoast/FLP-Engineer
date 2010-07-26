$(function() {
	$('#add_site_btn').colorbox({href: 'forms/add_site.php'});
	$('#add_user_btn').colorbox({href: 'forms/add_user.php'});
	
	// Handle for marking all checkboxes
	$('#mark_all_btn').click(function() {
		$('.opts > input[type=checkbox]').attr('checked', true);
	});
});