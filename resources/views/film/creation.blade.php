@extends('layouts.zoomov')

@section('content')
    <div ng-controller="filmCtrl" ng-init="loaded()">
        <div class="container my-5">
            <div class="row pt-5">
                <div class="col-lg-2"></div>
                <div class="col-lg-8 col-md-12">
                    @include('film.templates.creation_'.app()->getLocale())
                </div>
                <div class="col-lg-2"></div>
            </div>
        </div>
        <div class="bg-white py-5">
            <form class="container" name="filmForm" action="/archives" method="POST">
                {{csrf_field()}}
                <div class="form-group row">
                    <div class="col-4 ">
                    </div>
                    <div class="col-2 radio-inline">
                        <input type="radio" name="screenplay" value="0" {{old('screenplay')?'':'checked'}}>
                        <label>{{trans('film.header.movie')}}</label>
                    </div>
                    <div class="col-2 radio-inline">
                        <input type="radio" name="screenplay" value="1" {{old('screenplay')?'checked':''}}>
                        <label>{{trans('film.header.screenplay')}}</label>
                    </div>
                    <div class="col-4 ">
                    </div>
                </div>
                <br/>
                <div class="form-group row">
                    <div class="col-lg-2 col-md-push-0"></div>
                    <label for="title_original" class="col-lg-2 col-md-4 label-justified required">{{trans('film.label.title_original')}}</label>
                    <div class="col-lg-6 col-md-8 input input--isao">
                        <input class="input__field input__field--isao" type="text" id="title_original" name="title"
                               value="{{old('title')}}"
                               ng-maxlength="80"
                               required>
                        <label class="input__label input__label--isao" for="title_original"
                               ng-class="{'isao_error':filmForm.title_original.$error}"
                               data-error="{{trans('film.error.require_title')}}"
                               data-content="{{trans('film.placeholder.title_original')}}">
                            <span class="input__label-content input__label-content--isao">{{trans('film.placeholder.title_original')}}</span>
                        </label>
                    </div>
                    <div class="col-lg-2 col-md-push-0"></div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-2 col-md-push-0"></div>
                    <label for="title_latin" class="col-lg-2 col-md-4 label-justified">{{trans('film.label.title_latin')}}</label>
                    <div class="col-lg-6 col-md-8 input input--isao">
                        <input class="input__field input__field--isao" type="text" id="title_latin" name="title_latin"
                               value="{{old('title_latin')}}"
                               ng-maxlength="80">
                        <label class="input__label input__label--isao" for="title_latin"
                               ng-class="{'isao_error':filmForm.title_latin.$error}"
                               data-error="{{trans('film.error.maxlength_title', ['cnt'=>80])}}"
                               data-content="{{trans('film.placeholder.title_latin')}}">
                            <span class="input__label-content input__label-content--isao">{{trans('film.placeholder.title_latin')}}</span>
                        </label>
                    </div>
                    <div class="col-lg-2 col-md-push-0"></div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-2 col-md-push-0"></div>
                    <label for="title_inter" class="col-lg-2 col-md-4 label-justified">{{trans('film.label.title_inter')}}<sup> </sup></label>
                    <div class="col-lg-6 col-md-8 input input--isao">
                        <input class="input__field input__field--isao" type="text" id="title_inter" name="title_inter"
                               value="{{old('title_inter')}}"
                               ng-maxlength="80">
                        <label class="input__label input__label--isao" for="title_inter"
                               ng-class="{'isao_error':filmForm.title_inter.$error}"
                               data-error="{{trans('film.error.maxlength_title', ['cnt'=>80])}}"
                               data-content="{{trans('film.placeholder.title_inter')}}">
                            <span class="input__label-content input__label-content--isao">{{trans('film.placeholder.title_inter')}}</span>
                        </label>
                    </div>
                    <div class="col-lg-2 col-md-push-0"></div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-2 col-md-push-0"></div>
                    <div class="col-lg-6 col-md-8 checkbox-inline checkbox-primary">
                        <input name="agreement" type="checkbox" ng-model="agreed" required/>
                        <span class="pl-3 text-danger">{{trans('film.declaration.copyright')}}<sup>*</sup></span>
                    </div>
                    <div class="col-lg-2 col-md-push-0"></div>
                </div>
                <div class="mt-5 py-5">
                    <div class="row">
                        <div class="col-lg-2 col-md-push-0"></div>
                        <h5 class="col-lg-8 col-md-8">{{trans('film.header.user_contact')}}</h5>
                        <div class="col-lg-2 col-md-4"><a class="btn btn-link" href="/personal?anchor=contact" target="_blank">{{trans('personal.BUTTONS.change_contact')}}>></a></div>
                    </div>
                    @if($contact)
                    <div class="row">
                        <div class="col-lg-2 col-md-push-0"></div>
                        <label for="title_international" class="col-lg-2 col-md-4 label-justified">{{trans('personal.LABELS.name')}}</label>
                        <div class="col-lg-6 col-md-8">
                            <span class="text-uppercase">{{$contact->last_name}}</span><span class="pl-1">{{$contact->first_name}}</span>
                        </div>
                        <div class="col-lg-2 col-md-push-0"></div>
                    </div>

                    <div class="row">
                        <div class="col-lg-2 col-md-push-0"></div>
                        <label class="col-lg-2 col-md-4 label-justified">{{trans('personal.LABELS.email')}}</label>
                        <div class="col-lg-6 col-md-8">
                            {{auth()->user()->email}}
                        </div>
                        <div class="col-lg-2 col-md-push-0"></div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-push-0"></div>
                        <label class="col-lg-2 col-md-4 label-justified">{{trans('personal.LABELS.mobile')}}</label>
                        <div class="col-lg-6 col-md-8">
                            @if($contact->mobile_code && $contact->mobile_code)
                            <span>{{$contact->mobile_code}}</span><span class="pl-1">{{$contact->mobile_number}}</span>
                            @endif
                        </div>
                        <div class="col-lg-2 col-md-push-0"></div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-push-0"></div>
                        <label class="col-lg-2 col-md-4 label-justified">{{trans('personal.LABELS.fix')}}</label>
                        <div class="col-lg-6 col-md-8">
                            @if($contact->fix_code && $contact->fix_number)
                            <span>{{$contact->fix_code}}</span><span class="pl-1">{{$contact->fix_number}}</span>
                            @endif
                        </div>
                        <div class="col-lg-2 col-md-push-0"></div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-push-0"></div>
                        <label class="col-lg-2 col-md-4 label-justified">{{trans('personal.LABELS.city')}}</label>
                        <div class="col-lg-6 col-md-8">
                            <span>{{$contact->city}}</span><span class="pl-1">({{$contact->country}})</span>
                        </div>
                        <div class="col-lg-2 col-md-push-0"></div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-push-0"></div>
                        <label class="col-lg-2 col-md-4 label-justified">{{trans('personal.LABELS.address')}}</label>
                        <div class="col-lg-6 col-md-8">
                            <span>{{$contact->address}}</span><span class="pl-1">({{$contact->postal}})</span>
                        </div>
                        <div class="col-lg-2 col-md-push-0"></div>
                    </div>
                    @else
                        <div class="row">
                            <div class="col-lg-2 col-md-push-0"></div>
                            <div class="col-lg-8 col-md-10">
                               <div class="alert alert-danger">{{trans('film.alert.entry_contact')}}</div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="text-right">
                    <button class="btn btn-primary" ng-disabled="filmForm.$invalid">{{trans('layout.BUTTONS.continue')}}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('script')
    <script src="/js/controllers/film/general.js"></script>
@endsection