
<?php
if($week == 1){
    $yearPrevious = $year - 1;
    $weekPrevious = 54;
    $weekNext = $week + 1;
    $yearNext = $year;
} else if($week == 54) {
    $yearPrevious = $year;
    $weekPrevious = $week - 1;
    $weekNext = 1;
    $yearNext = $year + 1;
} else {
    $weekPrevious = $week - 1;
    $yearPrevious = $year;
    $weekNext = $week + 1;
    $yearNext = $year;
}
$dto = new DateTime();
$dto->setISODate($year, $week);
$fromDate = $dto->format('M d');
$completeDate = $dto->format('Y, m-1, d');
$dto->modify('+' . CALENDER_DAYS . ' days');
$toDate = $dto->format('-d, Y');


$maxParticipationIndex = 0;
foreach ($timeTableData AS $data) :
    $maxParticipationIndex = ($data->averagePI > $maxParticipationIndex) ? $data->averagePI : $maxParticipationIndex;
endforeach;

?>
<link rel="stylesheet" href="<?php echo base_url('assets/css/calendar/easycal.css');?>" type="text/css"/>
<link rel="stylesheet" href="<?php echo base_url('assets/js/new-calendar/calendar.css');?>" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url('assets/js/new-calendar/jquery.min.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/new-calendar/jquery-ui.1.7.2.min.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/new-calendar/calendar.js');?>"></script>

<script type='text/javascript'>


    var year = new Date().getFullYear();
    var month = new Date().getMonth();
    var day = new Date().getDate();

    var eventData = {
        events : [
            <?php
                if (!empty($timeTableData)) :

                    $currentDate = strtotime(date("Y-m-d H:i:s"));

                    $endKey = count($timeTableData) - 1;
                    foreach($timeTableData AS $key => $data) :

                        $averagePI = (isset($data->averagePI)) ? $data->averagePI : 0;
                        $maxParticipationIndex = (isset($maxParticipationIndex)) ? $maxParticipationIndex : 0;
                        if ($maxParticipationIndex > 0) {
                            $averageParticipationIndexPosition = ($averagePI * 100) / $maxParticipationIndex;
                        }

                        $averageParticipationIndexPosition -= 3;

                           if ($averageParticipationIndexPosition > 88) {
                            $averageParticipationIndexPosition = 88;
                        }

                        $dataBar = array(
                            'studentsPresent' => $data->occupiedSeats,
                            'total' => $data->registeredSeats,
                        );

                        $attendancePercent = 0;
                        if ($data->occupiedSeats > 0) {
                            $attendancePercent = ($data->occupiedSeats/ $data->registeredSeats) * 100;
                            $attendancePercent= sprintf("%1\$.0f",$attendancePercent);
                        }

                        $string = $this->load->view('session/helper/attendance-bar.php', $dataBar, true);

                        $title = '<table class="event-calendar">';
                        $title .= '<tr><th width="70%">' . $data->subject_name . '</th>';
                        $title .= '<th>';

                        $title .= '<table><tr><td>';
                        $title .= preg_replace('/^\s+|\n|\r|\s+$/m', '', $string);
                        $title .= '</td><td style="vertical-align:bottom;padding:5px 0 0 2px;">' . $attendancePercent . '%</td></tr></table>';

                        $title .='</th></tr>';
                        $title .= '<tr><td width="50%">' . $data->class_name . '</td><td width="50%">' . $data->room_name . '</td></tr>';

                        $timestamp2 = strtotime($data->ends_on);

                        if ($currentDate > $timestamp2) :
                            $title .= '<tr><td width="50%" style="line-height: 38px;">Average PI: ' . $data->averagePI . '</td>';
                            $title .= '<td width="50%" style="line-height: 38px;">';
                            $title .= '<div class="titleBarParticipationIndexLine"><div class="centerIndex">';
                            $title .= '<div class="averageDiamond" style="left:' . $averageParticipationIndexPosition . '%;"></div>';
                            $title .= '</div></div>';
                            $title .= '</td></tr>';
                        endif;

                        $title .= '</table>';

                        $startsOn = date("Y, m-1, d, H, i" , strtotime($data->starts_on));
                           $endOn = date("Y, m-1, d, H, i" , strtotime($data->ends_on));
                   ?>
            {
                "id" : <?php echo $key; ?>,
                "start" : new Date(<?php echo $startsOn; ?> ),
                "end" : new Date(<?php echo $endOn; ?> ),
                "title" : '<?php echo $title; ?>'
            }<?php echo ($endKey != $key) ? ',' : ''; ?>
            <?php
                    endforeach;
                endif;
            ?>

        ]
    };

    $(document).ready(function() {

        $('#calendar').weekCalendar({
            date:new Date(<?php echo $completeDate; ?>),
            timeslotsPerHour: 2,
            businessHours: {start: 4, end: 19, limitDisplay: true},
            height: function($calendar){
                return $(window).height() - $("h1").outerHeight();
            },
            daysToShow : 6,
            firstDayOfWeek:1,
            use24Hour:false,
            readonly:true,
            timeslotHeight: 50,
            eventRender : function(calEvent, $event) {
                if(calEvent.end.getTime() < new Date().getTime()) {
                    $event.css({"backgroundColor" : "#f5f8f9", 'color' : '#000'});
                    $event.find(".time").css({"backgroundColor": "#999", "border":"1px solid #888"});
                }
            },
            //longDays: [ 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            data:eventData
        });

    });

    $(document).ready(function(){

        $('.wc-cal-event').mouseover(function(){
            var height = parseInt($( this ).height());
            if (height < 60) {
                $(this).attr('class','wc-cal-event ui-corner-all wc-cal-event-full');
            }
        });
        $('.wc-cal-event').mouseout(function(){
            $(this).attr('class','wc-cal-event ui-corner-all ');
        });

    });

</script>
<div id='calendar' style="width: 100%"></div>