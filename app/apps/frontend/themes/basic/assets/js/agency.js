jQuery(function(){var e=$("#gallery");e.length&&e.owlCarousel({autoWidth:!0,loop:!0,margin:10,nav:!1,autoplay:!0,autoplayTimeout:3e3,smartSpeed:1e3,autoplayHoverPause:!1,navText:["",""],responsive:{0:{items:1},600:{items:3},1e3:{items:5}}}),$(".payment-methods .pm-icon").on("click",function(e){var t=$(this);!t.parent().hasClass("no-active")&&$(e.target).hasClass("pm-icon")&&($.each($(".payment-methods form"),function(){$(this).data("formApi").reset()}),$(".payment-methods .pm-icon").find(".payment-info").not(t.find(".payment-info")).hide(),t.find(".payment-info").toggle())}),$(".phone-mask").mask("+7 (999) 999-9999"),$(".payment-methods form").formApi({fields:["_csrf","DetailsHistoryForm[advert_id]","DetailsHistoryForm[type]","DetailsHistoryForm[payment]","DetailsHistoryForm[phone]","DetailsHistoryForm[email]"],extraSubmitFields:{submit:"submit"},validateFields:["detailshistoryform-type","detailshistoryform-payment","detailshistoryform-phone","detailshistoryform-email"],success:function(e,t){$.isPlainObject(t)&&"status"in t&&(1==t.status?e.targetForm.parent().html('<div class="alert alert-success">'+t.message+"</div>"):showStackError("Ошибка",t.message))}}),$("#form-reserved").formApi({fields:["_csrf","Reservation[name]","Reservation[email]","Reservation[phone]","Reservation[arrived_date]","Reservation[arrived_time]","Reservation[out_date]","Reservation[out_time]","Reservation[transfer]","Reservation[clients_count]","Reservation[more_info]","Reservation[whau]","Reservation[verifyCode]"],extraSubmitFields:{submit:"submit"},validateFields:["reservation-name","reservation-email","reservation-phone","reservation-arrived_date","reservation-arrived_time","reservation-out_date","reservation-out_time","reservation-transfer","reservation-clients_count","reservation-more_info","reservation-whau","reservation-verifycode"],success:function(e,t){$.isPlainObject(t)&&"status"in t&&(1==t.status?$("#reserved-result").html('<div class="alert alert-success">'+t.message+"</div>"):$("#reserved-result").html('<div class="alert alert-danger">'+t.message+'</div><div><a href="javascript://" onclick="document.location.reload(); return false;">Попробовать еще раз.</a></div>'))},complete:function(e,t,a){308!=t.status&&302!=t.status&&$("#reservation-verifycode-image").length&&$("#reservation-verifycode-image").yiiCaptcha("refresh")}}),$("#form-want-pass").formApi({fields:["_csrf","WantPassForm[rent_types_array][]","WantPassForm[metro_array][]","WantPassForm[address]","WantPassForm[name]","WantPassForm[phone]","WantPassForm[phone2]","WantPassForm[email]","WantPassForm[rooms]","WantPassForm[description]","WantPassForm[files][]","WantPassForm[verifyCode]"],extraSubmitFields:{submit:"submit"},validateFields:["wantpassform-rent_types_array","wantpassform-metro_array","wantpassform-address","wantpassform-name","wantpassform-phone","wantpassform-phone2","wantpassform-email","wantpassform-rooms","wantpassform-description","wantpassform-files","wantpassform-verifycode"],success:function(e,t){$.isPlainObject(t)&&"status"in t&&(1==t.status?$("#want-pass-result").html('<div class="alert alert-success">'+t.message+"</div>"):$("#want-pass-result").html('<div class="alert alert-danger">'+t.message+'</div><div><a href="javascript://" onclick="document.location.reload(); return false;">Попробовать еще раз.</a></div>'))},complete:function(e,t,a){302!=t.status&&$("#wantpassform-verifycode-image").length&&$("#wantpassform-verifycode-image").yiiCaptcha("refresh")}}),$("#form-select").formApi({fields:["_csrf","SelectForm[rent_types_array][]","SelectForm[metro_array][]","SelectForm[name]","SelectForm[phone]","SelectForm[phone2]","SelectForm[email]","SelectForm[rooms]","SelectForm[description]","SelectForm[verifyCode]"],extraSubmitFields:{submit:"submit"},validateFields:["selectform-rent_types_array","selectform-metro_array","selectform-name","selectform-phone","selectform-phone2","selectform-email","selectform-rooms","selectform-description","selectform-verifycode"],success:function(e,t){$.isPlainObject(t)&&"status"in t&&(1==t.status?$("#select-result").html('<div class="alert alert-success">'+t.message+"</div>"):$("#select-result").html('<div class="alert alert-danger">'+t.message+'</div><div><a href="javascript://" onclick="document.location.reload(); return false;">Попробовать еще раз.</a></div>'))},complete:function(e,t,a){302!=t.status&&($("#selectform-verifycode-image").length&&$("#selectform-verifycode-image").yiiCaptcha("refresh"),$("html, body").animate({scrollTop:$("div.alert").height()},2e3))}}),$("#wantpassform-files").bind("change",function(e){if(window.FileReader){var t=$("#form-want-pass").data("formApi"),s=document.getElementById("wantpassform-files").files;if(60<s.length||60<$("#images-preview img").length+s.length)showStackError("Внимание","Вы можете загрузить до 60 изображений Вашего объекта. Остальные изображения будут проигнорированы.");else for(var a,r=0,i=s.length;r<i;r++){60<=r||((a={})["WantPassForm[files]["+r+"]"]=s[r],t.addFile(a),function(t){var a=new FileReader;a.onloadend=function(e){$("#images-preview").append("<div style='width:150px; display: inline-block; margin-right: 5px;' data-id='WantPassForm[files]["+t+"]''><img style='width: 100%' src='"+a.result+"' alt='' /></div>")},a.readAsDataURL(s[t])}(r))}}}),$("#images-preview").bind("click",function(e){e.preventDefault();var t,a=e.target,s=$("#form-want-pass").data("formApi");"IMG"==a.tagName&&(t=$(a),s.removeFile(t.parent().data("id")),t.parent().remove())})});