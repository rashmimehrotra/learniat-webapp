<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $this->load->view('layouts/header.php'); ?>
<?php $this->load->view('layouts/menu.php', array('selectedLink' => 'Lesson Plan')); ?>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tabs.css" />
<link rel="stylesheet" href="<?php echo base_url('assets/css/graph.css');?>" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url('assets/css/lesson.css');?>" />

<div id="topicDetailsComplete<?php echo $parentTopicId; ?>">
    <?php
    $data = array(
        'parentTopicData' => $parentTopicData,
        'subTopicData' => $subTopicData,
        'otherDetails' => $otherDetails,
        'schoolId' => $schoolId,
        'parentTopicId' => $parentTopicId,
        'classId' => $classId,
        'error' => $error,
        'success' => $success
    );
    $this->load->view('lesson/question/topic-details.php', $data);
    ?>
</div>


<?php $this->load->view('layouts/footer.php'); ?>
<script type="text/javascript" src="<?php echo base_url('assets/js/common/lesson.js');?>?v=123"></script>