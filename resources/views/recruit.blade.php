<h4>{{trans('project.LABELS.recruitment')}}</h4>
<form id="recruitForm" name="recruitForm" class="margin-top-md" style="position: relative" novalidate>
    <div ng-if="submitted" class="alert alert-danger small">
        {{trans("project.ALERTS.recruitment")}}
    </div>
    <div class="row">
        <div class="col-sm-5">
            <select ng-model="newrecruit.occupation_id" name="occupation" class="form-control" required>
                <option disabled="disabled" value="">{{trans('project.PLACES.recruitment_role')}}</option>
                @foreach($occupations as $occupation)
                    <option id="opt_role_{{$occupation->id}}" value="{{$occupation->id}}">{{$occupation->name}}</option>
                @endforeach
            </select>
            <div role="alert" class="error" ng-class="{'visible':error.role}">
                <span ng-show="!newrecruit.occupation_id">
                    {{trans("project.ERRORS.require.recruitment_role")}}
                </span>
            </div>
        </div>
        <div class="col-sm-5">
            <input type="number" name="quantity" min="1"
                   class="form-control" ng-model="newrecruit.quantity" required/>
            <div role="alert" class="error" ng-class="{'visible':error.quantity}">
               <span ng-show="recruitForm.quantity.$error.required || newrecruit.quantity < 1">
                   {{trans("project.ERRORS.valid.quantity")}}
               </span>
            </div>
        </div>
        <div class="col-sm-2">
            <span class="text-right" translate="application.person"></span>
        </div>
    </div>
    <div>
        <textarea ng-model="newrecruit.description" class="form-control" name="description"
                  rows="3" placeholder="{{trans("project.PLACES.recruitment_description")}}"
                  ng-minlength="15" ng-maxlength="400" required></textarea>
        <div role="alert" class="error" ng-class="{'visible':error.description}">
            <span ng-show="recruitForm.description.$error.required">
                {{trans("project.ERRORS.require.recruitment_description")}}
            </span>
            <span ng-show="recruitForm.description.$error.minlength">
                {{trans("project.ERRORS.minlength.recruitment_description")}}
            </span>
            <span ng-show="recruitForm.description.$error.maxlength">
                {{trans("project.ERRORS.maxlength.recruitment_description")}}
            </span>
        </div>
    </div>
    <div class="text-right">
        <span class="btn btn-default fa fa-undo" ng-click="newrecruit={quantity:1};"></span>
        <span class="btn btn-primary fa fa-plus" ng-disabled="recruitForm.$invalid"
              ng-click="addRecruit()"></span>
    </div>
    <div ng-if="recruit.loading" class="loader-content"><div class="loader"></div></div>
</form>
<br/>
<div ng-repeat="r in recruit" id="recruit_<%r.id%>">
    <div ng-show="recruitInEdit.id != r.id" style="position:relative;">
        <div class="row" >
            <div class="col-sm-6">
                <span ng-bind="r.name"></span>
            </div>
            <div class="col-sm-5">
                <span ng-bind="r.quantity"></span>&nbsp;<span translate="application.person"></span>
            </div>
            <div class="col-sm-1 text-right" >
                <span class="text-danger fa fa-trash btn" ng-click="removeRecruit(r.id)"></span>
            </div>
        </div>
        <div ng-bind="r.description"></div>
        <div class="text-right">
            <span class="btn fa fa-edit" ng-click="switchEditRecruit(r)"></span>
        </div>
        <div ng-if="recruit.loading" class="loader-content"><div class="loader"></div></div>
    </div>
    <form name="recruit" ng-if="recruitInEdit.id == r.id" style="position: relative">
        <div class="row" >
            <div class="col-sm-7">
                <select ng-disabled="submitted" ng-model="recruitInEdit.occupation_id" class="form-control" required>
                    <option disabled="disabled" value="">{{trans('project.PLACES.recruitment_role')}}</option>
                    @foreach($occupations as $occupation)
                        <option ng-selected="recruitInEdit.occupation_id=={{$occupation->id}}" id="role_opt_{{$occupation->id}}" value="{{$occupation->id}}">{{$occupation->name}}</option>
                    @endforeach
                </select>
                <div role="alert" class="error" ng-class="{'visible':error.role}">
                    <span ng-show="!recruitInEdit.occupation_id">
                         {{trans("project.ERRORS.require.recruitment_role")}}
                    </span>
                </div>
            </div>
            <div class="col-sm-5">
                <input type="number" name="quantity" ng-model="recruitInEdit.quantity" min="1" class="form-control" required/>
                <div role="alert" class="error" ng-class="{'visible':recruit.quantity.$touched || recruit.$submitted}">
                    <span ng-show="recruit.$error.required || recruitInEdit.quantity < 1">
                        {{trans("project.ERRORS.valid.quantity")}}
                    </span>
                </div>
            </div>
        </div>
        <br/>
        <textarea ng-readonly="submitted" ng-model="recruitInEdit.description" class="form-control" name="description"
                  rows="3" placeholder="{{trans("project.PLACES.recruitment_description")}}"
                  ng-minlength="15" ng-maxlength="400" required></textarea>
        <div role="alert" class="error" ng-class="{'visible':recruit.description.$touched || recruit.$submitted}">
            <span ng-show="recruitForm.description.$error.required">
                {{trans("project.ERRORS.require.recruitment_description")}}
            </span>
                <span ng-show="recruitForm.description.$error.minlength">
                {{trans("project.ERRORS.minlength.recruitment_description")}}
            </span>
                <span ng-show="recruitForm.description.$error.maxlength">
                {{trans("project.ERRORS.maxlength.recruitment_description")}}
            </span>
        </div>
        <div class="text-right">
            <span class="btn fa fa-undo" ng-click="cancelEditRecruit()"></span>&nbsp;&nbsp;
            <span class="btn fa fa-check" ng-click="saveEditRecruit()"></span>
        </div>
        <div class="loader-content" ng-if="recruit.editing">
            <div class="loader"></div>
        </div>
    </form>
    <hr />
</div>