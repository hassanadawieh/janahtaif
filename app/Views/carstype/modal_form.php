<?php echo form_open(get_uri("carstype/save"), array("id" => "carstype-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
        <div class="form-group">
            <div class="row">
                <label for="city_name" class=" col-md-3"><?php echo app_lang('car_type'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "car_type",
                        "name" => "car_type",
                        "value" => $model_info->car_type,
                        "class" => "form-control",
                        "placeholder" => app_lang('car_type'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required")
                    ));
                    ?>
                </div>
            </div>
        </div>

              
        <div class="form-group">
            <div class="row">

                <label for="status" class=" col-md-3"><?php echo app_lang('status'); ?></label>
                <div class=" col-md-9">
                     <?php
                        $status[1] =  app_lang("open");
                        $status[2] =  app_lang("closed");
                        

                        echo form_dropdown("status", $status, $model_info->status ?array($model_info->status):1, "class='select2' id='status' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
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
        $("#carstype-form").appForm({
            onSuccess: function (result) {
                $("#carstype-table").appTable({newData: result.data, dataId: result.id});
                appAlert.success(result.message, {duration: 10000});
            }
        });

        $("#carstype-form .select2").select2();
      

        setTimeout(function () {
            $("#car_type").focus();
        }, 200);
    });
</script>    