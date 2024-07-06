<li class="js-cookie-tab2 <?php echo ($active_tab == 'tasks_list') ? 'active' : ''; ?>" data-tab="tasks_list"><a href="<?php echo_uri('suppliers/view/' . $supplier_info->id.'/company_info_tab'); ?>"><?php echo app_lang("list"); ?></a></li>

<li class="js-cookie-tab2 <?php echo ($active_tab == 'supplier_tasks_kanban') ? 'active' : ''; ?>" data-tab="supplier_tasks_kanban"><a href="<?php echo_uri('suppliers/view/' . $supplier_info->id.'/tasks'); ?>" ><?php echo app_lang('kanban'); ?></a></li>
    
    
    <li class="<?php echo ($active_tab == 'gantt') ? 'active' : ''; ?>" ><a href="<?php echo_uri('suppliers/view/' . $supplier_info->id.'/supplier_tasks_kanban'); ?>" ><?php echo app_lang('gantt'); ?></a></li>



<script>
    var tab = "<?php echo $selected_tab; ?>";
    if (!tab) {
        var activeTab = "<?php echo $active_tab; ?>";
        var selectedTab = getCookie("s_selected_tab_" + "<?php echo $login_user->id; ?>");

        if (activeTab != "gantt" && selectedTab && selectedTab !== "<?php echo $active_tab ?>" && selectedTab === "supplier_tasks_kanban") {
            window.location.href = "<?php echo_uri('suppliers/view/' . $supplier_info->id.'/supplier_tasks_kanban'); ?>";
        }
    }

    //save the selected tab in browser cookie
    $(document).ready(function () {
        $(".js-cookie-tab2").click(function () {
            var tab = $(this).attr("data-tab");
            if (tab) {
                setCookie("s_selected_tab_" + "<?php echo $login_user->id; ?>", tab);
            }
        });
    });
</script>