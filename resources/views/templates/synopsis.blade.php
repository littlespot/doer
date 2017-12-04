@section('synopsis')
    <div class="flex-rows">
        <div id="title" class="text-primary font-xl">{{$project->title}}</div>
        <div class="tag tag-default {{strlen($project->recommendation) > 1 ? 'text-important' : ($project->active == 3 ? 'text-success' : ($project->active ? '' : 'text-chocolate'))}}">
            <a href="/discover?genre={{$project->genre_id}}">{{$project->genre_name}} <span class="tail">{{is_null($genres_cnt) ? $project->genres_cnt : $genres_cnt}}</span></a>
        </div>
    </div>
    <br/>
    <div class="flex-rows">
        <div class="poster-panel">
            <img src="/context/projects/{{$project->id}}.jpg" />
        </div>
        <div class="info-panel flex-cols">
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
            <div class="flex-rows">
                <span class="text-chocolate">
                    <i class="fa fa-clock-o" aria-hidden="true"></i>
                    {!! trans("project.TAGS.duration", ['min' => $project->duration]) !!}
                </span>
                <span class="text-default">
                    <?php echo file_get_contents(public_path("/images/icons/location.svg")); ?>
                    <a href="/discover?city={{$project->city_id}}">{{$project->city_name}}&nbsp;({{$project->sortname}})</a>
                </span>
            </div>

            <div class="synopsis">{{ $project->synopsis }}</div>
            <div>
                <a class="inner" href="/profile/{{$project->user_id}}">
                    <img class="img-circle img-responsive" src="/context/avatars/{{$project->user_id}}.small.jpg" />
                </a>
                <a id="user" class="inner" href="/profile/{{$project->user_id}}">
                    {{$project->username}}
                </a>
            </div>
            <div style="padding-top: 10px;" id="project_status">
                @if($project->active == 3)
                    <div class="alert alert-success">{{trans('project.STATUS.completed')}}</div>
                @elseif($project->admin)
                    @if(!$project->active)
                        <div class="alert alert-danger">{{trans('project.STATUS.online')}}</div>
                    @elseif($project->active == 2)
                        <div class="alert alert-success">{{trans('project.STATUS.wait')}}</div>
                    @else
                        <div class="form-group">
                            <a href="/admin/projects/{{$project->id}}" class="btn btn-primary">
                                {{trans("project.BUTTONS.edit")}}
                            </a>
                            <span class="btn btn-info" ng-click="finish()">
                                {{trans("project.BUTTONS.finish")}}
                            </span>
                        </div>
                    @endif
                @endif
            </div>
            <div style="position: relative;display: table">
                <div class="text-default" style="display:table-cell; width: 100%; vertical-align: middle">
                    <div>
                        {!!trans("project.TAGS.finish", ["date"=>date('Y-m-d', strtotime($project->finish_at))])!!}
                    </div>

                    @if(sizeof($project->team) > 0)
                        <div>{!! trans("project.TAGS.team", ['cnt' => sizeof($project->team)]) !!}</div>
                    @endif
                    @if(sizeof($project->recruit) === 0)
                        <div class="text-danger">
                            {!! trans("project.TAGS.recruited") !!}
                        </div>
                    @else
                        <div>{!! trans("project.TAGS.recruitment", ['cnt' => $project->recruit->sum('quantity')]) !!}</div>
                    @endif
                </div>
                <div class="progress-content">
                    <div class="progress-text text-important">
                        <div class='{{$project->daterest > 7 ? "text-primary" : ($project->daterest > 3 ? "text-warning" : "text-danger") }}'
                             translate="project.TAGS.rest" translate-values="{days:{{$project->daterest}}}"></div>
                    </div>
                    <div class="progress-wrapper">
                        <div
                                round-progress
                                max="100"
                                current="{{(int)$project->daterest * 100/(int)$project->datediff}}"
                                color="#293a4f"
                                bgcolor="#e6e6e6"
                                radius="80"
                                semi="false"
                                rounded="false"
                                clockwise="true"
                                responsive="true"
                                stroke="9"
                                animation="easeOutCubic"
                                offset="inherit">
                        </div>
                    </div>
                </div>
            </div>
            <br/>
        </div>
    </div>
@show