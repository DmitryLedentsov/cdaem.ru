jQuery(function(){$(document).on("click","#helpdesk-complaint",function(){var a=$(this);return $.get("/complaint/"+a.data("id"),function(a){$("#modal-helpdesk-complaint").remove(),$("body").append(a),$("#modal-helpdesk-complaint").modal("show")}),!1}),$("#form-helpdesk-ask").formApi({fields:["_csrf","Helpdesk[user_name]","Helpdesk[email]","Helpdesk[priority]","Helpdesk[theme]","Helpdesk[text]"],extraSubmitFields:{submit:"submit"},validateFields:["helpdesk-user_name","helpdesk-email","helpdesk-priority","helpdesk-theme","helpdesk-text"],success:function(a,b){}})});

$(document).ready(function () {
    $("#country").on('change','',function(e){
        if ($("#country option:selected").text() == "Russia") {
        $("#phone").inputmask("+79999999999");}
        if ($("#country option:selected").text() == "Ukraine") {
        $("#phone").inputmask("+380999999999"); }
    if ($("#country option:selected").text() == "Belarus") {
        $("#phone").inputmask("+375999999999"); } 
});
});