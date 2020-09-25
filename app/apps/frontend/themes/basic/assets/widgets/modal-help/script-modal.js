//if ($.cookie("popup2") == null)
//{
//    var delay_popup = 2001;
//   setTimeout("$('#example1').click();", delay_popup);
//}

//if ($.cookie("popup3") == null)
//{
//   var delay_popup = 2000;
//   setTimeout("$('#example2').click();", delay_popup);
//}

//if ($.cookie("popup4") == null)
//{
//var delay_popup = 1998;
//setTimeout("$('#example3').click();", delay_popup);
//}

//if ($.cookie("popup5") == null)
//{
//var delay_popup = 2002;
//setTimeout("$('#example4').click();", delay_popup);
//}

//if ($.cookie("popup6") == null)
//{
//var delay_popup = 2004;
//setTimeout("$('#example5').click();", delay_popup);
//}
//if ($.cookie("popup7") == null)
//{
//var delay_popup = 2003;
//setTimeout("$('#example6').click();", delay_popup);
//} 

$('.example5').click(function () {
    var date = new Date();
    date.setTime(date.getTime() + (60000 * 1000));
    $.cookie("popup", "", {expires: date});
    $('#exampleModal5_' + $(this).data('body')).arcticmodal();
});


$('.example6').click(function () {
    var date = new Date();
    date.setTime(date.getTime() + (60000 * 30000));
    $.cookie("popup2", "", {expires: date});
    $('#exampleModal1').arcticmodal();
});

$('.example7').click(function () {
    var date = new Date();
    date.setTime(date.getTime() + (60000 * 30000));
    $.cookie("popup3", "", {expires: date});
    $('#exampleModal2').arcticmodal();
});

$('.example8').click(function () {
    var date = new Date();
    date.setTime(date.getTime() + (60000 * 30000));
    $.cookie("popup4", "", {expires: date});
    $('#exampleModal3').arcticmodal();
});

$('.example9').click(function () {
    var date = new Date();
    date.setTime(date.getTime() + (60000 * 30000));
    $.cookie("popup5", "", {expires: date});
    $('#exampleModal4').arcticmodal();
});

$('.example10').click(function () {
    var date = new Date();
    date.setTime(date.getTime() + (60000 * 30000));
    $.cookie("popup6", "", {expires: date});
    $('#exampleModal5').arcticmodal();
});

$('.example11').click(function () {
    var date = new Date();
    date.setTime(date.getTime() + (60000 * 30000));
    $.cookie("popup7", "", {expires: date});
    $('#exampleModal6').arcticmodal();
});


















