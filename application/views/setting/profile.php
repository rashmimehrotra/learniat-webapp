<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php $this->load->view('layouts/header.php'); ?>
<?php $this->load->view('layouts/menu.php', array('selectedLink' => 'Profile')); ?>
<link rel="stylesheet" href="<?php echo base_url('assets/css/profile.css');?>" />

<!-- Start right content page -->
<div class="content-page">
    <div class="content">

        <!-- Heading Start -->
        <div class="heading_cont">

            <div class="sess-bar-left">
                <h2 class="sess-left">Your Profile</h2>
            </div>
            <div class="sess-bar-right">
                <div class = "sess-bar-right-content">
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <!-- Heading Ends -->



        <?php
        $attributes = array('name' => 'profileData', 'id' => 'profileData');
        echo form_open_multipart('', $attributes);
        ?>

        <div class="edit_profile_wrapper">

            <table>
                <tr>
                    <td>

                        <table>
                            <tr>
                                <td>First Name: </td>
                                <td>
                                    <?php
                                    $data = array('id' => 'firstName', 'name' => 'firstName');
                                    echo form_input($data, '');
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <td>Last Name: </td>
                                <td>
                                    <?php
                                    $data = array('id' => 'lastName', 'name' => 'lastName');
                                    echo form_input($data, '');
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <td>Profile Image: </td>
                                <td>

                                </td>
                            </tr>


                            <tr>
                                <td>Email Address: </td>
                                <td>
                                    <?php echo form_input('email', ''); ?>
                                </td>
                            </tr>

                            <tr>
                                <td>Phone: </td>
                                <td>
                                    <?php echo form_input('phone', ''); ?>
                                </td>
                            </tr>

                            <tr>
                                <td>Address: </td>
                                <td>
                                    <?php
                                    $data = array('name' => 'address', 'cols' => 30, 'rows' => 3);
                                    echo form_textarea($data, '');
                                    ?>
                                </td>
                            </tr>
                        </table>

                    </td>

                    <td>

                    </td>
                </tr>
            </table>
        </div>



        <?php echo form_close(); ?>

    </div>

</div>
<!-- End right content page -->

<script type="text/javascript" src="<?php echo base_url('assets/js/qtip/jquery.min.js');?>"></script>
<?php $this->load->view('layouts/footer.php'); ?>