$(function() {

    //show post time on post image hover
    $('.post_img').hover(function () {
        $(this).siblings('.carousel-caption').show();
        if ($(this).is('video')) $(this)[0].play();
    }).on('mouseleave', function () {
        $(this).siblings('.carousel-caption').hide();
        if ($(this).is('video')) $(this)[0].pause();
    });

});