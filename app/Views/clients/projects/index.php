<?php if (isset($page_type) && $page_type === "full") { ?>
    <div id="page-content" class="page-wrapper clearfix">
    <?php } ?>

    <div class="card rounded-bottom" id="my_cards">
        <?php if (isset($page_type) && $page_type === "full") { ?>
            <div class="page-title clearfix">
                <h1><?php echo app_lang('projects'); ?></h1>
                <div class="title-button-group">
                    <?php
                    if ($client_id && isset($can_create_projects) && $can_create_projects) {
                        echo modal_anchor(get_uri("projects/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_project'), array("class" => "btn btn-default", "data-post-client_id" => $client_id, "title" => app_lang('add_project')));
                    }
                    ?>
                </div>
            </div>
        <?php } else if (isset($page_type) && $page_type === "dashboard") { ?>
            <div class="page-title bg-info text-white clearfix">
                <h1><?php echo app_lang('projects'); ?></h1>
            </div>
        <?php } else { ?>
            <div class="tab-title clearfix">
                <h4><?php echo app_lang('projects'); ?></h4>
                <div class="title-button-group">
                    <?php
                    if ($client_id && isset($can_create_projects) && $can_create_projects) {
                        echo modal_anchor(get_uri("projects/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_project'), array("class" => "btn btn-default", "data-post-client_id" => $client_id, "title" => app_lang('add_project')));
                    }
                    ?>
                </div>
            </div>
        <?php } ?>

        <div class="table-responsive">
            <table id="client-project-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
    <?php if (isset($page_type) && $page_type === "full") { ?>
    </div>
<?php } ?>




<script type="text/javascript">

    
    $(document).ready(function () {
       

        
        var optionVisibility = true;
        

        var selectOpenStatus = true, selectCompletedStatus = false, selectHoldStatus = false;
<?php if (isset($status) && $status == "closed") { ?>
            selectOpenStatus = false;
            selectCompletedStatus = true;
            selectHoldStatus = false;
<?php } else if (isset($status) && $status == "hold") { ?>
            selectOpenStatus = false;
            selectCompletedStatus = false;
            selectHoldStatus = true;
<?php } ?>


        $("#client-project-table").appTable({
            source: '<?php echo_uri("projects/projects_list_data_of_client/" . $client_id) ?>',
            multiSelect: [
                {
                    name: "status",
                    text: "<?php echo app_lang('status'); ?>",
                    options: [
                        {text: '<?php echo app_lang("open") ?>', value: "open", isChecked: selectOpenStatus},
                        {text: '<?php echo app_lang("closed") ?>', value: "closed", isChecked: selectCompletedStatus},
                        
                    ]
                }
            ],
            
            
            columns: [
                {title: '<?php echo app_lang("id") ?>', "class": "all w50"},
                {title: '<?php echo app_lang("title") ?>', "class": "all"},
                {title: '<?php echo app_lang("title") ?> English'},
                {title: '<?php echo app_lang("client") ?>', "class": "all w250"},
                
                {title: '<?php echo app_lang("created_by") ?>', "iDataSort": 6},
               
                {title: '<?php echo app_lang("status") ?>', "class": "w10p"},
                {visible: optionVisibility, title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            order: [[1, "desc"]],
            printColumns: combineCustomFieldsColumns([0, 1, 2, 3,4], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3,4], '<?php echo $custom_field_headers; ?>'),
            onInitComplete: function () {
               
                setPageScrollable();

            }
        });
    });

    
</script>