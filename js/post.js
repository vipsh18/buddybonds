$(function() {
    var color;
    if(localStorage.getItem('themecolor')) color = localStorage.getItem('themecolor');
    else color = "#9400D3";
    $('#uplun').css('color', color);
	//cmnt btn on click
	$('#cmnt_btn').on('click',function() {
		$('#cmnt_box').focus();
	});
	//set width of comment form acc. to larger screen width
	if(window.matchMedia("(min-width:1100px) and (max-width:2000px)").matches) {
		var $mdcw=$('#modal_details_col').width();//width of mdc
		$mdcw-=90;
		$('#cmnt_box').width($mdcw);
	}
	if(window.matchMedia("(min-width:200px) and (max-width:1100px)").matches) {
		var $mdcw1=$('#modal_details_col').width();//width of mdc
		$mdcw1-=85;
		$('#cmnt_box').width($mdcw1);
	}
	//able and disable post cmnt btn
	$('#cmnt_box').on('keyup',function() {
		if($('#cmnt_box').val().length>=1) $('#pycb').prop('disabled',false);
		else $('#pycb').prop('disabled',true);
	});
	//set logout time on window close
	$(window).on('beforeunload', function() {
	    $.ajax({
	        type:"POST",
	        url:"http://localhost/buddyBonds_backup/scripts/set_logout_time.php",
	        success:function(result) {}
        });
	});
	$('.share_btn').click(function() {
        var link = "http://localhost/buddyBonds_backup/post.php?p=" + $(this).data('pid');
        var tempInput = document.createElement("input");
        tempInput.style = "position: absolute; left: -1000px; top: -1000px";
        tempInput.value = link;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        $(this).tooltip('hide').attr('data-original-title', 'Copied!').tooltip('show');
    });
    $('.share_btn').mouseout(function() {
         $(this).tooltip('hide').attr('data-original-title', 'Click To Copy Link');
    });
});