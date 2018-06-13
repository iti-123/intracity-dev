//////////////////////

//////////////////////

$(document).ready(function () {
    $(".service_left li").each(function () {
        $(this).removeClass('submenu-dropdown').removeClass('intra_active');
    });
    $(".service_left li:last").addClass('submenu-dropdown').addClass('intra_active');
   
});


///////////////////////

////////////////////////
