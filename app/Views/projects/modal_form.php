<?php echo form_open(get_uri("projects/save"), array("id" => "project-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
        <?php if($client_id){ ?>
        <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
    <?php } ?>

    <?php if(!$client_id){ 
         ?>
        <div class="form-group">
            <div class="row">
                <label for="client_id" class=" col-md-3"><?php echo app_lang('client'); ?></label>
                    <div class=" col-md-9">
                        <?php
                        echo form_dropdown("client_id", $clients_dropdown, array($model_info->client_id), "class='select2 validate-hidden' id='client_id' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                        ?>
                    </div>
            </div>
        </div>
    <?php }   ?>

        
        <div class="form-group">
            <div class="row">
                <label for="title" class=" col-md-3"><?php echo app_lang('title'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "title",
                        "name" => "title",
                        "value" => $model_info->title,
                        "class" => "form-control",
                        "placeholder" => app_lang('title'),
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div> 
            </div>
        </div>


        <div class="form-group">
            <div class="row">
                <label for="title_en" class=" col-md-3"><?php echo app_lang('title'); ?> English</label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "title_en",
                        "name" => "title_en",
                        "value" => $model_info->title_en,
                        "class" => "form-control",
                        "placeholder" => app_lang('title').' English',
                        "autofocus" => true,
                        
                    ));
                    ?>
                </div> 
            </div>
        </div>

        
      

        <div class="form-group">
            <div class="row">
                <label for="description" class=" col-md-3"><?php echo app_lang('description'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_textarea(array(
                        "id" => "description",
                        "name" => "description",
                        "value" => $model_info->description,
                        "class" => "form-control",
                        "placeholder" => app_lang('description'),
                        "style" => "height:150px;",
                        "data-rich-text-editor" => true
                    ));
                    ?>
                </div>
            </div>
        </div>
       
        

        

        <?php if ($model_info->id) { ?>
            <div class="form-group">
                <div class="row">
                    <label for="status" class=" col-md-3"><?php echo app_lang('status'); ?></label>
                    <div class=" col-md-9">
                        <?php
                        echo form_dropdown("status", array("open" => app_lang("open"), "closed" => app_lang("closed")), array($model_info->status), "class='select2'");
                        ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        

    </div>
</div>

<div class="modal-footer">
    <div id="link-of-add-project-member-modal" class="hide">
        <?php echo modal_anchor(get_uri("projects/project_member_modal_form"), "", array()); ?>
    </div>

    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">

     /*function function_name(c_id,contact_id) {
        $.ajax({
                    url: "<?php //echo get_uri('projects/get_client_contact_dropdown') ?>" + "/" + c_id+"/"+contact_id,
                    dataType: "json",
                    success: function (result) {
                        
                        //$("#contact_not_exsist").show();
                        //$("#contact_exsist").hide();
                        //alert("dsadas");
                        $('#contact_id').select2({data: result.contacts_dropdown});
                        
                        
                    }
                });
    }*/
    $(document).ready(function () {

         <?php //if($contacts_dropdown){ ?>
        //function_name(<?php //echo $model_info->client_id; ?>,<?php //echo $model_info->client_contact_id; ?>);
    <?php //} ?>

        window.projectForm = $("#project-form").appForm({
            closeModalOnSuccess: false,
            onSuccess: function (result) {
                if (typeof RELOAD_PROJECT_VIEW_AFTER_UPDATE !== "undefined" && RELOAD_PROJECT_VIEW_AFTER_UPDATE) {
                    location.reload();

                    window.projectForm.closeModal();
                } else if (typeof RELOAD_VIEW_AFTER_UPDATE !== "undefined" && RELOAD_VIEW_AFTER_UPDATE) {
                    RELOAD_VIEW_AFTER_UPDATE = false;
                    window.location = "<?php echo site_url('projects/view'); ?>/" + result.id;

                    window.projectForm.closeModal();
                } else if (window.showAddNewModal) {
                    var $addProjectMemberLink = $("#link-of-add-project-member-modal").find("a");

                    $addProjectMemberLink.attr("data-action-url", "<?php echo get_uri("projects/project_member_modal_form"); ?>");
                    $addProjectMemberLink.attr("data-title", "<?php echo app_lang("add_new_project_member"); ?>");
                    $addProjectMemberLink.attr("data-post-project_id", result.id);
                    $addProjectMemberLink.attr("data-post-view_type", "from_project_modal");

                    $addProjectMemberLink.trigger("click");

                    $("#project-table").appTable({newData: result.data, dataId: result.id});
                    $("#client-project-table").appTable({newData: result.data, dataId: result.id});
                } else {
                    $("#project-table").appTable({newData: result.data, dataId: result.id});
                    $("#client-project-table").appTable({newData: result.data, dataId: result.id});

                    window.projectForm.closeModal();
                }
            }
        });

        setTimeout(function () {
            $("#title").focus();
        }, 200);
        $("#project-form .select2").select2();

        //setDatePicker("#start_date, #deadline");


        //save and open add new project member modal
        window.showAddNewModal = false;

        $("#save-and-continue-button").click(function () {
            window.showAddNewModal = true;
            $(this).trigger("submit");
        });


       



      /*$("#client_id").select2().on("change", function () {
            var client_id = $(this).val();
            if ($(this).val()) {
                $('#contact_id').select2("destroy");
                $("#contact_id").hide();
               
                appLoader.show({container: "#dropdown-apploader-section", zIndex: 1});
                $.ajax({
                    url: "<?php echo get_uri('clients/get_client_contact_dropdown') ?>" + "/" + client_id,
                    dataType: "json",
                    success: function (result) {
                        $("#contact_id").show().val("");
                        //$("#contact_not_exsist").show();
                        //$("#contact_exsist").hide();
                        $('#contact_id').select2({data: result.contacts_dropdown});
                        
                        appLoader.hide();
                    }
                });
            }
        });*/

    });

</script>    

