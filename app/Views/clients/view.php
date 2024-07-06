<div class="page-title clearfix no-border no-border-top-radius no-bg">
    <h1>
        <?php echo app_lang('client_details') . " - " . $client_info->company_name ?>
        


    </h1>

    
</div>

<div id="page-content" class="page-wrapper clearfix">

    <div class="client-widget-section">
        <?php //echo view("clients/info_widgets/index"); ?>
    </div>

    <ul id="client-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
        <li><a  role="presentation" data-bs-toggle="tab" href="<?php echo_uri("clients/company_info_tab/" . $client_info->id); ?>" data-bs-target="#client-info"> <?php echo app_lang('client_info'); ?></a></li>
        
        <li><a  role="presentation" data-bs-toggle="tab" href="<?php echo_uri("clients/contacts/" . $client_info->id); ?>" data-bs-target="#client-contacts"> <?php echo app_lang('contacts'); ?></a></li>

        <li><a  role="presentation" data-bs-toggle="tab" href="<?php echo_uri("clients/projects/" . $client_info->id); ?>" data-bs-target="#client-projects"> <?php echo app_lang('projects'); ?></a></li>

        <li><a  role="presentation" data-bs-toggle="tab" href="<?php echo_uri("clients/clients_tasks/" . $client_info->id); ?>" data-bs-target="#client-tasks"> <?php echo app_lang('tasks'); ?></a></li>
        


        

        <?php
        $hook_tabs = array();
        $hook_tabs = app_hooks()->apply_filters('app_filter_client_details_ajax_tab', $hook_tabs, $client_info->id);
        $hook_tabs = is_array($hook_tabs) ? $hook_tabs : array();
        foreach ($hook_tabs as $hook_tab) {
            ?>
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo get_array_value($hook_tab, 'url') ?>" data-bs-target="#<?php echo get_array_value($hook_tab, 'target') ?>"><?php echo get_array_value($hook_tab, 'title') ?></a></li>
            <?php
        }
        ?>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade" id="client-info"></div>
        <div role="tabpanel" class="tab-pane fade" id="client-contacts"></div>
        <div role="tabpanel" class="tab-pane fade" id="client-projects"></div>
        <div role="tabpanel" class="tab-pane fade" id="client-tasks"></div>
       
        <?php foreach ($hook_tabs as $hook_tab) { ?>
            <div role="tabpanel" class="tab-pane fade" id="<?php echo get_array_value($hook_tab, 'target') ?>"></div>
        <?php } ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        setTimeout(function () {
            var tab = "<?php echo $tab; ?>";
            if (tab === "info") {
                $("[data-bs-target='#client-info']").trigger("click");
            } else if (tab === "projects") {
                $("[data-bs-target='#client-projects']").trigger("click");
            } else if (tab === "tasks") {
                $("[data-bs-target='#client-tasks']").trigger("click");
            } else if (tab === "contacts") {
                $("[data-bs-target='#client-contacts']").trigger("click");
            }
        }, 210);

        $('[data-bs-toggle="tooltip"]').tooltip();

    });
</script>
