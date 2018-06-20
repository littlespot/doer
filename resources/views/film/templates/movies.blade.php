<div class="py-3">
    <div class="card">
        <div class="card-header bg-primary d-flex text-white">
            <ul class="list-inline mr-auto">
                <li class="list-inline-item">
                    @if($movie->screenplay)
                        <span class="btn fa fa-file-pdf-o"></span>
                    @else
                        <span class="btn fa fa-film"></span>
                    @endif
                </li>
                @if($movie->silent)
                    <li class="list-inline-item">
                        <span class="btn fa fa-deaf"></span>
                    </li>
                @elseif($movie->attributes && $movie->attributes->music_original == 1)
                    <li class="list-inline-item">
                        <span class="btn fa fa-music"></span>
                    </li>
                @endif
                @if($movie->virgin)
                    <li class="list-inline-item">
                        <span class="btn fa fa-italic"></span>
                    </li>
                @endif
                <li class="list-inline-item">
                    <span class="btn fa fa-microphone{{$movie->mute || $movie->silent ? '-slash':''}}"></span>
                </li>
            </ul>
            <div>{{$movie->submitted ? '':trans('festival.MESSAGES.incompleted')}}</div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-3">
                    <img src="{{$movie->poster}}?{{time()}}" style="width: 100%">
                </div>
                <div class="col-6 px-3 d-flex flex-column film-banner">
                    <h5>{{$movie->title}}</h5>
                    <div class="py-1">
                        @foreach($movie->workers as $worker)
                            <span>{{$worker->last_name}} {{$worker->first_name}}</span>
                            @if (!$loop->last)
                                <span class="px-1">/</span>
                            @endif
                        @endforeach
                    </div>
                    <div class="mb-auto">
                        <span class="badge">{{$movie->country}}</span>
                        <span class="px-1">/</span>
                        @if(!$movie->screenplay)
                        <span class="badge">{{$movie->year}}</span>
                        <span class="px-1">/</span>
                        @endif
                        @foreach($movie->genres as $genre)<span class="badge badge-primary mx-1">{{$genre}}</span>@endforeach
                        @if(!$movie->screenplay)
                        <span class="px-1">/</span>
                        <span class="badge">
                            @if($movie->hour)
                                {{trans('film.label.minute', ['cnt'=>$movie->hour*60 + $movie->minute])}}
                            @elseif($movie->minute)
                                {{trans('film.label.minute', ['cnt'=>$movie->minute])}}
                            @elseif($movie->second)
                                {{trans('film.label.second', ['cnt'=>$movie->second])}}
                            @else
                                {{trans('film.label.hour', ['cnt'=>'?']).' '.trans('film.label.minute', ['cnt'=>'?']).' '.trans('film.label.second', ['cnt'=>'?'])}}
                            @endif
                        </span>
                            @endif
                    </div>
                    <div class="d-flex justify-content-between">
                        @if($movie->submitted)
                            <a href="/{{$movie->type}}s/{{$movie->id}}?step=1" class="btn btn-outline-primary">{{trans('festival.BUTTONS.go_edit')}}</a>
                            <a href="/{{$movie->type}}s/{{$movie->id}}" class="btn btn-outline-primary">{{trans('film.buttons.upload')}}</a>
                            @if($movie->uploaded)
                                <a href="/festivals/" class="btn btn-primary">{{trans('film.buttons.submit')}}</a>
                            @else
                                <div class="btn btn-outline-secondary" disabled="">{{trans('film.buttons.submit')}}</div>
                            @endif
                        @else
                            <a href="/{{$movie->type}}s/{{$movie->id}}" class="btn btn-outline-primary">{{trans('festival.BUTTONS.go_edit')}}</a>
                            <div class="btn btn-outline-secondary" disabled="">{{trans('film.buttons.upload')}}</div>
                            <div class="btn btn-outline-secondary" disabled="">{{trans('film.buttons.submit')}}</div>

                        @endif
                    </div>
                </div>
                <div class="col-3 px-5 border-left d-flex flex-column justify-content-between film-banner" ng-init="movies[{{$index}}].index=0">
                    <h5>{{trans('festival.HEADERS.honors')}}</h5>
                    <div ng-if="{{sizeof($movie->honors)}}>shown_count" class="text-center btn-link border-0" ng-class="{'disabled':movies[{{$index}}].index==0}" ng-click="downIndex({{$index}})">
                        <span class="fa fa-caret-up"></span>
                    </div>
                    <div>
                        @foreach($movie->honors as $k=>$honor)
                            <div class="py-1 {{$honor->rewarded ? 'text-primary' :'small'}}" ng-hide="{{$k}} >= movies[{{$index}}].index + shown_count || {{$k}} < movies[{{$index}}].index">
                                {{trans('festival.LABELS.session', ['cnt'=>$honor->session])}} {{$honor->festival}}
                            </div>
                        @endforeach
                    </div>
                    <div ng-if="{{sizeof($movie->honors)}}>shown_count" class="text-center btn-link border-0" ng-class="{'disabled':movies[{{$index}}].index + shown_count>={{sizeof($films)}}}"
                         ng-click="upIndex({{$index}}, {{sizeof($movie->honors)}})">
                        <span class="fa fa-caret-down"></span>
                    </div>
                </div>
            </div>
        </div>
        @if(sizeof($movie->inscriptions))
        <div class="mt-3 btn btn-block btn-white border-top" data-toggle="collapse" href="#entries_{{$index}}_{{$movie->id}}" role="button" aria-expanded="false" aria-controls="entries_{{$index}}_{{$movie->id}}">
            <span class="fa fa-caret-down"></span>
        </div>
        <div class="card-footer collapse" id="entries_{{$index}}_{{$movie->id}}">
            <table class="table table-sm table-strippled table-bordered">
                <thead>
                    <tr class="text-primary">
                        <td width="30%">
                            {{trans('film.header.festival')}}
                        </td>
                        <td width="30%">
                            {{trans('film.header.unit')}}
                        </td>
                        <td width="20%">
                            {{trans('film.header.due_at')}}
                        </td>
                        <td width="20%">
                            {{trans('film.header.entry_status')}}
                        </td>
                    </tr>
                </thead>
                @foreach($movie->inscriptions as $inscription)
                    <tr>
                        <td>
                            {{$inscription->festival}}
                        </td>
                        <td>
                            {{$inscription->unit_locale?:$inscription->unit}}
                        </td>
                        <td>
                            {{$inscription->due_at}}
                        </td>
                        <td>
                            @if($inscription->receipt)
                                @if(!$inscription->sent_at)
                                    {{trans('film.status.inscripted')}}
                                @elseif(!$inscription->received_at)
                                    {{trans('film.status.sent')}}
                                @elseif(!$inscription->accepted_at)
                                    {{trans('film.status.received')}}
                                @elseif(!array_key_exists($inscription->id, $movie->honors))
                                    {{trans('film.status.accepted')}}
                                @elseif(!$inscription->honors->rewarded)
                                    {{trans('film.status.honored', ['name'=>$inscription->honors->honor])}}
                                @else
                                    {{trans('film.status.rewarded', ['name'=>$inscription->honors->honor])}}
                                @endif
                            @elseif($inscription->payed)
                                {{trans('film.status.entried')}}
                            @else
                                <a href="/entry/{{$inscription->id}}" class="text-danger">{{trans('film.buttons.go_pay')}}</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
            <div class="btn btn-block btn-white border-top" data-toggle="collapse" href="#entries_{{$index}}_{{$movie->id}}" role="button" aria-expanded="false" aria-controls="entries_{{$index}}_{{$movie->id}}">
                <span class="fa fa-caret-up"></span>
            </div>
        </div>
        @endif
    </div>
</div>
