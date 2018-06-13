$(document).ready(function () {
    $("#Discounts_id").click(function () {
        $("#Discounts_show").toggle();
    });
    $("#Discounts_id_1").click(function () {
        $("#Discounts_oepn").toggle();
    });
    $(".materialSelector.ng-scope.ng-binding").on("click", function () {

    });

    var xw = $(window).width();

    $(".detail_b").click(function () {
        var x = $(this).attr("data-id");
        $(this).toggleClass("open");
        $("#" + x).toggleClass("detail_show");
    });

    $('.domastic >li').click(function () {
        var id1 = $(this).attr('data-tab');
        $('.domastic >li').removeClass("active_tabs");
        $(this).addClass("active_tabs");
        $(".training_m").removeClass("activetab1");
        $('#' + id1).addClass('activetab1');
    });


    if (xw > 1001) {
        $('.btn_f >a').click(function () {
            var id = $(this).attr('data-tab');
            $('.btn_f >a').removeClass("active_ts");
            $(this).addClass("active_ts");
            $('#' + id).addClass('active_class').siblings().removeClass('active_class');

        });
        $('.s_content').click(function () {
            var id1 = $(this).attr('data-tab');
            $('.s_content').removeClass("active_services");
            $(this).addClass("active_services");
            $(".loin_log").removeClass('active_serv');
            $('#' + id1).addClass('active_serv');
        });

    } else {
        $('.btn_f >a').click(function () {
            var id = $(this).attr('data-tab');
            $('.btn_f >a').removeClass("active_ts");
            $(this).addClass("active_ts");
            $('#' + id).toggleClass('active_class');

        });
        $('.s_content').click(function () {
            var id1 = $(this).attr('data-tab');
            //  $('.s_content').removeClass("active_services");
            $(this).toggleClass("active_services");
            //  $(".loin_log").removeClass('active_serv')
            $('#' + id1).toggleClass('active_serv');
        });
    }


    $('.inter_tab >li').click(function () {
        var id = $(this).attr('data-tab');
        $('.training_menu1 >li').removeClass("activem_inter");
        $(this).addClass("activem_inter");
        $(".traing_sub").removeClass('activetab1_internat');
        $('#' + id).addClass('activetab1_internat');
    });

    $('.tab_content >ul >li').click(function () {
        var id = $(this).attr('data-tab');
        $('.tab_content ul li').removeClass("active_b");
        $(this).addClass("active_b");
        $('#' + id).addClass('activetab').siblings().removeClass('activetab');
    });

    $('.training_menu >li ,.training_menu1 >li').click(function () {
        var id = $(this).attr('data-tab');
        $('.training_menu >li ,.training_menu1 >li').removeClass("activem");
        $(this).addClass("activem");
        $('#' + id).addClass('activetab').siblings().removeClass('activetab');
    });

    $('.menu_sub_msg >.tab_1').click(function () {
        var id = $(this).attr('data-tab');
        $('.menu_sub_msg >.tab_1').removeClass("active_mesg");
        $(this).addClass("active_mesg");
        $('#' + id).addClass('activetab').siblings().removeClass('activetab');
    });
    $('.menu_sub_msg >.tab_2').click(function () {
        var id = $(this).attr('data-tab');
        $('.menu_sub_msg >.tab_2').removeClass("active_mesg");
        $(this).addClass("active_mesg");
        $('#' + id).addClass('activetab').siblings().removeClass('activetab');
    });
    $('.buyer_ul >.tab_10').click(function () {
        var id = $(this).attr('data-tab');
        $('.buyer_ul >.tab_10').removeClass("active_buyer");
        $(this).addClass("active_buyer");
        $('#' + id).addClass('buyer_activetab').siblings().removeClass('buyer_activetab');
    });


    //$("button").click(function () {
    //    var selected = $("#dropdown option:selected").text();
    //    var departing = $("#departing").val();
    //    var departing1 = $("#departing1").val();
    //    var returning = $("#returning").val();
    //    if (departing === "" || returning === "" || departing1 === "") {
    //        alert("Please select departing and returning dates.");
    //    } else {
    //        confirm("Would you like to go to " + selected + " on " + departing + " and return on " + returning + "?");
    //    }
    //});

    $('input[type="radio"]').click(function () {
        var id = $(this).attr('data-target');
        $('#' + id).addClass('activeintra').siblings().removeClass('activeintra');
    });

    $('#myCarousel').on('slid', function (e) {
        // This event is fired when the carousel has completed its slide transition.
        $("#myCarousel .active h4").removeClass("fadeInDown");
        $("#myCarousel .active h4").addClass("fadeOutUp");
    }).on('slide', function (e) {
        // This event fires immediately when the slide instance method is invoked.
        $("#myCarousel .active h4").removeClass("fadeOutUp");
        $("#myCarousel .active h4").addClass("fadeInDown");
    });

    $(".menu_icon").click(function () {
        $(".top_menu").toggleClass("left_body");
    });
    $(".close_i").click(function () {
        $(".top_menu").removeClass("left_body");
    });

    if ($(window).width() < 1001) {
        // $('.content_sub').hide();
        $(".content_sub").removeClass("activetab");
        $(".cins_spand").click(function () {
            var v_id = $(this).attr("data-profile");
            var collapsed = $(this).find('i').hasClass('fa-plus-square-o');
            $('.cins_spand').find('i').removeClass('fa-minus-square-o');
            $('.cins_spand').find('i').addClass('fa-plus-square-o');
            if (collapsed)
                $(this).find('i').toggleClass('fa-plus-square-o fa-2x fa-minus-square-o fa-2x');
            //  $(".content_sub").not($(this).next()).hide();
            // debugger;
            // alert(v_id);
            if ($("#" + v_id).hasClass("activetab_s")) {
                $("#" + v_id).removeClass("activetab_s");
                return false
            } else {
                $(".content_sub").removeClass("activetab_s");
                $("#" + v_id).addClass("activetab_s");

                return false
            }

        });

        // $(".message_sub").removeClass("activetab");

        //$(".menu_sub_msg >li").click(function () {
        //    var v_id1 = $(this).attr("data-tab");
        //    var coll = $(this).find('div').hasClass('activetab')
        //    $(".message_sub").removeClass("activetab");
        //    $("#" + v_id1).addClass("activetab");
        //    if (v_id1)
        //        $(this).find('div').toggleClass('message_sub');

        //});
        $(".t1").click(function () {
            var x = $(this).attr("data-tab");
            // $(".t1").removeClass("active");
            $(this).toggleClass("active");
            //  $(".m_sub1").removeClass("activetab");
            $("#" + x).toggleClass("activetab");
        });
        $(".t2").click(function () {
            var x = $(this).attr("data-tab");
            //  $(".t2").removeClass("active");
            $(this).toggleClass("active");
            //  $(".m_sub2").removeClass("activetab");
            $("#" + x).toggleClass("activetab");
        });
    } else {

    }


    $('.cd-faq-content_a').hide();
    $(".cd-faq-trigger_a").click(function () {

        var collapsed = $(this).find('i').hasClass('fa-plus-square-o');
        $('.cd-faq-trigger_a').find('i').removeClass('fa-minus-square-o');
        $('.cd-faq-trigger_a').find('i').addClass('fa-plus-square-o');
        if (collapsed)
            $(this).find('i').toggleClass('fa-plus-square-o fa-2x fa-minus-square-o fa-2x');

        $(".cd-faq-content_a").not($(this).next()).hide();
        $("div[rel='clickA_" + $(this).attr("clickA") + "']").slideToggle("600");
    });


    $('.cd-faq-content_b').hide();
    $(".cd-faq-trigger_b").click(function () {

        var collapsed = $(this).find('i').hasClass('fa-plus-square-o');
        $('.cd-faq-trigger_b').find('i').removeClass('fa-minus-square-o');
        $('.cd-faq-trigger_b').find('i').addClass('fa-plus-square-o');
        if (collapsed)
            $(this).find('i').toggleClass('fa-plus-square-o fa-2x fa-minus-square-o fa-2x');

        $(".cd-faq-content_b").not($(this).next()).hide();
        $("div[rel='clickB_" + $(this).attr("clickB") + "']").slideToggle("600");
    });


    $('.cd-faq-content_c').hide();
    $(".cd-faq-trigger_c").click(function () {

        var collapsed = $(this).find('i').hasClass('fa-plus-square-o');
        $('.cd-faq-trigger_c').find('i').removeClass('fa-minus-square-o');
        $('.cd-faq-trigger_c').find('i').addClass('fa-plus-square-o');
        if (collapsed)
            $(this).find('i').toggleClass('fa-plus-square-o fa-2x fa-minus-square-o fa-2x');

        $(".cd-faq-content_c").not($(this).next()).hide();
        $("div[rel='clickC_" + $(this).attr("clickC") + "']").slideToggle("600");
    });


    $("#departing").datepicker();
    $("#departing_56").datepicker();
    $("#returning").datepicker();
    $("#returning1").datepicker();
    $("#departingDate").datepicker();


    $("#returning2").datepicker();

    $('body').on('click', '.service_left li', function () {
        $(".service_left li").each(function () {
            $(this).removeClass('submenu-dropdown').removeClass('intra_active');
        });
        $(this).addClass('submenu-dropdown').addClass('intra_active');
    });

});
