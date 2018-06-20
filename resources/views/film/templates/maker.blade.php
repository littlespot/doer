<div maker="{{$position}}" film="{{$film->id}}">
    <div class="modal fade" id="deleteMakerModal" tabindex="-1" role="dialog" aria-labelledby="deleteMakerModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center" id="modal-body">
                    <div>{{trans('film.alert.delete_maker')}}
                        <span class="px-1 text-uppercase text-primary" ng-bind="makerToDelete.last_name"></span>
                        <span class="text-primary" ng-bind="makerToDelete.first_name"></span>
                    </div>
                </div>
                <div class="modal-footer d-flex px-5">
                    <button class="btn btn-danger mr-auto" type="button" data-dismiss="modal" >
                        {{trans("project.BUTTONS.cancel")}}
                    </button>
                    <button class="btn btn-primary" type="button" ng-click="makerDeleted('{{$film->id}}', '{{$position}}')" >
                        {{trans("project.BUTTONS.confirm")}}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteContactModal" tabindex="-1" role="dialog" aria-labelledby="deleteContactModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{trans('film.label.delete_contact')}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-5 py-3" id="modal-body">
                    <h6 ng-bind="contactToDelete.name"></h6>
                    <div class="alert alert-danger my-2">
                        {{trans('film.alert.delete_address')}}
                    </div>
                    <div class="well">
                        <div class="row">
                            <div class="col-md-4 col-sm-12 label-justified">
                                {{trans('personal.LABELS.country')}}
                            </div>
                            <div class="col-md-8 col-sm-12">
                                <span ng-bind="contactToDelete.country"></span>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-md-4 col-sm-12 label-justified">
                                {{trans('personal.LABELS.state')}}
                            </div>
                            <div class="col-md-8 col-sm-12">
                                <span ng-bind="contactToDelete.department"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-sm-12 label-justified">
                                {{trans('personal.LABELS.city')}}
                            </div>
                            <div class="col-md-8 col-sm-12">
                                <span ng-bind="contactToDelete.city"></span>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-md-4 col-sm-12 label-justified">
                                {{trans('personal.LABELS.postal')}}
                            </div>
                            <div class="col-md-8 col-sm-12">
                                <span ng-bind="contactToDelete.postal"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-sm-12 label-justified">
                                {{trans('personal.LABELS.address')}}
                            </div>
                            <div class="col-md-8 col-sm-12">
                                <span ng-bind="contactToDelete.address"></span>
                            </div>
                        </div>
                    </div>
                    <div class="text-danger" ng-bind="errors.contact.delete"></div>
                </div>
                <div class="modal-footer d-flex px-5">
                    <button class="btn btn-outline-danger mr-auto" type="button" data-dismiss="modal" >
                        {{trans("project.BUTTONS.cancel")}}
                    </button>
                    <button class="btn btn-primary" type="button" ng-click="contactDeleted()" >
                        {{trans("project.BUTTONS.confirm")}}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-lg" id="editContactModal" tabindex="-1" role="dialog" aria-labelledby="editContactModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{trans('film.label.editContact')}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-5 py-3" id="modal-body">
                    <div class="row py-2">
                        <div class="col-md-6 col-xs-12 input input--isao">
                            <input id="name_<%contactCopy.id%>" ng-model="contactCopy.name" class="input__field input__field--isao" />
                            <label class="input__label input__label--isao" for="name_<%contactCopy.id%>" data-content="{{trans('personal.PLACES.address_name')}}">
                                <span class="input__label-content input__label-content--isao">
                                    <sup class="text-danger">*</sup>
                                    <span ng-if="!errors.contact.name && contactCopy.name && contactCopy.name.length > 0 && contactCopy.name.length < 45">{{trans('personal.LABELS.address_name')}}</span>
                                    <span class="text-danger" ng-if="errors.contact.name">{{trans('personal.ERRORS.repeat_address_name')}}</span>
                                    <span class="text-danger" ng-if="!errors.contact.name && (!contactCopy.name || contactCopy.name.length == 0)">{{trans('personal.ERRORS.require_address_name')}}</span>
                                    <span class="text-danger" ng-if="!errors.contact.name && contactCopy.name && contactCopy.name.length > 44">{{trans('personal.ERRORS.maxlength_address_name', ['cnt'=>45])}}</span>
                                </span>
                            </label>
                        </div>
                        <div class="col-md-6 col-xs-12 input input--isao">
                            <input id="company_<%contactCopy.id%>" ng-model="contactCopy.company" class="input__field input__field--isao" />
                            <label class="input__label input__label--isao" for="company_<%contactCopy.id%>" data-content="{{trans('personal.LABELS.company')}}">
                                <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.company')}}</span>
                            </label>
                        </div>
                    </div>

                    <div class="row py-2">
                        <div class="col-lg-6 col-md-6 col-sm-12 input input--isao">
                            <select name="contact_country_id" ng-model="contactCopy.country_id" id="contact_country_id"
                                    ng-change="loadDepartmet(contactCopy)" class="input__field input__field--isao">
                                @foreach($countries->where('region', '<>', 1) as $country)
                                    <option value="{{$country->id}}" ng-selected="contactCopy.country_id=={{$country->id}}">{{$country->name}}</option>
                                @endforeach
                            </select>
                            <label class="input__label input__label--isao" for="nation_shootings" data-content="{{trans('personal.LABELS.country')}}">
                                <span class="input__label-content input__label-content--isao">
                                    <sup class="text-danger">*</sup>
                                    <span ng-class="{'text-danger':!contactCopy.country_id}">{{trans('personal.LABELS.country')}}</span>
                                </span>
                            </label>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 input input--isao">
                            <select class="input__field input__field--isao" ng-model="contactCopy.department_id" id="contact_department_id"
                                    ng-options="x.id as x.name for x in contactDepartments"
                                    ng-change="loadCity(contactCopy)"
                                    ng-disabled="disabled.depart || disabled.city">
                            </select>
                            <label class="input__label input__label--isao" for="nation_shootings" data-content="{{trans('personal.LABELS.state')}}">
                                <span class="input__label-content input__label-content--isao" ng-class="{'text-danger':!contactCopy.department_id}">
                                    <sup class="text-danger">*</sup>
                                    {{trans('personal.LABELS.state')}}
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="row py-2">
                        <div class="col-lg-6 col-md-6 col-sm-12 input input--isao">
                            <select class="input__field input__field--isao" ng-model="contactCopy.city_id" name="contact_city_id" id="contact_city_id"
                                    ng-options="c.id as c.name for c in contactCities">
                            </select>
                            <label class="input__label input__label--isao" for="nation_shootings" data-content="{{trans('personal.LABELS.city')}}">
                                <span class="input__label-content input__label-content--isao" ng-class="{'text-danger':!contactCopy.city_id}">
                                    <sup class="text-danger">*</sup>{{trans('personal.LABELS.city')}}
                                </span>
                            </label>
                        </div>
                        <div class="col-lg-6 col-md-6 input input--isao">
                            <input id="postal_<%contactCopy.id%>" ng-model="contactCopy.postal" class="input__field input__field--isao" />
                            <label class="input__label input__label--isao" for="postal_<%contactCopy.id%>" data-content="{{trans('personal.LABELS.postal')}}">
                                <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.postal')}}</span>
                            </label>
                        </div>
                    </div>
                    <div class="py-2 input input--isao">
                        <textarea id="address_<%contactCopy.id%>" ng-model="contactCopy.address" class="input__field input__field--isao"></textarea>
                        <label class="input__label input__label--isao" for="address_<%contactCopy.id%>" data-content="{{trans('personal.LABELS.address')}}">
                            <span class="input__label-content input__label-content--isao">
                                <sup class="text-danger">*</sup>
                                <span ng-if="contactCopy.address && contactCopy.address.length <= 200">{{trans('personal.LABELS.address')}}</span>
                                <span ng-if="!contactCopy.address || contactCopy.address.length > 200">{{trans('personal.ERRORS.maxlength_address', ['cnt'=>200])}}</span>
                            </span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer d-flex px-5">
                    <button class="btn btn-outline-danger mr-auto" type="button" data-dismiss="modal" >
                        {{trans("project.BUTTONS.cancel")}}
                    </button>
                    <button class="btn btn-danger" type="button" ng-click="saveContact()">
                        {{trans("film.buttons.save_new_address")}}
                    </button>
                    <button class="btn btn-primary" type="button" ng-click="validContact()">
                        {{trans("film.buttons.save_old_address")}}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-lg" id="editMakerModal" tabindex="-1" role="dialog" aria-labelledby="editMakerModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{trans('film.label.editMaker')}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-5 py-3" id="modal-body">
                    <div class="row">
                        <div class="col-12 input input--isao">
                            <select id="searchUser" name="user" class="input__field input__field--isao"
                                    ng-model="makerCopy.related_id" ng-options="m.id as (m.username + ' [' + m.location + '] ') for m in users">
                            </select>
                            <label class="input__label input__label--isao" for="searchmaker" data-content="{{trans('film.placeholder.search_user')}}">
                                <span class="input__label-content input__label-content--isao">{{trans('film.label.users')}}</span>
                            </label>
                        </div>
                    </div>
                    <div class="row py-2">
                        <div class="col-md-6 col-sm-12 text-primary">
                            <input id="lastName_<%makerCopy.id%>" name="last_name" class="input__field input__field--isao text-uppercase"
                                    ng-model="makerCopy.last_name"  />
                            <label class="input__label input__label--isao" for="searchmaker" data-content="{{trans('personal.LABELS.last_name')}}">
                                <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.last_name')}}</span>
                            </label>
                        </div>
                        <div class="col-md-6 col-sm-12 input input--isao">
                            <input id="firstName_<%makerCopy.id%>" name="first_name" class="input__field input__field--isao"
                                   ng-model="makerCopy.first_name"  />
                            <label class="input__label input__label--isao" for="searchmaker" data-content="{{trans('personal.LABELS.first_name')}}">
                                <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.first_name')}}</span>
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-12 input input--isao">
                            <select id="title_<%makerCopy.id%>" name="prefix" class="input__field input__field--isao"
                                    ng-model="makerCopy.prefix">
                                @foreach(trans('personal.TITLES') as $key=>$title)
                                    <option value="{{$key}}" ng-selected="makerCopy.prefix == '{{$key}}'">{{$title}}</option>
                                @endforeach
                            </select>
                            <label class="input__label input__label--isao" for="title_<%makerCopy.id%>" data-content="{{trans('personal.LABELS.title')}}">
                                <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.title')}}</span>
                            </label>
                        </div>
                        <div class="col-md-6 col-sm-12 input input--isao">
                            <input id="email_<%makerCopy.id%>" ng-model="makerCopy.email" class="input__field input__field--isao" />
                            <label class="input__label input__label--isao" for="email_<%makerCopy.id%>" data-content="{{trans('personal.LABELS.email')}}">
                                <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.email')}}</span>
                            </label>
                        </div>
                    </div>
                    <div class="row py-2">
                        <div class="col-lg-6 col-md-6 input input--isao">
                            <select id="nationality" class="input__field input__field--isao" ng-model="makerCopy.country_id">
                                @foreach($countries as $country)
                                    <option id="nation_{{$country->id}}" value="{{$country->id}}" ng-selected="makerCopy.country_id == {{$country->id}}">{{$country->name}}</option>
                                @endforeach
                            </select>
                            <label class="input__label input__label--isao" for="nationality_<%makerCopy.id%>" data-content="  {{trans('personal.LABELS.nationality')}}">
                                <span class="input__label-content input__label-content--isao">  {{trans('personal.LABELS.nationality')}}</span>
                            </label>
                        </div>
                        <div class="col-lg-6 col-md-6 input input--isao">
                            <select id="born_<%makerCopy.id%>" ng-model="makerCopy.born" class="input__field input__field--isao">
                                @for($year = date("Y"); $year > 1900; $year--)
                                    <option value="{{$year}}" ng-selected="makerCopy.born=={{$year}}">{{$year}}</option>
                                @endfor
                            </select>
                            <label class="input__label input__label--isao" for="born_<%makerCopy.id%>" data-content="{{trans('personal.LABELS.born')}}">
                                <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.born')}}</span>
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 input input--isao">
                            <input id="mobile_<%makerCopy.id%>" ng-model="makerCopy.mobile" class="input__field input__field--isao" />
                            <label class="input__label input__label--isao" for="mobile_<%makerCopy.id%>" data-content="{{trans('personal.LABELS.mobile')}}">
                                <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.mobile')}}</span>
                            </label>
                        </div>
                        <div class="col-lg-6 col-md-6 input input--isao">
                            <input id="fix_<%makerCopy.id%>" ng-model="makerCopy.tel" class="input__field input__field--isao" />
                            <label class="input__label input__label--isao" for="fix_<%makerCopy.id%>" data-content="{{trans('personal.LABELS.fix')}}">
                                <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.fix')}}</span>
                            </label>
                        </div>
                    </div>
                    <div class="py-2 input input--isao">
                        <input id="web_<%makerCopy.id%>" ng-model="makerCopy.web" class="input__field input__field--isao" />
                        <label class="input__label input__label--isao" for="web_<%makerCopy.id%>" data-content="{{trans('personal.LABELS.web')}}">
                            <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.web')}}</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer d-flex px-5">
                    <button class="btn btn-danger mr-auto" type="button" data-dismiss="modal" >
                        {{trans("project.BUTTONS.cancel")}}
                    </button>
                    <button class="btn btn-primary" type="button" ng-click="updateMaker()" >
                        {{trans("project.BUTTONS.confirm")}}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-lg" id="contactListModal" tabindex="-1" role="dialog" aria-labelledby="contactListModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{trans('film.label.another_address')}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-5" id="modal-body">
                    <div class="row">
                        <div ng-repeat="c in contacts" class="col-lg-6 col-md-12 py-3">
                            <div class="card border border-dark"  ng-class="{'bg-secondary':c.selected}" >
                                <div class="card-header d-flex">
                                    <div class="radio-inline">
                                        <input type="radio" ng-value="c.contact_id" name="selected_contact" id="contact_<%c.contact_id%>" ng-click="selectContact(c.contact_id)">
                                        <span ng-bind="c.name"></span>
                                    </div>
                                    <div class="btn fa" ng-class="{'fa-caret-up':c.viewed, 'fa-caret-down':!c.viewed}" ng-click="c.viewed = !c.viewed"></div>
                                </div>
                                <div class="card-body" ng-show="c.viewed">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-12 label-justified">
                                            {{trans('personal.LABELS.company')}}
                                        </div>
                                        <div class="col-md-8 col-sm-12">
                                            <span ng-bind="c.company"></span>
                                        </div>
                                    </div>
                                    <div class="row py-2">
                                        <div class="col-md-4 col-sm-12 label-justified">
                                            {{trans('personal.LABELS.country')}}
                                        </div>
                                        <div class="col-md-8 col-sm-12">
                                            <span ng-bind="c.country"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-sm-12 label-justified">
                                            {{trans('personal.LABELS.state')}}
                                        </div>
                                        <div class="col-md-8 col-sm-12">
                                            <span ng-bind="c.department"></span>
                                        </div>
                                    </div>
                                    <div class="row py-2">
                                        <div class="col-md-4 col-sm-12 label-justified">
                                            {{trans('personal.LABELS.city')}}
                                        </div>
                                        <div class="col-md-8 col-sm-12">
                                            <span ng-bind="c.city"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-sm-12 label-justified">
                                            {{trans('personal.LABELS.postal')}}
                                        </div>
                                        <div class="col-md-8 col-sm-12">
                                            <span ng-bind="c.postal"></span>
                                        </div>
                                    </div>
                                    <div class="row pt-2">
                                        <div class="col-md-4 col-sm-12 label-justified">
                                            {{trans('personal.LABELS.address')}}
                                        </div>
                                        <div class="col-md-8 col-sm-12">
                                            <span ng-bind="c.address"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-danger" ng-bind="errors.contact.another"></div>
                </div>
                <div class="modal-footer d-flex px-5">
                    <button class="btn btn-outline-danger mr-auto" type="button" data-dismiss="modal" >
                        {{trans("project.BUTTONS.cancel")}}
                    </button>
                    <button class="btn btn-primary" type="button" ng-click="contactChosen()" ng-disabled="(contacts | filter:{selected:true}:true).length == 0">
                        {{trans("project.BUTTONS.confirm")}}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-lg" id="makerListModal" tabindex="-1" role="dialog" aria-labelledby="makerListModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{trans('film.label.another_maker')}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body small" id="modal-body">
                    <div class="row">
                        <div ng-repeat="d in persons" class="col-lg-6 col-md-12">
                            <div class="card border border-dark m-3"  ng-class="{'bg-secondary':d.selected }">
                                <div class="card-header d-flex ">
                                    <div class="checkbox-inline mr-auto">
                                        <input type="checkbox" ng-model="d.selected" >
                                        <span ng-bind="d.last_name"></span><span class="pl-1" ng-bind="d.first_name"></span>
                                        <span ng-if="d.contact.company">(<i ng-bind="d.contact.company"></i>)</span>
                                    </div>
                                    <div class="btn fa" ng-class="{'fa-caret-down':!d.viewed, 'fa-caret-up':d.viewed}" ng-click="d.viewed = !d.viewed"></div>
                                </div>
                                <div class="card-body" ng-show="d.viewed">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-12 label-justified">
                                            {{trans('film.label.user')}}
                                        </div>
                                        <div class="col-md-8 col-sm-12">
                                            <a ng-if="d.username" href="/profile/<%d.related_id%>" target="_blank" ng-bind="d.username"></a>
                                        </div>
                                    </div>
                                    <div class="row py-2">
                                        <div class="col-md-4 label-justified">
                                            {{trans('personal.LABELS.title')}}
                                        </div>
                                        <div class="col-md-8 col-sm-12">
                                            <span ng-switch="d.prefix">
                                                <span ng-switch-when="mr">{{trans('personal.TITLES.mr')}}</span>
                                                <span ng-switch-when="ms">{{trans('personal.TITLES.ms')}}</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 label-justified">
                                            {{trans('personal.LABELS.email')}}
                                        </div>
                                        <div class="col-md-8 col-sm-12">
                                            <span ng-bind="d.email"></span>
                                        </div>
                                    </div>
                                    <div class="row py-2">
                                        <div class="col-md-4 label-justified">
                                            {{trans('personal.LABELS.nationality')}}
                                        </div>
                                        <div class="col-md-8 col-sm-12">
                                            <span ng-bind="d.country"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 label-justified">
                                            {{trans('personal.LABELS.born')}}
                                        </div>
                                        <div class="col-md-8 col-sm-12">
                                            <span ng-bind="d.born"></span>
                                        </div>
                                    </div>
                                    <div class="row py-2">
                                        <div class="col-md-4 label-justified">
                                            {{trans('personal.LABELS.mobile')}}
                                        </div>
                                        <div class="col-md-8 col-sm-12">
                                            <span ng-bind="d.mobile"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 label-justified">
                                            {{trans('personal.LABELS.fix')}}
                                        </div>
                                        <div class="col-md-8 col-sm-12">
                                            <span ng-bind="d.tel"></span>
                                        </div>
                                    </div>
                                    <div class="row py-2">
                                        <div class="col-md-4 col-sm-12 label-justified">
                                            {{trans('personal.LABELS.web')}}
                                        </div>
                                        <div class="col-md-8 col-sm-12">
                                            <a href="<%d.web%>" target="_blank" ng-bind="d.web"></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div ng-if="!persons">
                            @include('templates.empty')
                        </div>
                    </div>
                    <div class="text-danger" ng-bind="errors.maker.another"></div>
                </div>
                <div class="modal-footer d-flex px-5">
                    <button class="btn btn-outline-danger mr-auto" type="button" data-dismiss="modal" >
                        {{trans("project.BUTTONS.cancel")}}
                    </button>
                    <button class="btn btn-primary" type="button" ng-click="makerChosen('{{$film->id}}','{{$position}}')">
                        {{trans("project.BUTTONS.confirm")}}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bd-example-modal-lg" id="newMakerModal" tabindex="-1" role="dialog" aria-labelledby="newMakerModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{trans('film.label.add_maker')}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-5 py-3" id="modal-body" ng-init="allContacts = 0;" >
                    <div class="row">
                        <div class="col-sm-12 input input--isao">
                            <select class="input__field input__field--isao" id="newmaker_related"
                                    ng-model="newMaker.related_id" ng-options="m.id as (m.username + ' [' + m.location + '] ') for m in users"></select>
                            <label class="input__label input__label--isao" for="searchmaker" data-content="{{trans('film.label.user')}}">
                                <span class="input__label-content input__label-content--isao">{{trans('film.label.user')}}</span>
                            </label>
                        </div>
                    </div>
                    <div class="row py-2">
                        <div class="col-lg-6 col-md-6 input input--isao">
                            <input ng-model="newMaker.last_name" autofocus class="input__field input__field--isao text-uppercase" />
                            <label class="input__label input__label--isao" for="title_new" data-content="{{trans('personal.LABELS.last_name')}}">
                                <span class="input__label-content input__label-content--isao"><sup class="text-danger">*</sup>{{trans('personal.LABELS.last_name')}}</span>
                            </label>
                        </div>
                        <div class="col-lg-6 col-md-6 input input--isao">
                            <input ng-model="newMaker.first_name" class="input__field input__field--isao" />
                            <label class="input__label input__label--isao" for="email" data-content="{{trans('personal.LABELS.first_name')}}">
                                <span class="input__label-content input__label-content--isao"><sup class="text-danger">*</sup>{{trans('personal.LABELS.first_name')}}</span>
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 input input--isao">
                            <select id="title" ng-model="newMaker.prefix" class="input__field input__field--isao" >
                                @foreach(trans('personal.TITLES') as $key=>$title)
                                    <option value="{{$key}}">{{$title}}</option>
                                @endforeach
                            </select>
                            <label class="input__label input__label--isao" for="title_new" data-content="{{trans('personal.LABELS.title')}}">
                                <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.title')}}</span>
                            </label>
                        </div>
                        <div class="col-lg-6 col-md-6 input input--isao">
                            <input id="email" ng-model="newMaker.email" class="input__field input__field--isao" />
                            <label class="input__label input__label--isao" for="email" data-content="{{trans('personal.LABELS.email')}}">
                                <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.email')}}</span>
                            </label>
                        </div>
                    </div>
                    <div class="row py-2">
                        <div class="col-lg-6 col-md-6 input input--isao">
                            <select id="newMaker_nationality" class="input__field input__field--isao" ng-model="newMaker.country_id">
                                @foreach($countries as $country)
                                    <option id="nation_{{$country->id}}" value="{{$country->id}}">{{$country->name}}</option>
                                @endforeach
                            </select>
                            <label class="input__label input__label--isao" for="nationality" data-content="  {{trans('personal.LABELS.nationality')}}">
                                <span class="input__label-content input__label-content--isao">  {{trans('personal.LABELS.nationality')}}</span>
                            </label>
                        </div>
                        <div class="col-lg-6 col-md-6 input input--isao">
                            <select id="born" ng-model="newMaker.born" class="input__field input__field--isao">
                                @for($year = date("Y"); $year > 1900; $year--)
                                    <option value="{{$year}}">{{$year}}</option>
                                @endfor
                            </select>
                            <label class="input__label input__label--isao" for="born" data-content="{{trans('personal.LABELS.born')}}">
                                <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.born')}}</span>
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 input input--isao">
                            <input id="mobile" ng-model="newMaker.mobile" class="input__field input__field--isao" />
                            <label class="input__label input__label--isao" for="mobile_<%d.id%>" data-content="{{trans('personal.LABELS.mobile')}}">
                                <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.mobile')}}</span>
                            </label>
                        </div>
                        <div class="col-lg-6 col-md-6 input input--isao">
                            <input id="fix" ng-model="newMaker.tel" class="input__field input__field--isao" />
                            <label class="input__label input__label--isao" for="fix" data-content="{{trans('personal.LABELS.fix')}}">
                                <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.fix')}}</span>
                            </label>
                        </div>
                    </div>
                    <div class="py-2 input input--isao">
                        <input id="web" ng-model="newMaker.web" class="input__field input__field--isao" />
                        <label class="input__label input__label--isao" for="web" data-content="{{trans('personal.LABELS.web')}}">
                            <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.web')}}</span>
                        </label>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="btn" ng-class="{'btn-outline-primary':allContacts != 2, 'btn-primary':allContacts == 2}"
                              ng-show="!allContacts" ng-click="allContacts = 2">{{trans('film.label.add_address')}}</div>
                        <div class="btn"  ng-class="{'btn-outline-primary':allContacts != 1, 'btn-primary':allContacts == 1}"
                             ng-show="contacts.length > 0 && !allContacts" ng-click="allContacts = 1">{{trans('film.label.another_address')}}</div>
                    </div>
                    <div class="btn btn-block" ng-class="{'btn-outline-primary':allContacts, 'btn-primary': !allContacts}"
                         ng-show="allContacts" ng-click="newMaker.contact={};allContacts = 0">{{trans('film.label.without_address')}}</div>
                    <div ng-show="contacts.length > 0 && allContacts == 1" class="row">
                        <div ng-repeat="c in contacts" class="col-lg-6 col-md-12 py-3 small">
                            <div class="card border border-dark"  ng-class="{'bg-secondary':newMaker.contact.contact_id == c.contact_id}">
                                <div class="card-header d-flex">
                                    <div class="radio-inline">
                                        <input type="radio" ng-value="c.contact_id" name="newMaker_contact_id" ng-model="newMaker.contact.contact_id">
                                        <span ng-bind="c.name"></span>
                                    </div>
                                    <div class="btn fa" ng-class="{'fa-caret-up':c.viewed, 'fa-caret-down':!c.viewed}" ng-click="c.viewed = !c.viewed"></div>
                                </div>
                                <div class="card-body" ng-show="c.viewed">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-12 label-justified">
                                            {{trans('personal.LABELS.country')}}
                                        </div>
                                        <div class="col-md-8 col-sm-12">
                                            <span ng-bind="c.country"></span>
                                        </div>
                                    </div>
                                    <div class="row py-2">
                                        <div class="col-md-4 col-sm-12 label-justified">
                                            {{trans('personal.LABELS.state')}}
                                        </div>
                                        <div class="col-md-8 col-sm-12">
                                            <span ng-bind="c.department"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-sm-12 label-justified">
                                            {{trans('personal.LABELS.city')}}
                                        </div>
                                        <div class="col-md-8 col-sm-12">
                                            <span ng-bind="c.city"></span>
                                        </div>
                                    </div>
                                    <div class="row py-2">
                                        <div class="col-md-4 col-sm-12 label-justified">
                                            {{trans('personal.LABELS.postal')}}
                                        </div>
                                        <div class="col-md-8 col-sm-12">
                                            <span ng-bind="c.postal"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-sm-12 label-justified">
                                            {{trans('personal.LABELS.address')}}
                                        </div>
                                        <div class="col-md-8 col-sm-12">
                                            <span ng-bind="c.address"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div ng-show="allContacts == 2" class="mt-5">
                        <div class="row">
                            <div class="col-md-6 col-xs-12 input input--isao">
                                <input id="newContact_name" ng-model="newMaker.contact.name" class="input__field input__field--isao"/>
                                <label class="input__label input__label--isao" for="newContact_name" data-content="{{trans('personal.PLACES.address_name')}}">
                                    <span class="input__label-content input__label-content--isao">
                                        <sup class="text-danger">*</sup>
                                        <span ng-if="!errors.contact.name && newMaker.contact.name && newMaker.contact.name.length > 0 && newMaker.contact.name.length < 45">{{trans('personal.LABELS.address_name')}}</span>
                                        <span class="text-danger" ng-if="errors.contact.name">{{trans('personal.ERRORS.repeat_address_name')}}</span>
                                        <span class="text-danger" ng-if="!errors.contact.name && (!newMaker.contact.name || newMaker.contact.name.length == 0)">{{trans('personal.ERRORS.require_address_name')}}</span>
                                        <span class="text-danger" ng-if="!errors.contact.name && newMaker.contact.name && newMaker.contact.name.length > 44">{{trans('personal.ERRORS.maxlength_address_name', ['cnt'=>45])}}</span>
                                    </span>
                                </label>
                            </div>
                            <div class="col-md-6 col-xs-12 input input--isao">
                                <input id="newContact_company" ng-model="newMaker.contact.company" class="input__field input__field--isao" />
                                <label class="input__label input__label--isao" for="newContact_company" data-content="{{trans('personal.LABELS.company')}}">
                                    <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.company')}}</span>
                                </label>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-lg-6 col-md-6 col-sm-12 input input--isao">
                                <select id="newmaker_country_id" name="contact_country_id" ng-model="newMaker.contact.country_id"
                                        ng-change="loadDepartmet(newMaker.contact)" class="input__field input__field--isao">
                                    @foreach($countries as $country)
                                        <option value="{{$country->id}}">{{$country->name}}</option>
                                    @endforeach
                                </select>
                                <label class="input__label input__label--isao" for="nation_shootings" data-content="{{trans('personal.LABELS.country')}}">
                                    <span class="input__label-content input__label-content--isao" ng-class="{'text-danger':!newMaker.contact.country_id}">
                                         <sup class="text-danger">*</sup>{{trans('personal.LABELS.country')}}
                                    </span>
                                </label>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 input input--isao">
                                <select id="newmaker_depart_id" class="input__field input__field--isao" ng-model="newMaker.contact.department_id"
                                        ng-options="x.id as x.name for x in contactDepartments"
                                        ng-change="loadCity(newMaker.contact)"
                                        ng-disabled="disabled.depart || disabled.city">
                                </select>
                                <label class="input__label input__label--isao" for="nation_shootings" data-content="{{trans('personal.LABELS.state')}}">
                                    <span class="input__label-content input__label-content--isao" ng-class="{'text-danger':!newMaker.contact.department_id}">
                                         <sup class="text-danger">*</sup>{{trans('personal.LABELS.state')}}
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 input input--isao">
                                <select id="newmaker_city_id" class="input__field input__field--isao" ng-model="newMaker.contact.city_id" name="contact_city_id"
                                        ng-options="c.id as c.name for c in contactCities">
                                </select>
                                <label class="input__label input__label--isao" for="nation_shootings" data-content="{{trans('personal.LABELS.city')}}">
                                    <span class="input__label-content input__label-content--isao" ng-class="{'text-danger':!newMaker.contact.city_id}">
                                         <sup class="text-danger">*</sup>{{trans('personal.LABELS.city')}}
                                    </span>
                                </label>
                            </div>
                            <div class="col-lg-6 col-md-6 input input--isao">
                                <input id="postal" ng-model="newMaker.contact.postal" class="input__field input__field--isao" />
                                <label class="input__label input__label--isao" for="postal" data-content="{{trans('personal.LABELS.postal')}}">
                                    <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.postal')}}</span>
                                </label>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-12 input input--isao">
                                <textarea id="address" ng-model="newMaker.contact.address" class="input__field input__field--isao"></textarea>
                                <label class="input__label input__label--isao" for="address" data-content="{{trans('personal.LABELS.address')}}">
                                    <span class="input__label-content input__label-content--isao">
                                        <sup class="text-danger">*</sup>
                                        <span ng-if="newMaker.contact.address && newMaker.contact.address.length <= 200">{{trans('personal.LABELS.address')}}</span>
                                        <span class="text-danger" ng-if="!newMaker.contact.address || newMaker.contact.address.length > 200">{{trans('personal.ERRORS.maxlength_address', ['cnt'=>200])}}</span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer d-flex px-5">
                    <button class="btn btn-outline-danger mr-auto" type="button" data-dismiss="modal" >
                        {{trans("project.BUTTONS.cancel")}}
                    </button>
                    <button class="btn btn-primary" type="button" ng-click="makerCreated('{{$film->id}}','{{$position}}')" ng-disabled="!newMaker.last_name || !newMaker.first_name">
                        {{trans("project.BUTTONS.confirm")}}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <br/>
    <div ng-repeat="d in makers" class="py-2 px-4">
        <div class="d-flex border-secondary border-bottom" >
            <div class="mr-auto">
                <span ng-bind="d.last_name"></span>&nbsp;<span ng-bind="d.first_name"></span>
                <span ng-if="d.contact.company"> | <label ng-bind="d.contact.company"></label></span>
            </div>
            <div class="btn fa" ng-class="{'fa-caret-down':!d.viewed, 'fa-caret-up':d.viewed}" ng-click="viewMaker(d)"></div>
        </div>
        <div ng-hide="!d.viewed" class="small">
            <div class="row py-2">
                <div class="col-lg-2 col-md-4 col-sm-12 label-justified">
                    {{trans('film.label.user')}}
                </div>
                <div class="col-lg-10 col-md-8 col-sm-12">
                    <a ng-if="d.username" href="/profile/<%d.related_id%>" target="_blank" ng-bind="d.username"></a>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-2 col-md-3 label-justified">
                    {{trans('personal.LABELS.title')}}
                </div>
                <div class="col-lg-4 col-md-3 col-sm-12">
                    <span ng-switch="d.prefix">
                        <span ng-switch-when="mr">{{trans('personal.TITLES.mr')}}</span>
                        <span ng-switch-when="ms">{{trans('personal.TITLES.ms')}}</span>
                    </span>
                </div>
                <div class="col-lg-2 col-md-3 label-justified">
                    {{trans('personal.LABELS.email')}}
                </div>
                <div class="col-lg-4 col-md-3 col-sm-12">
                    <span ng-bind="d.email"></span>
                </div>
            </div>
            <div class="row py-2">
                <div class="col-lg-2 col-md-3 label-justified">
                    {{trans('personal.LABELS.nationality')}}
                </div>
                <div class="col-lg-4 col-md-3 col-sm-12">
                    <span ng-bind="d.country"></span>
                </div>
                <div class="col-lg-2 col-md-3 label-justified">
                    {{trans('personal.LABELS.born')}}
                </div>
                <div class="col-lg-4 col-md-3 col-sm-12">
                    <span ng-bind="d.born"></span>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-2 col-md-3 label-justified">
                    {{trans('personal.LABELS.mobile')}}
                </div>
                <div class="col-lg-4 col-md-3 col-sm-12">
                    <span ng-bind="d.mobile"></span>
                </div>
                <div class="col-lg-2 col-md-3 label-justified">
                    {{trans('personal.LABELS.fix')}}
                </div>
                <div class="col-lg-4 col-md-3 col-sm-12">
                    <span ng-bind="d.tel"></span>
                </div>
            </div>
            <div class="row py-2">
                <div class="col-lg-2 col-md-3 col-sm-12 label-justified">
                    {{trans('personal.LABELS.web')}}
                </div>
                <div class="col-lg-10 col-md-9 col-sm-12">
                    <a href="<%d.web%>" target="_blank" ng-bind="d.web"></a>
                </div>
            </div>
            <div class="d-flex">
                <div class="btn btn-sm btn-outline-danger mr-auto" ng-click="deleteMaker(d)">
                    {{trans('film.buttons.delete_'.$position)}}
                </div>
                <div class="btn btn-sm btn-primary" ng-click="editMaker(d);">
                    {{trans('film.buttons.edit_maker')}}
                </div>
            </div>
            <br/>
            <h6 class="mr-auto">{{trans('film.label.contact')}}</h6>
            <div class="row py-1">
                <div class="col-lg-2 col-md-3 col-sm-12 label-justified">
                    {{trans('personal.LABELS.address_name')}}
                </div>
                <div class="col-lg-4 col-md-3 col-sm-12">
                    <span ng-bind="d.contact.name"></span>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-12 label-justified">
                    {{trans('personal.LABELS.company')}}
                </div>
                <div class="col-lg-4 col-md-3 col-sm-12">
                    <span ng-bind="d.contact.company"></span>
                </div>
            </div>
            <div class="row  py-1">
                <div class="col-lg-2 col-md-3 col-sm-12 label-justified">
                    {{trans('personal.LABELS.country')}}
                </div>
                <div class="col-lg-4 col-md-3 col-sm-12">
                    <span ng-bind="d.contact.country"></span>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-12 label-justified">
                    {{trans('personal.LABELS.state')}}
                </div>
                <div class="col-lg-4 col-md-3 col-sm-12">
                    <span ng-bind="d.contact.department"></span>
                </div>
            </div>
            <div class="row  py-1">
                <div class="col-lg-2 col-md-3 col-sm-12 label-justified">
                    {{trans('personal.LABELS.city')}}
                </div>
                <div class="col-lg-4 col-md-3 col-sm-12">
                    <span ng-bind="d.contact.city"></span>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-12 label-justified">
                    {{trans('personal.LABELS.postal')}}
                </div>
                <div class="col-lg-4 col-md-3 col-sm-12">
                    <span ng-bind="d.contact.postal"></span>
                </div>
            </div>
            <div class="row  py-1">
                <div class="col-lg-2 col-md-3 col-sm-12 label-justified">
                    {{trans('personal.LABELS.address')}}
                </div>
                <div class="col-lg-10 col-md-9 col-sm-12">
                    <span ng-bind="d.contact.address"></span>
                </div>
            </div>
            <div class="d-flex">
                <div class="mr-auto">
                    <div ng-if="d.contact" class="btn btn-sm btn-outline-danger" ng-click="deleteContact(d)">
                        {{trans('film.label.delete_address')}}
                    </div>
                </div>
                <div class="btn btn-sm btn-outline-dark" ng-click="chooseContact(d)">
                    {{trans('film.label.another_address')}}
                </div>
                <div class="btn btn-sm btn-primary" ng-click="editContact(d);">
                    <span ng-if="d.contact">{{trans('film.label.edit_address')}}</span>
                    <span ng-if="!d.contact">{{trans('film.label.add_address')}}</span>
                </div>
            </div>
            <hr/>
        </div>
    </div>
</div>
