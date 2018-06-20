@section('recruits')
    <style>
        .recruit-block{
            cursor: pointer;
            position: relative;
        }
        .recruit-block .overlay{
            position: absolute;
            top:0;
            left:0;
            background: #293a4f;
            color:#fff;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .recruit-block:hover .overlay{
            z-index: 7;
        }

        .apply-form{
            border: 2px solid #293a4f;
            padding: 5px 10px 5px 10px;
            position: relative;
        }

        .apply-form>.title{
            background: #fff;
            margin:0;
            padding: 0 5px;
            position: absolute;
            top:-15px;
            color:#999
        }

        .recruit-applied{
            opacity: 0.5;
        }
    </style>
    <h4>{{trans("project.LABELS.recruitment")}}</h4>
    @foreach($project->recruit as $r)
        <div id="recruit_{{$r->id}}" class="py-2">
            <div class="recruit-block {{$r->applied ? 'recruit-applied': ''}}" >
                <div class="d-flex justify-content-between">
                    <span class="text-primary">{{$r->name}}</span>
                    <span class="text-danger">{{trans("project.TAGS.recruit", ["cnt"=>$r->quantity])}}</span>

                </div>
                <div class="recruit-description">{!! $r->description !!}</div>
                @if(!$project->admin && $project->active && !$r->applied)
                    <div ng-hide="myapplication.recruit_id == '{{$r->id}}'"
                         class="overlay text-center d-flex flex-column justify-content-center" ng-click="openApplication('{{$r->id}}','{{$r->name}}');">
                        <div>{{trans('project.LABELS.apply')}}</div>
                    </div>
                @endif
            </div>
            <div class="text-muted text-right small">
                @if($r->applied)
                    {{is_null($r->accepted) ? trans('project.STATUS.suspend') : ($r->accepted ? trans('project.STATUS.accepted') : trans('project.STATUS.refused'))}}
                @else
                    <span id="recruitStatus_{{$r->id}}" style="display: none">{{trans('project.STATUS.suspend')}}</span>
                @endif
            </div>
            <form id="applicationForm" name="applicationForm"
                  class="apply-form mt-3" ng-if="myapplication.recruit_id == '{{$r->id}}'" novalidate>
                <div class="title">{{trans("project.LABELS.application")}}</div>
                <div class="pt-1">
                    {!! trans('project.TAGS.application', ["project"=>$project->title]) !!}<label ng-bind="myapplication.occupation"></label>
                </div>
                <div ng-if="!myapplication.sending" class="input input--isao" style="width: 100%;">
                    <textarea ng-model="myapplication.motivation" class="input__field input__field--isao" name="motivation"
                              rows="5" placeholder="{{trans('project.PLACES.motivation')}}"
                              ng-minlength="15" ng-maxlength="2000" required></textarea>
                    <label class="input__label input__label--isao" for="prefix" data-content=" {{trans('project.ERRORS.require.motivation')}}">
                        <span class="input__label-content input__label-content--isao">
                            <span ng-show="!applicationForm.motivation.$error.minlength && !applicationForm.motivation.$error.maxlength">
                                {{trans('project.ERRORS.require.motivation')}}
                            </span>
                             <span ng-show="applicationForm.motivation.$error.minlength" class="text-danger">
                                  {{trans('project.ERRORS.minlength.motivation', ['cnt'=>15])}}
                            </span>
                            <span ng-show="applicationForm.motivation.$error.maxlength" class="text-danger">
                                {{trans('project.ERRORS.minlength.motivation', ['cnt'=>2000])}}
                            </span>
                        </span>
                    </label>
                    <div class="text-right">
                        <div class="btn btn-xs btn-outline-secondary" ng-click="cancelApplication()">
                            <span class="fa fa-undo"></span>
                        </div>
                        <button class="btn btn-xs btn-primary" ng-disabled="applicationForm.$invalid" ng-click="sendApplication('{{$project->user_id}}')">
                            <span class="fa fa-paper-plane-o"></span>
                        </button>
                    </div>
                </div>
                <div ng-if="myapplication.sending" class="loader-content">
                    <div class="loader"></div>
                </div>
            </form>
        </div>
        <hr/>
    @endforeach

@show