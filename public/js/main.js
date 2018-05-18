var showAlert = function (type,message) {
    if(type == 'error') {
        toastr["error"](message, "Error");
    }

    if(type == 'success') {
        toastr["success"](message, "Success");
    }
}

var waitMe = function (element) {

    $(element).waitMe({
        effect : 'pulse',
        waitTime : -1,
        textPos : 'vertical',
    });
}

var hideWaitMe = function (element) {
    $(element).waitMe('hide');
}

var initScrollTable = function  () {
    $(".scroll-table").each(function(key,val) {

        var height = $(val).css('height');

        if(height == 'auto') return;

        var height = $(val).height();

        var theadHeight = $(val).find('thead').height();
        var tbodyHeight = $(val).find('tbody').height();

        console.log(tbodyHeight);

    });
}