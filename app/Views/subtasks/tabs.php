<?php if($mang=="supplymang"){ ?>

<li class="js-cookie-tab <?php echo ($active_tab == 'tasks_list') ? 'active' : ''; ?>" data-tab="tasks_list"><a href="<?php echo_uri('subtasks/supply_mang/'.$task_id); ?>"><?php echo app_lang("list"); ?></a></li>

<li class="js-cookie-tab <?php echo ($active_tab == 'tasks_kanban') ? 'active' : ''; ?>" data-tab="tasks_kanban"><a href="<?php echo_uri('subtasks/all_tasks_kanban_supply/'.$task_id); ?>" ><?php echo app_lang('kanban'); ?></a></li>

<?php } else{ ?>

<li class="js-cookie-tab <?php echo ($active_tab == 'tasks_list') ? 'active' : ''; ?>" data-tab="tasks_list"><a href="<?php echo_uri('subtasks/index/'.$task_id); ?>"><?php echo app_lang("list"); ?></a></li>
<li class="js-cookie-tab <?php echo ($active_tab == 'tasks_kanban') ? 'active' : ''; ?>" data-tab="tasks_kanban"><a href="<?php echo_uri('subtasks/all_tasks_kanban/'.$task_id); ?>" ><?php echo app_lang('kanban'); ?></a></li>

<?php } ?>


    
   


<script>
    var tab = "<?php echo $selected_tab; ?>";
    if (!tab) {
        var activeTab = "<?php echo $active_tab; ?>";
        var selectedTab = getCookie("sub_selected_tab_" + "<?php echo $login_user->id; ?>");

        /*if (activeTab != "gantt" && selectedTab && selectedTab !== "<?php echo $active_tab ?>" && selectedTab === "tasks_kanban") {
            window.location.href = "<?php echo_uri('subtasks/all_tasks_kanban/'.$task_id); ?>";
        }*/
    }

    //save the selected tab in browser cookie
    $(document).ready(function () {
        $(".js-cookie-tab").click(function () {
            var tab = $(this).attr("data-tab");
            if (tab) {
                setCookie("sub_selected_tab_" + "<?php echo $login_user->id; ?>", tab);
            }
        });
    });
</script>