<div class="modal fade bd-example-modal-lg" id="newAuthorModal" tabindex="-1" role="dialog" aria-labelledby="newAuthorModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5>{{trans("project.LABELS.author_info")}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-body">
                <form name="authorForm" novalidate>
                    <div class="form-group">
                        <input class="form-control" type="text" name="authorname"
                               placeholder="{{trans("project.PLACES.author_name")}}"
                               ng-model="author.name" ng-minlength="2" ng-maxlength="40" required>
                        <div role="alert" class="error" ng-class="{'visible':authorForm.authorname.$touched || authorForm.$submitted}">
                            <span ng-show="authorForm.authorname.$error.required">
                                {{trans("project.ERRORS.require.author_name")}}
                            </span>
                            <span ng-show="authorForm.authorname.$error.minlength">
                                 {{trans("project.ERRORS.minlength.author_name")}}
                            </span>
                            <span ng-show="authorForm.authorname.$error.maxlength">
                                {{trans("project.ERRORS.maxlength.author_name")}}
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="email" name="email"
                               placeholder="{{trans("project.PLACES.author_email")}}"
                               ng-model="author.email" ng-maxlength="100" />
                        <div role="alert" class="error" ng-class="{'visible':authorForm.email.$touched || authorForm.link.$touched || authorForm.$submitted}">
                            <span ng-show="authorForm.email.$error.required">
                                {{trans("project.ERRORS.require.author_email")}}
                            </span>
                            <span ng-show="authorForm.email.$error.email">
                                {{trans("validation.email", ['attribute'=>'EMAIL'])}}
                            </span>
                            <span ng-show="authorForm.email.$error.maxlength">
                                {{trans("validation.max.string", ['attribute'=>'EMAIL', 'max'=>100])}}
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="text" name ="link"
                               placeholder="{{trans("project.PLACES.author_site")}}"
                               ng-model="author.link" ng-maxlength="200" />
                        <div role="alert" class="error" ng-class="{'visible':authorForm.link.$touched || authorForm.$submitted}">
                            <span ng-show="authorForm.link.$error.maxlength">
                                {{trans("project.ERRORS.maxlength.author_link")}}
                            </span>
                        </div>
                    </div>
                </form>
                <div ng-if="scripts.error" class="alert alert-danger" ng-bind="scripts.error"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" data-dismiss="modal" >
                    {{trans("project.BUTTONS.cancel")}}
                </button>
                <div class="btn btn-success" ng-click="authorAdded(author)" data-dismiss="modal"> {{trans("project.BUTTONS.confirm")}}</div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="deleteScriptModal" tabindex="-1" role="dialog" aria-labelledby="deleteScriptModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 translate="script.confirm" translate-values="{title:scriptToDelete.title}"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-body">

                <div ng-if="!submitted">
                    <div class="checkbox-primary">
                        <div>
                            <input type="checkbox" ng-model="deleteauthor" id="deleteauthor" name="deleteauthor"/>
                        </div>
                        <div class="label">{{trans("project.ALERTS.delete_author")}}</div>
                    </div>
                </div>
                <div ng-if="scripts.error" class="alert alert-danger" ng-bind="scripts.error"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" data-dismiss="modal" >
                    {{trans("project.BUTTONS.cancel")}}
                </button>
                <div class="btn btn-success" ng-click="scriptDeleted(deleteauthor)" data-dismiss="modal"> {{trans("project.BUTTONS.confirm")}}</div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="authorErrorModal" tabindex="-1" role="dialog" aria-labelledby="authorErrorModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5>{{trans('project.ERRORS.invalid.author')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-body">
                <h6 ng-if="!isProject">{{trans('project.MESSAGES.notfriends')}}</h6>
                <h6 ng-if="isProject">{{trans('project.MESSAGES.notteam')}}</h6>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" data-dismiss="modal" >
                    {{trans("project.BUTTONS.cancel")}}
                </button>
                <div class="btn btn-success" ng-click="openUser()" data-dismiss="modal">{{trans("project.BUTTONS.confirm")}}</div>
            </div>
        </div>
    </div>
</div>
<form name="scriptForm" class="py-5" id="script-content" ng-class="{'loading':scripts.loading}">
    <h4>{{trans("project.LABELS.script")}}</h4>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th width="30%">{{trans("project.LABELS.script_title")}}</th>
                <th width="45%">{{trans("project.LABELS.script_author")}}</th>
                <th width="15%" class="text-right">
                    {{trans("project.LABELS.script_date")}}
                </th>
                <th width="5%" class="text-center">
                    <div class="btn fa fa-plus"  ng-class="{'text-warning':scriptInEdit && !scriptInEdit.id}" ng-click="addScript()"></div>
                </th>
                <th width="5%"></th>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="s in scripts" ng-class="{'editing':scriptInEdit.id == s.id}">
                <td>
                    <a ng-href="<%s.link%>" ng-bind="s.title" target="_blank"></a>
                </td>
                <td>
                    <ol class="breadcrumb bg-white" style="padding:0" ng-repeat="a in s.authors | orderBy:name">
                        <li class="breadcrumb-item">
                            <a ng-if="a.email === null || a.email.length < 3" ng-href="<%a.link%>" target="_blank" ng-bind="a.name"></a>
                            <a ng-if="a.email != null && a.email.length > 2" ng-href="mailto:<%a.email%>" target="_blank" ng-bind="a.name"></a>
                        </li>
                    </ol>
                </td>
                <td class="text-right" >
                   <span ng-bind="s.created_at | limitTo:10"></span>
                </td>
                <td class="text-center">
                    <span class="btn text-info fa fa-edit" ng-class="{'text-warning':scriptInEdit.id == s.id}"  ng-click="editScript(s)"></span>
                </td>
                <td class="text-center" style="border-left: 1px solid #ddd;">
                    <div class="btn text-danger fa fa-trash" data-toggle="modal" ng-click="deleteScript(s)"></div>
                </td>
            </tr>
        </tbody>
    </table>
    <div ng-if="scriptInEdit" class="bg-light p-3">
        <div class="alert alert-info" translate="script.alert"></div>
        <div class="row">
            <div class="col-md-8 col-sm-12 input input--isao">
                <input class="input__field input__field--isao" name="title"
                       ng-model="scriptInEdit.title" ng-maxlength="40" required/>
                <label class="input__label input__label--isao" for="country_id" data-content="{{trans('project.PLACES.script_title')}}">
                    <span class="input__label-content input__label-content--isao" >
                         <i class="text-danger pr-1">*</i>
                        <span ng-if="!scriptForm.title.$error.required && !scriptForm.title.$error.maxlength">{{trans("project.LABELS.script_title")}}</span>
                        <span class="text-danger" ng-if="scriptForm.title.$error.required">{{trans("project.ERRORS.require.script_title")}}</span>
                        <span class="text-danger" ng-if="scriptForm.title.$error.maxlength">{{trans("project.ERRORS.maxlength.script_title")}}</span>
                    </span>
                </label>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="input input--isao">
                    <input class="input__field input__field--isao" uib-datepicker-popup="yyyy-MM-dd" id="scriptInEdit_created_at" alt="{{$project->finish_at}}"
                           ng-model="scriptInEdit.created_at"
                           name="created_at"
                           is-open="scriptInEdit.opened"
                           show-button-bar="false"
                           popup-placement="left"
                           ng-required="true"
                           placeholder="yyyy-MM-dd"
                           alt-input-formats="['M!/d!/yyyy']"
                           required/>
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-outline-secondary" ng-click="openScriptDate(scriptInEdit)"><i class="fa fa-calendar"></i></button>
                    </span>
                    <label class="input__label input__label--isao" for="input-title" data-content="{{trans("project.PLACES.script_date")}}">
                        <span class="input__label-content input__label-content--isao">
                            <i class="text-danger pr-1">*</i>
                            <span ng-show="!scriptForm.script_date.$error.required">{{trans('project.LABELS.script_date')}}</span>
                            <span class="text-danger" ng-show="scriptForm.script_date.$error.required">{{trans("project.ERRORS.require.script_date")}}</span>
                        </span>
                    </label>
                </div>
            </div>
        </div>
        <div class="row py-1">
            <div class="col-sm-12">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">https://</span>
                    </div>
                    <input class="form-control" name="link" ng-model="scriptInEdit.link" ng-minlength="4" ng-maxlength="200" required/>
                </div>
                <label class="small">
                    <sup class="text-danger pr-1">*</sup>
                    <span ng-show="!scriptForm.link.$error.required && !scriptForm.link.$error.minlength && !scriptForm.link.$error.maxlength">{{trans("project.PLACES.script_link")}}</span>
                    <span class="text-danger" ng-show="scriptForm.link.$error.required">
                        {{trans("project.ERRORS.require.script_link")}}
                    </span>
                    <span class="text-danger" ng-show="scriptForm.link.$error.minlength">
                        {{trans("project.ERRORS.minlength.script_link")}}
                    </span>
                    <span class="text-danger" ng-show="scriptForm.link.$error.maxlength">
                        {{trans("project.ERRORS.maxlength.script_link")}}
                    </span>
                </label>
            </div>
        </div>
        <div class="py-1">
              <label><sup class="text-danger">*</sup>{{trans("project.LABELS.script_author")}}:</label>
              <ol class="breadcrumb bg-light" ng-repeat="a in scriptInEdit.authors | orderBy:name">
                   <li class="breadcrumb-item ">
                       <span class="bg-secondary" ng-class="{'btn btn-link': a.email && a.email.length > 0}" ng-click="editAuthor(a)" ng-bind="a.name"></span>
                       <sup class="text-danger fa fa-times-circle closer"
                             ng-click="removeAuthor(a.id)"></sup>
                   </li>
                </ol>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 flex-left">
                <angucomplete-alt id="user" input-name="author"
                                  placeholder="{{trans("project.PLACES.script_")}}"
                                  pause="100"
                                  selected-object="userSelected"
                                  local-data="{{$users}}"
                                  search-fields="username"
                                  title-field="username"
                                  description-field="location"
                                  image-uri="/storage/avatars"
                                  image-field="id"
                                  minlength="1"
                                  override-suggestions="false"
                                  clear-selected = "true"
                                  input-class="form-control"
                                  match-class="highlight"  text-no-results="{{trans('layout.MENU.none')}}"
                                  text-searching="{{trans('layout.MENU.searching')}}"/>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 flex-left">
                <angucomplete-alt id="author" input-name="author"
                                  placeholder="{{trans("project.PLACES.script_author")}}"
                                  pause="100"
                                  selected-object="authorSelected"
                                  local-data="{{$authors}}"
                                  search-fields="username"
                                  title-field="username"
                                  description-field="location"
                                  minlength="1"
                                  override-suggestions="true"
                                  clear-selected = "true"
                                  input-class="form-control"
                                  match-class="highlight"  text-no-results="{{trans('layout.MENU.add')}}"
                                  text-searching="{{trans('layout.MENU.searching')}}"/>
            </div>
        </div>
        <div role="alert" class="error" ng-class="{'visible':scriptForm.$touched || scriptForm.$submitted}">
            <span ng-show="!scriptInEdit.authors.length">
                 {{trans("project.ERRORS.require.script_author")}}
            </span>
        </div>
        <div role="alert" class="text-center text-danger" ng-show="scripts.error" translate="script.ERRORS.unique.Email"></div>
        <div class="py-3 input input--isao">
            <textarea class="input__field input__field--isao" rows="3" name="description" id="script_descrption"
                      ng-model="scriptInEdit.description" ng-maxlength="400" required>
            </textarea>
            <label class="input__label input__label--isao" for="script_descrption" data-content="{{trans("project.PLACES.script_description")}}">
                <span class="input__label-content input__label-content--isao">
                     <i class="text-danger pr-1">*</i>
                    <sapn ng-show="!scriptForm.description.$error.required && !scriptForm.description.$error.maxlength">{{trans("project.LABELS.script_description")}}</sapn>
                    <span ng-show="scriptForm.description.$error.required" class="text-danger">
                        {{trans("project.ERRORS.require.script_description")}}
                    </span>
                    <span ng-show="scriptForm.description.$error.maxlength" class="text-danger">
                        {{trans("project.ERRORS.maxlength.script_description")}}
                    </span>
                </span>
            </label>
        </div>
        <div class="flex-cols">
            <div role="alert" class="error" ng-class="{'visible':scriptForm.created_at.$touched || scriptForm.$submitted}">
                <span ng-show="scriptForm.created_at.$error.required">
                     {{trans("project.ERRORS.require.script_date")}}
                </span>
            </div>
            <div class="text-right">
                <span ng-click="cancelScript()" class="btn btn-outline-danger fa fa-undo"></span>&nbsp;&nbsp;&nbsp;
                <span ng-click="saveScript(scriptForm.$invalid)" class="btn btn-success fa fa-check" ng-disabled="scriptForm.$invalid" ></span>
            </div>
        </div>
    </div>
</form>