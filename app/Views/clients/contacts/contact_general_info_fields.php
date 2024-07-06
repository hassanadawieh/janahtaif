<div class="container-fluid">
    <input type="hidden" name="contact_id" value="<?php echo $model_info->id; ?>" />
    <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
    <div class="form-group">
        <div class="row">
            <?php
            $label_column = isset($label_column) ? $label_column : "col-md-3";
            $field_column = isset($field_column) ? $field_column : "col-md-9";
            ?>
            <label for="first_name" class="<?php echo $label_column; ?>"><?php echo app_lang('first_name'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php
                echo form_input(array(
                    "id" => "first_name",
                    "name" => "first_name",
                    "value" => $model_info->first_name,
                    "class" => "form-control",
                    "placeholder" => app_lang('first_name'),
                    "data-rule-required" => true,
                    "data-msg-required" => app_lang("field_required"),
                ));
                ?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label for="last_name" class="<?php echo $label_column; ?>"><?php echo app_lang('last_name'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php
                echo form_input(array(
                    "id" => "last_name",
                    "name" => "last_name",
                    "value" => $model_info->last_name,
                    "class" => "form-control",
                    "placeholder" => app_lang('last_name'),
                    "data-rule-required" => true,
                    "data-msg-required" => app_lang("field_required"),
                ));
                ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <label for="address" class="<?php echo $label_column; ?>"><?php echo app_lang('address'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php
                echo form_input(array(
                    "id" => "address",
                    "name" => "address",
                    "value" => $model_info->address ? $model_info->address : "",
                    "class" => "form-control",
                    "placeholder" => app_lang('address')
                ));
                ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <label for="phone" class="<?php echo $label_column; ?>"><?php echo app_lang('phone'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php
                echo form_input(array(
                    "id" => "phone",
                    "name" => "phone",
                    "value" => $model_info->phone ? $model_info->phone : "",
                    "class" => "form-control",
                    "placeholder" => app_lang('phone')
                ));
                ?>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <div class="row">
            <label for="job_title" class="<?php echo $label_column; ?>"><?php echo app_lang('job_title'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php
                echo form_input(array(
                    "id" => "job_title",
                    "name" => "job_title",
                    "value" => $model_info->job_title,
                    "class" => "form-control",
                    "placeholder" => app_lang('job_title')
                ));
                ?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label for="gender" class="<?php echo $label_column; ?>"><?php echo app_lang('gender'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php
                echo form_radio(array(
                    "id" => "gender_male",
                    "name" => "gender",
                    "class" => "form-check-input",
                        ), "male", ($model_info->gender == "male") ? true : true);
                ?>
                <label for="gender_male" class="mr15 p0"><?php echo app_lang('male'); ?></label> 
                <?php
                echo form_radio(array(
                    "id" => "gender_female",
                    "name" => "gender",
                    "class" => "form-check-input",
                        ), "female", ($model_info->gender == "female") ? true : false);
                ?>
                <label for="gender_female" class="p0 mr15"><?php echo app_lang('female'); ?></label>
               
            </div>
        </div>
    </div>

    <?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => $label_column, "field_column" => $field_column)); ?> 


</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#generate_password").click(function () {
            $("#login_password").val(getRndomString(8));
        });
        $("#show_hide_password").click(function () {
            var $target = $("#login_password"),
                    type = $target.attr("type");
            if (type === "password") {
                $(this).attr("title", "<?php echo app_lang("hide_text"); ?>");
                $(this).html("<span data-feather='eye-off' class='icon-16'></span>");
                feather.replace();
                $target.attr("type", "text");
            } else if (type === "text") {
                $(this).attr("title", "<?php echo app_lang("show_text"); ?>");
                $(this).html("<span data-feather='eye' class='icon-16'></span>");
                feather.replace();
                $target.attr("type", "password");
            }
        });
    });
</script>    