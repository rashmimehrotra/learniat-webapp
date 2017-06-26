/**
 * Created by avinash on 26-9-15.
 */


/**
 * Get param by mname
 * @param name
 * @param href
 * @returns {string}
 */
function getParameterByName( name, href )
{
    name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
    var regexS = "[\\?&]"+name+"=([^&#]*)";
    var regex = new RegExp( regexS );
    var results = regex.exec( href );
    if( results == null )
        return "";
    else
        return decodeURIComponent(results[1].replace(/\+/g, " "));
}


/**
 * Show topic div
 * @param integer id
 */
function showTopicDiv(id)
{
    $('#addLink-' + id).children(".add-topic-span").attr('id', 'expand-minus');
    $('#addTopicDiv-' + id).show();
}

/**
 * Hidden topic div
 * @param integer id
 */
function hideTopicDiv(id)
{
    $('#addLink-' + id).children(".add-topic-span").attr('id', 'expand-plus');
    $('#addTopicDiv-' + id).hide();
}

/**
 * Get self page
 * @param integer classId
 * @param integer lastParentTopicId
 * @param integer schoolId
 * @param integer lastSubTopicId
 */
function getSelfPage(classId, lastParentTopicId, schoolId, lastSubTopicId)
{
    var urlParam = "lastParentTopicId=" + lastParentTopicId + "&classId=" + classId + "&schoolId=" + schoolId ;
    if (typeof lastSubTopicId !== 'undefined') {
        // variable is undefined
        urlParam = urlParam + "&lastSubTopicId=" + lastSubTopicId ;
    }

    var url = baseUrl + '/lesson/topics/index?' + urlParam;
    window.location.href = url;
}

/**
 * Show question div
 * @param integer topicId
 * @param integer questionId
 * @param boolean duplicate
 */
function showQuestionDiv(topicId, questionId, duplicate)
{
    $( "#modal_popup").html('');
    var urlParam = "topicId=" + topicId + "&questionId=" + questionId + "&duplicate=" + duplicate;

    $.ajax({
        type: "get",
        url: baseUrl + '/lesson/index/question_edit?' + urlParam,
        cache: true,
        success: function(response){
            try{
                $( "#modal_popup" ).html(response);
            }catch(e) {
                //alert('Exception while request..');
            }
        },
        error: function(xhr){
            validateAjaxError(xhr);
        }
    });

    var pos = $("#modal_popup").position();
    $("#modal_popup").css({ position: 'fixed',top: pos.y});
    $("#modal_popup").show();
    $(".black_overlay").show();


    $(window).scroll(function () {
        pos = $("#modal_popup").position();
        //adjust the dialog box so that it scrolls as you scroll the page
        $("#modal_popup").css({
            position: 'fixed',
            top: pos.y
        });
    });

}

/**
 * Hide question div
 */
function hideQuestionDiv()
{
    $("#modal_popup").hide();
    $(".black_overlay").hide();
    $(window).unbind('scroll');


    //If you remove jscrop then remove bottom line
    $("[id^=editTopicTextBox-]").hide();
}



/**
 * Add sub topic for question details
 * @param integer parentTopicId
 * @param integer classId
 * @param integer schoolId
 */
function addSubTopicForQuestion(parentTopicId, classId, schoolId, topicId)
{
    var editParentTopicId = classId + '-' + parentTopicId;
    var subTopicName = $('#subTopicName-' + editParentTopicId).val();
    if (subTopicName == "") {
        alert('Topic name can not be empty');
        return false;
    }

    var urlParam = "parentTopicId=" + parentTopicId + "&classId=" + classId + "&schoolId=" + schoolId + "&subTopicName=" + subTopicName;

    $.ajax({
        type: "get",
        url: baseUrl + "/lesson/index/addsubtopic?" + urlParam,
        cache: true,
        success: function(response){
            getSelfPageWithoutRedirect('Topic data has been saved successfully.');
        },
        error: function(xhr){
            validateAjaxError(xhr);
        }
    });
}

/**
 * Get question details
 * @param topicId
 * @param classId
 * @param schoolId
 * @param parentTopicId
 */
function getQuestionDetails(topicId, classId, schoolId, parentTopicId)
{
    var content = $.trim($( "#topicQuestionReference-" + topicId ).html());
    if (content.length == 0) {
        var urlParam = "classId=" + classId + "&schoolId=" + schoolId + "&topicId=" + topicId;
        if (typeof parentTopicId !== 'undefined') {
            urlParam += '&parentTopicId=' + parentTopicId;
        }

        $.ajax({
            type: "get",
            async: false,
            url: baseUrl + '/lesson/index/question_view?' + urlParam,
            cache: true,
            success: function(response){
                try{
                    $( "#topicQuestionReference-" + topicId ).html(response);
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