module.exports.showAlert = function (type,message) {

    if(type == 'error') {
        toastr["error"](message);
    }

    if(type == 'success') {
        toastr["success"](message);
    }

    if(type == 'info') {
        toastr["info"](message);
    }

    toastr.options.onShown = function() {
        countNotification++;
        if(countNotification == 10) {
            countNotification = 0;
            toastr.clear();
        }
    }

    toastr.options.onHidden = function() {
        if(countNotification > 0)
            countNotification--;
    }
}

module.exports.showTradeNotification = function (message) {
    toastr.clear();
    toastr["info"](message);
}

module.exports.waitMe = function (element) {

    $(element).waitMe({
        effect : 'pulse',
        waitTime : -1,
        textPos : 'vertical',
    });
}

module.exports.hideWaitMe = function (element) {
    $(element).waitMe('hide');
}

module.exports.initScrollTable = function  () {
    $(".scroll-table").each(function(key,val) {

        var height = $(val).css('height');

        if(height == 'auto') return;

        var height = $(val).height();

        var theadHeight = $(val).find('thead').height();
        var tbodyHeight = $(val).find('tbody').height();

        console.log(tbodyHeight);

    });
}

module.exports.formatFloatNumber = function (number) {
    var string = parseFloat(number).toFixed(8).toString();

    var grey = [];
    var light = [];
    var count = 0;
    var key = -1;

    for(var i = string.length - 1; i >= 0; i--) {
        if(string.charAt(i) != '0') {
            key = i;
            break;
        } else {
            grey[count] = '0';
            count++;
        }
    }

    if(string.charAt(key) == '.') {
        grey.unshift('.');
        key--;
    }

    count = 0;

    for(var i = 0; i <= key; i++) {
        light[count] = string.charAt(i);
        count++;
    }

    return light.join("") + '<span class="number-grey">'+grey.join("")+'</span>';
}

module.exports.cutFloatNumber = function (number) {
    var string = parseFloat(number).toFixed(8).toString();

    var light = [];
    var count = 0;
    var key = -1;

    for(var i = string.length - 1; i >= 0; i--) {
        if(string.charAt(i) != '0') {
            key = i;
            break;
        }
    }

    if(string.charAt(key) == '.') key--;

    for(var i = 0; i <= key; i++) {
        light[count] = string.charAt(i);
        count++;
    }

    return light.join("");
}