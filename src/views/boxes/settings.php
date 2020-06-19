<div id="settingsBox" class="boxSlide">
    <div id="settingsOverview">
        <div class="row">
            <div class="col-md-8">
                  <div class="card bg-dark">
                  <div class="card-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center" role="tab" >
                      <h5>
                        <a class="text-white" data-toggle="collapse" data-parent="#accordion" href="#currentSettingsTable" aria-expanded="true" aria-controls="currentSettingsTable">
                          Current Settings
                        </a>
                        </h5>
                        <div class="btn-toolbar float-right">
                            <div class="btn-group mr-2">
                                <a target="_blank" class="btn btn-primary" href="https://lxdmosaic.readthedocs.io/en/latest/LXDMosaic_Settings/">
                                    <i class="fas fa-book-open"></i>
                                </a>
                                <button class="btn btn-success" id="saveSettings">
                                    <i class="fas fa-save"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="currentSettingsTable" class="collapse in show" role="tabpanel" >
                      <div class="card-body bg-dark">
                        <table class="table table-dark table-bordered" id="settingListTable">
                            <thead>
                                <tr>
                                    <th>Setting</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
              </div>
            <div class="col-md-4">
                  <div class="card bg-dark">
                    <div class="card-header bg-dark" role="tab" >
                      <h5>
                        <a class="text-white" data-toggle="collapse" data-parent="#accordion" href="#currentSettingsTable" aria-expanded="true" aria-controls="currentSettingsTable">
                          LXDMosaic Details
                        </a>
                      </h5>
                    </div>
                    <div id="currentSettingsTable" class="collapse in show" role="tabpanel" >
                      <div class="card-body bg-dark">
                        <table class="table table-dark table-bordered" id="">
                            <tbody>
                                <tr>
                                    <th>Current Version</th>
                                    <td id="currentVersion"></td>
                                </tr>
                                <tr>
                                    <th>New Version</th>
                                    <td id="newVersion"></td>
                                </tr>
                            </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
              </div>
         </div>
         <div class="row">
             <div class="col-md-6">
                  <div class="card bg-dark" id="recordedActionsCard">
                    <div class="card-header bg-dark" role="tab" >
                      <h5>
                        <a class="text-white" data-toggle="collapse" data-parent="#accordion" href="#lastRecordedActions" aria-expanded="true" aria-controls="lastRecordedActions">
                          Last <span id="actionCount"></span> Recorded Actions
                        </a>
                        <button class="btn btn-primary float-right" id="loadMoreRecordedActions">
                            <i class="fas fa-search-plus"></i>
                        </button>
                      </h5>
                    </div>
                    <div id="lastRecordedActions" class="collapse in show" role="tabpanel">
                      <div class="card-body table-responsive bg-dark">
                        <table class="table table-dark table-bordered" id="recordedActionsTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Controller</th>
                                    <th>Params</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
            </div>
             <div class="col-md-6">
                  <div class="card bg-dark" id="usersCard">
                    <div class="card-header" role="tab" >
                      <h5>
                        <a class="text-white" data-toggle="collapse" data-parent="#accordion" href="#users" aria-expanded="true" aria-controls="users">
                          Users
                        </a>
                        <button class="btn btn-primary float-right" id="addUser">
                            <i class="fas fa-plus"></i>
                        </button>
                      </h5>
                    </div>
                    <div id="users" class="collapse in show" role="tabpanel">
                      <div class="card-body">
                        <table class="table table-dark table-bordered" id="usersTable">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Added</th>
                                    <th>Admin</th>
                                    <th>Reset Password</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
            </div>

    </div>
</div>
</div>

<script>

function loadSettingsView()
{
    $(".boxSlide").hide();
    $("#settingsOverview, #settingsBox").show();
    $(".sidebar-lg-show").removeClass("sidebar-lg-show");

    if(!userDetails.isAdmin){
        $("#saveSettings, #addUser, #recordedActionsCard, #usersCard").remove();
    }else{
        loadRecordedActions();
        loadUsers();
    }

    setBreadcrumb("Settings", "viewSettings active");

    ajaxRequest(globalUrls.settings.getAll, {}, (data)=>{
        data = makeToastr(data);
        let trs = "";
        $.each(data, function(_, item){
            trs += `<tr data-setting-id="${item.settingId}">
                    <td>${item.settingName}</td>
                    <td><input class="form-control" name="settingValue" value="${item.currentValue}"/></td>
                </tr>`
        });
        $("#settingListTable > tbody").empty().append(trs);
    });

    ajaxRequest(globalUrls.settings.getOverview, {}, (data)=>{
        data = makeToastr(data);
        if(data.hasOwnProperty("state") && data.state == "error"){
            return false;
        }

        if(data.versionDetails.cantSeeGithub){
            $("#currentVersion").text("Cant see github");
            $("#newVersion").text("Cant see github");
            return false;
        }

        $("#currentVersion").text(data.versionDetails.currentVersion);

        let newVersion = "";

        if(data.versionDetails.newVersionUrl !== false){
            newVersion = `<a target="_blank" href="${data.versionDetails.newVersionUrl}">${data.versionDetails.newVersion}</a>`;
        } else if(data.versionDetails.master == true){
            newVersion = "N/A - You are on the master branch";
        } else if(data.versionDetails.snap == true){
            newVersion = "N/A - Snap will keep you up to date";
        }

        $("#newVersion").html(newVersion);
    });

}

function loadRecordedActions(ammount = 30){
    $("#actionCount").text(ammount);
    ajaxRequest(globalUrls.settings.recordedActions.getLastResults, {ammount: ammount}, (data)=>{
        data = $.parseJSON(data);
        let trs = "";
        if(data.length > 0 ){
            $.each(data, (_, item)=>{
                trs += `<tr>
                    <td>${moment(item.date).fromNow()}</td>
                    <td>${item.controller}</td>
                    <td class="text-break">${item.params}</td>
                </tr>`;
            });
        }else{
            trs += `<tr><td colspan="999" class="text-info text-center">No Recorded Actions</td></tr>`
        }
        $("#recordedActionsTable > tbody").empty().append(trs);
    });
}

function loadUsers(){
    ajaxRequest(globalUrls.settings.users.getAll, {}, (data)=>{
        data = $.parseJSON(data);
        let trs = "";
        if(data.length > 0 ){
            $.each(data, (_, user)=>{
                let isAdmin = user.isAdmin == 1  ? "check-circle" : "times-circle";
                trs += `<tr data-user-id="${user.id}">
                    <td>${user.username}</td>
                    <td>${moment(user.created).fromNow()}</td>
                    <td><i class="fas fa-${isAdmin}"></i></td>
                    <td>
                        <button class="btn btn-primary resetPassword">
                            <i class="fas fa-wrench"></i>
                        </button>
                    </td>
                </tr>`;
            });
        }else{
            trs += `<tr><td colspan="999" class="text-info">No Users</td></tr>`
        }
        $("#usersTable > tbody").empty().append(trs);
    });
}

$("#settingsOverview").on("click", "#loadMoreRecordedActions", function(){
    $.confirm({
    title: 'Prompt!',
    content: `
    <div class="alert alert-danger">
        Loading to many logs may crash your browser!
    </div>
    <form action="" class="formName">
        <div class="form-group">
            <label>Ammount Of Logs To Fetch</label>
            <input type="text" value="30" class="ammount form-control" required />
        </div>
    </form>`,
    buttons: {
        formSubmit: {
            text: 'Submit',
            btnClass: 'btn-blue',
            action: function () {
                var ammount = this.$content.find('.ammount').val();
                if(!$.isNumeric(ammount)){
                    $.alert('provide a valid ammount');
                    return false;
                }
                loadRecordedActions(ammount)
            }
        },
        cancel: function () {
            //close
        },
    },
    onContentReady: function () {
        // bind to events
        var jc = this;
        this.$content.find('form').on('submit', function (e) {
            // if the user submits the form by pressing enter in the field.
            e.preventDefault();
            jc.$$formSubmit.trigger('click'); // reference the button and click it
        });
    }
});
});

$("#settingsOverview").on("click", "#addUser", function(){
    $.confirm({
        title: 'Create user!',
        content: `<form action="" class="formName">
            <div class="form-group">
                <label>Username</label>
                <input type="text" class="form-control" name="username" required />
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" name="password" required />
            </div>
        </form>`,
        buttons: {
            formSubmit: {
                text: 'Submit',
                btnClass: 'btn-blue',
                action: function () {
                    let username = this.$content.find('input[name=username]').val().trim();
                    let password = this.$content.find('input[name=password]').val().trim();
                    if(username == ""){
                        $.alert('provide a username');
                        return false;
                    } else if(password == ""){
                        $.alert('provide a password');
                        return false;
                    }

                    ajaxRequest(globalUrls.settings.users.add, {username: username, password: password}, (data)=>{
                        data = makeToastr(data);
                        if(data.hasOwnProperty("state") && data.state == "error"){
                            return false;
                        }
                        loadUsers();
                    });
                }
            },
            cancel: function () {
                //close
            },
        }
    });
});

$("#settingsOverview").on("click", ".resetPassword", function(){
    let targetUser = $(this).parents("tr").data("userId");
    $.confirm({
        title: 'Reset password!',
        content: `<form action="" class="formName">
            <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" name="password" required />
            </div>
        </form>`,
        buttons: {
            formSubmit: {
                text: 'Submit',
                btnClass: 'btn-blue',
                action: function () {
                    let password = this.$content.find('input[name=password]').val().trim();
                    if(password == ""){
                        $.alert('provide a password');
                        return false;
                    }

                    ajaxRequest(globalUrls.settings.users.resetPassword, {targetUser: targetUser, newPassword: password}, (data)=>{
                        data = makeToastr(data);
                        if(data.hasOwnProperty("state") && data.state == "error"){
                            return false;
                        }
                        loadUsers();
                    });
                }
            },
            cancel: function () {
                //close
            },
        }
    });
});

$("#settingsOverview").on("click", "#saveSettings", function(){
    let settings = [];
    $("#settingListTable > tbody > tr").each(function(){
        let tr = $(this);

        settings.push({
            id: tr.data("settingId"),
            value: tr.find("input[name=settingValue]").val()
        });
    });
    ajaxRequest(globalUrls.settings.saveAll, {settings: settings}, (data)=>{
        makeToastr(data)
    });
});
</script>
