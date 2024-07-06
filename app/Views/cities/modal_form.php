<?php echo form_open(get_uri("cities/save"), array("id" => "cities-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
        <div class="form-group">
            <div class="row">
                <label for="city_name" class=" col-md-3"><?php echo app_lang('city_name'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "city_name",
                        "name" => "city_name",
                        "value" => $model_info->city_name,
                        "class" => "form-control",
                        "placeholder" => app_lang('city_name'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required")
                    ));
                    ?>
                </div>
            </div>
        </div>

        
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#cities-form").appForm({
            onSuccess: function (result) {
                $("#cities-table").appTable({reload: true});
                appAlert.success(result.message, {duration: 10000});
            }
        });

        setTimeout(function () {
            $("#city_name").focus();
        }, 200);
    });
</script>    