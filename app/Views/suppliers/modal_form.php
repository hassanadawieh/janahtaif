<?php echo form_open(get_uri("suppliers/save"), array("id" => "client-form", "class" => "general-form", "role" => "form")); ?>
<div id="suppliers-dropzone" class="post-dropzone">

    <div class="modal-body clearfix">
        <div class="container-fluid">
            <?php echo view("suppliers/supplier_form_fields"); ?>
        </div>
        <div class="form-group">
            <div class="col-md-12">
                <?php echo view("includes/file_list", array("files" => $model_info->files)); ?>
            </div>
        </div>

        <?php echo view("includes/dropzone_preview"); ?>
    </div>

    <div class="modal-footer">
        <button class="btn btn-default upload-file-button float-start me-auto btn-sm round" type="button" style="color:#7988a2">
            <i data-feather="camera" class="icon-16"></i> <?php echo app_lang("upload_file"); ?>
        </button>

        <div id="link-of-add-contact-modal" class="hide">
            <?php echo modal_anchor(get_uri("suppliers/add_new_contact_modal_form"), "", array()); ?>
        </div>

        <button type="button" class="btn btn-default" data-bs-dismiss="modal">
            <span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?>
        </button>

        <?php if (!$model_info->id) { ?>
            <!--<button type="button" id="save-and-continue-button" class="btn btn-info text-white"><span data-feather="check-circle" class="icon-16"></span> <?php //echo app_lang('save_and_continue'); ?></button>-->
        <?php } ?>
        <button type="submit" class="btn btn-primary">
            <span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?>
        </button>
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        var uploadUrl = "<?php echo get_uri("suppliers/upload_file"); ?>";
        var validationUri = "<?php echo get_uri("suppliers/validate_file"); ?>";
        var dropzone = attachDropzoneWithForm("#suppliers-dropzone", uploadUrl, validationUri);

        // var dropzone = new Dropzone("#suppliers-dropzone", {
        //     url: uploadUrl,
        //     params: {
        //     },
        //     acceptedFiles: "image/*,application/pdf,.psd",
        //     autoProcessQueue: false, 
        //     addRemoveLinks: true,
        //     init: function () {
        //         var myDropzone = this;

        //         $(".upload-file-button").on("click", function () {
        //             myDropzone.hiddenFileInput.click();
        //         });

        //         this.on("success", function (file, response) {
        //             console.log("File uploaded successfully.");
        //         });

        //         this.on("error", function (file, response) {
        //             console.log("Error uploading file.");
        //         });
        //     }
        // });

        // Initialize form
        window.clientForm = $("#client-form").appForm({
            closeModalOnSuccess: false,
            onSuccess: function (result) {
                var $addMultipleContactsLink = $("#link-of-add-contact-modal").find("a");

                if (result.view === "details") {
                    if (window.showAddNewModal) {
                        $addMultipleContactsLink.attr("data-post-supplier_id", result.id);
                        $addMultipleContactsLink.attr("data-title", "<?php echo app_lang('add_multiple_contacts') ?>");
                        $addMultipleContactsLink.attr("data-post-add_type", "multiple");

                        $addMultipleContactsLink.trigger("click");
                    } else {
                        appAlert.success(result.message, { duration: 10000 });
                        setTimeout(function () {
                            location.reload();
                        }, 500);
                    }
                } else if (window.showAddNewModal) {
                    $addMultipleContactsLink.attr("data-post-supplier_id", result.id);
                    $addMultipleContactsLink.attr("data-title", "<?php echo app_lang('add_multiple_contacts') ?>");
                    $addMultipleContactsLink.attr("data-post-add_type", "multiple");

                    $addMultipleContactsLink.trigger("click");

                    $("#supplier-table").appTable({ newData: result.data, dataId: result.id });
                } else {
                    $("#supplier-table").appTable({ newData: result.data, dataId: result.id });
                    window.clientForm.closeModal();
                }
            }
        });

        setTimeout(function () {
            $("#company_name").focus();
        }, 200);

        // Save and open add new contact member modal
        window.showAddNewModal = false;

        $("#save-and-continue-button").click(function () {
            window.showAddNewModal = true;
            $(this).trigger("submit");
        });
    });
</script>