var spinnerImage = '<br><div class="centerDiv"><img src="' + imagePath + 'spinner-1.gif"></div>';
/**
 * Get topic query details
 * @param integer sessionId
 */
function getStudentSortDetails(sessionId, sortBy)
{
    if (sortBy.length == 0) {
        sortBy ='participation-index';
    }
    $("#modal").show();
    $("#fade").show();
    $.ajax({
        type: "get",
        url: baseUrl + "/session/summary/sortStudentDetails?sortBy=" + sortBy + "&sessionId=" + sessionId,
        cache: true,
        success: function(response){
            try{
                $("#tab2").html(response);
                $("#modal").hide();
                $("#fade").hide();

            }catch(e) {
                alert('Exception while request..');
            }
        },
        error: function(xhr){
            validateAjaxError(xhr);
        }
    });
}

/**
 * Get student question popup screen
 * @param integer questionId
 * @param integer sessionId
 */
function getStudentQuestionPopupScreen(questionId, sessionId)
{
    $.ajax({
        type: "get",
        url: baseUrl + "/session/topic/resultquestion?questionId=" + questionId + '&sessionId=' + sessionId,
        cache: true,
        success: function(response) {
            try {
                $('#modal_popup_no_bg').css('height', '89%');
                $("#modal_popup_no_bg").html(response);
                //$('.modalpop_wrapper').css('max-height', '46%');
                //$('.modalpop_wrapper').css('max-height', '46em');
            } catch (e) {
                alert('Exception while request..');
            }
        },
        error: function(xhr){
            validateAjaxError(xhr);
        }
    });

    showModalNoBgPopup();
}

/**
 * Get topic question details
 * @param integer topicId
 */
function getTopicQuestionDetails(topicId)
{
    var content = $.trim($( "#tabQueryDetails-2" + topicId).html());
    if (content.length == 0) {
        var sessionId = $("#sessionId").val();
        $.ajax({
            type: "get",
            url: baseUrl + "/session/topic/question?topicId=" + topicId + "&sessionId=" + sessionId,
            cache: true,
            success: function(response){
                try{
                    $( "#tabQueryDetails-2" + topicId).html(response);

                }catch(e) {
                    alert('Exception while request..');
                }
            },
            error: function(xhr){
                validateAjaxError(xhr);
            }
        });
    }
}

/**
 * Get topic query details
 * @param integer topicId
 */
function getTopicQueryDetails(topicId)
{
    var content = $.trim($( "#tabQueryDetails-1" + topicId).html());
    $("#tabQueryDetails-1" + topicId).attr('style',  'display:block;');
    $("#tabQueryDetails-2" + topicId).attr('style',  'display:none;');
    $("#tabQuery-1" + topicId).find("a").attr('class',  'link-topic-tab topic-exp-active');
    $("#tabQuery-2" + topicId).find("a").attr('class',  'link-topic-tab');

    if (content.length == 0) {
        var sessionId = $("#sessionId").val();
        $.ajax({
            type: "get",
            url: baseUrl + "/session/topic/query?topicId=" + topicId + "&sessionId=" + sessionId,
            cache: true,
            success: function(response){
                try{
                    //alert(date + classSessionId);
                    $( "#tabQueryDetails-1" + topicId).html(response);
                }catch(e) {
                    alert('Exception while request..');
                }
            },
            error: function(xhr){
                validateAjaxError(xhr);
            }
        });
    }
}


/**
 * Get student query details
 * @param integer studentId
 */
function getStudentQueryDetails(studentId)
{
    var content = $.trim($( "#tabQuestionDetails-1" + studentId).html());
    $("#tabQuestionDetails-1" + studentId).attr('style',  'display:block;');
    $("#tabQuestionDetails-2" + studentId).attr('style',  'display:none;');
    $("#tabQuestion-1" + studentId).find("a").attr('class',  'link-student-tab topic-exp-active');
    $("#tabQuestion-2" + studentId).find("a").attr('class',  'link-student-tab');
    if (content.length == 0) {
        var sessionId = $("#studentSessionId").val();

        $.ajax({
            type: "get",
            url: baseUrl + "/session/student/query?studentId=" + studentId + "&sessionId=" + sessionId,
            cache: true,
            //data: $('#userForm').serialize(),
            success: function(response){
                try{
                    //alert(date + classSessionId);
                    $( "#tabQuestionDetails-1" + studentId).html(response);
                }catch(e) {
                    alert('Exception while request..');
                }
            },
            error: function(xhr){
                validateAjaxError(xhr);
            }
        });
    }
}

/**
 * Get Student question details
 * @param integer studentId
 */
function getStudentQuestionDetails(studentId)
{
    var content = $.trim($( "#tabQuestionDetails-2" + studentId).html());
    if (content.length == 0) {
        var sessionId = $("#studentSessionId").val();
        $.ajax({
            type: "get",
            url: baseUrl + "/session/student/question?studentId=" + studentId + "&sessionId=" + sessionId,
            cache: true,
            //data: $('#userForm').serialize(),
            success: function(response){
                try{
                    //alert(date + classSessionId);
                    $( "#tabQuestionDetails-2" + studentId).html(response);
                }catch(e) {
                    alert('Exception while request..');
                }
            },
            error: function(xhr){
                validateAjaxError(xhr);
            }
        });
    }
}