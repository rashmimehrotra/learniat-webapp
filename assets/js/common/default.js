var spinnerImage = '<br><img src="' + imagePath + 'spinner-1.gif">';

function showModalNoBgPopup()
{
	var pos = $("#modal_popup_no_bg").position();
    $("#modal_popup_no_bg").css({ position: 'fixed',top: pos.y});
    $("#modal_popup_no_bg").show();
    $(".black_overlay").show();


    $(window).scroll(function () {
        pos = $("#modal_popup_no_bg").position();
        //adjust the dialog box so that it scrolls as you scroll the page
        $("#modal_popup_no_bg").css({
            position: 'fixed',
            top: pos.y
        });
    });
    
    $(document).keydown(function(e) {
        // ESCAPE key pressed
        if (e.keyCode == 27) {
        	$("#modal_popup_no_bg").hide();
            $(".black_overlay").hide();
        }
    });
}


function showModalNoBgPopupFull()
{
    var pos = $("#modal_popup_no_bg_full").position();
    $("#modal_popup_no_bg_full").css({ position: 'fixed',top: pos.y});
    $("#modal_popup_no_bg_full").show();
    $(".black_overlay").show();


    $(window).scroll(function () {
        pos = $("#modal_popup_no_bg_full").position();
        //adjust the dialog box so that it scrolls as you scroll the page
        $("#modal_popup_no_bg_full").css({
            position: 'fixed',
            top: pos.y
        });
    });

    $(document).keydown(function(e) {
        // ESCAPE key pressed
        if (e.keyCode == 27) {
            $("#modal_popup_no_bg_full").hide();
            $(".black_overlay").hide();
        }
    });
}

function validateAjaxError(response)
{
    if (response.status == 401) {
        var location = baseUrl;
        window.location.reload(location);
    } else {
        alert('Error while request..');
    }
}