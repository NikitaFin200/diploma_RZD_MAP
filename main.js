function isFieldEmpty(obj) {
    return obj == null || isEmpty(obj.val());
}

function isEmpty(value) {
    return value.replace(/\ /g, "").length == 0;
}

function locationHMenu(path) {
    var url = null;

    switch (path) {
        case "main":
            url = ""; break;
        case "nsi":
            url = path; break;
        case "profile":
            url = path; break;
        case "auth":
            url = path; break;
        case "exit":
            if (confirm("Выйти из профиля?")) {
                var formData = new FormData();
                formData.append("action", "profile.logout");

                $.ajax({
                    url: "ajax/api.php",
                    dataType: "JSON",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    type: "post",
                    success: function(data){
                        location.reload(true);
                    }
                });
            }

            break;
    }

    if (url != null) {
        location.href = (url == "" ? "/map_weather" : url);
    }
}

function locationBranch(path, params) {
    var url = null;

    switch (path) {
        case "reports":
            url = path; break;
    }

    if (url != null) {
        location.href = (url == "" ? "/map_weather" : url +'?station='+params);
    }
}

function ajaxError(xhr, str) {
    loaderRequest(false, function () {
        alert("Возникла неизвестная ошибка");
    });
}

function ajaxSuccessError(error, errorContent, errorCodes) {
    var errorText = "Возникла неизвестная ошибка";

    if (errorContent != null)
        errorText = errorContent;
    else if (errorCodes[error] != null)
        errorText = "Ошибка: " + errorCodes[error];

    alert(errorText);
}

function loaderRequest(enabled, callback) {
    if (enabled) {
        $("#loader_bar").fadeIn(300);
        $("#content").addClass("__blur");
        $("#header").addClass("__blur");
        $("html,body").css("overflow", "hidden");
    }
    else {
        $("#loader_bar").fadeOut(300);
        $("#content").removeClass("__blur")
        $("#header").removeClass("__blur");
        $("html,body").css("overflow", "");
    }

    if (callback !== null) {
        setTimeout(callback, 300);
    }

}