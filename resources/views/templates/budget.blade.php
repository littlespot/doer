<div class="modal fade" id="deleteBudgetModal" tabindex="-1" role="dialog" aria-labelledby="deleteBudgetModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5>{{trans('project.HEADERS.delete_budget')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-body">
                <div>{{trans('project.MESSAGES.delete_budget')}}</div>
                <div ng-if="budgets.error" class="alert alert-danger" ng-bind="budgets.error"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" data-dismiss="modal" >
                    {{trans("project.BUTTONS.cancel")}}
                </button>
                <div class="btn btn-success" ng-click="budgetDeleted(budgetToDelete)" data-dismiss="modal"> {{trans("project.BUTTONS.confirm")}}</div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="deleteSponsorModal" tabindex="-1" role="dialog" aria-labelledby="deleteSponsorModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5>{{trans('project.HEADERS.delete_sponsor')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-body">
                <div>{{trans('project.MESSAGES.delete_sponsor')}}</div>
                <div ng-if="sponsors.error" class="alert alert-danger" ng-bind="sponsors.error"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" data-dismiss="modal" >
                    {{trans("project.BUTTONS.cancel")}}
                </button>
                <div class="btn btn-success" ng-click="sponsorDeleted(sponsorToDelete)" data-dismiss="modal"> {{trans("project.BUTTONS.confirm")}}</div>
            </div>
        </div>
    </div>
</div>
<form name="budgetForm" ng-class="{'loading':budgets.loading}">
    <h4>{{trans("project.LABELS.budget")}}</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th width="30%">{{trans("project.LABELS.budget_type")}}</th>
                <th width="20%" class="text-center">{{trans("project.LABELS.sum")}}</th>
                <th width="40%">{{trans("project.LABELS.budget_comment")}}</th>
                <th width="5%" class="text-center">
                    <div class="btn fa fa-plus" ng-class="{'text-warning':budgetNew,'text-info':!budgetNew}"  ng-click="addBudgetRow();"></div>
                </th>
                <th width="5%">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="b in budgets" >
                <td>
                    <span ng-if="budgetEdit.id != b.id" translate="budget.types.<%b.budget_type_id%>"></span>
                    <div ng-if="budgetEdit.id == b.id" class="input input--isao">
                        <select class="input__field input__field--isao"
                            ng-model="budgetEdit.budget_type_id" name="type"
                            ng-options="t.id as t.name for t in budgetTypes" required>
                         </select>
                        <label class="input__label input__label--isao" for="script_descrption" data-content="{{trans("project.PLACES.budget_type")}}">
                            <span class="input__label-content input__label-content--isao">
                                 <i class="text-danger pr-1">*</i>
                                <sapn ng-show="budgetEdit.budget_type_id">{{trans("project.PLACES.budget_type")}}</sapn>
                                <span ng-show="!budgetEdit.budget_type_id" class="text-danger">
                                     {{trans('project.ERRORS.require.budget_type')}}
                                </span>
                            </span>
                        </label>
                    </div>
                </td>
                <td class="text-right">
                    <span ng-if="budgetEdit.id != b.id" ng-bind="b.quantity"></span>
                    <div ng-if="budgetEdit.id == b.id"  class="input input--isao">
                        <input ng-model="budgetEdit.quantity" name="quantity" type="number" class="input__field input__field--isao" ng-pattern="budgetRegex" required />
                        <label class="input__label input__label--isao" for="script_descrption" data-content="{{trans('project.ERRORS.require.quantity')}}">
                            <span class="input__label-content input__label-content--isao">
                                 <i class="text-danger pr-1">*</i>
                                <sapn ng-show="!budgetForm.quantity.$error.required && !budgetForm.quantity.$error.pattern">{{trans('project.ERRORS.require.quantity')}}</sapn>
                                <span ng-show="budgetForm.quantity.$error.required || budgetForm.quantity.$error.pattern" class="text-danger">
                                      {{trans('project.ERRORS.invalid.quantity')}}
                                </span>
                            </span>
                        </label>
                    </div>
                </td>
                <td>
                     <span ng-if="budgetEdit.id != b.id" ng-bind="b.comment"></span>
                    <div ng-if="budgetEdit.id == b.id" class="input input--isao">
                        <input ng-model="budgetEdit.comment" type="text" name="comment" class="input__field input__field--isao"
                               ng-maxlength="100" required/>
                        <label class="input__label input__label--isao" for="script_descrption" data-content="{{trans("project.PLACES.budget_comment")}}">
                            <span class="input__label-content input__label-content--isao">
                                 <i class="text-danger pr-1">*</i>
                                <sapn ng-show="budgetEdit.comment.length >= 1 && budgetEdit.comment.length <=100">{{trans("project.PLACES.budget_comment")}}</sapn>
                                <span ng-show="budgetEdit.comment.length < 1" class="text-danger">
                                     {{trans('project.ERRORS.require.budget_comment')}}
                                </span>
                                <span ng-show="budgetEdit.comment.length > 100" class="text-danger">
                                       {{trans('project.ERRORS.maxlength.budget_comment')}}
                                </span>
                            </span>
                        </label>
                    </div>
                </td>
                <td class="text-center">
                    <span class="btn fa" ng-class="{'fa-edit text-info':!budgetEdit || budgetEdit.id != b.id, 'fa-check text-success':budgetEdit && budgetEdit.id == b.id}"
                          ng-click="switchEditBudget(b,budgetForm.$invalid)">
                    </span>
                </td>
                <td class="text-center">
                    <span class="btn text-danger fa" ng-class="{'fa-trash':!budgetEdit || budgetEdit.id != b.id, 'fa-undo':budgetEdit && budgetEdit.id == b.id}"
                        ng-click="cancelEditBudget(b.id)">
                    </span>
                </td>
            </tr>
            <tr ng-if="budgetNew">
                <td>
                    <div class="input input--isao">
                        <select class="input__field input__field--isao"
                                ng-model="budgetNew.budget_type_id" name="type"
                                ng-options="t.id as t.name for t in budgetTypes" required>
                        </select>
                        <label class="input__label input__label--isao" for="script_descrption" data-content="{{trans("project.PLACES.budget_type")}}">
                        <span class="input__label-content input__label-content--isao">
                             <i class="text-danger pr-1">*</i>
                            <sapn ng-show="budgetNew.budget_type_id">{{trans("project.PLACES.budget_type")}}</sapn>
                            <span ng-show="!budgetNew.budget_type_id" class="text-danger">
                                 {{trans('project.ERRORS.require.budget_type')}}
                            </span>
                        </span>
                        </label>
                    </div>
                </td>
                <td>
                    <div class="input input--isao">
                        <input ng-model="budgetNew.quantity" name="quantity" type="number" class="input__field input__field--isao"  ng-pattern="budgetRegex"  required />
                        <label class="input__label input__label--isao" for="script_descrption" data-content="{{trans('project.ERRORS.require.quantity')}}">
                        <span class="input__label-content input__label-content--isao">
                             <i class="text-danger pr-1">*</i>
                            <sapn ng-show="!budgetForm.quantity.$error.required && !budgetForm.quantity.$error.pattern">{{trans('project.ERRORS.require.quantity')}}</sapn>
                            <span ng-show="budgetForm.quantity.$error.required || budgetForm.quantity.$error.pattern" class="text-danger">
                                  {{trans('project.ERRORS.invalid.quantity')}}
                            </span>
                        </span>
                        </label>
                    </div>
                </td>
                <td>
                    <div class="input input--isao">
                        <input ng-model="budgetNew.comment" type="text" name="comment" class="input__field input__field--isao"
                               placeholder="{{trans("project.PLACES.budget_comment")}}"
                               ng-maxlength="100" required/>
                        <label class="input__label input__label--isao" for="script_descrption" data-content="{{trans("project.PLACES.budget_comment")}}">
                        <span class="input__label-content input__label-content--isao">
                             <i class="text-danger pr-1">*</i>
                            <sapn ng-show="budgetNew.comment.length >= 1 && budgetNew.comment.length <=100">{{trans("project.PLACES.budget_comment")}}</sapn>
                            <span ng-show="!budgetNew.comment || budgetNew.comment.length < 1" class="text-danger">
                                 {{trans('project.ERRORS.require.budget_comment')}}
                            </span>
                            <span ng-show="budgetNew.comment.length > 100" class="text-danger">
                                   {{trans('project.ERRORS.maxlength.budget_comment')}}
                            </span>
                        </span>
                        </label>
                    </div>
                </td>
                <td class="text-center">
                    <button class="btn btn-link fa fa-check" ng-disabled="budgetForm.$invalid" ng-click="saveBudget(budgetForm.$invalid);"></button>
                </td>
                <td class="text-center">
                    <span class="btn btn-link fa fa-undo" ng-click="cancelBudget()"></span>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right">
                    <div>
                       <span translate="budget.total" translate-values="{sum:getTotal(budgets)}"></span>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
    <div class="loader-content" ng-if="budgets.loading"><div class="loader"></div></div>
</form>
<div class="alert alert-info">
    {{trans("project.ALERTS.funds")}}
</div>
<form name="sponsorForm" ng-class="{'loading':sponsors.loading}">
    <h4>{{trans("project.LABELS.funds")}}</h4>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th width="40%" class="text-center">{{trans("project.LABELS.sponsor")}}</th>
            <th width="20%" class="text-center">{{trans("project.LABELS.sum")}}</th>
            <th width="30%" class="text-right">{{trans("project.LABELS.funds_date")}}</th>
            <th width="5%">
                <div class="btn" ng-class="{'text-warning':sponsorNew,'text-info':!sponsorNew}" ng-click="addSponsorRow();">
                    <span class="fa fa-plus"></span>
                </div>
            </th>
            <th width="5%">

            </th>
        </tr>
        </thead>
        <tbody>
            <tr ng-repeat="s in sponsors">
                <td>
                    <span ng-if="sponsorInEdit.id != s.id">
                        <a ng-if="s.user_id" href="/profile/<%s.user_id%>" target="_blank" ng-bind="s.sponsor_name"></a>
                        <span ng-if="!s.user_id" ng-bind="s.sponsor_name"></span>
                    </span>
                    <div ng-if="sponsorInEdit.id == s.id">
                        <angucomplete-alt id="sponsorName" input-name="user"
                                          placeholder="{{trans('project.PLACES.sponsor')}}"
                                          pause="100"
                                          selected-object="sponsorInEdit.sponsor"
                                          local-data="authors"
                                          initial-value= "sponsorInEdit.sponsor_name"
                                          search-fields="username"
                                          title-field="username"
                                          image-uri="/storage/avatars"
                                          image-field="id"
                                          minlength="1"
                                          override-suggestions="true"
                                          description-field="location"
                                          input-class="form-control"  text-no-results="{{trans('layout.MENU.none')}}"
                                          text-searching="{{trans('layout.MENU.searching')}}" />
                        <div role="alert" class="error" ng-class="{'visible':sponsorForm.user.$touched || sponsorForm.$submitted}">
                            <span ng-show="sponsorForm.user.$error.required">
                                {{trans('project.ERRORS.require.sponsor')}}
                            </span>
                        </div>
                    </div>
                </td>
                <td class="text-right">
                    <span ng-if="sponsorInEdit.id != s.id" ng-bind="s.quantity"></span>
                    <div ng-if="sponsorInEdit.id == s.id">
                        <input ng-model="sponsorInEdit.quantity" name="quantity" type="number"  ng-pattern="budgetRegex"  class="form-control" required />
                        <div role="alert" class="error" ng-class="{'visible':sponsorForm.quantity.$touched || sponsorForm.$submitted}">
                            <span ng-show="sponsorForm.quantity.$error.required || sponsorForm.quantity.$error.pattern">
                                {{trans('project.ERRORS.require.quantity')}}
                            </span>
                        </div>
                    </div>
                </td>
                <td class="text-right">
                    <span ng-if="sponsorInEdit.id != s.id" ng-bind="s.sponsed_at|limitTo:10"></span>
                    <div ng-if="sponsorInEdit.id == s.id">
                        <span class="input-group">
                          <input type="text" class="form-control" uib-datepicker-popup="yyyy-MM-dd"
                                 ng-model="sponsorInEdit.sponsed_at" name="sponsed"
                                 is-open="sponsorInEdit.opened"
                                 show-button-bar="false"
                                 popup-placement="left"
                                 ng-required="true"
                                 readonly
                                 placeholder="{{trans('project.PLACES.funds_date')}}"
                                 alt-input-formats="['M!/d!/yyyy']" />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-outline-secondary" ng-click="openSponsedDate(sponsorInEdit)"><i class="fa fa-calendar"></i></button>
                            </span>
                        </span>
                        <div role="alert" class="error" ng-class="{'visible':sponsorForm.sponsed.$touched || sponsorForm.$submitted}">
                            <span ng-show="sponsorForm.sponsed.$error.required">
                                {{trans('project.ERRORS.require.funds_date')}}
                            </span>
                        </div>
                    </div>
                </td>
                <td class="text-center">
                    <span class="btn fa" ng-class="{'fa-edit text-info': !sponsorInEdit || sponsorInEdit.id != s.id, 'fa-check text-success':sponsorInEdit.id == s.id}"
                          ng-click="switchEditSponsor(s, sponsorForm.$invalid)"></span>
                </td>
                <td class="text-center">
                    <span class="btn text-danger fa" ng-class="{'fa-trash':!sponsorInEdit || sponsorInEdit.id != s.id, 'fa-undo': sponsorInEdit.id == s.id}"
                          ng-click="cancelEditSponsor(s.id)"  ></span>
                </td>
            </tr>
            <tr ng-if="sponsorNew">
                <td>
                    <angucomplete-alt id="newSponsor" input-name="new_user"
                                  placeholder="{{trans('project.PLACES.sponsor')}}"
                                  pause="100"
                                  selected-object="sponsorNew.sponsor"
                                  local-data="authors"
                                  search-fields="username"
                                  title-field="username"
                                  image-uri="/storage/avatars"
                                  image-field="id"
                                  minlength="1"
                                  description-field="location"
                                  override-suggestions="true"
                                  input-class="form-control"
                                  text-no-results="{{trans('layout.MENU.none')}}"
                                  text-searching="{{trans('layout.MENU.searching')}}"/>
                    <div role="alert" class="error" ng-class="{'visible':sponsorForm.new_user.$touched || sponsorForm.$submitted}">
                        <span ng-show="sponsorForm.new_user.$error.required">
                            {{trans('project.ERRORS.require.sponsor')}}
                        </span>
                    </div>
                </td>
                <td class="text-right">
                    <input ng-model="sponsorNew.quantity" name="new_quantity" type="number"  ng-pattern="budgetRegex" class="form-control" required />
                    <div role="alert" class="error" ng-class="{'visible':sponsorForm.new_quantity.$touched || sponsorForm.$submitted}">
                        <span ng-show="sponsorForm.new_quantity.$error.required || sponsorForm.new_quantity.$error.pattern">
                            {{trans('project.ERRORS.require.quantity')}}
                        </span>
                    </div>
                </td>
                <td>
                    <span class="input-group">
                      <input type="text" class="form-control" uib-datepicker-popup="yyyy-MM-dd" name="finish"
                             ng-model="sponsorNew.sponsed_at" name="new_sponsed"
                             is-open="sponsorNew.opened"
                             datepicker-options="dateOptions"
                             ng-required="true"
                             placeholder="{{trans('project.PLACES.funds_date')}}"
                             alt-input-formats="['M!/d!/yyyy']" />
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-outline-secondary" ng-click="openSponsedDate(sponsorNew)"><i class="fa fa-calendar"></i></button>
                        </span>
                    </span>
                    <div role="alert" class="error" ng-class="{'visible':sponsorForm.new_sponsed.$touched || sponsorForm.$submitted}">
                        <span ng-show="sponsorForm.new_sponsed.$error.required">
                            {{trans('project.ERRORS.require.funds_date')}}
                        </span>
                    </div>
                </td>
                <td class="text-center">
                    <span class="btn text-success fa fa-check" ng-disabled="sponsorForm.$invalid" ng-click="saveSponsor(sponsorForm.$invalid);"></span>
                </td>
                <td class="text-center">
                    <span class="btn text-danger fa fa-undo" ng-click="cancelSponsor()"></span>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right">
                    <div>
                        <span translate="budget.total" translate-values="{sum:getTotal(sponsors)}"></span>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
</form>

<div ng-if="sponsors && budgets">
    <div ng-if="getTotal(sponsors) >= getTotal(budgets)" class="alert alert-success">
        <span translate="budget.success" translate-values="{sum:getTotal(sponsors) - getTotal(budgets)}"></span>
    </div>
    <div ng-if="getTotal(sponsors) < getTotal(budgets)" class="alert alert-danger">
        <span translate="budget.inprogress" translate-values="{sum: getTotal(budgets) - getTotal(sponsors)}"></span>
    </div>
</div>