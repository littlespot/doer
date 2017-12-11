@extends('film.card')

@section('filmForm')
    <h4 class="header-slogan">{{trans('film.card.rights')}}</h4>
    <div class="alert alert-info" role="alert">
        <div>{!! trans('film.alert.history') !!}</div>
        <div>{!! trans('layout.ALERTS.compulsive') !!}</div>
    </div>
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon">
                 {{trans('film.label.music_rights')}}
            </span>
            <input type="checkbox"  name="music" value="1">
        </div>
    </div>
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon">
                 {{trans('film.label.film_rights')}}
            </span>
            <input type="checkbox"  name="film" value="1">
        </div>
    </div>
    <h6 class="header-slogan">{{trans('film.label.history_festival')}}</h6>
    <table class="table table-striped table-responsive">
        <thead>
            <tr>
                <th>{{trans('film.label.year')}}</th>
                <th>{{trans('film.label.event')}}</th>
                <th>{{trans('personal.LABELS.country')}}</th>
                <th>{{trans('personal.LABELS.city')}}</th>
                <th>{{trans('film.label.competition')}}</th>
                <th>{{trans('film.label.award')}}</th>
                <th width="20px"><button class="btn text-important fa fa-plus"></button> </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <select name="year">
                        @for($y = $year; $y > $year -100; $y--)
                            <option value="{{$y}}">{{$y}}</option>
                        @endfor
                    </select>
                </td>
                <td>
                    <input type="text" name="event[]" class="form-text">
                </td>
                <td>
                    <select name="country">
                    @foreach($countries as $country)
                        <option value="{{$country->id}}">{{$country->name}}</option>
                    @endforeach
                    </select>
                </td>
                <td>
                    <select name="city">
                        @foreach($countries as $country)
                            <option value="{{$country->id}}">{{$country->name}}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="checkbox" value="1" name="competition">
                </td>
                <td>
                    <input type="text" name="awards[]">
                </td>
                <td>
                    <button class="btn text-success fa fa-save"></button>
                    <button class="btn text-muted fa fa-undo"></button>
                </td>
            </tr>
        </tbody>
    </table>
    <hr/>
    <h6 class="header-slogan">{{trans('film.label.film_print')}}</h6>
    <table class="table table-striped table-responsive">
        <thead>
        <tr>
            <th>{{trans('film.label.channel')}}</th>
            <th>{{trans('film.label.name_tv')}}</th>
            <th>{{trans('personal.LABELS.country')}}</th>
            <th>{{trans('film.label.year')}}</th>
            <th width="20px"><button class="btn text-important fa fa-plus"></button> </th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <select name="channel">
                        @foreach(trans('film.channel') as $key=>$channel)
                            <option value="{{$key}}">{{$channel}}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" class="form-text">
                </td>
                <td>
                    <select name="country">
                        @foreach($countries as $country)
                            <option value="{{$country->id}}">{{$country->name}}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="year">
                        @for($y = $year; $y > $year -100; $y--)
                            <option value="{{$y}}">{{$y}}</option>
                        @endfor
                    </select>
                </td>
                <td>
                    <button class="btn text-success fa fa-save"></button>
                    <button class="btn text-muted fa fa-undo"></button>
                </td>
            </tr>
        </tbody>
    </table>
    <hr/>
    <h6 class="header-slogan">{{trans('film.label.video_copy')}}</h6>
    <table class="table table-striped table-responsive">
        <thead>
        <tr>
            <th>{{trans('film.label.program')}}</th>
            <th>{{trans('film.label.name_program')}}</th>
            <th>{{trans('personal.LABELS.country')}}</th>
            <th>{{trans('film.label.distributed')}}</th>
            <th>{{trans('film.label.contact')}}</th>
            <th>{{trans('film.label.year')}}</th>
            <th width="20px"><button class="btn text-important fa fa-plus"></button> </th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <select name="program">
                        @foreach(trans('film.program') as $key=>$program)
                            <option value="{{$key}}">{{$program}}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                   <input type="text" name="name" class="form-text">
                </td>
                <td>
                    <select name="country">
                        @foreach($countries as $country)
                            <option value="{{$country->id}}">{{$country->name}}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" name="distribution" class="form-text">
                </td>
                <td>
                    <input type="text" name="contact" class="form-text">
                </td>
                <td>
                    <select name="year">
                        @for($y = $year; $y > $year -100; $y--)
                            <option value="{{$y}}">{{$y}}</option>
                        @endfor
                    </select>
                </td>
                <td>
                    <button class="btn text-success fa fa-save"></button>
                    <button class="btn text-muted fa fa-undo"></button>
                </td>
            </tr>
        </tbody>
    </table>

    <hr/>
    <div class="text-right">
        <button class="btn btn-primary">{{trans('layout.BUTTONS.continue')}}</button>
    </div>
@endsection