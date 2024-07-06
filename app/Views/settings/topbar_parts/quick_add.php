<?php
//get the array of hidden menu
$hidden_menu = explode(",", get_setting("hidden_client_menus"));
$permissions = $login_user->permissions;

$links = "";

if (($login_user->user_type == "staff" && ($login_user->is_admin || get_array_value($permissions, "can_create_tasks") == "1")) ) {
    //add tasks 
    $links .= modal_anchor(get_uri("tasks/task_modal_form"), app_lang('add_task'), array("class" => "dropdown-item clearfix", "title" => app_lang('add_task'), "id" => "js-quick-add-task"));

    //add multiple tasks
    $links .= modal_anchor(get_uri("tasks/task_modal_form"), app_lang('add_multiple_tasks'), array("class" => "dropdown-item clearfix", "title" => app_lang('add_multiple_tasks'), "data-post-add_type" => "multiple", "id" => "js-quick-add-multiple-task"));
}

//add project time



if ($links) {
    ?>
    <li class="nav-item dropdown">
        <?php echo js_anchor("<i data-feather='plus-circle' class='icon'></i>", array("id" => "quick-add-icon", "class" => "nav-link dropdown-toggle", "data-bs-toggle" => "dropdown")); ?>

        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <?php echo $links; ?></li>
        </ul>
    </li>
    <?php
} 
