<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$menuList = array (
		'Session Summary' => 'session/summary/index',
		'Time Table' => 'time-table/index/calendar',
		'Lesson Plan' => 'lesson/topics/index',
		'Students' => 'student/index/index',
	);

$selectedLink = isset($selectedLink) ? $selectedLink : 'Session Summary';
?>
<div class="left side-menu">
	<div style="position: relative; overflow: hidden; width: auto; height: 581px;" class="slimScrollDiv">
		<div style="overflow: hidden; width: auto; height: 581px;" class="sidebar-inner slimscrollleft">
		   
			<div class="clearfix"></div>					
			
			<div id="sidebar-menu">
			  	<ul>
					<?php
					foreach ($menuList AS $title => $link) :
                        $classActive = ($selectedLink == $title) ? 'active' : '';
                        echo "<li class='has_sub'>
                            <a class='$classActive' href='" .site_url($link) . "'>
                                <i class='icon-home-3'></i>
                                <span>$title</span>
                                <span class='pull-right'></span>
                            </a>
                        </li>";
					endforeach; 
					?>
			 	</ul>
			</div>
			
		<div class="clearfix"></div>
		
	</div>
		<div style="background: none repeat scroll 0% 0% rgb(122, 134, 143); width: 5px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; left: 1px; height: 313.427px; visibility: visible;" class="slimScrollBar">
		</div>
		<div style="width: 5px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: none repeat scroll 0% 0% rgb(51, 51, 51); opacity: 0.2; z-index: 90; left: 1px;" class="slimScrollRail">
		</div>
	</div>
	<div class="left-footer">
		<div class="progress progress-xs">
		  <div class="progress-bar bg-green-1" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 80%">
			<span class="progress-percentage">80%</span>
		  </div>
		  
		  <a data-toggle="tooltip" title="See task progress" class="btn btn-default md-trigger" data-modal="task-progress"><i class="fa fa-inbox"></i></a>
		</div>
	</div>
</div>
