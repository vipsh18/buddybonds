$(function () {
	check_updates();
	//set logout time on window close
	$(window).on('beforeunload', function () {
		$.ajax({
			type: "POST",
			url: "http://localhost/buddyBonds_backup/scripts/set_logout_time.php",
			success: function (result) {}
		});
	});
});
var ct = $(document).prop('title');

function newNotification(Title, Body, Icon, act, act_ttl) {
	var notification = new Notification(Title, {
		body: Body,
		icon: Icon
	});
}
var ntfs_sent = false;
//function check for new updates
function check_updates() {
	fetch("http://localhost/buddyBonds_backup/scripts/check_for_updates.php", {
			method: "POST",
		}).then(res => res.json())
		.then(result => {
			if (result.new_msg >= 1 || result.new_ntf >= 1) {
				if (result.new_msg >= 1 && result.new_ntf >= 1) {
					var tot_ntfs = result.new_msg + result.new_ntf;
					$('#updated_msgs').addClass('badge badge-danger');
					$('#updated_ntfs').addClass('badge badge-danger');
					$('#m_updated_ntfs').addClass('badge badge-danger');
					$('#m_updated_msgs').addClass('badge badge-danger');
					$('#updated_msgs').html(result.new_msg);
					$('#updated_ntfs').html(result.new_ntf);
					$('#m_updated_ntfs').html(result.new_ntf);
					$('#m_updated_msgs').html(result.new_msg);
					if (!ntfs_sent) {
						newNotification("buddyBonds", "You Have A New Unseen Message And Notification On buddyBonds", "../images/color-star-3-48-217610.png");
						ntfs_sent = true;
					}
					$(document).prop('title', ct + " • " + tot_ntfs + " New Notifications");
				} else if (result.new_msg >= 1 && result.new_ntf <= 0) {
					$('#updated_msgs').addClass('badge badge-danger');
					$('#m_updated_msgs').addClass('badge badge-danger');
					$('#updated_msgs').html(result.new_msg);
					$('#m_updated_msgs').html(result.new_msg);
					if (!ntfs_sent) {
						newNotification("buddyBonds", "You Have A New Unseen Message On buddyBonds", "../images/color-star-3-48-217610.png");
						ntfs_sent = true;
					}
					$(document).prop('title', ct + " • " + result.new_msg + " New Notifications");
				} else if (result.new_msg <= 0 && result.new_ntf >= 1) {
					$('#updated_ntfs').addClass('badge badge-danger');
					$('#m_updated_ntfs').addClass('badge badge-danger');
					$('#updated_ntfs').html(result.new_ntf);
					$('#m_updated_ntfs').html(result.new_ntf);
					if (!ntfs_sent) {
						newNotification("buddyBonds", "You Have A New Unseen Notification On buddyBonds", "../images/color-star-3-48-217610.png", "Get", "IT NOw");
						ntfs_sent = true;
					}
					$(document).prop('title', ct + " • " + result.new_ntf + " New Notifications");
				}
			}
			setTimeout(check_updates, 5000);
		}).catch(error => console.log(Error(error)));
}