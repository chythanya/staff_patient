<?php $this->load->view('includes/header'); ?>
<?php $this->load->view('includes/staff_navbar'); ?>
<?php $row = $this->patient_model->get_by_id($id); ?>

<div class="well">
    <ul class="nav nav-tabs">
        <li class="<?php if ($active == 'profile') echo 'active' ?>"><a href="#profile" data-toggle="tab">Profile</a></li>
        <li class="<?php if ($active == 'ekg') echo 'active' ?>"><a href="#ekg" data-toggle="tab">EKG Data</a></li>
    </ul>
    <div id="myTabContent" class="tab-content">
        <div class="tab-pane <?php if ($active == 'profile') echo 'active' ?> " id="profile">
            <form id="tab" action="<?php echo site_url('staff/update_patient_profile') ?>" method="POST">
                <input type="hidden" name="id" value="<?php echo $row->id ?>" />                
                <label>First Name</label>
                <input type="text" name ="firstname" value="<?php echo $row->firstname; ?>" class="input-xlarge">
                <label>Last Name</label>
                <input type="text" name ="lastname" value="<?php echo $row->lastname; ?>" class="input-xlarge">
                <!-- Select Role -->
                <div class="control-group">
                    <label class="control-label" for="sex">Sex</label>
                    <div class="controls">
                        <select id="sex" name="sex" class="input-xlarge">
                            <option <?php if ($row->sex == 'M') echo 'selected'; ?> value="M">M</option>
                            <option <?php if ($row->sex == 'F') echo 'selected'; ?> value="F">F</option>
                        </select>
                    </div>
                </div>
                <label>Birth Date</label>
                <div class="input-append date" id="dp1" data-date="<?php echo $row->birth ?>" data-date-format="yyyy-mm-dd">
                    <input class="span2" name="birth" size="16" type="text" value="<?php echo $row->birth ?>">
                    <span class="add-on"><i class="icon-calendar"></i></span>
                </div>
                <label>Device id</label>
                <input type="text" name ="device" value="<? echo $row->device; ?>" class="input-xlarge">
                <?php
                if ($error) {
                    echo '<div class="alert alert-error">';
                    echo '<strong>Error!</strong>' . $error_message;
                    echo '</div>';
                } else if (isset($message)) {
                    echo '<div class="alert alert-success">';
                    echo $message;
                    echo '</div>';
                }
                ?>                   

                <div>
                    <button type ="submit" class="btn btn-primary">Update</button>
                    <button href ="#deleteModal" role ="button" data-toggle="modal" class="btn btn-danger">Delete patient</button>
                </div>

                <div class="modal small hide fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 id="myModalLabel">Delete Confirmation</h3>
                    </div>
                    <div class="modal-body">
                        <p class="error-text">Delete patient <?php echo $row->firstname . ' ' . $row->lastname ?>?</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
                        <button type="button" onclick="location.href = '<?php echo site_url('staff/delete_patient/' . $row->id) ?>'" class="btn btn-danger" data-dismiss="modal">Delete</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="tab-pane <?php if ($active == 'ekg') echo 'active' ?> " id="ekg">
            <form id="tab2" action="<?php echo site_url('staff/show_ekg') ?>" method="POST">
                <input type="hidden" name="id" value="<?php echo $row->id ?>" />
                <label>Date</label>
                <div class="input-append date" id="dp2" data-date="<?php echo date('Y-m-d') ?>" data-date-format="yyyy-mm-dd">
                    <input class="span2" name="date" size="16" type="text" value="<?php echo date('Y-m-d') ?>">
                    <span class="add-on"><i class="icon-calendar"></i></span>
                </div>
                <label>Time interval</label>
                <div class="input-append bootstrap-timepicker ">
                    <input id="timepicker1" name="timestart"type="text" class="input-small" >
                    <span class="add-on">
                        <i class="icon-time"></i>
                    </span>
                </div>
                <div class="input-append bootstrap-timepicker">
                    <input id="timepicker2" name="timeend"type="text" class="input-small">
                    <span class="add-on">
                        <i class="icon-time"></i>
                    </span>
                </div>
                <script type="text/javascript">
                            $('#timepicker1').timepicker({
                                minuteStep: 1,
                                template: 'modal',
                                appendWidgetTo: 'body',
                                showSeconds: true,
                                showMeridian: false,
                                defaultTime: 'current'
                            });
                            $('#timepicker2').timepicker({
                                minuteStep: 1,
                                template: 'modal',
                                appendWidgetTo: 'body',
                                showSeconds: true,
                                showMeridian: false,
                                defaultTime: 'current'
                            });
                </script>
                <div>
                    <button type="submit" class="btn btn-primary">Show EKG Data</button>
                </div>
            </form>

            <table class="table table-striped table-bordered table-hover table-condensed " id="ekgtable">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Time (UTC)</th>
                        <th>L0</th>
                        <th>L1</th>
                        <th>L2</th>
                        <th>L3</th>
                        <th>L4</th>
                        <th>L5</th>
                        <th>L6</th>
                        <th>L7</th>
                        <th>L8</th>
                        <th>L9</th>
                        <th>L10</th>
                        <th>L11</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($ekgdata)) foreach ($ekgdata as $ekgrecord): ?>
                            <tr>
                                <td><?php echo $ekgrecord['timestamp'] ?></td>
                                <td><?php $ekgdate = new DateTime('UTC');
                    $ekgdate->setTimestamp($ekgrecord['timestamp'] / 100);
                    echo $ekgdate->format('Y-m-d G:i:s') . ' ' . fmod($ekgrecord['timestamp'], 100) ?></td>
                                <td><?php echo $ekgrecord['l0'] ?></td>
                                <td><?php echo $ekgrecord['l1'] ?></td>
                                <td><?php echo $ekgrecord['l2'] ?></td>
                                <td><?php echo $ekgrecord['l3'] ?></td>
                                <td><?php echo $ekgrecord['l4'] ?></td>
                                <td><?php echo $ekgrecord['l5'] ?></td>
                                <td><?php echo $ekgrecord['l6'] ?></td>
                                <td><?php echo $ekgrecord['l7'] ?></td>
                                <td><?php echo $ekgrecord['l8'] ?></td>
                                <td><?php echo $ekgrecord['l9'] ?></td>
                                <td><?php echo $ekgrecord['l10'] ?></td>
                                <td><?php echo $ekgrecord['l11'] ?></td>

                            </tr>   
    <?php endforeach; ?>
                </tbody>
            </table>        
        </div>
    </div>

    <script>
        if (top.location != location) {
            top.location.href = document.location.href;
        }
        $(function() {
            window.prettyPrint && prettyPrint();

            $('#dp1').datepicker();
            $('#dp2').datepicker();
            $('#dp3').datepicker();
        });
    </script>
<?php $this->load->view('includes/footer'); ?>
