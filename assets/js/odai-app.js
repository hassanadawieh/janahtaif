//instant popover modifier
(function ($) {
    $.fn.appModifier = function (options) {
        var defaults = {
            actionUrl: "", //the url where the response will go after modification
            value: "", //existing value
            actionType: "select2", //action type
            showbuttons: false, //show submit/cancel button
            datepicker: {}, //options for datepicker
            select2Option: {}, //options for select2
            dataType: 'json',
            onSuccess: function () {
            }
        };

        var settings = $.extend({}, defaults, options);

        //create popover content dom
        var tempId = getRandomAlphabet(5);

        //prepare submit or close buttons
        var buttonDom = "";
        if (settings.showbuttons) {
            buttonDom = "<div class='custom-popover-button-area mt10 clearfix row'>\n\
                            <div id='custom-popover-submit-btn-" + tempId + "' class='col-md-6 pr5'><button class='btn btn-primary btn-sm w100p'><i data-feather='check' class='icon-16'></i></button></div>\n\
                            <div class='col-md-6 pl5 custom-popover-close-btn'><button class='btn btn-default btn-sm w100p'><i data-feather='x' class='icon-16'></i></button></div>\n\
                        </div>";
        }

        //prepare container dom
        var containerDom = "";
        if (settings.actionType === "select2") {
            containerDom = "<input id='" + tempId + "' value='" + settings.value + "' type='text' class='form-control popover-tempId' /> " + buttonDom;
        } else if (settings.actionType === "date") {
            var dateFormat = getJsDateFormat();
            var dateArray = settings.value.split("-"),
                    year = dateArray[0],
                    month = dateArray[1],
                    day = dateArray[2];
            var dateValue = dateFormat.replace("yyyy", year).replace("mm", month).replace("dd", day);

            containerDom = "<div style='height: 240px;' id='" + tempId + "'  data-date='" + dateValue + "' data-date-format='" + dateFormat + "' class='popover-tempId'></div>"; //set height first for right popover position
        }

        var $instance = $(this);
        //show popover
        var offset = $instance.offset();
        var top = offset.top;
        var leftOffset = offset.left;
        var topOffset = top + $instance.outerHeight() + 10; //10 for arrow

        //create popover dom
        var popoverDom = "<div class='app-popover' style='top: " + topOffset + "px; left: " + leftOffset + "px'>\n\
                                <span class='app-popover-arrow' ></span>\n\
                                <div class='app-popover-body'>\n\
                                    <div class='loader-container inline-loader hide'></div>\n\
                                    " + containerDom + " \n\
                                </div>\n\
                            </div>";

        $(".app-popover").remove();
        $("body").append(popoverDom);
        feather.replace();

        //apply select2/datepicker on popover content
        var $inputField = $("#" + tempId);
        if (settings.actionType === "select2") {
            //select2 
            if (settings.showbuttons) {
                //submit with buttons
                $("#" + tempId).select2(settings.select2Option);
            } else {
                $("#" + tempId).select2(settings.select2Option).change(function (action) {
                    initAjaxAction($instance, $(this).val(), settings, action["added"]["text"]);
                });
            }
        } else if (settings.actionType === "date") {
            settings.datepicker.onChangeDate = function (response) {
                initAjaxAction($instance, response, settings);
            };

            setDatePicker("#" + tempId, settings.datepicker);
        }

        //check if the right side is overflowed
        $("body").find(".app-popover").each(function () {
            //position content
            var right = $(window).width() - ($(this).offset().left + $(this).outerWidth());
            if (right < 0) {
                //overflowed
                $(this).css({"left": "unset", "right": "10px"});

                //position arrow
                var right = $(window).width() - ($instance.offset().left + (($instance.outerWidth() / 2) * 1));
                $(this).find(".app-popover-arrow").css({"left": "unset", "right": right});
            }
        });

        //submit button
        $("div#custom-popover-submit-btn-" + tempId).click(function () {
            initAjaxAction($instance, $inputField.val(), settings);
        });

        //close button
        $(".custom-popover-close-btn").click(function () {
            $(".app-popover").remove(); //hide popover
        });

        function initAjaxAction($instance, value, settings, changedText) {
            var popoverContentHeight = $inputField.closest(".app-popover-body").height();
            var popoverContentWidth = $inputField.closest(".app-popover-body").width();
            $inputField.closest(".app-popover-body").find(".loader-container").removeClass("hide").css({"height": popoverContentHeight, "width": popoverContentWidth});
            $inputField.closest(".app-popover-body").find(".custom-popover-button-area").addClass("hide");
            $inputField.addClass("hide");

            $.ajax({
                url: settings.actionUrl,
                type: 'POST',
                dataType: settings.dataType,
                data: {value: value},
                success: function (result) {
                    $(".app-popover").remove(); //hide popover
                    setTimeout(function () {
                        $inputField.closest(".app-popover-body").find(".loader-container").addClass("hide");
                        $inputField.closest(".app-popover-body").find(".custom-popover-button-area").removeClass("hide");
                        $inputField.removeClass("hide");
                    }, 200);

                    if (result.success) {
                        settings.onSuccess(result);

                        //update for select2
                        if (changedText) {
                            $instance.text(changedText);
                        }

                        $instance.attr("data-value", value); //update value for instant future use
                        $(".app-popover").remove();
                    } else {
                        appAlert.error(result.message, {duration: 7000});
                    }
                }
            });
        }
    };
})(jQuery);