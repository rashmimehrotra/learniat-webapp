<div class=" topic-bar-box">
    <div class="class-student-list-margin">
    <table width="95%">
        <tr>
            <td width="30%">

                <div class="profile_img_student">
                    <img src="<?php echo LEARNIAT_IMAGE_PATH . '/' . $student['studentId']; ?>_79px.jpg" class="profilePic40">
                </div>
                <label style="margin-top:11px;padding-left:10px;"><?php echo $student['studentFirstName']; ?></label>

            </td>
            <td width="32%">
                <?php
                $graphIndexData = array($student);
                $data = array(
                    'graphIndexData' => $graphIndexData,
                    'showParticipate' => FALSE,
                    'averageCountText' => FALSE,
                    'maxGraspIndex' => (isset($maxGraspIndex)) ? $maxGraspIndex : 0,
                    'maxParticipationIndex' => (isset($maxParticipationIndex)) ? $maxParticipationIndex : 0,
                );
                ?>
                <div class="containerIndex" style="padding-top:10px;">
                    <?php $this->load->view('graph/index.php', $data); ?>
                </div>
            </td>
            <td width="32%">

                <div class="containerIndex" style="padding-top:10px;">
                    <?php  $this->load->view('graph/grasp-index.php', $data); ?>
                </div>
            </td>
        </tr>
    </table>
    </div>
</div>