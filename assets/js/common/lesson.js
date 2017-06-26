//Remove topic
//baseUrl defined in header file

var spinnerImage2 = '<div id="overlay-inner"><img src="' + imagePath + 'spinner.gif"></div>';
var spinnerImage = '<br><div class="centerDiv"><img src="' + imagePath + 'spinner-1.gif"></div>';

var topicSuccessMessage = 'Topic data has been saved successfully.';

var topicDeletedSuccessMessage = 'Topic has been deleted successfully.';
var questionDeletedSuccessMessage = 'Question has been deleted successfully.';

var delayFadeOut = 3000;

/**
 * Confirm delete topic
 * @param integer classId
 * @param integer topicId
 * @param integer parentTopicId
 */
function confirmDeleteTopic(classId, topicId, parentTopicId) {
    var editTopicId = classId + '-' + topicId;
    var oldTopicName = String($('#editTopicLink-' + editTopicId).text());
    var topicQuestionCount =  parseInt($.trim($('#topicQuestionCount-' + editTopicId).attr('count')));

    if (topicQuestionCount !== 0) {
        var dialogMsg = 'Subtopic <b>"' + oldTopicName + '"</b>  contains <b>';
        if (topicQuestionCount > 1 ) {
            dialogMsg +=  topicQuestionCount + '</b> questions.';
        } else {
            dialogMsg +=  topicQuestionCount + '</b> question.';
        }
        dialogMsg += 'Do you want to delete this topic?';
        $("#dialog-confirm").html(dialogMsg );

        // Define the Dialog and its properties.
        $("#dialog-confirm").dialog({
            resizable: true,
            modal: true,
            draggable: false,
            closeOnEscape: true,
            title: "Delete topic",
            async: false,
            height: 250,
            width: 450,
            buttons: {
                "Yes": function () {
                    $(this).dialog('close');
                    callbackRemoveTopic(true, classId, topicId, parentTopicId);
                    updateAndRemoveOtherRelatedTopic(topicId);
                },
                "No": function () {
                    $(this).dialog('close');
                }
            }
        });
    } else {
        callbackRemoveTopic(true, classId, topicId, parentTopicId);
        updateAndRemoveOtherRelatedTopic(topicId);
    }

}

/**
 * Update and remove topic from other class
 * @param integer topicId
 */
function updateAndRemoveOtherRelatedTopic(topicId)
{
    $(".topicQuestionCountClass-" + topicId).each(function() {
        var topicId = $(this).attr('topicId');
        var classId = $(this).attr('classId');
        var parentTopicId = $(this).attr('parentTopicId');
        var editTopicId = classId + '-' + topicId;
        callbackRemoveTopic(true, classId, topicId, parentTopicId);
        $('#topicCompleteDiv-' + editTopicId).remove();
    });
}

/**
 * Call back to remove topic
 * @param boolean value
 * @param integer classId
 * @param integer topicId
 * @param integer parentTopicId
 */
function callbackRemoveTopic(value, classId, topicId ,parentTopicId)
{

    var editTopicId = classId + '-' + topicId;
    var editParentTopicId = classId + '-' + parentTopicId;
    var topicQuestionCount = $.trim($('#topicQuestionCount-' + editTopicId).text());
    var subTopicCount = parseInt($.trim($('#subTopicCount-' + editParentTopicId).attr('count')));
    var totalTopicQuestionCount = parseInt($.trim($('#topicQuestionCount-' + editParentTopicId).attr('count')));
    var subTopicQuestionCount = parseInt($.trim($('#topicQuestionCount-' + editTopicId).attr('count')));

    if (value) {
        var successFlag = deleteTopicByTopicId(topicId);
        setSuccessDiv('topicCompleteDiv-' + editTopicId, topicDeletedSuccessMessage);
        if (successFlag === true) {
            subTopicCount -= 1;
            topicCountStatistics(subTopicCount, editParentTopicId);

            totalTopicQuestionCount -= subTopicQuestionCount;
            questionCountStatistics(totalTopicQuestionCount, editParentTopicId);
        }
    }
}

/**
 * Delete question by topic id
 * @param integer topicId
 * @returns {boolean}
 */
function deleteTopicByTopicId(topicId)
{
    var successFlag = false;
    var urlParam = "topicId=" + topicId;
    $.ajax({
        type: "get",
        url:  baseUrl + "/lesson/index/delete?" + urlParam,
        cache: true,
        async: false,
        success: function(response){

            alert("Topic deleted successfully.");
            successFlag = true;
        },
        error: function(xhr){
            validateAjaxError(xhr);
        }
    });

    return successFlag;
}


/**
 * Remove sub topic
 * @param integer classId
 * @param integer topicId
 * @param integer parentTopicId
 */
function removeSubTopicId(classId, topicId, parentTopicId)
{
    confirmDeleteTopic(classId, topicId, parentTopicId);
}

/**
 * Parent topics statistics
 * @param integer subTopicCount
 * @param string topicUniqueId
 */
function topicCountStatistics(subTopicCount, topicUniqueId)
{
    if (subTopicCount < 1) {
        $('#subTopicCount-' + topicUniqueId).text('No Subtopic');
    } else {
        $('#subTopicCount-' + topicUniqueId).text(subTopicCount + ' Subtopics');
    }
    $('#subTopicCount-' + topicUniqueId).attr('count', subTopicCount);
}

/**
 * Parent question statistics
 * @param integer totalTopicQuestionCount
 * @param string topicUniqueId
 */
function questionCountStatistics(totalTopicQuestionCount, topicUniqueId)
{
    if (totalTopicQuestionCount < 1 ) {
        $.trim($('#topicQuestionCount-' + topicUniqueId).text('No Question'));
    } else {
        $.trim($('#topicQuestionCount-' + topicUniqueId).text(totalTopicQuestionCount + ' Questions'));
    }

    $('#topicQuestionCount-' + topicUniqueId).attr('count', totalTopicQuestionCount);
}
/**
 * Remove topic by class id
 * @param integer classId
 * @param integer topicId
 *
 */
function removeTopicByClassId(classId, topicId)
{
    var editTopicId = classId + '-' + topicId;
    var oldTopicName = String($('#editTopicLink-' + editTopicId).text());
    var subTopicCount = parseInt($.trim($('#subTopicCount-' + editTopicId).attr('count')));
    var successFlag = false;

    if (subTopicCount !== 0) {
        $("#dialog-confirm").html('Topic <b>' + oldTopicName + '</b> has still ' + subTopicCount + ' subtopic. Delete all subtopic first.');

        $("#dialog-confirm").dialog({
            resizable: true,
            modal: true,
            draggable: false,
            closeOnEscape: true,
            title: "Delete topic",
            height: 250,
            width: 450,
            buttons: {
                "Cancel": function () {
                    $(this).dialog('close');
                }
            }
        });
    } else {
        successFlag = callbackRemoveTopic(true, classId, topicId);
        updateAndRemoveOtherRelatedTopic(topicId);
    }

    return successFlag;
}

/**
 * Subtopic Functions
 * @param integer parentTopicId
 * @param integer classId
 * @param integer schoolId
 * @param integer lastSubTopicId
 * @param boolean showFlag
 */
function getSubtopicDetails(parentTopicId, classId, schoolId, lastSubTopicId, showFlag)
{
    var content = $.trim($( "#subTopicInfo-" + classId + '-' + parentTopicId ).html());
    if (content.length == 0) {
        $( "#subTopicInfo-" + classId + '-' + parentTopicId ).html(spinnerImage);
        var urlParam = "parentTopicId=" + parentTopicId + "&classId=" + classId + "&schoolId=" + schoolId;
        if (typeof lastSubTopicId !== 'undefined') {
            // variable is undefined
            urlParam += "&lastSubTopicId=" + lastSubTopicId ;
        }

        if (typeof showFlag === 'undefined') {
            // variable is undefined
            showFlag = true ;
        }

        $.ajax({
            type: "get",
            url: baseUrl + "/lesson/index/subtopic?" + urlParam,
            cache: true,
            success: function(response){
                try {
                    if (showFlag === true) {
                        setSuccessMessage(topicSuccessMessage);
                    }
                    $( "#subTopicInfo-" + classId + '-' + parentTopicId).html(response);
                } catch(e) {
                    //alert('Exception while request..');
                }
            },
            error: function(xhr){
                validateAjaxError(xhr);
            }
        });
    }
}

/**
 * Update topic
 * @param integer classId
 * @param integer topicId
 */
function updateTopic(classId, topicId)
{
    var editTopicId = classId + '-' + topicId;
    var newTopicName = String($('#editTopicTextBox-' + editTopicId).val());
    var oldTopicName = String($('#editTopicLink-' + editTopicId).html());

    newTopicName = $.trim(newTopicName);
    oldTopicName = $.trim(oldTopicName);
    if (oldTopicName !== newTopicName) {
        var urlParam = "topicId=" + topicId + "&topicName=" + newTopicName;
        $.ajax({
            type: "get",
            url: baseUrl + "/lesson/index/update?" + urlParam,
            cache: true,
            success: function(response){
                //Apply change for all topic which are exist under other classes
                $('.editTopicLinkClass-' + topicId).html(newTopicName);
                $('.editTopicTextBoxClass-' + topicId).val(newTopicName);

                $('#editTopicLink-' + editTopicId).show();
                $('#editTopicTextBox-' + editTopicId).hide();
            }
        });
    } else {
        $('#editTopicLink-' + editTopicId).show();
        $('#editTopicTextBox-' + editTopicId).hide();
        $('#editTopicLink-' + editTopicId).focus();
    }
}

//Enter event
$('input[id^="editTopicTextBox-"]').keypress(function (e) {
    var currentId = $(this).attr('id');
    var key = e.which;
    processEnterEvent(currentId, key);
});

/**
 * Process enter event for save topic
 * @param integer currentId
 * @param integer key
 */
function processEnterEvent(currentId, key)
{
    var arr = currentId.split('-');
    // the enter key code
    if(key == 13 && arr[1] !== 'undefined') {
        updateTopic(arr[1], arr[2]);
    }
}

/**
 * Inline topic edit
 * @param integer classId
 * @param integer topicId
 */
function inlineTopicEdit(classId, topicId)
{
    var editTopicId = classId + '-' + topicId;
    $('#editTopicLink-' + editTopicId).hide();
    $('#editTopicTextBox-' + editTopicId).show();
    $('#editTopicTextBox-' + editTopicId).focus();
}

/**
 * Delete question
 * @param integer topicId
 * @param integer classId
 * @param integer schoolId
 * @param integer questionId
 * @param integer parentTopicId
 */
function deleteQuestion(topicId, classId, schoolId, questionId, parentTopicId)
{
    var urlParam = "questionId=" + questionId;
    $.ajax({
        type: "get",
        async: false,
        url: baseUrl + "/lesson/questions/delete?" + urlParam,
        cache: true,
        success: function(response){
            setSuccessDiv('questionList-' + questionId, questionDeletedSuccessMessage);
            callbackRemoveQuestion(true, classId, topicId , parentTopicId);
            alert("Question deleted successfully.");
        },
        error: function(xhr){
            validateAjaxError(xhr);
        }
    });
}

/**
 * Call back remove question
 * @param {boolean} value
 * @param integer classId
 * @param integer topicId
 * @param integer parentTopicId
 */
function callbackRemoveQuestion(value, classId, topicId ,parentTopicId)
{
    var editTopicId = classId + '-' + topicId;
    var editParentTopicId = classId + '-' + parentTopicId;
    var totalTopicQuestionCount = parseInt($.trim($('#topicQuestionCount-' + editParentTopicId).attr('count')));
    var subTopicQuestionCount = parseInt($.trim($('#topicQuestionCount-' + editTopicId).attr('count')));

    if (value) {
        subTopicQuestionCount -= 1;
        totalTopicQuestionCount -= 1;

        questionCountStatistics(subTopicQuestionCount, editTopicId);
        questionCountStatistics(totalTopicQuestionCount, editParentTopicId);
    }
}









/**
 * Add topic
 * @param integer classId
 * @param integer schoolId
 */
function addTopic(classId, schoolId)
{
    var topicName = $('#addTopicName-' + classId).val();
    $('#addTopicName-' + classId).val('');
    if (topicName == "") {
        alert('Topic name can not be empty');
        return false;
    }
    var urlParam = "classId=" + classId + "&topicName=" + topicName;

    $.ajax({
        type: "get",
        url: baseUrl + "/lesson/index/addtopic?" + urlParam,
        cache: true,
        async: false,
        success: function(response) {
            setSuccessMessage(topicSuccessMessage);
            $( "#classInfo-" + classId ).html('');
            getClassDetailsWithLastData(classId, schoolId);
            hideTopicDiv(classId);
        },
        error: function(xhr){
            validateAjaxError(xhr);
        }
    });
}

/**
 * Add sub topic
 * @param integer parentTopicId
 * @param integer classId
 * @param integer schoolId
 */
function addSubTopic(parentTopicId, classId, schoolId)
{
    var editParentTopicId = classId + '-' + parentTopicId;
    var subTopicName = $('#subTopicName-' + editParentTopicId).val();
    $('#subTopicName-' + editParentTopicId).val('');
    if (subTopicName == "") {
        alert('Topic name can not be empty');
        return false;
    }
    var urlParam = "parentTopicId=" + parentTopicId + "&classId=" + classId + "&schoolId=" + schoolId + "&subTopicName=" + subTopicName;

    $.ajax({
        type: "get",
        url: baseUrl + "/lesson/index/addsubtopic?" + urlParam,
        cache: true,
        async: false,
        success: function(response){
            setSuccessMessage(topicSuccessMessage);
            $( "#subTopicInfo-" + parentTopicId ).html('');
            getSubtopicDetails(parentTopicId, classId, schoolId);
            hideTopicDiv(parentTopicId);
        },
        error: function(xhr){
            validateAjaxError(xhr);
        }
    });

    var subTopicCount = parseInt($.trim($('#subTopicCount-' + editParentTopicId).attr('count')));
    subTopicCount += 1;
    topicCountStatistics(subTopicCount, editParentTopicId);
}


/**
 * Get class details with last data
 * @param integer lastClassId
 * @param integer lastSchoolId
 * @param integer lastParentTopicId
 * @param integer lastSubTopicId
 */
function getClassDetailsWithLastData(lastClassId, lastSchoolId, lastParentTopicId, lastSubTopicId)
{
    var content = $.trim($( "#classInfo-" + lastClassId ).html());
    if (content.length == 0) {
        $( "#classInfo-" + lastClassId ).html(spinnerImage);

        var urlParam = "classId=" + lastClassId + "&schoolId=" + lastSchoolId;

        if (typeof lastParentTopicId !== 'undefined') {
            // variable is undefined
            urlParam += "&lastParentTopicId=" + lastParentTopicId ;
        }

        if (typeof lastSubTopicId !== 'undefined') {
            // variable is undefined
            urlParam += "&lastSubTopicId=" + lastSubTopicId ;
        }

        $.ajax({
            type: "get",
            url:  baseUrl + "/lesson/index/view?" + urlParam,
            cache: true,
            async: false,
            success: function(response){
                try{
                    $( "#classInfo-" + lastClassId ).html(response);
                }catch(e) {
                    //alert('Exception while request..');
                }
            },
            error: function(xhr){
                validateAjaxError(xhr);
            }
        });
    }
}

/**
 * Set error message
 * @param string message
 */
function setErrorMessage(message)
{
    $('.error').html(message);
    $('.error').fadeIn();
    setTimeout(function() {
        $('.error').fadeOut('fast');
    }, delayFadeOut);
}

/**
 * Set success message
 * @param string message
 */
function setSuccessMessage(message)
{
    $('.success').html(message);
    $('.success').fadeIn();
    setTimeout(function() {
        $('.success').fadeOut('fast');
    }, delayFadeOut);
}

/**
 * Set success div
 * @param string elementId
 * @param string message
 */
function setSuccessDiv(elementId, message)
{
    createGrowl('<div class="success-head alert-box-flash-head">' + message + '</div>', 'qtip-green');
    $('#' + elementId).remove();
}

/**
 * Set error message
 * @param string message
 */
function setErrorDiv(elementId, message)
{
    createGrowl('<div class="error-head alert-box-flash-head">' + message + '</div>', 'qtip-red');
    $('#' + elementId).remove();
}



window.createGrowl = function(message, uiClasses) {
    var target = $('.qtip.jgrowl:visible:last');
    var xCord = $( window ).width() - 300;
    var yCord = $( window ).height() - 50;

    $('<div/>').qtip({
        content: {
            text: message,
            title: {
                text: '',
                button: false
            }
        },
        position: {
            my: 'top right',
            at: 'top right',
            target: $(window),
            container: $('#qtip-growl-container')
        },
        show: {
            event: false,
            ready: true,
            effect: function() {
                $(this).stop(0, 1).animate({ height: 'toggle' }, 400, 'swing');
            },
            delay: 0
        },
        hide: {
            event: false,
            effect: function(api) {
                $(this).stop(0, 1).animate({ height: 'toggle' }, 400, 'swing');
            }
        },
        style: {
            width: 350,
            height: 50,
            classes: uiClasses,
            tip: false
        },
        events: {
            render: function(event, api) {
                if(!api.options.show.persistent) {
                    $(this).bind('mouseover mouseout', function(e) {
                        var lifespan = delayFadeOut;

                        clearTimeout(api.timer);
                        if (e.type !== 'mouseover') {
                            api.timer = setTimeout(function() { api.hide(e) }, lifespan);
                        }
                    })
                        .triggerHandler('mouseout');
                }
            }
        }
    });
}


/**
 * Set parent topic according to sub-topic checkbox
 * @param integer classId
 * @param integer parentTopicId
 */
function setParentWithSubTopicTagging(classId, parentTopicId)
{
    var linkToParent = '.linkToParent-' + classId + '-' + parentTopicId;
    var numberOfSubTopics =  $(linkToParent).length;
    var parentTopicCheckboxId = '#topicName-' + classId + '-' + parentTopicId;
    var countSubTopicChecked = 0;
    var countSubTopicUnChecked = 0;
    var parentTopicCheckboxProp = $(parentTopicCheckboxId).prop('checked');

    $(linkToParent).each(function() {
        if($(this).prop('checked') == true){
            countSubTopicChecked ++;
        } else {
            countSubTopicUnChecked ++;
        }
    });

    if ((countSubTopicChecked !== numberOfSubTopics) && (countSubTopicUnChecked !== numberOfSubTopics)) {
        $(parentTopicCheckboxId).prop("indeterminate", true);
    } else {
        $(parentTopicCheckboxId).prop("indeterminate", false);
        if(countSubTopicChecked === numberOfSubTopics){
            $(parentTopicCheckboxId).prop('checked', true);
        } else {
            $(parentTopicCheckboxId).prop('checked', false);
        }
    }

}

/**
 * Set sub topic tagging according parent topic
 * @param integer classId
 * @param integer parentTopicId
 */
function setSubTopicTagging(classId, parentTopicId)
{
    var parentTopicCheckboxId = '#topicName-' + classId + '-' + parentTopicId;
    var linkToParent = '.linkToParent-' + classId + '-' + parentTopicId;

    $(parentTopicCheckboxId).prop("indeterminate", false);

    if($(parentTopicCheckboxId).prop('checked') == true){
        $(linkToParent).prop('checked', true);
    } else {
        $(linkToParent).prop('checked', false);
    }
}


/**
 * Update topic tag
 * @param integer classId
 * @param integer topicId
 * @param integer topicTaggedId
 * @param integer schoolId
 * @param string parentTopicFlag
 * @param integer lastParentTopicId
 */
function updateLessonTagged(classId, topicId, topicTaggedId, schoolId, parentTopicFlag, lastParentTopicId)
{
    var topicTagged = 0;
    if(document.getElementById(topicTaggedId).checked){
        topicTagged = 1;
    }
    var urlParam = "classId=" + classId + "&topicId=" + topicId + "&topicTagged=" + topicTagged;
    $.ajax({
        type: "get",
        url:  baseUrl + "/lesson/index/updateTagged?" + urlParam,
        cache: true,
        success: function(response){
            if (parentTopicFlag === 'parentTopic') {
                setSubTopicTagging(classId, topicId);
            } else if (parentTopicFlag === 'subParentTopic') {
                setParentWithSubTopicTagging(classId, lastParentTopicId);
            }
        },
        error: function(xhr){
            validateAjaxError(xhr);
        }
    });
}

/**
 * Set success div
 * @param string elementId
 * @param string message
 */
function setSuccessGrowlDiv( message)
{
    createGrowl('<div class="success-head alert-box-flash-head">' + message + '</div>', 'qtip-green');
}

/**
 * Set error message
 * @param string message
 */
function setErrorGrowlDiv( message)
{
    createGrowl('<div class="error-head alert-box-flash-head">' + message + '</div>', 'qtip-red');
}

/**
 * Update question count by topic id
 * @param integer topicId
 * @param integer totalQuestionCount
 */
function updateQuestionCountByTopicId(topicId, totalQuestionCount)
{
    $(".topicQuestionCountClass-" + topicId).attr('count', totalQuestionCount)
    var textHtml = totalQuestionCount + ' Question';
    if (totalQuestionCount > 1) {
        textHtml += 's';
    }
    $(".topicQuestionCountClass-" + topicId).html(textHtml);
}