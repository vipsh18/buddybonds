$(function () {

	//tooltip and popover activation
	$('[data-toggle="tooltip"]').tooltip();
	$('[data-toggle="popover"]').popover();
	$("#theme_toast").toast({
		delay: 4000
	});
	$("#mode_toast").toast({
		delay: 4000
	});
	$("#message_toast").toast({
		delay: 6000
	});

	//var declarations
	var mode, themecolor;
	var dark_mode = "#1e1e2f";
	var default_mode = "#f5f6fa";
	var semi_dark = "#27293d";
	var text_muted = "#4b515d";
	var tm2 = "#9a9a9a";
	var tm_white = "#525f7f";
	var dc_grey = "#dcdcdc";
	var def_bg_dark = "#dde0ee";
	var themecolor_arr = ["#9400D3", "#FF0000", "#008080", "#FFD700", "#00FF7F", "#663399",
		"#FF6347", "#800080", "#FF00FF", "#00BFFF", "#4169E1", "#008000", "#FF1493", "#483D8B", "#FF4500"
	];
	var themecolor_dict = {
		"#9400D3": "Dark Violet", "#FF0000": "Red", "#008080": "Teal", "#FFD700":"Gold", "#00FF7F":"Spring Green", 
		"#663399":"Rebecca Purple", "#FF6347":"Tomato Red", "#800080":"Purple", "#FF00FF":"Fuchsia", "#00BFFF":"Deep Sky Blue", 
		"#4169E1":"Royal Blue", "#008000":"Green", "#FF1493":"Deep Purple", "#483D8B":"Dark Slate Blue", "#FF4500":"Orange"
	};
	var updateWorker = new Worker("js/update_worker.js");
	var ct = $(document).prop('title');

	//function declarations
	//To set mode acc. to user
	function set_bgmode(bg_mode) {
		localStorage.setItem("mode", bg_mode);
		mode = bg_mode;
		if (bg_mode == dark_mode) {
			$('body').css('background-color', bg_mode);
			$('.ccatm').css('color', 'white');
			$('.cbatm').css('background-color', semi_dark);
			$('.toast-header').css('background-color', semi_dark);
			$('.toast-header > small').css('color', 'white');
			$('#nav').css('background-color', bg_mode);
			$('.enclose_post').css('border', '0');
		} else {
			$('body').css('background-color', default_mode);
			$('.ccatm').css('color', dark_mode);
			$('.cbatm').css('background-color', 'white');
			$('.toast-header').css('background-color', dc_grey);
			$('.toast-header > small').css('color', text_muted);
			$('#nav').css('background-color', default_mode);
			$('.enclose_post').css('border', '2px solid ' + dc_grey);
		}
		modify_inputs(bg_mode);
	}

	//To set themecolor acc. to user
	function set_themecolor(color) {
		localStorage.setItem("themecolor", color);
		themecolor = color
		//apply to #nav top border
		$('#nav').css('border-top', '3px solid ' + color);
		//apply to sidebar
		$('#nav2').css('background-color', color);
		//set text for theme change toast
		$('#theme_toast .toast-body').html("buddyBonds theme changed to " + themecolor_dict[color]);
		//
	}

	//To modify inputs acc. to mode
	function modify_inputs(mode) {
		if (mode == dark_mode) {
			$('#search').css('background-color', mode);
			$('#search').removeClass("pseudo_modify_input_dark");
			$('#search').addClass("pseudo_modify_input_light");
			$('#search').css('border', '2px solid white');
		} else {
			$('#search').css('background-color', default_mode);
			$('#search').removeClass("pseudo_modify_input_light");
			$('#search').addClass("pseudo_modify_input_dark");
			$('#search').css('border', '2px solid ' + dark_mode);
		}
	}

	//To check for new updates
	function check_updates() {
		updateWorker.postMessage("1");
		updateWorker.onmessage = function (e) {
			var result = e.data;
			if (result.new_msg >= 1 || result.new_ntf >= 1) {
				if (result.new_msg >= 1 && result.new_ntf >= 1) {
					var tot_ntfs = result.new_msg + result.new_ntf;
					$('#updated_msgs').addClass('badge badge-danger');
					$('#updated_ntfs').addClass('badge badge-danger');
					$('#updated_msgs').html(result.new_msg);
					$('#updated_ntfs').html(result.new_ntf);
					$(document).prop('title', ct + " • " + tot_ntfs + " New Notifications");
					$('#message_toast .toast-body').html("<a href='messages.php' style='color: " + dark_mode + "'><b>1 New Message</b> and 1 <b>New Notification</b> from your buddies. Join them online to make your bond stronger</a>");
					if (!sessionStorage.getItem("ntfs_rec_mn")) $('#message_toast').toast('show');
					sessionStorage.setItem("ntfs_rec_mn", "true");
				} else if (result.new_msg >= 1 && result.new_ntf <= 0) {
					$('#updated_msgs').addClass('badge badge-danger');
					$('#updated_msgs').html(result.new_msg);
					if (result.new_msg >= 2) {
						$(document).prop('title', ct + " • " + result.new_msg + " New Chats");
						$('#message_toast .toast-body').html("<a href='messages.php' style='color: " + dark_mode + "'><b>" + result.new_msg + " New Chats</b> from your buddies. Join them online to make your bond stronger</a>");
					} else {
						$(document).prop('title', ct + " • " + result.new_msg + " New Chat");
						$('#message_toast .toast-body').html("<a href='messages.php' style='color: " + dark_mode + "'><b>1 New Chat</b> from your buddy. Join them online to make your bond stronger</a>");
					}
					if (!sessionStorage.getItem("ntfs_rec_m")) $('#message_toast').toast('show');
					sessionStorage.setItem("ntfs_rec_m", "true");
				} else if (result.new_msg <= 0 && result.new_ntf >= 1) {
					$('#updated_ntfs').addClass('badge badge-danger');
					$('#updated_ntfs').html(result.new_ntf);
					if (result.new_ntf >= 2) {
						$(document).prop('title', ct + " • " + result.new_ntf + " New Notifications");
						$('#message_toast .toast-body').html("<a href='notifications.php' style='color: " + dark_mode + "'><b>" + result.new_ntf + " New Notifications</b> from your buddies. Join them online to make your bond stronger</a>");
					} else {
						$(document).prop('title', ct + " • " + result.new_ntf + " New Notification");
						$('#message_toast .toast-body').html("<a href='notifications.php' style='color: " + dark_mode + "'><b>1 New Notification</b> from your buddies. Join them online to make your bond stronger</a>");
					}
					if (!sessionStorage.getItem("ntfs_rec_n")) $('#message_toast').toast('show');
					sessionStorage.setItem("ntfs_rec_n", "true");
				}
			}
		}
		setTimeout(check_updates, 5000);
	}

	//call fn to check for updates
	check_updates();

	//set bg color initially
	if (localStorage.getItem('mode') == dark_mode) mode = localStorage.getItem('mode');
	else mode = default_mode;
	set_bgmode(mode);

	//set theme color initially, fetching from db user's themecolor
	$.ajax({
		type: "GET",
		url: "http://localhost/buddyBonds_backup/scripts/get_theme.php",
		data: {
			"theme_id": uid
		}
	}).done(function (color) {
		set_themecolor(color);
	});

	//change search placeholder on focus & blur
	$('#search').focus(function () {
		$('#search::-moz-placeholder').hide();
		$('#search::-webkit-input-placeholder').hide();
		$(this).attr('placeholder', '\uf002');
		$(this).css('box-shadow', '0 0 0 0 ' + mode);
	}).blur(function () {
		$(this).attr('placeholder', '\uf002 Search buddyBonds');
		modify_inputs(mode);
	});

	//scroll to top on central bB link click
	$('#nn_ch').click(function () {
		if ($(window).scrollTop() >= 2500) $(window).scrollTop(0);
		else location.reload();
	});

	//nav2 header wallpaper - set dims
	$('#nav2_hdr_wp').css({
		"width": $('#nav2').width(),
		"border-radius": "8px 8px 0 0"
	});

	//set search placeholder
	$('#search').attr('placeholder', '\uf002 Search buddyBonds');

	//set theme box colors
	$('#theme_box_popover').on('shown.bs.popover', function () {
		//
		$('#tbp_tooltip').tooltip('hide');
		$('.theme_box_pc').each(function (i) {
			$(this).data("themecolor", themecolor_arr[i]);
			$(this).css('background-color', $(this).data('themecolor'));
		});
		//set theme box functionality
		$('.theme_box_pc').click(function () {
			color = $(this).data('themecolor');
			localStorage.setItem('themecolor', color);
			$.ajax({
				type: "GET",
				url: "http://localhost/buddyBonds_backup/scripts/set_theme.php",
				data: {
					"themecolor": color
				}
			}).done(function (result) {
				set_themecolor(color);
			});
			$('#theme_toast').toast('show');
		});
	});

	//hide themes tooltip if themes popover is shown
	$('#tbp_tooltip').on('shown.bs.tooltip', function () {
		if ($('.theme_box_pc').is(":visible")) $('#tbp_tooltip').tooltip('hide');
	});

	//random no. for getting wallppr no.
	var rand = Math.floor((Math.random() * 33) + 1);
	$('#nav2_hdr_wp').attr('src', 'images/wp' + rand + '.jpg');

	//set mode button functionality
	$('#tdm').click(function () {
		if (mode == dark_mode) {
			set_bgmode(default_mode);
			$('#mode_toast .toast-body').html("buddyBonds switched to Default Mode");
		} else {
			set_bgmode(dark_mode);
			$('#mode_toast .toast-body').html("buddyBonds switched to Dark Mode");
		}
		$('#mode_toast').toast('show');
	});

	//
});