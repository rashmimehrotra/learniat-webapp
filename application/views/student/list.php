
<?php
if (empty($studentData)) : ?>
    <div class="topic-bar-box">
        <div class="student_txt" style="padding-left:20px;">
            <p>Student not found</p>
        </div>
    </div>
<?php
else :
    $rowCount = 0;
    $rowLimit = 2;
    $endRowLimit = $rowLimit + 1;
    $totalCount = count($studentData);

    //Last first 3 record
    foreach ($studentData AS $key => $student) :
        if ($rowCount > $rowLimit) :
            break;
        endif;

        unset($studentData[$key]);
        $data = array( 'student' => $student);
        $this->load->view('student/helper/class-student.php', $data);

        $rowCount ++;
    endforeach;

    $middleLimit = 0;
    if ($totalCount > $endRowLimit) :
        $middleLimit = $totalCount - $rowCount ;
    endif;


    if ($middleLimit > $endRowLimit) :
        $countStudentHidden = $middleLimit- $endRowLimit;
?>
        <div class="cursorPointer"
             onclick="showHiddenStudentDiv(<?php echo $classId; ?>)"
             id="hiddenStudentLink-<?php echo $classId; ?>"
             style="display: <?php echo ($hiddenStudent === 1) ? 'none' : '';  ?>">
            <div class="stdPageShowFull topic-bar-box ">
                <div class="class-student-list-margin">
                    <img src="<?php echo base_url('assets/images/student-page-show-full.png'); ?>" alt="full-show-image" />
                    <span class="stdPageFullText">
                        Show all student<?php echo ($countStudentHidden > 1) ? 's' : ''; ?>
                        (<?php echo $countStudentHidden; ?>)
                    </span>
                </div>
            </div>
        </div>

        <div style="display: <?php echo ($hiddenStudent === 1) ? 'block' : 'none';  ?>"
             id="hiddenStudentDiv-<?php echo $classId; ?>">
            <?php
                foreach ($studentData AS $key => $student) :
                    if ($rowCount >= $middleLimit) :
                        break;
                    endif;

                    unset($studentData[$key]);
                    $data = array( 'student' => $student);
                    $this->load->view('student/helper/class-student.php', $data);

                    $rowCount ++;
                endforeach;
            ?>
        </div>
<?php
    endif;
    //Last 3 record
    if (!empty($studentData)) :
        foreach ($studentData AS $key => $student) :
            unset($studentData[$key]);
            $data = array( 'student' => $student);
            $this->load->view('student/helper/class-student.php', $data);

            $rowCount ++;
        endforeach;
    endif;

endif;


?>