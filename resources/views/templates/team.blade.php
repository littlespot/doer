<script type="text/ng-template" id="script.html">
    <div class="modal-header">
        <div class="font-md">{{trans("project.LABELS.member_info")}}</div>
    </div>
    <div class="modal-body">
        <div class="alert alert-warning">
            {!! trans("project.ALERTS.add_member") !!}
        </div>
        <form name="authorForm" class="margin-top-sm margin-right-md margin-left-md" novalidate>
            <div>
                <input class="form-text" type="text" name="name"
                       placeholder="{{trans("project.PLACES.member_name")}}"
                       ng-model="author.name" ng-minlength="2" ng-maxlength="40" required>
                <div role="alert" class="error" ng-class="{'visible':authorForm.authorname.$touched || authorForm.$submitted}">
                    <span ng-show="authorForm.authorname.$error.required">
                        {{trans("project.ERRORS.require.member_name")}}
                    </span>
                    <span ng-show="authorForm.authorname.$error.minlength">
                         {{trans("project.ERRORS.minlength.member_name")}}
                    </span>
                    <span ng-show="authorForm.authorname.$error.maxlength">
                        {{trans("project.ERRORS.maxlength.member_name")}}
                    </span>
                </div>
            </div>
            <div>
                <input class="form-text" type="email" name="email"
                       placeholder="{{trans("project.PLACES.member_email")}}"
                       ng-model="author.email" ng-maxlength="100" />
                <div role="alert" class="error" ng-class="{'visible':authorForm.email.$touched || authorForm.link.$touched || authorForm.$submitted}">
                    <span ng-show="authorForm.email.$error.required">
                        {{trans("project.ERRORS.require.member_email")}}
                    </span>
                    <span ng-show="authorForm.email.$error.email">
                        {{trans("validation.email", ['attribute'=>'EMAIL'])}}
                    </span>
                    <span ng-show="authorForm.email.$error.maxlength">
                        {{trans("validation.max.string", ['attribute'=>'EMAIL', 'max'=>100])}}
                    </span>
                </div>
            </div>
            <div>
                <input class="form-text" type="text" name ="link"
                       placeholder="{{trans("project.PLACES.member_site")}}"
                       ng-model="author.link" ng-maxlength="200" />
                <div role="alert" class="error" ng-class="{'visible':authorForm.link.$touched || authorForm.$submitted}">
                    <span ng-show="authorForm.link.$error.maxlength">
                        {{trans("project.ERRORS.maxlength.member_link")}}
                    </span>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-default" type="button" ng-click="$close(null)">
            {{trans("project.BUTTONS.cancel")}}
        </button>
        <button class="btn btn-success text-uppercase" type="button" ng-disabled="authorForm.$invalid"
                ng-click="$close(author)">
            {{trans("project.BUTTONS.confirm")}}
        </button>
    </div>
</script>
<table class="table">
    <thead>
        <tr>
            <th width="24px"></th>
            <th width="200px"></th>
            <th width="200px"></th>
            <th></th>
            <th width="5px">
                <div class="btn text-info fa fa-plus" ng-disabled="teamInEdit" ng-click="addTeam()"></div>
            </th>
            <th width="5px"></th>
        </tr>
    </thead>
    <tbody>
        <tr ng-repeat="member in team" ng-class="{'deleted':member.deleted}">
            <td style="padding: 0; vertical-align: middle">
                <img ng-if="!member.outsider" class="img-circle img-fluid center" src="/storage/avatars/<%member.user_id%>.small.jpg" />
            </td>
            <td>
                <span ng-if="teamInEdit.id != member.id">
                    <a ng-if="!member.outsider" href="/profile/<%member.user_id%>" ng-bind="member.username"></a>
                    <a ng-if="member.outsider" href="<%member.link ? member.link : '#'%>" ng-bind="member.username"></a>
                </span>
                <span ng-if="teamInEdit.id == member.id">
                    <span ng-if="!member.outsider" ng-bind="member.username"></span>
                    <span ng-if="member.outsider" class="btn title" ng-click="editMember(member)" ng-bind="member.username"></span>
                    <div role="alert" class="text-center text-danger" ng-show="team_error.user == 'e'">
                        {{trans("project.ERRORS.require.member_email")}}
                    </div>
                </span>
            </td>
            <td>
                <span ng-bind="member.location"></span>
            </td>
            <td>
                <span ng-repeat="role in member.occupation" >
                    <b ng-bind="role.name"></b>
                    <span ng-if="teamInEdit.id == member.id && role.occupation_id != 20" class="btn text-danger fa fa-times" ng-click="removeRole(role)"></span>
                    <span ng-if="!$last">,</span>
                </span>
                <select class="form-text" ng-if="teamInEdit.id == member.id" ng-model="teamInEdit.newRole">
                    <option value="" disabled>{{trans("project.PLACES.team_occupation")}}</option>
                    @foreach($occupations as $occupation)
                        <option id="opt_role_{{$occupation->id}}" value="{{$occupation->id}}">{{$occupation->name}}</option>
                    @endforeach
                </select>
                <span ng-if="teamInEdit.id == member.id" ng-click="addRole(teamInEdit.newRole)" class="btn text-info fa fa-plus"></span>
                <div ng-if="teamInEdit.id == member.id" class="error" ng-class="{'visible':team_error.role || member.occupation.length ==0}"
                     translate="project.ERRORS.require.role"></div>
                <span ng-if="submitted && !teamInEdit.outsider" class="text-danger"></span>
            </td>
            <td>
                <span ng-if="!member.deleted" class="btn fa" ng-class="{'text-warning fa-check':teamInEdit.id == member.id, 'text-info fa-edit':teamInEdit.id != member.id}"
                      ng-click="editTeam(member)"></span>
            </td>
            <td class="text-center" style="border-left: 1px solid #ddd;">
                <span ng-if="!member.deleted" class="btn fa" ng-class="{'text-danger fa-trash':teamInEdit.id != member.id, 'text-info fa-undo':teamInEdit.id == member.id}"
                      ng-click="cancelTeam(member, '{{!is_null($project)}}')"></span>
            </td>
        </tr>
        <tr ng-if="teamInEdit.id == 0">
            <td colspan="3">
                <div>
                    <angucomplete-alt id="author" input-name="member"
                        placeholder="{{trans('project.PLACES.team')}}"
                        pause="100"
                        selected-object="teamInEdit.member"
                        local-data="users"
                        search-fields="username"
                        title-field="username"
                        description-field="location"
                        image-uri="/storage/avatars"
                        image-field="id"
                        minlength="1"
                        override-suggestions="true"
                        input-class="form-text"
                        match-class="highlight"  text-no-results="{{trans('layout.MENU.none')}}"
                                      text-searching="{{trans('layout.MENU.searching')}}"/>
                </div>
                <div role="alert" class="text-center text-danger" ng-show="team_error.user == 'e'">
                    {{trans("project.ERRORS.unique.Email")}}
                </div>
                <div role="alert" class="text-center text-danger" ng-show="team_error.user == 'i'">
                    {{trans("project.ERRORS.minlength.Email")}}
                </div>
                <div role="alert" class="text-center text-danger" ng-show="team_error.user == 'n'">
                    {{trans("project.ERRORS.require.memeber_name")}}
                </div>
            </td>
            <td>
                <div style="float:left" ng-repeat="role in teamInEdit.occupation" >
                    <span class="text-info" ng-bind="role.name"></span>
                    <span class="btn text-danger fa fa-times" ng-click="removeRole(role.occupation_id)"></span>
                </div>
                <select id="selector_role" class="form-text" ng-model="teamInEdit.newRole" ng-change="addRole(teamInEdit.newRole)">
                    <option value="" disabled>{{trans('project.PLACES.team_role')}}</option>
                    @foreach($occupations as $occupation)
                        <option id="opt_role_{{$occupation->id}}" value="{{$occupation->id}}">{{$occupation->name}}</option>
                    @endforeach
                </select>
                <div class="error" ng-class="{'visible':team_error.role || member.occupation.length ==0}">
                    {{trans("project.ERRORS.require.team_role")}}
                </div>
            </td>
            <td>
                <span class="btn text-warning fa fa-check" ng-click="saveTeam()"></span>
            </td>
            <td class="text-center" style="border-left: 1px solid #ddd;">
                <span class="btn text-info fa fa-undo" ng-click="cancelSave()"></span>
            </td>
        </tr>
    </tbody>
</table>

<div class="text-center" ng-show="pagination.show">
    <ul uib-pagination ng-change="pageChanged()"
        max-size="5"
        rotate = true
        items-per-page = 'pagination.perPage'
        boundary-links="true"
        total-items="pagination.total"
        ng-model="pagination.currentPage"
        class="pagination-sm"
        previous-text="&lsaquo;"
        next-text="&rsaquo;"
        first-text="&laquo;"
        last-text="&raquo;"></ul>
</div>