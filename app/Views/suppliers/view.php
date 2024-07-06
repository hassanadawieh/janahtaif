<div class="page-title clearfix no-border no-border-top-radius no-bg">
    <h1>
        <?php echo app_lang('supplier_details') . " - " . $supplier_info->name ?>
       


    </h1>

   
</div>

<div id="page-content" class="page-wrapper clearfix">

    

    <ul id="client-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
        
        
        <li><a  role="presentation" data-bs-toggle="tab" href="<?php echo_uri("suppliers/company_info_tab/" . $supplier_info->id); ?>" data-bs-target="#supplier-info"> <?php echo app_lang('supplier_info'); ?></a></li>

            <li><a  role="presentation" data-bs-toggle="tab" href="<?php echo_uri("suppliers/tasks/" . $supplier_info->id); ?>" data-bs-target="#supplier-tasks"><?php echo app_lang('tasks_list'); ?></a></li>

            
            <?php
        $hook_tabs = array();
        $hook_tabs = app_hooks()->apply_filters('app_filter_tasks_details_ajax_tab', $hook_tabs, $supplier_info->id);
        $hook_tabs = is_array($hook_tabs) ? $hook_tabs : array();
        foreach ($hook_tabs as $hook_tab) {
            ?>
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo get_array_value($hook_tab, 'url') ?>" data-bs-target="#<?php echo get_array_value($hook_tab, 'target') ?>"><?php echo get_array_value($hook_tab, 'title') ?></a></li>
            <?php
        }
        ?>
       

       
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade" id="supplier-tasks"></div>
        <div role="tabpanel" class="tab-pane fade" id="supplier-info"></div>
        

        <?php foreach ($hook_tabs as $hook_tab) { ?>
            <div role="tabpanel" class="tab-pane fade" id="<?php echo get_array_value($hook_tab, 'target') ?>"></div>
        <?php } ?>
        
       
    </div>

    <?php //echo view("suppliers/task/tabs", array("active_tab" => "tasks_list", "selected_tab" => $tab)); ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        setTimeout(function () {
            var tab = "<?php echo $tab; ?>";
            if (tab === "info") {
                $("[data-bs-target='#supplier-info']").trigger("click");
            } else if (tab === "tasks") {
                $("[data-bs-target='#supplier-tasks']").trigger("click");
            }
             
        }, 210);

        $('[data-bs-toggle="tooltip"]').tooltip();

    });
</script>
