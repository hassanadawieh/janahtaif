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



       

      

         $("#client_id").select2().on("change", function () {
            var client_id = $(this).val();
            if ($(this).val()) {
                $('#contact_id').select2("destroy");
                $("#contact_id").hide();
                $('#project_id').select2("destroy");
                $("#project_id").hide();
               
                appLoader.show({container: "#dropdown-apploader-section", zIndex: 1});
                $.ajax({
                    url: "<?php echo get_uri('clients/get_client_contact_dropdown') ?>" + "/" + client_id,
                    dataType: "json",
                    success: function (result) {
                        appLoader.hide();
                        $("#contact_id").show().val("");
                        //$("#contact_not_exsist").show();
                        //$("#contact_exsist").hide();
                        $('#contact_id').select2({data: result.contacts_dropdown});

                        //$("#project_id").show().val("");
                        //alert(result.projects_dropdown);
                        $("#project_id").show().val("");
                        var htm="";
                        for (var i = 0; i < result.projects_dropdown.length; i++) {
                            htm+="<option value='"+result.projects_dropdown[i].id+"'>"+result.projects_dropdown[i].text+"</option>";
                        }

                        /*
                        for (var index = 0; index <= result.projects_dropdown.length; index++) {
                            $('#project_id2').append('<option value="' + result.projects_dropdown[index].id + '">' + result.projects_dropdown[index].text + '</option>');
                        }
                        */

                        $("#project_id").html(htm);
                        if(result.projects_dropdown.length>0){
                        $("#project_id").select2().select2('val',result.projects_dropdown[0].id);
                    }
                        //$('#project_id').select2({data: result.projects_dropdown});
                        
                        
                    }
                });
            }
        });


         
    });
</script>