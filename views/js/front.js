/**
 * NOTICE OF LICENSE
 *
 * only18plus is a module for blocking and verifying user age
 * Copyright (C) 2017 Yuri Blanc
 * Email: yuxblank@gmail.com
 * Website: www.yuriblanc.it
 * This program is distributed WITHOUT ANY WARRANTY;
 * @license GNU General Public License v3.0
 */
(function ($){

    $.only18Plus = function(options) {

        var settings = $.extend({
            minAge : 21,
            redirectTo : '',
            redirectOnFail : '',
            title : 'Age Verification',
            text : 'This Website requires you to be [21] years or older to enter. Please enter your Date of Birth in the fields below in order to continue:',
            ajaxUrl : "ajaxProcessAgeVerify",
            language : "it",
            submit_label: 'Submit',
            thank_you : 'Thank you!',
            access : 'Now you can access our store...',
            warning : 'Warning',
            no_access: 'You can not login to our shop as it is required greater age for buying.',
            invalid_day : 'Day missing or invalid',
            invalid_month : 'Month missing or invalid',
            invalid_year : 'Year missing or invalid',
            service_error : 'Unable to verify service, please come back later',
            policy_text : 'To continue, you must at least be %s years old, please verify your age by entering the date of birth in the form below .',
            modal_title : 'Confirm your age',
            months : ['January','February','March','April','May','June','July','August','September','October','November','December']
        }, options);


        var _this = {
            month : '',
            day : '',
            year : '',
            age : '',
            errors : Array(),


            getSubmitLabel : function () {
                return settings.submit_label;
            },
            getMonths: function () {
               return settings.months;
            },
            getSuccessMessage : function() {
               return "<h3>" +settings.thank_you +"<h3><p>" + settings.access+ "</p>";

            },
            getErrorMessage : function () {
                return "<h3 class='only18plus-title-warning'>" + settings.warning + "</h3>" +
                    "<p>"+ settings.no_access+"</p>";
            },
            getErrorByMessage : function (msg) {
                return settings[msg];
            },
            getDate : function(){
                var month = $('.ac-container .month').val();
                var day = $('.ac-container .day').val()
                _this.month = month;
                _this.day = day.replace(/^0+/, ''); //remove leading zero
                _this.year = $('.ac-container .year').val();

                return _this.day +_this.getMonths().indexOf(month) + "-" + _this.year;
            },
            validate : function(){
                _this.errors = [];
                if (/^([0-9]|[12]\d|3[0-1])$/.test(_this.day) === false) {
                    _this.errors.push(_this.getErrorByMessage('invalid_day'));
                };
                if (/^(19|20)\d{2}$/.test(_this.year) === false) {
                    _this.errors.push(_this.getErrorByMessage('invalid_year'));
                };
                _this.clearErrors();
                _this.displayErrors();

                return _this.errors.length < 1;
            },
            clearErrors : function(){
                $('.errors').html('');
            },
            displayErrors : function(){
                var html = '<ul>';
                for (var i = 0; i < _this.errors.length; i++) {
                    html += '<li><span>x</span>' + _this.errors[i] + '</li>';
                }
                html += '</ul>';
                setTimeout(function(){$('.ac-container .errors').html(html)},200);
            },
            showError : function (error) {
                _this.errors.push(error);
                _this.displayErrors();
            },
            reCenter : function (b){
                b.css("top", Math.max(0, (($(window).height() - (b.outerHeight() + 150)) / 2) +
                        $(window).scrollTop()) + "px");
                b.css("left", Math.max(0, (($(window).width() - b.outerWidth()) / 2) +
                        $(window).scrollLeft()) + "px");
            },
            buildHtml : function(){

                var text = settings.policy_text;
                var months = _this.getMonths();
                var html = '';
                html += '<div class="ac-overlay"></div>';
                html += '<div class="ac-container">';
                html += '<h2>' + settings.modal_title + '</h2>';
                html += '<p>' + text.replace('[21]','<strong>'+settings.minAge+'</strong>'); + '</p>';
                html += '<div class="errors"></div>';
                html += '<div class="fields"><select class="month">';
                for(var i=0;i<_this.getMonths().length;i++){
                    html += '<option value="'+i+'">'+months[i]+'</option>'
                }
                html += '</select>';
                html += '<input class="day" maxlength="2" placeholder="01" />';
                html += '<input class="year" maxlength="4" placeholder="1989"/>';
                html += '<button>'+_this.getSubmitLabel()+'</button></div></div>';

                $('body').append(html);

                $('.ac-overlay').animate({
                    opacity: 0.8
                }, 500, function() {
                    _this.reCenter($('.ac-container'));
                    $('.ac-container').css({opacity: 1})
                });

                $(".ac-container .day, .ac-container .year").focus(function(){
                    $(this).removeAttr('placeholder');
                });
            },

            ajaxVerify : function (date) {

                return $.ajax({
                    url: settings.ajaxUrl,
                    method: 'POST',
                    dataType:'json',
                    data: {
                        action: 'verifyAge',
                        ajax: true,
                        requestData: {
                            date: date
                        }
                    }

                });

            },
            handleSuccess : function(){

                $('.ac-container').html(_this.getSuccessMessage());
                setTimeout(function(){
                    $('.ac-container').animate({'top':'-350px'},200, function(){
                        $('.ac-overlay').animate({'opacity':'0'},500, function(){
                            if (settings.redirectTo != '') {
                                window.location.replace(settings.redirectTo);
                            }else{
                                $('.ac-overlay, .ac-container').remove();
                            }
                        });
                    });
                },2000);
            },
            handleUnderAge : function() {
                $('.ac-container').html(_this.getErrorMessage());
                if (settings.redirectOnFail != '') {
                    setTimeout(function(){
                        window.location.replace(settings.redirectOnFail);
                    },2000);
                }
            }
        }; //end _this

        _this.buildHtml();

        $('.ac-container button').on('click', function(){
            var date = _this.getDate();
            if (_this.validate() === true) {

                _this.ajaxVerify(date)
                    .done(function(data) {
                        if (data.result === 'ok'){
                            _this.handleSuccess();
                        } else {
                            _this.handleUnderAge();
                        }
                    })
                    .fail(function() {
                        _this.showError(_this.getErrorByMessage('service_error'));
                    });

            }
        });

        $(window).resize(function() {
            _this.reCenter($('.ac-container'));
            setTimeout(function() {
                _this.reCenter($('.ac-container'));
            }, 500);
        });
    };
}(jQuery));
$(document).ready(function () {
    if (only18PlusConfig) {
        $.only18Plus(only18PlusConfig);
    }
});


