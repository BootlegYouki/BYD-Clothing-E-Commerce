$(document).ready(function () {
    $(".sidebar-btn").click(function () {
        let page = $(this).data("page");

        if (page) {
            $("#dynamicContent").fadeOut(200, function () {
                $.ajax({
                    url: page,
                    type: "GET",
                    success: function (response) {
                        $("#dynamicContent").html(response).fadeIn(200);
                    },
                    error: function () {
                        $("#dynamicContent").html("<p class='text-danger'>Error loading page. Please try again.</p>").fadeIn(200);
                    }
                });
            });

            $(".sidebar-btn").removeClass("active");
            $(this).addClass("active");
        }
    });
});
