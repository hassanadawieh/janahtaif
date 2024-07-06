<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo app_lang('projects'); ?></h1>
            <div class="title-button-group">
                <?php
                if ($can_create_projects) {
                

                    echo modal_anchor(get_uri("projects/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_project'), array("class" => "btn btn-default", "title" => app_lang('add_project')));
                }
                ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="project-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var optionVisibility = false;
        if ("<?php echo ($can_edit_projects ); ?>") {
            optionVisibility = true;
        }

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

        $("#project-table").appTable({
            source: '<?php echo_uri("projects/list_data") ?>',
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
            printColumns: combineCustomFieldsColumns([0, 1, 2, 3,4,5], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3,4,5], '<?php echo $custom_field_headers; ?>'),
            onInitComplete: function () {
                
                setPageScrollable();
            }
        });
    });

    
</script>