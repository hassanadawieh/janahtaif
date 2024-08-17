<div class="card">
    <div class="table-responsive">
        <table id="supplier-table" class="display" cellspacing="0" width="100%">            
        </table>
    </div>
</div>

<script type="text/javascript">
    loadClientsTable = function (selector) {
    var showInvoiceInfo = true;
    if (!"<?php echo $show_invoice_info; ?>") {
    showInvoiceInfo = false;
    }

    var showOptions = true;
    if (!"<?php echo $can_edit_suppliers; ?>") {
    showOptions = false;
    }

    var quick_filters_dropdown = <?php echo view("suppliers/quick_filters_dropdown"); ?>;
    if (window.selectedClientQuickFilter){
    var filterIndex = quick_filters_dropdown.findIndex(x => x.id === window.selectedClientQuickFilter);
    if ([filterIndex] > - 1){
    //match found
    quick_filters_dropdown[filterIndex].isSelected = true;
    }
    }

    $(selector).appTable({
    source: '<?php echo_uri("suppliers/list_data") ?>',
            serverSide: true,
            filterDropdown: [
            //{name: "group_id", class: "w200", options: <?php echo $groups_dropdown; ?>},
<?php if ($login_user->is_admin || get_array_value($login_user->permissions, "client") === "all") { ?>
                 {name: "created_by", class: "w200", options: <?php echo $team_members_dropdown; ?>},
<?php } ?>
            //{name: "quick_filter", class: "w200", options: quick_filters_dropdown},

            ],
            columns: [
            {title: "<?php echo app_lang("id") ?>", "class": "text-center w50 all", order_by: "id"},
            {title: "<?php echo app_lang("company_name") ?>", "class": "all", order_by: "name"},
            {title: "<?php echo app_lang("address") ?>", order_by: "address"},
            {title: "<?php echo app_lang("phone") ?>", order_by: "phone"},
            {title: "<?php echo app_lang("email") ?>"},
            {title: "<?php echo "Files" ?>", order_by: "client_groups"},
           
           
            {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100", visible: showOptions}
            ],
            printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4], '<?php echo $custom_field_headers; ?>')
    });
    };
    $(document).ready(function () {
    loadClientsTable("#supplier-table");
    });
</script>