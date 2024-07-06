<?php echo form_open(get_uri("drivers/save"), array("id" => "drivers-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
        <div class="form-group">
            <div class="row">
                <label for="city_name" class=" col-md-3"><?php echo app_lang('driver_name'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "driver_nm",
                        "name" => "driver_nm",
                        "value" => $model_info->driver_nm,
                        "class" => "form-control",
                        "placeholder" => app_lang('driver_name'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required")
                    ));
                    ?>
                </div>
            </div>
        </div>

                <div class="form-group">
            <div class="row">

                <label for="email" class=" col-md-3"><?php echo app_lang('email'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "email",
                        "name" => "email",
                        "value" => $model_info->email,
                        "class" => "form-control",
                        "placeholder" => app_lang('email'),
                        //"data-rule-required" => true,
                        //"data-msg-required" => app_lang("field_required")
                    ));
                    ?>
                </div>
            </div>
        </div>
                <div class="form-group">
            <div class="row">

                <label for="phone" class=" col-md-3"><?php echo app_lang('phone'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "phone",
                        "name" => "phone",
                        "value" => $model_info->phone,
                        "class" => "form-control",
                        "placeholder" => app_lang('phone'),
                        //"data-rule-required" => true,
                        //"data-msg-required" => app_lang("field_required")
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
        $("#drivers-form").appForm({
            onSuccess: function (result) {
                $("#drivers-table").appTable({newData: result.data, dataId: result.id});
                //$("#drivers-table").appTable({reload: true});
                appAlert.success(result.message, {duration: 10000});
            }
        });

        $("#drivers-form .select2").select2();
      

        setTimeout(function () {
            $("#driver_nm").focus();
        }, 200);
    });
</script>    