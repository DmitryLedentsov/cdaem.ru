 if ( $.cookie("popup") == null )
{
 var delay_popup = 5000;
	setTimeout("$('#ret').click();", delay_popup);
}

var delay_popup = 2000;

setTimeout("$('#example1').click();", delay_popup);

var delay_popup = 2100;

setTimeout("$('#example2').click();", delay_popup);

$('.example5').click(function() {
var date = new Date();
date.setTime(date.getTime() + (60 * 1000));
$.cookie("popup", "", {expires: date} );
$('#exampleModal5_' + $(this).data('body')).arcticmodal();
});


$('.example6').click(function() {
$('#exampleModal1').arcticmodal();
});

$('.example7').click(function() {
$('#exampleModal2').arcticmodal();
});


 












	

			
