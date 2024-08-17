<script>
    $(document).ready(function () {
        //load all related data of the selected project
       /* $("#project_id").select2().on("change", function () {
            var projectId = $(this).val();
            if ($(this).val()) {
                $('#milestone_id').select2("destroy");
                $("#milestone_id").hide();
                $('#assigned_to').select2("destroy");
                $("#assigned_to").hide();
                $('#collaborators').select2("destroy");
                $("#collaborators").hide();
                $('#project_labels').select2("destroy");
                $("#project_labels").hide();
                appLoader.show({container: "#dropdown-apploader-section", zIndex: 1});
                $.ajax({
                    url: "<?php //echo get_uri('tasks/get_all_related_data_of_selected_project') ?>" + "/" + projectId,
                    dataType: "json",
                    success: function (result) {
                        $("#milestone_id").show().val("");
                        $('#milestone_id').select2({data: result.milestones_dropdown});
                        $("#assigned_to").show().val("");
                        $('#assigned_to').select2({data: result.assign_to_dropdown});
                        $("#collaborators").show().val("");
                        $('#collaborators').select2({multiple: true, data: result.collaborators_dropdown});
                        $("#project_labels").show().val("");
                        $('#project_labels').select2({multiple: true, data: result.label_suggestions});
                        appLoader.hide();
                    }
                });
            }
        });*/


        /*$("#project_id").select2().on("change", function () {
            //var projectId = $(this).val();
            if ($(this).val()==-1) {
                $('#milestone_id').select2("destroy");
                $("#milestone_id").hide();
                $('#assigned_to').select2("destroy");
                $("#assigned_to").hide();
                $('#collaborators').select2("destroy");
                $("#collaborators").hide();
                $('#project_labels').select2("destroy");
                $("#project_labels").hide();
                appLoader.show({container: "#dropdown-apploader-section", zIndex: 1});
                $.ajax({
                    url: "<?php //echo get_uri('tasks/get_all_related_data_of_selected_project') ?>" + "/" + projectId,
                    dataType: "json",
                    success: function (result) {
                        $("#milestone_id").show().val("");
                        $('#milestone_id').select2({data: result.milestones_dropdown});
                        $("#assigned_to").show().val("");
                        $('#assigned_to').select2({data: result.assign_to_dropdown});
                        $("#collaborators").show().val("");
                        $('#collaborators').select2({multiple: true, data: result.collaborators_dropdown});
                        $("#project_labels").show().val("");
                        $('#project_labels').select2({multiple: true, data: result.label_suggestions});
                        appLoader.hide();
                    }
                });
            }
        });*/



       

        //intialized select2 dropdown for first time
      



         $("#service_type").select2().on("change", function () {
            var service_type = $(this).val();
            if ($(this).val()=="with_driver") {
                $('#driver_nm').prop('disabled', false);
                //$('#car_status').prop('disabled', true);
                $('#car_status').prop('data-rule-required', true);

                $('#day_count').prop('disabled', false);
                $('#inv_day_count').prop('disabled', false);
                //$('#inv_day_count').prop('disabled', false);
                $('#tmp_return_date').prop('disabled', false);
                $('#tmp_return_date').prop('data-rule-required', true);
                //$('#inv_day_count').prop('data-rule-required', true);
                $('#car_type').prop('data-rule-required', true);

                $('#driver_id').prop('disabled', false);

            }else if ($(this).val()=="no_driver") {
                $('#car_status').prop('data-rule-required', true);
                $('#driver_nm').prop('disabled', true);
                $('#driver_nm').val('');

                $('#day_count').prop('disabled', false);
                $('#inv_day_count').prop('disabled', false);
                //$('#inv_day_count').prop('disabled', false);
                $('#tmp_return_date').prop('disabled', false);
                $('#tmp_return_date').prop('data-rule-required', true);
                //$('#inv_day_count').prop('data-rule-required', true);
                $('#day_count').prop('data-rule-required', true);
                $('#car_type').prop('data-rule-required', true);

                $('#driver_id').prop('disabled', true);

            }else if ($(this).val()=="deliver") {
                $('#car_status').prop('data-rule-required', false);
                $('#driver_nm').prop('disabled', false);

                $('#day_count').prop('disabled', true);
                $('#day_count').prop('data-rule-required', false);
                $('#day_count').val('');
                $('#inv_day_count').prop('disabled', true);
                $('#inv_day_count').val('');

                //$('#inv_day_count').prop('disabled', true);
                //$('#inv_day_count').prop('data-rule-required', false);
                //$('#inv_day_count').val('');
                
                $('#tmp_return_date').prop('disabled', true);
                $('#tmp_return_date').prop('data-rule-required', false);
                $('#tmp_return_date').val('');
                $('#car_type').attr('data-rule-required', false);
                $('#driver_id').prop('disabled', false);

            }else if($(this).val()=="no_car"){
                $('#car_type').prop('disabled', true);
            }
        });
    });
</script>