<div class="modal fade" id="contactChangedModal" tabindex="-1" role="dialog" aria-labelledby="contactChangedModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" id="modal-body">
                <div>{{trans('personal.ALERTS.contact_changed')}}</div>
                <div class="alert alert-warning">{{trans('personal.ALERTS.page_jump')}}</div>
                <div>{{trans('personal.MESSAGES.page_jump')}}</div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" ng-click="cancelPageJump()" >
                    {{trans("project.BUTTONS.cancel")}}
                </button>
                <button class="btn btn-success" type="button" ng-click="confirmPageJump()" >
                    {{trans("project.BUTTONS.confirm")}}
                </button>
            </div>
        </div>
    </div>
</div>
<form id="contactForm" name="contactForm" action="/contact" method="POST">
    {{csrf_field()}}
    <input type="hidden" name="anchor" value="contact">

    <div class="card  border">
        <div class="card-header">
            <h6 class="card-title">{{trans('personal.LABELS.name')}}</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-2 col-xs-12 input input--isao">
                    <select name="prefix" class="input__field input__field--isao" id="prefix" ng-model="contact.prefix" ng-init="contact.prefix='{{$contact['prefix']}}'" ng-disabled="!contact.edited" required>
                        <option value="">{{trans('personal.PLACES.title')}}</option>
                        @foreach(trans('personal.TITLES') as $key=>$title)
                            <option value="{{$key}}">{{$title}}</option>
                        @endforeach
                    </select>
                    <label class="input__label input__label--isao" for="prefix"
                           data-content="{{trans('personal.PLACES.title')}}">
                        <span class="input__label-content input__label-content--isao">
                            <i class="text-danger pr-1">*</i>
                            <span ng-if="!errors.prefix && !contactForm.prefix.$error.required">{{trans('personal.LABELS.title')}}</span>
                            <span class="text-danger" ng-if="contact.edited && (errors.prefix || contactForm.prefix.$error.required)">{{trans('personal.ERRORS.require_prefix')}}</span>
                        </span>
                    </label>
                </div>
                <div class="col-md-5 col-xs-12 input input--isao">
                    <input type="text" class="input__field input__field--isao" placeholder="{{trans('personal.PLACES.last_name')}}"
                           name="last_name" id="last_name"
                           ng-model="contact.last_name" ng-init="contact.last_name='{{$contact['last_name']}}'" ng-disabled="!contact.edited"
                           required>
                    <label class="input__label input__label--isao" for="last_name"
                           data-content="{{trans('personal.PLACES.last_name')}}">
                        <span class="input__label-content input__label-content--isao">
                            <i class="text-danger pr-1">*</i>
                            <span ng-if="!errors.last_name && !contactForm.last_name.$error.required">{{trans('personal.LABELS.last_name')}}</span>
                            <span class="text-danger" ng-if="contact.edited && (errors.last_name || contactForm.last_name.$error.required)">{{trans('personal.ERRORS.require_name')}}</span>
                        </span>
                    </label>
                </div>
                <div class="col-md-5 col-xs-12">
                    <input type="text" class="input__field input__field--isao" placeholder="{{trans('personal.PLACES.first_name')}}"
                           name="first_name" id="first_name"
                           ng-model="contact.first_name" ng-init="contact.first_name='{{$contact['first_name']}}'" ng-disabled="!contact.edited"
                           required>
                    <label class="input__label input__label--isao" for="first_name"
                           data-content="{{trans('personal.PLACES.first_name')}}">
                        <span class="input__label-content input__label-content--isao">
                            <i class="text-danger pr-1">*</i>
                            <span ng-if="!errors.first_name && !contactForm.first_name.$error.required">{{trans('personal.LABELS.first_name')}}</span>
                            <span class="text-danger" ng-if="contact.edited && (errors.first_name || contactForm.first_name.$error.required)">{{trans('personal.ERRORS.require_name')}}</span>
                        </span>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <br/>
    <div class="card-deck">
        <div class="card border">
            <div class="card-header">
                <h6 class="card-title">{{trans('personal.LABELS.fix')}}</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-xs-12 input input--isao">
                        <select name="fix_code" class="input__field input__field--isao" id="fix_code"
                                ng-model="contact.fix_code"  ng-init="contact.fix_code = '{{$contact["fix_code"]}}'"
                                ng-disabled="!contact.edited" >
                            <option  title="{{trans('personal.LABELS.country_code')}}" disabled>{{trans('personal.LABELS.country_code')}}</option>
                            @foreach($countries as $country)
                                <option value="{{$country->phonecode}}" title="{{$country->name}}">+{{$country->phonecode}}</option>
                            @endforeach
                        </select>
                        <label class="input__label input__label--isao" for="fix_code" data-content="{{trans('personal.PLACES.country_code')}}">
                            <span class="input__label-content input__label-content--isao" id="label_fix_code">{{trans('personal.LABELS.country_code')}}</span>
                        </label>
                    </div>
                    <div class="col-md-6 col-xs-12 input input--isao">
                        <input type="text" class="input__field input__field--isao"  name="fix_number"
                               ng-model="contact.fix_number"  ng-init="contact.fix_number = '{{$contact["fix_number"]}}'"
                               ng-disabled="!contact.edited">
                        <label class="input__label input__label--isao" for="prefix"
                               data-content="{{trans('personal.PLACES.phone')}}">
                            <span class="input__label-content input__label-content--isao" >{{trans('personal.LABELS.fix_number')}}</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border">
            <div class="card-header">
                <h6 class="card-title">{{trans('personal.LABELS.mobile')}}</h6>
            </div>
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-6 col-xs-12 input input--isao">
                        <select name="mobile_code" class="input__field input__field--isao" id="mobile_code"
                                ng-model="contact.mobile_code" ng-init="contact.mobile_code = '{{$contact["mobile_code"]}}'"
                                ng-disabled="!contact.edited">
                            <option  title="{{trans('personal.LABELS.country_code')}}" disabled>{{trans('personal.LABELS.country_code')}}</option>
                            @foreach($countries as $country)
                                <option value="{{$country->phonecode}}" title="{{$country->name}}">+{{$country->phonecode}}</option>
                            @endforeach
                        </select>
                        <label class="input__label input__label--isao" for="prefix" data-content="{{trans('personal.PLACES.country_code')}}">
                            <span class="input__label-content input__label-content--isao" id="label_mobile_code">{{trans('personal.LABELS.country_code')}}</span>
                        </label>
                    </div>
                    <div class="col-md-6 col-xs-12 input input--isao">
                        <input type="text" class="input__field input__field--isao"  name="mobile_number"
                               ng-model="contact.mobile_number"  ng-init="contact.mobile_number = '{{$contact["mobile_number"]}}'"
                               ng-disabled="!contact.edited">
                        <label class="input__label input__label--isao" for="prefix" data-content="{{trans('personal.PLACES.phone')}}">
                            <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.mobile_number')}}</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="alert alert-warning">{{trans('personal.ALERTS.phone')}}</div>
    <br/>
    <div class="card border">
        <div class="card-header">
            <h6 class="card-title">{{trans('personal.LABELS.address')}}</h6>
        </div>
        <div class="card-body" ng-init="loadLocation({{$contact['city_id']}}, 'contact')">
            <div class="row" >
                <div class="col-md-4 col-xs-12 input input--isao">
                    <select class="input__field input__field--isao" ng-model="contact.country_id" id="country_id"
                            ng-disabled="!contact.edited"
                            ng-change="changeCountry(contact.country_id, 'contact')"
                            required>
                        <option value="0" disabled>{{trans('personal.PLACES.region')}}</option>
                        @foreach($countries as $country)
                            <option ng-selected="contact.country_id == '{{$country->id}}'" value="{{$country->id}}">{{$country->name}}</option>
                        @endforeach
                    </select>
                    <label class="input__label input__label--isao" for="country_id" data-content="{{trans('project.PLACES.country')}}">
                        <span class="input__label-content input__label-content--isao" ng-class="{'text-danger':contact.country_id == 0 || (errors && errors.city_id)}">
                            <i class="text-danger">*</i>
                            {{trans('project.LABELS.country')}}
                        </span>
                    </label>
                </div>
                <div class="col-md-4 col-xs-12 input input--isao">
                    <select class="input__field input__field--isao" ng-model="contact.department_id" id="department_id"
                            ng-options="d.id as d.name for d in contact.departments"
                            ng-change="changeDepartment(contact.department_id, 'contact')"
                            ng-disabled="!contact.edited || !contact.departments.length"
                            required>
                        <option value="" disabled>{{trans('project.PLACES.department')}}</option>
                    </select>
                    <label class="input__label input__label--isao" for="department_id" data-content="{{trans('project.PLACES.department')}}">
                        <span class="input__label-content input__label-content--isao" ng-class="{'text-danger':contact.department_id == 0 || (errors && errors.city_id)}">
                             <i class="text-danger">*</i>
                            {{trans('project.LABELS.department')}}
                        </span>
                    </label>
                </div>
                <div class="col-md-4 col-xs-12 input input--isao">
                    <select class="input__field input__field--isao" ng-model="contact.city_id" name="city_id" id="city_id"
                            ng-options="c.id as c.name for c in contact.cities"
                            ng-disabled="!contact.edited || !contact.cities.length"
                            required>
                        <option value="" disabled>{{trans('project.PLACES.city')}}</option>
                    </select>
                    <label class="input__label input__label--isao" for="city_id" data-content="{{trans('project.PLACES.city')}}">
                        <span class="input__label-content input__label-content--isao" ng-class="{'text-danger':contact.department_id == 0 || (errors && errors.city_id)}">
                             <i class="text-danger">*</i>
                            {{trans('project.LABELS.city')}}
                        </span>
                    </label>
                </div>
            </div>
            <br/>
            <div class="row">
                <div class="col-md-2 col-xs-12 input input--isao">

                    <input type="text" class="input__field input__field--isao" placeholder="{{trans('personal.PLACES.postal')}}" name="postal" value="{{$contact['postal']}}" ng-disabled="!contact.edited"/>
                    <label class="input__label input__label--isao" for="prefix" data-content="{{trans('personal.PLACES.postal')}}">
                        <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.postal')}}</span>
                    </label>
                </div>
                <div class="col-md-10 col-xs-12 input input--isao" >
                    <input type="text" class="input__field input__field--isao" name="address" id="address" ng-disabled="!contact.edited"
                        ng-model="contact.address" ng-init="contact.address='{{$contact['address']}}'" ng-maxlength="200"
                        required></input>
                    <label class="input__label input__label--isao" for="prefix" data-content="{{trans('personal.PLACES.address')}}">
                        <span class="input__label-content input__label-content--isao" ng-class="{'text-danger':contactForm.address.$error.required || contactForm.address.$error.maxlength || (errors && errors.address)}">
                             <i class="text-danger">*</i>
                            {{trans('personal.PLACES.address')}}
                        </span>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="my-2 text-right" >
        <input name="previous" value="{{url()->previous()}}" type="hidden" />
        <a ng-if="!contact.edited" href="{{url()->previous()}}" class="btn btn-outline-danger mr-3">{{trans('layout.BUTTONS.back')}}</a>
        <div ng-if="!contact.edited" class="btn btn-primary" ng-click="contact.edited = true">{{trans('layout.BUTTONS.edit')}}</div>
        <div ng-if="contact.edited" class="btn btn-outline-danger mr-3" ng-click="contact.edited = false">{{trans('layout.BUTTONS.cancel')}}</div>
        <div ng-if="contact.edited" class="btn btn-primary" ng-click="changeContact();">{{trans('layout.BUTTONS.submit')}}</div>
    </div>
</form>
