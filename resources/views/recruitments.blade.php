@section('recruits')
    <style>
        .recruit-block{
            cursor: pointer;
            position: relative;
        }
        .recruit-block .overlay{
            display: block;
            background: #333;
            color:#fff;
            width: 100%;
            height: 100%;
        }
        .recruit-block:hover .overlay{

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
        }

        .recruit-applied{
            opacity: 0.5;
        }
    </style>
    <h4>{{trans("project.LABELS.recruitment")}}</h4>
    @foreach($project->recruit as $r)
        <div id="recruit_{{$r->id}}">
            <div class="recruit-block {{$r->applied ? 'recruit-applied': ''}}" >
                <div class="title">
                    {{$r->name}}
                    <span class="quantity">{{trans("project.TAGS.recruit", ["cnt"=>$r->quantity])}}</span>
                    @if($r->applied)
                        <span class="pull-right">
                                {{is_null($r->accepted) ? trans('project.STATUS.suspend') : ($r->accepted ? trans('project.STATUS.accepted') : trans('project.STATUS.refused'))}}
                            </span>
                    @else
                        <span class="pull-right" style="display: none">{{trans('project.STATUS.suspend')}}</span>
                    @endif
                </div>
                <div class="recruit-description">{!! $r->description !!}</div>
                @if(!$project->admin && $project->active && !$r->applied)
                    <div ng-hide="myapplication.recruit_id == '{{$r->id}}'" class="overlay" ng-click="openApplication('{{$r->id}}','{{$r->name}}');">
                        {{trans('project.LABELS.apply')}}
                    </div>
                @endif
            </div>
            <form id="applicationForm" name="applicationForm"
                  class="apply-form" ng-if="myapplication.recruit_id == '{{$r->id}}'" novalidate>
                <div class="title">{{trans("project.LABELS.application")}}</div>
                <h5>
                    {!! trans('project.TAGS.application', ["project"=>$project->title]) !!}<label ng-bind="myapplication.occupation"></label>
                </h5>
                <div ng-if="!myapplication.sending">
                        <textarea ng-model="myapplication.motivation" class="form-control" name="motivation"
                                  rows="5" placeholder="{{trans('project.PLACES.motivation')}}"
                                  ng-minlength="15" ng-maxlength="2000" required></textarea>
                    <div class="error" role="alert" ng-class="{'visible':applicationForm.motivation.$touched || applicationForm.$submitted}">
                           <span ng-show="applicationForm.motivation.$error.required">
                               {{trans('project.ERRORS.require.motivation')}}
                           </span>
                        <span ng-show="applicationForm.motivation.$error.minlength">
                                  {{trans('project.ERRORS.minlength.motivation', ['cnt'=>15])}}
                            </span>
                        <span ng-show="applicationForm.motivation.$error.maxlength">
                                {{trans('project.ERRORS.minlength.motivation', ['cnt'=>2000])}}
                            </span>
                    </div>
                    <div class="text-right">
                        <div class="btn btn-xs btn-default" ng-click="cancelApplication()">
                            <span class="fa fa-undo"></span>
                        </div>
                        <div class="btn btn-xs btn-primary" ng-disabled="applicationForm.$invalid" ng-click="sendApplication('{{$project->user_id}}')">
                            <span class="fa fa-paper-plane-o"></span>
                        </div>
                    </div>
                </div>
                <div ng-if="myapplication.sending" class="loader-content">
                    <div class="loader"></div>
                </div>
            </form>
        </div>
    @endforeach

@show