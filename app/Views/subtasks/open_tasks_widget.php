 <?php 
        if ($login_user->user_type == "staff") {
            //if ($login_user->is_admin || (get_array_value($login_user->permissions, "can_manage_all_projects") == "1")) {

            if (get_array_value($login_user->permissions, "reserv_mang") == "1") {
                //check is user a project member
               $my_url = "subtasks/index";
            }else if (get_array_value($login_user->permissions, "supply_mang") == "1") {
                //check is user a project member
               $my_url = "subtasks/supply_mang";
           }else{
            $my_url = "subtasks/index";
           }
        } 


        ?>

        <?php echo form_open(get_uri($my_url), array("id" => "task-form_in", "class" => "general-form", )); ?>
<input type="hidden" name="filter" value="24houer">

<div class="card dashboard-icon-widget" > <div class="card-body" style="padding-left: 10px;padding-right: 10px;"><button style="border: none;background: transparent;width: 100%;" type="submit" >
       
            <div class="widget-icon bg-coral bg-coral">
                <i data-feather="clock" class="icon"></i>
            </div>
            <div class="widget-details">
                <h1><?php echo count($subtasks->task_statuses); ?></h1>
                <span class="bg-transparent-white"><?php echo app_lang('subtasks_to_go'); ?></span>
            </div>
        
     </button></div></div>
       
   
<?php echo form_close(); ?>



<script type="text/javascript">
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>

