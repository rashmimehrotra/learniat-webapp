<?php $this->load->view('layouts/header.php'); ?>
<?php $this->load->view('layouts/menu.php', array('selectedLink' => 'Time Table')); ?>
    <script type="text/javascript" src="<?php echo base_url('assets/js/jquery-1.8.2.min.js'); ?>"></script>
    <link href='<?php echo base_url('assets/js/fullcalendar/fullcalendar.css'); ?>' rel='stylesheet' />
    <link href='<?php echo base_url('assets/js/fullcalendar/fullcalendar.print.css'); ?>' rel='stylesheet' media='print' />
    <script src='<?php echo base_url('assets/js/fullcalendar/moment.min.js'); ?>'></script>
    <script src='<?php echo base_url('assets/js/fullcalendar/jquery.min.js'); ?>'></script>
    <script src='<?php echo base_url('assets/js/fullcalendar/fullcalendar.js'); ?>'></script>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/calendar/easycal.css');?>" type="text/css"/>
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
$completeDate = $dto->format('Y-m-d');
$dto->modify('+' . CALENDER_DAYS . ' days');
$toDate = $dto->format('-d, Y');

$hideDays = array('Sun' => 0, 'Sat' => 6);
$maxParticipationIndex = 0;
foreach ($timeTableData AS $data) :
    $maxParticipationIndex = ($data->averagePI > $maxParticipationIndex) ? $data->averagePI : $maxParticipationIndex;
endforeach;

?>

    <script type='text/javascript'>

        var eventData = [
            {
                title: '<table class="event-calendar"><tbody><tr><th width="70%">Physics</th><th><table><tbody><tr><td><div class="container-bar"><div style="height: 30%;background: #ffcc00;" class="bar"></div><div style="height: 60%;background: #ffcc00;" class="bar"></div><div style="height: 83.33%;background: #ffcc00;" class="bar"></div></div></td><td style="vertical-align:bottom;padding:5px 0 0 2px;">83%</td></tr></tbody></table></th></tr><tr><td width="50%">10th grade physics class</td><td width="47%">Room 16A</td></tr><tr><td width="50%" style="line-height: 38px;">Average PI: 0</td><td width="47%" style="line-height: 38px;"><div class="titleBarParticipationIndexLine"><div class="centerIndex"><div style="left:-3%;" class="averageDiamond"></div></div></div></td></tr></tbody></table>',
                start: '2015-02-13T07:00:00',
                end: '2015-02-13T07:50:00',
                color : '#f5f8f9'
            },
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
                    $title .= '<tr><th width="65%">' . htmlspecialchars($data->subject_name) . '</th>';
                    $title .= '<th>';

                    $title .= '<table><tr><td>';
                    $title .= preg_replace('/^\s+|\n|\r|\s+$/m', '', $string);
                    $title .= '</td><td style="vertical-align:bottom;padding:5px 0 0 2px;">' . $attendancePercent . '%</td></tr></table>';

                    $title .='</th></tr>';
                    $title .= '<tr><td width="50%">' . htmlspecialchars($data->class_name) . '</td>';
                    $title .= '<td width="47%" style="vertical-align: top;">' . htmlspecialchars($data->room_name) . '</td></tr>';

                    $timestamp2 = strtotime($data->ends_on);

                    if ($currentDate > $timestamp2) :
                        $title .= '<tr><td width="50%" style="line-height: 38px;">Average PI: ' . $data->averagePI . '</td>';
                        $title .= '<td width="47%" style="line-height: 38px;">';
                        $title .= '<div class="titleBarParticipationIndexLine"><div class="centerIndex">';
                        $title .= '<div class="averageDiamond" style="left:' . $averageParticipationIndexPosition . '%;"></div>';
                        $title .= '</div></div>';
                        $title .= '</td></tr>';
                    endif;

                    $title .= '</table>';

                    //$startsOn = date("Y-m-d" , strtotime($data->starts_on)) . 'T' . date("H:i:00" , strtotime($data->starts_on));
                    //$endOn = date("Y-m-d" , strtotime($data->ends_on)) . 'T' . date("H:i:00" , strtotime($data->ends_on));
                    $startsOn = date("c" , strtotime($data->starts_on));
                    $endOn = date("c" , strtotime($data->ends_on));
                    $day = date("D" , strtotime($data->starts_on));
                    if ($day === 'Sun' || $day === 'Sat') {
                        unset($hideDays[$day]);
                    }
               ?>
            {
                start :'<?php echo $startsOn; ?>',
                end : '<?php echo $endOn; ?>',
                title : '<?php echo $title; ?>',
                borderColor : '#c6c6c6',
                color : '#f2f5f5'//f5f8f9
            }<?php echo ($endKey != $key) ? ',' : ''; ?>
            <?php
                endforeach;
            endif;
            ?>

        ];
        $(document).ready(function() {


            $('#calendar').fullCalendar({
                header: {
                    left: '',
                    center: '',
                    right: ''//prev, title, next today
                },
                defaultDate: '<?php echo $completeDate; ?>',
                firstDay: 1,
                minTime: "<?php echo $formatData['minStartTime']; ?>:00:00",
                maxTime: "<?php echo $formatData['maxEndTime']; ?>:00:00",
                hiddenDays: [ <?php echo implode(',' , $hideDays); ?> ],
                editable: false,
                eventTextColor : '#000',
                allDaySlot:false,
                displayEventTime: false,
                columnFormat:{week : 'dddd'} ,
                defaultView : 'agendaWeek',
                eventLimit: true, // allow "more" link when too many events
                events: eventData
            });

        });

    </script>
    <div class="content-page">
        <div class="content">


            <div style="width: 100% ">
                <div class="heading_cont">
                    <div class="sess-bar-left">
                        <h2 class="sess-left">Time Table</h2>
                    </div>

                    <div class="sess-bar-right">
                        <div style='margin-right:15px;float:right;'>
                            <div class="date-btn">
                                <a href="<?php echo site_url('time-table/index/calendar') . "?year=$yearPrevious&week=$weekPrevious"; ?>">
                                    <INPUT TYPE="image" class="calendarButton" SRC="<?php echo base_url('assets/images');?>/left-btn.png" ALT="left">
                                </a>
                            </div>
                            <div class="date-year"><?php echo $fromDate . $toDate; ?></div>
                            <div class="date-btn">
                                <a href="<?php echo site_url('time-table/index/calendar') . "?year=$yearNext&week=$weekNext"; ?>">
                                    <INPUT TYPE="image" class="calendarButton" SRC="<?php echo base_url('assets/images');?>/right-btn.png" ALT="right">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div id='calendar' style="width: 99% "></div>
            </div>

        </div>
    </div>


<?php $this->load->view('layouts/footer.php'); ?>