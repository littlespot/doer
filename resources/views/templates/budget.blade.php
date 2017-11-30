<style>
    .table-bordered{
        border-top: 0;
        border-right: 1px solid #fff;
    }

    .table-bordered>thead>tr>th{
        border-top: 1px solid #ddd;
    }
    .table-bordered>thead>tr>th:last-child{
        background: #fff;
        border: 0
    }
    .table-bordered>tbody>tr>td:last-child{
        border-right: 1px solid #ddd
    }

    .table-bordered td{
        vertical-align: bottom;
    }

</style>
<script type="text/ng-template" id="budget.html">
    <div class="modal-body" id="modal-body">
        <h3 translate="budget.MESSAGES.<%confirm%>"></h3>
    </div>
    <div class="modal-footer">
        <button class="btn btn-default" type="button" ng-click="$close(false)">{{trans("project.BUTTONS.cancel")}}</button>
        <button class="btn btn-danger" type="button" ng-click="$close(true)">{{trans("project.BUTTONS.confirm")}}</button>
    </div>
</script>
<form name="budgetForm" class="table-responsive" style="position: relative">
    <h4>{{trans("project.LABELS.budget")}}</h4>
    <table class="table table-bordered" style="">
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
                    <div ng-if="budgetEdit.id == b.id">
                        <select class="form-control"
                            ng-model="budgetEdit.budget_type_id" name="type"
                            ng-options="t.id as t.name for t in budgetTypes" required>
                             <option value="" disabled>{{trans("project.PLACES.budget_type")}}</option>
                         </select>
                        <div role="alert" class="error" ng-class="{'visible':budgetForm.type.$touched || budgetForm.$submitted}">
                            <span ng-show="budgetForm.type.$error.required">
                               {{trans('project.ERRORS.require.budget_type')}}
                            </span>
                        </div>
                    </div>
                </td>
                <td class="text-right">
                    <span ng-if="budgetEdit.id != b.id" ng-bind="b.quantity"></span>
                    <div ng-if="budgetEdit.id == b.id">
                        <input ng-model="budgetEdit.quantity" name="quantity" type="number" class="form-text" ng-pattern="budgetRegex" required />
                        <div role="alert" class="error" ng-class="{'visible':budgetForm.quantity.$touched || budgetForm.$submitted}">
                            <span ng-show="budgetForm.quantity.$error.required || budgetForm.quantity.$error.pattern">
                               {{trans('project.ERRORS.invalid.quantity')}}
                            </span>
                        </div>
                    </div>
                </td>
                <td>
                     <span ng-if="budgetEdit.id != b.id" ng-bind="b.comment"></span>
                    <div ng-if="budgetEdit.id == b.id">
                        <input ng-model="budgetEdit.comment" type="text" name="comment" class="form-text"
                               placeholder="{{trans("project.PLACES.budget_comment")}}"
                               ng-maxlength="100" required/>
                        <div role="alert" class="error" ng-class="{'visible':budgetForm.comment.$touched || budgetForm.$submitted}">
                            <span ng-show="budgetForm.comment.$error.required">
                                {{trans('project.ERRORS.require.budget_comment')}}
                            </span>
                            <span ng-show="budgetForm.comment.$error.maxlength">
                               {{trans('project.ERRORS.maxlength.budget_comment')}}
                            </span>
                        </div>
                    </div>
                </td>
                <td class="text-center">
                    <span class="btn fa" ng-class="{'fa-edit':!budgetEdit || budgetEdit.id != b.id, 'fa-check':budgetEdit && budgetEdit.id == b.id}"
                          ng-click="switchEditBudget(b,budgetForm.$invalid)">
                    </span>
                </td>
                <td class="text-center">
                    <span class="btn fa" ng-class="{'text-danger fa-trash':!budgetEdit || budgetEdit.id != b.id, 'fa-undo':budgetEdit && budgetEdit.id == b.id}"
                        ng-click="cancelEditBudget(b.id)">
                    </span>
                </td>
            </tr>
            <tr ng-if="budgetNew">
                <td>
                    <select class="form-control"
                            ng-model="budgetNew.budget_type_id" name="type"
                            ng-options="t.id as t.name for t in budgetTypes" required>
                        <option value="" disabled>{{trans("project.PLACES.budget_type")}}</option>
                    </select>
                    <div role="alert" class="error" ng-class="{'visible': errors.budget_type_id || budgetForm.type.$touched || budgetForm.$submitted}">
                        <span ng-show="budgetForm.type.$error.required">
                             {{trans('project.ERRORS.require.budget_type')}}
                        </span>
                    </div>
                </td>
                <td class="text-right">
                    <input ng-model="budgetNew.quantity" name="quantity" type="number" class="form-text"  ng-pattern="budgetRegex"  required />
                    <div role="alert" class="error" ng-class="{'visible': errors.quantity || budgetForm.quantity.$touched || budgetForm.$submitted}">
                        <span ng-show="budgetForm.quantity.$error.required || budgetForm.quantity.$error.pattern">
                            {{trans('project.ERRORS.require.quantity')}}
                        </span>
                    </div>
                </td>
                <td>
                    <input ng-model="budgetNew.comment" type="text" name="comment" class="form-text"
                           placeholder="{{trans("project.PLACES.budget_comment")}}"
                           ng-maxlength="100" required/>
                    <div role="alert" class="error" ng-class="{'visible':errors.quantity || budgetForm.comment.$touched || budgetForm.$submitted}">
                        <span ng-show="budgetForm.comment.$error.required">
                            {{trans('project.ERRORS.require.budget_comment')}}
                        </span>
                        <span ng-show="budgetForm.comment.$error.maxlength">
                             {{trans('project.ERRORS.maxlength.budget_comment')}}
                        </span>
                    </div>
                </td>
                <td class="text-center">
                    <span class="btn btn-link fa fa-check" ng-disabled="budgetForm.$invalid" ng-click="saveBudget(budgetForm.$invalid);"></span>
                </td>
                <td class="text-center">
                    <span span class="btn btn-link fa fa-undo" ng-click="cancelBudget()"></span>
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
<div class="alert alert-danger">
    {{trans("project.ALERTS.funds")}}
</div>
<form name="sponsorForm" class="table-responsive" style="position: relative">
    <div class="loader-content" ng-if="sponsors.loading"><div class="loader"></div></div>
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
                                          image-uri="/context/avatars"
                                          image-field="id"
                                          minlength="1"
                                          override-suggestions="true"
                                          description-field="location"
                                          input-class="form-text"  text-no-results="{{trans('layout.MENU.none')}}"
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
                        <input ng-model="sponsorInEdit.quantity" name="quantity" type="number"  ng-pattern="budgetRegex"  class="form-text" required />
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
                          <input type="text" class="form-text" uib-datepicker-popup="yyyy-MM-dd"
                                 ng-model="sponsorInEdit.sponsed_at" name="sponsed"
                                 is-open="sponsorInEdit.opened"
                                 show-button-bar="false"
                                 popup-placement="left"
                                 ng-required="true"
                                 placeholder="{{trans('project.PLACES.funds_date')}}"
                                 alt-input-formats="['M!/d!/yyyy']" />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default" ng-click="openSponsedDate(sponsorInEdit)"><i class="glyphicon glyphicon-calendar"></i></button>
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
                    <span class="btn fa" ng-class="{'fa-edit': !sponsorInEdit || sponsorInEdit.id != s.id, 'fa-check':sponsorInEdit.id == s.id}"
                          ng-click="switchEditSponsor(s, sponsorForm.$invalid)"></span>
                </td>
                <td class="text-center">
                    <span class="btn fa" ng-class="{'text-danger fa-trash':!sponsorInEdit || sponsorInEdit.id != s.id, 'fa-undo': sponsorInEdit.id == s.id}"
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
                                  image-uri="/context/avatars"
                                  image-field="id"
                                  minlength="1"
                                  description-field="location"
                                  override-suggestions="true"
                                  input-class="form-text"  text-no-results="{{trans('layout.MENU.none')}}"
                                      text-searching="{{trans('layout.MENU.searching')}}"/>
                    <div role="alert" class="error" ng-class="{'visible':sponsorForm.new_user.$touched || sponsorForm.$submitted}">
                        <span ng-show="sponsorForm.new_user.$error.required">
                            {{trans('project.ERRORS.require.sponsor')}}
                        </span>
                    </div>
                </td>
                <td class="text-right">
                    <input ng-model="sponsorNew.quantity" name="new_quantity" type="number"  ng-pattern="budgetRegex" class="form-text" required />
                    <div role="alert" class="error" ng-class="{'visible':sponsorForm.new_quantity.$touched || sponsorForm.$submitted}">
                        <span ng-show="sponsorForm.new_quantity.$error.required || sponsorForm.new_quantity.$error.pattern">
                            {{trans('project.ERRORS.require.quantity')}}
                        </span>
                    </div>
                </td>
                <td>
                    <span class="input-group">
                      <input type="text" class="form-text" uib-datepicker-popup="yyyy-MM-dd" name="finish"
                             ng-model="sponsorNew.sponsed_at" name="new_sponsed"
                             is-open="sponsorNew.opened"
                             datepicker-options="dateOptions"
                             ng-required="true"
                             placeholder="{{trans('project.PLACES.funds_date')}}"
                             alt-input-formats="['M!/d!/yyyy']" />
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default" ng-click="openSponsedDate(sponsorNew)"><i class="glyphicon glyphicon-calendar"></i></button>
                        </span>
                    </span>
                    <div role="alert" class="error" ng-class="{'visible':sponsorForm.new_sponsed.$touched || sponsorForm.$submitted}">
                        <span ng-show="sponsorForm.new_sponsed.$error.required">
                            {{trans('project.ERRORS.require.funds_date')}}
                        </span>
                    </div>
                </td>
                <td class="text-center">
                    <span class="btn fa fa-check" ng-disabled="sponsorForm.$invalid" ng-click="saveSponsor(sponsorForm.$invalid);"></span>
                </td>
                <td class="text-center">
                    <span span class="btn fa fa-undo" ng-click="cancelSponsor()"></span>
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

<div ng-if="sponsors && budgets" class="btn btn-block">
    <div ng-if="getTotal(sponsors) >= getTotal(budgets)" class="text-success">
        <span translate="budget.success" translate-values="{sum:getTotal(sponsors) - getTotal(budgets)}"></span>
    </div>
    <div ng-if="getTotal(sponsors) < getTotal(budgets)" class="text-danger">
        <span translate="budget.inprogress" translate-values="{sum: getTotal(budgets) - getTotal(sponsors)}"></span>
    </div>
</div>