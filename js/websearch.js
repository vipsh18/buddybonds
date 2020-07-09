$(function () {
	//
	//dynamically change href acc to query val and search type
	// 
	$('.srch_types').click(function () { 
		$('.srch_types').prop('href', 'websearch.php?q=' + $('#q').val() + '&type=' + $(this).data('type'));
	});
	//
	//highlight preferred search type
	//
	var url = new URL(window.location.href);
	var type = url.searchParams.get("type");
	if (type == "images") $('#wst_i').removeClass('btn-outline-primary').addClass('btn-primary');
	else if (type == "videos") $('#wst_v').removeClass('btn-outline-danger').addClass('btn-danger');
	else if (type == "news") $('#wst_n').removeClass('btn-outline-success').addClass('btn-success');
	else if (type == "books") $('#wst_b').removeClass('btn-outline-info').addClass('btn-info');
	//
	//do that something
	var q = url.searchParams.get("q");
	if (q != null) {
		$('#no_login_ws_head').removeClass("text-center");
		$('#no_login_ws_head').css('margin', '10px 190px 0 170px');
	}
	//
	$('#websearch_form').submit(function (e) {
		e.preventDefault();
		if (type == "images" || type == "videos" || type == "news" || type == "books")
			window.location.href = "http://localhost/buddybonds_backup/websearch.php?q=" + $('#q').val() + '&type=' + type;
		else
			window.location.href = "http://localhost/buddybonds_backup/websearch.php?q=" + $('#q').val();
	});
	//
	$('#q').focus(function () {
		$('#q::-moz-placeholder').hide();
		$(this).attr('placeholder', '\uf002');
	}).blur(function () {
		$(this).attr('placeholder', 'Type Here To Search Web');
	});
	$('#srch_wb').on('keyup', function () {
		var str = $('#srch_wb').val();
		if (str.length >= 1) {
			$('#spnr_wb').show();
			$('#srchwb_sugg').hide();
			$('#srchwb_rb').hide();
			$.ajax({
				type: "POST",
				url: "http://localhost/buddyBonds_backup/scripts/search_web_results.php",
				data: {
					"q": str
				},
				success: function (result) {
					$('#srchwb_sugg').show();
					$('#srchwb_sugg').html(result);
					$('#spnr_wb').hide();
					$('#srchwb_rb').show();
				}
			});
		} else {
			$('#srchwb_sugg').hide();
		}
	});
	//search reset btn
	$('#srch_wb').on('focus', function () {
		$('#srchwb_rb').fadeIn();
	});
	//search reset btn close
	$('#srch_wb').on('blur', function () {
		$('#srchwb_rb').fadeOut();
	});
	//show suggestions
	$('#srch_wb').on('focus', function () {
		var val = $('#srch_wb').val();
		if (val.length >= 0) {
			$('#srchwb_sugg').show();
		}
	});
	//hide suggestions
	$('#srch_wb').on('blur', function () {
		var val = $('#srch_wb').val();
		if (val.length <= 0) {
			$('#srchwb_sugg').hide();
		}
	});
});