<div class="tab-content">
    <?php echo form_open(get_uri("suppliers/save/"), array("id" => "company-form", "class" => "general-form dashed-row white", "role" => "form")); ?>
    <div class="card">
        <div class=" card-header">
            
                <h4> <?php echo app_lang('supplier_info'); ?></h4>
           
        </div>
        <div class="card-body">
            <?php echo view("suppliers/supplier_form_fields"); ?>
        </div>
        <?php if($can_edit_suppliers){ ?>
            <div class="card-footer rounded-bottom">
                <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
            </div>
        <?php } ?>
      
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#company-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
            }
        });
        setPageScrollable();
    });
</script>