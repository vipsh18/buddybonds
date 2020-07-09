$(function() {
	if(localStorage.getItem('hm_thm')) {
	    var hm_thm = "images/" + localStorage.getItem('hm_thm');
	    $('body').css('background-image','url(' + hm_thm + ')');
	}
	//tinker this and that
	if(window.matchMedia("(min-width:200px) and (max-width:900px)").matches) {
		var $saw=$('#explore_sec').width();
		$saw-=10;
		$('.inner-image').width($saw/3);
		var $dw=$(window).width();
		$('#explore_sec').width($dw-11);
	}
	$(document).on('scroll',checkMedia);
	//set logout time on window close
	$(window).on('beforeunload', function() {
	    $.ajax({
	        type:"POST",
	        url:"http://localhost/buddyBonds_backup/scripts/set_logout_time.php",
	        success:function(result) {}
        });
	});
});
//autoplay video when scrolled to
// Get media - with autoplay disabled (audio or video)
var media=$('video').not("[autoplay='autoplay']");
var tolerancePixel=40;
function checkMedia() {
    // Get current browser top and bottom
    var scrollTop=$(window).scrollTop()+tolerancePixel;
    var scrollBottom=$(window).scrollTop()+$(window).height()-tolerancePixel;
   	media.each(function(index,el) {
        var yTopMedia=$(this).offset().top;
        var yBottomMedia=$(this).height()+yTopMedia;
        if(scrollTop<yBottomMedia&&scrollBottom>yTopMedia) $(this).get(0).play();
        else $(this).get(0).pause();
    });
}