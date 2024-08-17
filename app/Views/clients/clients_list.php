<div class="card">
    <div class="table-responsive">
        <table id="client-table" class="display" cellspacing="0" width="100%">            
        </table>
    </div>
</div>

<script type="text/javascript">
    loadClientsTable = function (selector) {
    
    };
    $(document).ready(function () {
        
   
    var showOptions = true;
    if (!"<?php echo $can_edit_clients; ?>") {
    showOptions = false;
    }


    $("#client-table").appTable({
    source: '<?php echo_uri("clients/list_data") ?>',
            serverSide: true,
            filterDropdown: [
            {name: "group_id", class: "w200", options: <?php echo $groups_dropdown; ?>},
<?php if ($login_user->is_admin || get_array_value($login_user->permissions, "client") === "all") { ?>
                 {name: "created_by", class: "w200", options: <?php echo $team_members_dropdown; ?>},
<?php } ?>
<?php echo $custom_field_filters; ?>
            ],
            columns: [
            {title: "<?php echo app_lang("id") ?>", "class": "text-center w50 all", order_by: "id"},
            {title: "<?php echo app_lang("name") ?>", "class": "all", order_by: "company_name"},
            {title: "<?php echo app_lang("primary_contact") ?>", order_by: "primary_contact"},
            {title: "<?php echo app_lang("client_groups") ?>", order_by: "client_groups"},
            {title: "<?php echo "Files" ?>", order_by: "client_groups"}

<?php echo $custom_field_headers; ?>,
            {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100", visible: showOptions}
            ],
            printColumns: combineCustomFieldsColumns([0, 1, 2, 3], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3], '<?php echo $custom_field_headers; ?>'),
            onInitComplete: function () {
               
                setPageScrollable();

            }
    });
    });
</script>