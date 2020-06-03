$(function () {

    $("#create-review-button").on("click", function(event) {
        var $this = $(this);
        var $reviews = $("#reviews");
        if ( !$.trim( $reviews.html() ) ){
            $.get("/create-review/" + $this.data("id"), function (response) {
                $reviews.append(response);
            });
        }
        $reviews.show("slow");
    });
});