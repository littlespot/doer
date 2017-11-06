<style>
    .editing{
        filter: alpha(opacity=50);
        opacity: .5;
    }
</style>
<script type="text/ng-template" id="delete.html">
    <div class="modal-body">
        <h4 translate="script.confirm" translate-values="{title:title}"></h4>
        <div ng-if="!submitted">
            <input type="checkbox" ng-model="deleteauthor" id="deleteauthor" name="deleteauthor"/>
            <label for="deleteauthor"><span></span></label>
            <span>{{trans("project.ALERTS.delete_author")}}</span>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-default" type="button" ng-click="$close(null)">
            {{trans("project.BUTTONS.cancel")}}
        </button>
        <button class="btn btn-danger" type="button" ng-disabled="authorForm.$invalid"
                ng-click="$close(deleteauthor)">
            {{trans("project.BUTTONS.confirm")}}
        </button>
    </div>
</script>
<script type="text/ng-template" id="script.html">
    <div class="modal-header">
        <div class="font-md">{{trans("project.LABELS.author_info")}}</div>
    </div>
    <div class="modal-body">
        <div class="alert alert-warning">
            {{trans("project.ALERTS.add_author")}}
        </div>
        <form name="authorForm" class="margin-top-sm margin-right-md margin-left-md" novalidate>
            <div>
                <input class="form-text" type="text" name="authorname"
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
            <div>
                <input class="form-text" type="email" name="email"
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
            <div>
                <input class="form-text" type="text" name ="link"
                       placeholder="{{trans("project.PLACES.author_site")}}"
                       ng-model="author.link" ng-maxlength="200" />
                <div role="alert" class="error" ng-class="{'visible':authorForm.link.$touched || authorForm.$submitted}">
                    <span ng-show="authorForm.link.$error.maxlength">
                        {{trans("project.ERRORS.maxlength.author_link")}}
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
<form name="scriptForm" class="margin-bottom-lg" id="script-content" style="position: relative">
    <h4>{{trans("project.LABELS.script")}}</h4>
    <table class="table">
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
                    <a ng-href="<%s.link%>" ng-bind="s.title"></a>
                </td>
                <td>
                    <span ng-repeat="a in s.authors | orderBy:name">
                        <a ng-if="a.email === null || a.email.length < 3" ng-href="<%a.link%>" target="_blank" ng-bind="a.name"></a>
                        <a ng-if="a.email != null && a.email.length > 2" ng-href="mailto:<%a.email%>" target="_blank" ng-bind="a.name"></a>
                        <span ng-if="!$last">, </span>
                    </span>
                </td>
                <td class="text-right" >
                   <span ng-bind="s.created_at | limitTo:10"></span>
                </td>
                <td class="text-center">
                    <span class="btn text-info fa fa-edit" ng-class="{'text-warning':scriptInEdit.id == s.id}"  ng-click="editScript(s)"></span>
                </td>
                <td class="text-center" style="border-left: 1px solid #ddd;">
                    <span ng-show="scriptInEdit.id != s.id" class="btn text-danger fa fa-trash" ng-click="deleteScript(s)"></span>
                </td>
            </tr>
        </tbody>
    </table>
    <div ng-if="scriptInEdit">
        <div class="alert alert-info" translate="script.alert"></div>
        <div class="row">
            <div class="col-md-5 col-sm-12">
                <input class="form-text" name="title" placeholder="{{trans("project.PLACES.script_title")}}"
                       ng-model="scriptInEdit.title" ng-maxlength="40" required/>
                <div role="alert" class="error" ng-class="{'visible':scriptForm.title.$touched || scriptForm.$submitted}">
                    <span ng-show="scriptForm.title.$error.required">
                        {{trans("project.ERRORS.require.script_title")}}
                    </span>
                    <span ng-show="scriptForm.title.$error.maxlength">
                         {{trans("project.ERRORS.maxlength.script_title")}}
                    </span>
                </div>
            </div>
            <div class="col-md-5 col-sm-8">
                <input class="form-text" name="link" placeholder="{{trans("project.PLACES.script_link")}}"
                       ng-model="scriptInEdit.link" ng-minlength="4" ng-maxlength="200" required/>
                <div role="alert" class="error" ng-class="{'visible':scriptForm.link.$touched || scriptForm.$submitted}">
                    <span ng-show="scriptForm.link.$error.required">
                        {{trans("project.ERRORS.require.script_link")}}
                    </span>
                    <span ng-show="scriptForm.link.$error.minlength">
                        {{trans("project.ERRORS.minlength.script_link")}}
                    </span>
                    <span ng-show="scriptForm.link.$error.maxlength">
                        {{trans("project.ERRORS.maxlength.script_link")}}
                    </span>
                </div>
            </div>
            <div class="col-md-2 col-sm-4">
                <span class="input-group">
                    <input type="text" class="form-text" uib-datepicker-popup="yyyy-MM-dd"
                           ng-model="scriptInEdit.created_at" name="created_at"
                           is-open="scriptInEdit.opened"
                           show-button-bar="false"
                           popup-placement="left"
                           ng-required="true"
                           placeholder="{{trans("project.PLACES.script_date")}}"
                           alt-input-formats="['M!/d!/yyyy']" />
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default" ng-click="openScriptDate(scriptInEdit)"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>
                </span>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-10 col-md-8 col-sm-6 col-xs-12">
                 <span ng-repeat="a in scriptInEdit.authors | orderBy:name">
                    <span ng-class="{'btn btn-link': a.email && a.email.length > 0}" ng-click="editAuthor(a)" ng-bind="a.name"></span>
                    <span class="btn text-danger fa fa-times"
                          ng-click="removeAuthor(a.id)"></span>
                </span>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12 flex-left">
                <angucomplete-alt id="author" input-name="author"
                                  placeholder="{{trans("project.PLACES.script_author")}}"
                                  pause="100"
                                  selected-object="authorSelected"
                                  local-data="authors"
                                  search-fields="username"
                                  title-field="username"
                                  description-field="location"
                                  image-uri="/context/avatars"
                                  image-field="id"
                                  minlength="1"
                                  override-suggestions="true"
                                  clear-selected = "true"
                                  input-class="form-text"
                                  match-class="highlight"  text-no-results="{{trans('layout.MENU.none')}}"
                                  text-searching="{{trans('layout.MENU.searching')}}"/>
            </div>
        </div>
        <div role="alert" class="error" ng-class="{'visible':scriptForm.$touched || scriptForm.$submitted}">
            <span ng-show="!scriptInEdit.authors.length">
                 {{trans("project.ERRORS.require.script_author")}}
            </span>
        </div>
        <div role="alert" class="text-center text-danger" ng-show="scripts.error" translate="script.ERRORS.unique.Email"></div>
        <div class="row margin-top-sm">
            <textarea class="form-control" rows="3" name="description"
                      placeholder="{{trans("project.PLACES.script_description")}}"
                      ng-model="scriptInEdit.description" ng-maxlength="400" required>
            </textarea>
            <div role="alert" class="error" ng-class="{'visible':scriptForm.description.$touched || scriptForm.$submitted}">
                <span ng-show="scriptForm.description.$error.required">
                    {{trans("project.ERRORS.require.script_description")}}
                </span>
                <span ng-show="scriptForm.description.$error.maxlength">
                    {{trans("project.ERRORS.maxlength.script_description")}}
                </span>
            </div>
        </div>
        <div class="flex-cols">
            <div role="alert" class="error" ng-class="{'visible':scriptForm.created_at.$touched || scriptForm.$submitted}">
                <span ng-show="scriptForm.created_at.$error.required">
                     {{trans("project.ERRORS.require.script_date")}}
                </span>
            </div>
            <div class="text-right">
                <span ng-click="cancelScript()" class="btn btn-default fa fa-undo"></span>&nbsp;&nbsp;&nbsp;
                <span ng-click="saveScript(scriptForm.$invalid)" class="btn btn-success fa fa-check" ng-disabled="scriptForm.$invalid" ></span>
            </div>
        </div>
    </div>
    <div class="loader-content" ng-if="scripts.loading"><div class="loader"></div></div>
</form>