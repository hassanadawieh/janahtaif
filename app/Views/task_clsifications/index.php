

<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo app_lang('cls_list'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("maintask_clsifications/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('cls_add'), array("class" => "btn btn-default", "title" => app_lang('cls_add'), "id" => "task-status-button")); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="task-status-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        $("#task-status-table").appTable({
            source: '<?php echo_uri("maintask_clsifications/list_data") ?>',
            order: [[0, "asc"]],
            hideTools: true,
            displayLength: 100,
            columns: [
                {visible: false},
                {title: '<?php echo app_lang("cls_title"); ?>'},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            onInitComplete: function () {
                //apply sortable
                $("#task-status-table").find("tbody").attr("id", "custom-field-table-sortable");
                var $selector = $("#custom-field-table-sortable");

                Sortable.create($selector[0], {
                    animation: 150,
                    chosenClass: "sortable-chosen",
                    ghostClass: "sortable-ghost",
                    onUpdate: function (e) {
                        appLoader.show();
                        //prepare sort indexes 
                        var data = "";
                        $.each($selector.find(".field-row"), function (index, ele) {
                            if (data) {
                                data += ",";
                            }

                            data += $(ele).attr("data-id") + "-" + index;
                        });

                        //update sort indexes
                        $.ajax({
                            url: '<?php echo_uri("maintask_clsifications/update_field_sort_values") ?>',
                            type: "POST",
                            data: {sort_values: data},
                            success: function () {
                                appLoader.hide();
                            }
                        });
                    }
                });

            }

        });

        //change the add button attributes on changing tab panel
        var addButton = $("#task-status-button");
        $(".nav-tabs li").click(function () {
            var activeField = $(this).find("a").attr("data-bs-target");

            if (activeField === "#task-status-tab" || activeField === "#task-settings-tab") { //task status
                addButton.attr("title", "<?php echo app_lang("cls_add"); ?>");
                addButton.attr("data-title", "<?php echo app_lang("cls_add"); ?>");
                addButton.attr("data-action-url", "<?php echo get_uri("maintask_clsifications/modal_form"); ?>");

                addButton.html("<?php echo "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('cls_add'); ?>");
                feather.replace();
            } 
        });
    });
</script>