<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

</div><!-- This is from header -->
<!-- End of page -->
	<!-- the overlay modal element -->
<div class="md-overlay"></div>
<!-- End of overlay modal -->

<script>

  	function DropDown(el) {
		this.dd = el;
		this.initEvents();
	}
	DropDown.prototype = {
		initEvents : function() {
			var obj = this;

			obj.dd.on('click', function(event){
				$(this).toggleClass('active');
				event.stopPropagation();
			});
		}
	}

	$(function() {

		var dd = new DropDown( $('#dd') );

		$(document).click(function() {
			// all drop downs
			$('.wrapper-dropdown-5').removeClass('active');
		});

		$('.profileOption').click(function(event){
			// all drop downs
			$('#dd').toggleClass('active');
			event.stopPropagation();

		});
	});


</script>

<script type="text/javascript" src="<?php echo base_url('assets/files/tabs-accordions.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/common/default.js');?>"></script>

<div id="overlay"></div>
</body>
</html>
