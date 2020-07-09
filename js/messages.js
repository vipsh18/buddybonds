$(function() {
	//
	//remove comments when you wanna update chats every 1000 ms
	//setInterval(function() {
	//	update_chats();
	//}, 1000);
	//
	//set height of side by side divs
	if(window.matchMedia("(min-width:1000px) and (max-width:2000px)").matches) {
		$('.col-lg-3').css('max-height', $(window).height() - 40);
		$('.col-lg-6').css('max-height', $(window).height() - 40);
	}
	if(window.matchMedia("(min-width:900px) and (max-width:2000px)").matches) {
		//hide fas search
		$('#search_chat').on('focus',function() {
			$('#search_chat_fas').hide();
		});	
		//show back fas search
		$('#search_chat').on('blur',function() {
			var srch_len=$('#search_chat').val().length;
			if(srch_len<=0) {
				$('#search_chat_fas').fadeIn();
			}	
		});
	}
	//set container width to device width
	var $dw=$(window).width();
	$('#container').width($dw);
	if(window.matchMedia("(min-width:200px) and (max-width:900px)").matches) $('#srch_sugg').width($dw-35);
	if(window.matchMedia("(min-width:1020px) and (max-width:1100px)").matches) {
		$('#search_chat').css('width','100%');
		$('#search_chat_fas').hide();
		$('.srch_row').css('padding','5px');
		$('.srch_img').css({'width':'25px','height':'25px'});
		var $scw=$('#search_chat').width();
		$('#srch_fn').css('font-size','10px');
		$('#srch_sugg').width($scw+35);
		$('#chat_div').css('min-height','768px');
		$dh=$(window).height();
		$('#sbtc_div').css('min-height',$dh);
		$('#cg_div').css('min-height',$dh);
	}
	if(window.matchMedia("(min-width:768px) and (max-width:1020px)").matches) {
		$('#cg_div').hide();
		$('.msg_cntr').hide();
		$('#sbtc_list').hide();
		$('#srch_sugg').css({'position':'static','width':'100%'});
		$('#search_chat_fas').css({'left':'370px'});
	}
	//search buddy to chat bar
	$('#search_chat').on('keyup',function() {
		$('#suggestions').hide();
		$('#m_suggestions').hide();
		var srch=$('#search_chat').val();
		if(srch.length>=1) {
			$('#srch_spnr').show();
			$('#srch_sugg').hide();
			$.ajax({
				type:"POST",
				url:"http://localhost/buddyBonds_backup/scripts/srch_sugg.php",
				data:{
					"srch":srch
				},
				success:function(result) {
					$('#srch_sugg').show();
					$('#srch_sugg').html(result);
					$('#srch_spnr').hide();
				}
			});
		} else $('#srch_sugg').hide();
	});
	$('#search').on('focus',function() {
		$('#srch_sugg').hide();
	});
	//grp checkboxes div
	$('.grp_checkboxes_div').click(function() {
		if($(this).find(':checkbox').is(':checked')) {
			$(this).find(':checkbox').prop('checked', false);
			$(this).find('.grp_checkboxes_label').html('<i class="far fa-circle"></i>');
			var sp = parseInt($('#selected_participants').html(), 10) - 1;
			$('#selected_participants').html(sp);
			if(sp >= 3) {
				if($('#grp_tobemade_name').val().length >= 1) $('#create_grp_btn').prop('disabled', false);
				$('#grp_tobemade_picbtn').fadeIn();
				$('#grp_tobemade_name').fadeIn();
				$('#grp_tobemade_picname').fadeIn();
			}
			else {
				$('#create_grp_btn').prop('disabled', true);
				$('#grp_tobemade_picbtn').hide();
				$('#grp_tobemade_name').hide();
				$('#grp_tobemade_picname').hide();
			}
			$(this).css('background-color','white');
			$(this).tooltip('hide').attr('data-original-title', "Add To Group").tooltip('show');
		} else {
			$(this).find(':checkbox').prop('checked', true);
			$(this).find('.grp_checkboxes_label').html('<i class="fas fa-check-circle"></i>');
			var sp = parseInt($('#selected_participants').html(), 10) + 1;
			$('#selected_participants').html(sp);
			if(sp >= 3) {
				if($('#grp_tobemade_name').val().length >= 1) $('#create_grp_btn').prop('disabled', false);
				$('#grp_tobemade_picbtn').fadeIn();
				$('#grp_tobemade_name').fadeIn();
				$('#grp_tobemade_picname').fadeIn();
			}
			else {
				$('#create_grp_btn').prop('disabled', true);
				$('#grp_tobemade_picbtn').hide();
				$('#grp_tobemade_name').hide();
				$('#grp_tobemade_picname').hide();
			}
			$(this).css('background-color','lavender');
			$(this).tooltip('hide').attr('data-original-title', "Do Not Add To Group").tooltip('show');
			if(sp >= 26) {
				alert("Sorry, the current maximum for Group Members is 25");
				sp = 25;
				$('#selected_participants').html(sp);
				$(this).find(':checkbox').prop('checked', false);
				$(this).css('background-color','white');
				$(this).find('.grp_checkboxes_label').html('<i class="far fa-circle"></i>');
				$(this).tooltip('hide').attr('data-original-title', "Add To Group").tooltip('show');
			}
		}
	});
	$('#grp_tobemade_name').on('focus', function() {
		$(this).css('border-bottom','2px solid grey');
	});		
	$('#grp_tobemade_name').on('blur', function() {
		$(this).css('border-bottom','none');
	});	
	$('#grp_tobemade_name').keyup(function() {
		if($(this).val().length >= 1) $('#create_grp_btn').prop('disabled', false);
		else $('#create_grp_btn').prop('disabled', true);
	});
	$('#grp_tobemade_picbtn').click(function() {
		$('#grp_tobemade_pic').click();
	});
	$('#grp_tobemade_pic').change(function() {
		var file = this.files[0];
		var filetype = file.type;
		var match= ["image/jpeg", "image/png", "image/jpg"];
        if(!((filetype==match[0]) || (filetype==match[1]) || (filetype==match[2]))) {
            alert('Please select a valid image file (JPEG/JPG/PNG).');
            return false;
        } else $('#grp_tobemade_picname').html(file.name);
	});
	$('#create_grp_btn').click(function() {

	});
});
//
//to update the chats
//
function update_chats() {
	$.ajax({
		type:"POST",
		url:"http://localhost/buddyBonds_backup/scripts/update_chats.php",
		success:function(result) {
			$('#chats').html(result);
		}
	});
}
//check for edge
var winNav = window.navigator;
var isIEedge = winNav.userAgent.indexOf("Edge") > -1;
if(isIEedge) $('#search_chat_fas').hide();