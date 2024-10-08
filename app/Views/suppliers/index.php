<div id="page-content" class="page-wrapper clearfix">
    <div class="clearfix">
        <ul id="client-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
            
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("suppliers/suppliers_list/"); ?>" data-bs-target="#suppliers_list"><?php echo app_lang('suppliers'); ?></a></li>
            
            <div class="tab-title clearfix no-border">
                <div class="title-button-group">
                    <?php if ($can_edit_suppliers) { ?>
                        
                        <?php echo modal_anchor(get_uri("suppliers/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_supplier'), array("class" => "btn btn-default", "title" => app_lang('add_supplier'))); ?>
                    <?php } ?>
                </div>
            </div>
        </ul>
        <div class="tab-content">
            

            <div role="tabpanel" class="tab-pane fade" id="suppliers_list"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        setTimeout(function () {
            var tab = "<?php echo $tab; ?>";
            if (tab === "suppliers_list" || tab === "suppliers_list-has_open_projects") {
                $("[data-bs-target='#suppliers_list']").trigger("click");

                window.selectedClientQuickFilter = window.location.hash.substring(1);
            } else if (tab === "contacts") {
                $("[data-bs-target='#contacts']").trigger("click");

                window.selectedContactQuickFilter = window.location.hash.substring(1);
            }
        }, 210);
    });
</script>