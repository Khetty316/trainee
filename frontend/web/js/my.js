/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//(function(a){a.createModal=function(b){defaults={title:"",message:"Your Message Goes Here!",closeButton:true,scrollable:false};var b=a.extend({},defaults,b);var c=(b.scrollable===true)?'style="max-height: 420px;overflow-y: auto;"':"";html='<div class="modal fade" id="myModal">';html+='<div class="modal-dialog">';html+='<div class="modal-content">';html+='<div class="modal-header">';html+='<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';if(b.title.length>0){html+='<h4 class="modal-title">'+b.title+"</h4>"}html+="</div>";html+='<div class="modal-body" '+c+">";html+=b.message;html+="</div>";html+='<div class="modal-footer">';if(b.closeButton===true){html+='<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>'}html+="</div>";html+="</div>";html+="</div>";html+="</div>";a("body").prepend(html);a("#myModal").modal().on("hidden.bs.modal",function(){a(this).remove()})}})(jQuery);


$(function () {
    $(document).on("keydown", ":input:not(textarea)", function (event) {
        return event.key != "Enter";
    });

    $(".alert-success").animate({opacity: 1.0}, 1000).fadeOut("fast");

    $(".modalButton").click(function () {

        //This if function below checks if there is any data sent under the attribute element of 'data-modaltitle'
        if ($(this).attr('data-modaltitle')) {
            $('#myModal').find('p.modal-title').text($(this).attr('data-modaltitle'));
        }
        ;
        $('#myModal').modal('show').find('#myModalContent').load($(this).attr('value'));
    });

    $(".modalButton2").click(function () {
        $("#myModal").modal("show")
                .find("#myModalContent").html($(this).attr('value'));
    });

    $('#myModal').on('hide.bs.modal', function (event) {
        $("#myModalContent").html('');
    });



    $(".modalButtonSmall").click(function () {
        if ($(this).attr('data-modaltitle')) {
            $('#myModalSmall').find('p.modal-title')
                    .text($(this).attr('data-modaltitle'));
        }
        ;
        $("#myModalSmall").modal("show")
                .find("#myModalContentSmall")
                .load($(this).attr('value'));
    });

    $('#myModalSmall').on('hide.bs.modal', function (event) {
        $("#myModalContentSmall").html('');
    });
    $(".modalButtonMedium").click(function () {
        if ($(this).attr('data-modaltitle')) {
            $('#myModalMedium').find('p.modal-title')
                    .text($(this).attr('data-modaltitle'));
        }
        ;
        $("#myModalMedium").modal("show")
                .find("#myModalContentMedium")
                .load($(this).attr('value'));
    });

    $('#myModalMedium').on('hide.bs.modal', function (event) {
        $("#myModalContentMedium").html('');
    });
    $(".modalButtonSmall").click(function () {
        $("#myModalSmall").modal("show")
                .find("#myModalContentSmall")
                .load($(this).attr('value'));
    });

    $('.modalButtonPdf').on('click', function () {
        var pdf_link = $(this).attr('value');

        var iframe = "";
        if (get_url_extension(pdf_link).toUpperCase() === "PDF") {
            iframe = '<object data="' + pdf_link + '" frameborder="0" width="100%" height="' + (screen.availHeight / 4 * 3) + 'px"></object>';
        } else {
            var iframe = '<img src="' + pdf_link + '" style="object-fit: contain;" width="' + (screen.availWidth / 4 * 3) + '" height="' + (screen.availHeight / 4 * 3) + '"/>';
        }

        $("#myModal").modal("show").find("#myModalContent").html(iframe);
    });

    $('form').on('beforeSubmit', function (e) {
        $('#submitButton').attr('disabled', true).addClass('disabled');
        $('.submitButton').attr('disabled', true).addClass('disabled');
        $('#submitButton').html('Submitting...');

        let btn = $('.submitButton');
        if (btn.data('loadingword')) {
            btn.html(btn.data('loadingword'));
        }

        return true;
    });

    $(".customFileInput").on("change", function () {
        var fileName = $(this).val().split("\\").pop();
        $(this).parent().next('.customFileLabel').html(fileName);

    });

    $('.custom-file-input').on('change', function () {
        //get the file name
        var fileName = $(this).val().split("\\").pop();
        //replace the "Choose a file" label
        $(this).next('.custom-file-label').html(fileName);
    });


    $("input[type='number']").on("keydown", function (e) {
        let invalidChars = ["e", "+", "E"];
        if (invalidChars.includes(e.key)) {
            e.preventDefault();
        }
    });

    $('#alertModal').on('hide.bs.modal', function (event) {
        $("#alertModalContent").html('');
    });

    $(".twoDecimal").on("blur", function (e) {
        var val = round2Decimal($(this).val()).toFixed(2);
        $(this).val(val);
    });

});


function checkCheckBoxes() {
    var checkedList = [];
    var stepList = [];
    $('tbody input:checked').each(function () {
        stepList.push(this.step);
        checkedList.push(this.value);
    });
    $("#checkedList").val(checkedList);
    $("#stepList").val(stepList);
    if (checkedList != "") {
        $('#checkboxWorkingModel').modal('toggle');
    } else {
        $("#alertModalContent").html("No Item Selected");
        $('#alertModal').modal('toggle');
    }
}

function myAlert(msg) {
    $("#alertModalContent").html(msg);
    $('#alertModal').modal('toggle');
}

function get_url_extension(url) {
//    return url.split(/[#?]/)[0].split('.').pop().trim();
    return url.split('.').pop();
}


function showSpinner() {
    $('#spinnerModal').modal({
        backdrop: 'static',
        keyboard: false
    });
    $('#spinnerModal').modal('show');
}


// input is dateread
function countReadDateDays(date1, date2) {

    const oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
    var firstDate = getDateFromRead(date1);
    var secondDate = getDateFromRead(date2);
    return Math.round(Math.abs((firstDate - secondDate) / oneDay));
}

/**
 * Convert from dd/MM/yyyy to js date
 * @param {type} dateString
 * @returns {String}
 */
function getDateFromRead(dateString) {
    var dateParts = (dateString + '').split("/");
    return new Date(+dateParts[2], dateParts[1] - 1, +dateParts[0]);
//    return dateObject.toString();
}


function formatNumber(num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
}

// Cookies Handler
function setCookie(key, value, expiry) {
    var expires = new Date();
    expires.setTime(expires.getTime() + (expiry * 60 * 1000));
    document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
}

function getCookie(key) {
    var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
    return keyValue ? keyValue[2] : null;
}

function eraseCookie(key) {
    var keyValue = getCookie(key);
    setCookie(key, keyValue, '-1');
}

function round2Decimal(value) {
    if (isNaN(value)) {
        value = 0;
    }
    return  Math.round(value * 100) / 100;
}

function setPositionCookie() {
    var currentYOffset = window.pageYOffset;  // save current page postion.
    setCookie('jumpToScrollPostion', currentYOffset, 2);
}

function getPositionCookie() {
    var jumpTo = getCookie('jumpToScrollPostion');
    if (jumpTo !== "undefined" && jumpTo !== null) {
        window.scrollTo(0, jumpTo);
        eraseCookie('jumpToScrollPostion');  // and delete cookie so we don't jump again.
    }
}

$(window).on('beforeunload', function () {
    // Disable all elements with class 'btn' before leaving the page
    $('.btn').attr('disabled', true).addClass('disabled');
    $('#loading-icon').show();
})