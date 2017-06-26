<!-- <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>-->

<script>

	/*$(".switchActive").click(function(){
        
            remove();
            $('#'+$(this).attr('id')).addClass('leftmenu1');
            var id = $(this).attr('id').split('-');
            temp();
            $('#tabQuestionDetails-'+id[1]).show();
            
    });*/
   
    function remove(){
        $('.queryTab').removeClass('topic-exp-active');
        $('.questionTab').removeClass('topic-exp-active'); 
    }
 
    function temp(){
        $('.queryTab').hide();
        $('.questionTab').hide();           		   
    }


    function popupClose(popId)
    {
    	$('#' + popId).fadeOut();
        $('.black_overlay').hide();
    	//var participationPersonInfo = $('#' + popId).attr('value');
    	//console.log(participationPersonInfo);
    	//$( "#participationPersonInfo-" + participationPersonInfo).focus();
    }
  </script>
