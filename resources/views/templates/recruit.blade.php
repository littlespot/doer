<div class="modal fade " id="recruitDeleteModal" tabindex="-1" role="dialog" aria-labelledby="recruitDeleteModalModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" id="modal-body">
                {{trans('project.MESSAGES.delete_recruit')}}
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" data-dismiss="modal" >
                    {{trans("project.BUTTONS.cancel")}}
                </button>
                <button class="btn btn-success" type="button" ng-click="recruitDeleted()">{{trans("project.BUTTONS.confirm")}}</button>
            </div>
        </div>
    </div>
</div>
<form id="recruitForm" name="recruitForm" class="my-3"  style="position: relative" novalidate>
    <div ng-if="submitted" class="alert alert-danger small">
        {{trans("project.ALERTS.recruitment")}}
    </div>
    <div class="row">
        <div class="col-sm-8">
            <div class="input input--isao">
                <select class="input__field input__field--isao" ng-model="newrecruit.occupation_id" name="occupation" class="form-control" required>
                    @foreach($occupations as $occupation)
                        <option id="opt_role_{{$occupation->id}}" value="{{$occupation->id}}">{{$occupation->name}}</option>
                    @endforeach
                </select>
                <label class="input__label input__label--isao" for="input-title" data-content="{{trans('project.PLACES.recruitment_role')}}">
                    <span class="input__label-content input__label-content--isao">
                        <i class="text-danger pr-1">*</i>
                        <span ng-show="newrecruit.occupation_id">{{trans('project.LABELS.recruitment_role')}}</span>
                        <span class="text-danger" ng-show="!newrecruit.occupation_id">
                            {{trans("project.ERRORS.require.recruitment_role")}}
                        </span>
                    </span>
                </label>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="d-flex">
                <div class="input input--isao">
                    <input type="number" name="quantity" min="1"
                           class="input__field input__field--isao" ng-model="newrecruit.quantity" required/>
                    <label class="input__label input__label--isao" for="input-title" data-content="{{trans('project.PLACES.recruitment_quantity')}}">
                        <span class="input__label-content input__label-content--isao">
                            <i class="text-danger pr-1">*</i>
                            <span ng-show="!recruitForm.quantity.$error.required && newrecruit.quantity > 0">{{trans("project.LABELS.recruitment_quantity")}}</span>
                            <span class="text-danger" ng-show="recruitForm.quantity.$error.required || newrecruit.quantity < 1">
                               {{trans("project.ERRORS.valid.quantity")}}
                           </span>
                        </span>
                    </label>
                </div>
                <span class="text-right" translate="application.person"></span>
            </div>
        </div>
    </div>
    <div class="input input--isao">
        <textarea ng-model="newrecruit.description" class="input__field input__field--isao" name="description"
                  rows="3" placeholder="{{trans("project.PLACES.recruitment_description")}}"
                  ng-maxlength="400" required></textarea>
        <label class="input__label input__label--isao" for="input-title" data-content="{{trans('project.PLACES.recruitment_description')}}">
            <span class="input__label-content input__label-content--isao">
                <i class="text-danger pr-1">*</i>
                <span ng-show="!recruitForm.description.$error.required && !recruitForm.description.$error.minlength && !recruitForm.description.$error.maxlength" >{{trans("project.LABELS.recruitment_description")}}</span>
                <span ng-show="recruitForm.description.$error.required" class="text-danger">
                    {{trans("project.ERRORS.require.recruitment_description")}}
                </span>
                <span ng-show="recruitForm.description.$error.maxlength" class="text-danger">
                    {{trans("project.ERRORS.maxlength.recruitment_description")}}
                </span>
            </span>
        </label>
    </div>
    <div class="text-right">
        <span class="btn btn-outline-danger fa fa-undo" ng-click="newrecruit={quantity:1};"></span>
        <span class="btn btn-success fa fa-plus" ng-disabled="recruitForm.$invalid"
              ng-click="addRecruit()"></span>
    </div>
    <div ng-if="recruit.loading" class="loader-content"><div class="loader"></div></div>
</form>
<hr/>
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
            <span class="btn text-info fa fa-edit" ng-click="switchEditRecruit(r)"></span>
        </div>
        <div ng-if="recruit.loading" class="loader-content"><div class="loader"></div></div>
    </div>
    <form name="recruit" ng-if="recruitInEdit.id == r.id" style="position: relative">
        <div class="row" >
            <div class="col-sm-8">
                <div class="input input--isao">
                    <select class="input__field input__field--isao"  ng-disabled="submitted" ng-model="recruitInEdit.occupation_id" required>
                        <option disabled="disabled" value="">{{trans('project.PLACES.recruitment_role')}}</option>
                        @foreach($occupations as $occupation)
                            <option ng-selected="recruitInEdit.occupation_id=={{$occupation->id}}" id="role_opt_{{$occupation->id}}" value="{{$occupation->id}}">{{$occupation->name}}</option>
                        @endforeach
                    </select>
                    <label class="input__label input__label--isao" for="input-title" data-content="{{trans('project.PLACES.recruitment_role')}}">
                        <span class="input__label-content input__label-content--isao">
                            <i class="text-danger pr-1">*</i>
                            <span ng-show="recruitInEdit.occupation_id">{{trans('project.LABELS.recruitment_role')}}</span>
                            <span ng-show="!recruitInEdit.occupation_id" class="text-danger">
                                 {{trans("project.ERRORS.require.recruitment_role")}}
                            </span>
                        </span>
                    </label>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="d-flex">
                    <div class="input input--isao">
                        <input type="number" name="quantity" ng-model="recruitInEdit.quantity" min="1" class="input__field input__field--isao" required/>
                        <label class="input__label input__label--isao" for="input-title" data-content="{{trans('project.PLACES.recruitment_quantity')}}">
                            <span class="input__label-content input__label-content--isao">
                                <i class="text-danger pr-1">*</i>
                                <span ng-show="!recruit.$error.required && recruitInEdit.quantity > 0">{{trans("project.LABELS.recruitment_quantity")}}</span>
                                <span ng-show="recruit.$error.required || recruitInEdit.quantity < 1" class="text-danger">
                                    {{trans("project.ERRORS.valid.quantity")}}
                                </span>
                            </span>
                        </label>
                    </div>
                    <span class="text-right" translate="application.person"></span>
                </div>
            </div>
        </div>
        <div class="input input--isao">
            <textarea ng-readonly="submitted" ng-model="recruitInEdit.description" class="input__field input__field--isao" name="description"
                      rows="3" placeholder="{{trans("project.PLACES.recruitment_description")}}"
                      ng-maxlength="400" required></textarea>
            <label class="input__label input__label--isao" for="input-title" data-content="{{trans('project.PLACES.recruitment_description')}}">
                <span class="input__label-content input__label-content--isao">
                    <i class="text-danger pr-1">*</i>
                    <span ng-show="!recruitForm.description.$error.required && !recruitForm.description.$error.minlength && !recruitForm.description.$error.maxlength">{{trans("project.LABELS.recruitment_description")}}</span>
                    <span ng-show="recruitForm.description.$error.required" class="text-danger">
                        {{trans("project.ERRORS.require.recruitment_description")}}
                    </span>
                    <span ng-show="recruitForm.description.$error.maxlength" class="text-danger">
                        {{trans("project.ERRORS.maxlength.recruitment_description")}}
                    </span>
                </span>
            </label>
        </div>
        <div class="text-right">
            <span class="btn btn-outline-danger fa fa-undo" ng-click="cancelEditRecruit()"></span>&nbsp;&nbsp;
            <span class="btn btn-success fa fa-check" ng-click="saveEditRecruit()"></span>
        </div>
        <div class="loader-content" ng-if="recruit.editing">
            <div class="loader"></div>
        </div>
    </form>
    <hr />
</div>