<?php
$p_no_supplier = 0;
$p_no_act_return_date = 0;
$p_no_act_out_time = 0;
$p_wait_inv = 0;
$progress_draft = 0;
     if($all_subtasks>0){
    $p_no_supplier = round($no_supplier / $all_subtasks * 100);
    $p_no_act_return_date = round($no_act_return_date / $all_subtasks * 100);
    $p_no_act_out_time = round($no_act_out_time / $all_subtasks * 100);
    $p_wait_inv = round($wait_inv / $all_subtasks * 100);
}else{
    $p_no_supplier = 0;
    $p_no_act_return_date = 0;
    $p_no_act_out_time = 0;
    $p_wait_inv = 0;
}

?>

<div id="invoice-overview-widget-container">
    <div class="card bg-white">
        <div class="card-header">
            <i data-feather="file-text" class="icon-16"></i> &nbsp;<?php echo app_lang("sub_task_notify"); ?>

                <div class="float-end">
                   
                </div>
        </div>

        <?php 
        //if ($login_user->user_type == "staff") {
        $is_show=false;
            if ($login_user->is_admin || (get_array_value($login_user->permissions, "can_manage_all_projects") == "1")) {
                $my_url = "subtasks/index";
                $is_show=true;
            }else{

            if (get_array_value($login_user->permissions, "reserv_mang") == "1") {
                //check is user a project member
               $my_url = "subtasks/index";
               $is_show=false;
            }else if (get_array_value($login_user->permissions, "supply_mang") == "1") {
                //check is user a project member
               $my_url = "subtasks/supply_mang";
               $is_show=true;
           }else{
            $my_url = "subtasks/index";
           }
        } 

        ?>

        <div class="card-body rounded-bottom" id="invoice-overview-container">

            <?php 

            if ($is_show) {
                // code...
            

            echo form_open(get_uri($my_url), array("id" => "task-form_no_supplier", "class" => "general-form", )); ?>
            <input type="hidden" name="filter" value="no_supplier">
            <button style="margin: 11px 0px; text-align: inherit; padding: 0; border: none;background: transparent;width: 100%;" type="submit" >
                <a href="#" title="<?php echo app_lang("tasks_without_supplier"); ?>">
                <div class="d-flex">
                    <div class="w70p text-truncate">
                        <div style="background-color: #F5325C;" class="color-tag border-circle wh10"></div>
                       <?php echo app_lang("tasks_without_supplier"); ?>
                    </div>
                    <div class="w25p">
                        <div class='progress widget-progress-bar' title='<?php echo $p_no_supplier; ?>%'>
                            <div  class='progress-bar bg-danger' role='progressbar' style="width: <?php echo $p_no_supplier; ?>%;" aria-valuenow='<?php echo $p_no_supplier; ?>%' aria-valuemin='0' aria-valuemax='100'></div>
                        </div>
                    </div>
                    <div class="w10p text-center"><?php echo $no_supplier; ?></div>
                    <div class="w10p text-end"><?php echo $p_no_supplier; ?>%</div>
                </div>
            </a>
           </button>
            <?php echo form_close(); ?>
            <?php echo form_open(get_uri($my_url), array("id" => "task-form_no_act_return_date", "class" => "general-form", )); ?>
            <input type="hidden" name="filter" value="no_act_return_date">
            <button style="margin: 11px 0px; text-align: inherit; padding: 0; border: none;background: transparent;width: 100%;" type="submit" >
                <a href="#" title="<?php echo app_lang("tasks_without_return_date"); ?>">
                <div class="d-flex">
                    <div class="w70p text-truncate">
                        <div style="background-color: #FAC108;" class="color-tag border-circle wh10"></div>
                          <?php echo app_lang("tasks_without_return_date"); ?>
                    </div>
                    <div class="w25p">
                        <div class='progress widget-progress-bar' title='<?php echo $p_no_act_return_date; ?>%'>
                            <div  class='progress-bar bg-orange' role='progressbar' style="width: <?php echo $p_no_act_return_date; ?>%;" aria-valuenow='<?php echo $p_no_act_return_date; ?>%' aria-valuemin='0' aria-valuemax='100'></div>
                        </div>
                    </div>
                    <div class="w10p text-center"><?php echo $no_act_return_date; ?></div>
                    <div class="w10p text-end"><?php echo $p_no_act_return_date; ?>%</div>
                </div>
            </a>
            </button>
            <?php echo form_close(); ?>
            <?php echo form_open(get_uri($my_url), array("id" => "task-form_no_act_out_time", "class" => "general-form", )); ?>
            <input type="hidden" name="filter" value="no_act_out_time">
            <button style="margin: 11px 0px; text-align: inherit; padding: 0; border: none;background: transparent;width: 100%;" type="submit" >
                <a href="#" title="<?php echo app_lang("tasks_without_out_time"); ?>">
                <div class="d-flex">
                    <div class="w70p text-truncate">
                        <div style="background-color: #6690F4;" class="color-tag border-circle wh10"></div>
                      <?php echo app_lang("tasks_without_out_time"); ?>
                    </div>
                    <div class="w25p">
                        <div class='progress widget-progress-bar' title='<?php echo $p_no_act_out_time; ?>%'>
                            <div  class='progress-bar' role='progressbar' style="width: <?php echo $p_no_act_out_time; ?>%; background-color: #6690F4;" aria-valuenow='<?php echo $p_no_act_out_time; ?>%' aria-valuemin='0' aria-valuemax='100'></div>
                        </div>
                    </div>
                    <div class="w10p text-center"><?php echo $no_act_out_time; ?></div>
                    <div class="w10p text-end"><?php echo $p_no_act_out_time; ?>%</div>
                
                </div>
            </a>
                </button>
            <?php echo form_close(); ?>
            <?php echo form_open(get_uri($my_url), array("id" => "task-form_wait_inv", "class" => "general-form", )); ?>
            <input type="hidden" name="filter" value="wait_inv">
            <button style="margin: 11px 0px; text-align: inherit; padding: 0; border: none;background: transparent;width: 100%;" type="submit" >
                <a href="#" title="<?php echo app_lang("tasks_without_supplier_invoice"); ?>">
                <div class="d-flex">
                    <div class="w70p text-truncate">
                        <div style="background-color: #485BBD;" class="color-tag border-circle wh10"></div>
                       <?php echo app_lang("tasks_without_supplier_invoice"); ?>
                    </div>
                    <div class="w25p">
                        <div class='progress widget-progress-bar' title='<?php echo $p_wait_inv; ?>%'>
                            <div  class='progress-bar' role='progressbar' style="width: <?php echo $p_wait_inv; ?>%; background-color: #485BBD;" aria-valuenow='<?php echo $p_wait_inv; ?>%' aria-valuemin='0' aria-valuemax='100'></div>
                        </div>
                    </div>
                    <div class="w10p text-center"><?php echo $wait_inv; ?></div>
                    <div class="w10p text-end"><?php echo $p_wait_inv; ?>%</div>
                </div>
            </a>
            </button>
            <?php echo form_close(); 
            }else{
                echo '<div class="text-truncate" style="text-align: center;margin-top: 20%;font-weight: bold;font-size: 16px;">'.app_lang('dont_have_permissions').'</div>';
            }
            ?>
            

            
        </div>
    </div>
</div>


<script type="text/javascript">
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }

    $(document).ready(function () {
        initScrollbar('#invoice-overview-container', {
            setHeight: 280
        });
    });
</script>