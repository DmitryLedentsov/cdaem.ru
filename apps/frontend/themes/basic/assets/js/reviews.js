$(function(){$("#create-review-button").on("click",function(a){var b=$(this),c=$("#reviews");$.trim(c.html())||$.get("/create-review/"+b.data("id"),function(a){c.append(a)}),c.show("slow")})});