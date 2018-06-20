@section('synopsis')
    <div class="row py-4 d-flex justify-content-between">
        <h3>
            @if($project->active == 1)
                <a href="/project/{{$project->id}}">{{$project->title}}</a>
            @else
                <a href="/admin/preparations/{{$project->id}}">{{$project->title}}</a>
            @endif
        </h3>

        <div class="tag tag-default {{strlen($project->recommendation) > 1 ? 'text-danger' : ($project->active == 3 ? 'text-success' : ($project->active ? '' : 'text-chocolate'))}}">
            <a href="/discover?genre={{$project->genre_id}}">{{$project->genre_name}}
                @if(!is_null($project->active))
                    <span class="tail">{{is_null($project->genres_cnt) ? $genres_cnt : $project->genres_cnt}}</span>
                @else
                    <span class="tail"></span>
                @endif
            </a>
        </div>

    </div>
    <div class="row media bg-light">
        <img src="/storage/projects/{{$project->id}}.jpg" class="align-self-start" />
        <div class="media-body d-flex flex-column justify-content-between px-3 py-3 small" style="height: 360px">
            <div>
                <label class="text-primary">{{trans("project.LABELS.lang")}}</label> :
                @if(is_null($project->lang))
                    <span translate="NONE"></span>
                @else
                    @foreach($project->lang as $lang)
                        <span class="text-info" title="{{$lang->code}}">{{$lang->name}}</span>
                        @if (!$loop->last)
                            <span>&nbsp;/&nbsp;</span>
                        @endif
                    @endforeach
                @endif
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-success">
                    <i class="fa fa-clock-o" aria-hidden="true"></i>
                    {!! trans("project.TAGS.duration", ['min' => $project->duration]) !!}
                </span>
                <span class="text-muted">
                     <i class="fa fa-map-marker" aria-hidden="true"></i>
                    <a href="/discover?city={{$project->city_id}}">{{$project->city_name}}&nbsp;({{$project->country}})</a>
                </span>
            </div>

            <div class="abridge" data-toggle="tooltip" data-placement="bottom" title="{{ $project->synopsis}}">{{ $project->synopsis}}</div>
            <div class="media">
                <img class="align-self-center rounded-circle img-fluid mr-1" src="/storage/avatars/{{$project->user_id}}.small.jpg" />
                <div class="media-body">
                    <a id="user" class="btn"  href="/profile/{{$project->user_id}}">
                        {{$project->user_id == auth()->id()?auth()->user()->username:$project->username}}
                    </a>
                </div>
            </div>
            <div id="project_status">
                @if($project->active == 3)
                    <div class="alert alert-success">{{trans('project.STATUS.completed')}}</div>
                @elseif($project->admin)
                    @if(!$project->active)
                        <div class="alert alert-danger">{{trans('project.STATUS.online')}}</div>
                    @elseif($project->active == 2)
                        <div class="alert alert-success">{{trans('project.STATUS.wait')}}</div>
                    @else
                        <div class="form-group">
                            <a href="/admin/preparations/{{$project->id}}" class="btn btn-primary">
                                {{trans("project.BUTTONS.edit")}}
                            </a>
                            <span class="btn btn-info" ng-click="finish()">
                                {{trans("project.BUTTONS.finish")}}
                            </span>
                        </div>
                    @endif
                @endif
            </div>
            <div class="row pb-3">
                <div class="col-lg-9 col-md-8 col-sm-6 text-muted d-flex flex-column justify-content-end">
                    <div>
                        {!!trans("project.TAGS.finish", ["date"=>date('Y-m-d', strtotime($project->finish_at))])!!}
                    </div>

                    @if(sizeof($project->team) > 0)
                        <div class="py-1">{!! trans("project.TAGS.team", ['cnt' => sizeof($project->team)]) !!}</div>
                    @endif
                    @if(sizeof($project->recruit) > 0)
                        <div class="pt-1">{!! trans("project.TAGS.recruitment", ['cnt' => $project->recruit->sum('quantity')]) !!}</div>
                    @elseif(!is_null($project->active))
                        <div class="text-danger">
                            {!! trans("project.TAGS.recruited") !!}
                        </div>
                    @endif
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 align-self-end">
                    @if($project->active == 1)
                    <div style="position: absolute;top:40%;width: 100%; margin-left: -15px" class="text-center small"
                         ng-class="{'text-primary': p.daterest > 7, 'text-warning' : p.daterest > 3 && p.daterest < 8, 'text-danger': p.daterest < 4}">
                        <span>{{trans('project.TAGS.rest', ['days'=>$project->daterest])}}</span>
                    </div>
                    @if($project->datediff > 0)
                    <round-progress
                            max="max"
                            current="{{$project->daterest*100/$project->datediff}}"
                            color="{{$project->daterest < 3 ? '#993e25' : ($project->daterest < 7 ? 'ae6892' : '#293a4f')}}"
                            bgcolor="#e6e6e6"
                            radius="100"
                            stroke="9"
                            semi="false"
                            rounded="false"
                            clockwise="true"
                            responsive="true"
                            duration="800"
                            animation="easeOutCubic"
                            animation-delay="0">

                    </round-progress>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@show