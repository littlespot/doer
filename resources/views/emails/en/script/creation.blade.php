@component('mail::message')
    <P>{{$user}} as the administrator of project {{$title}} has added script {{$oldlink}}, and at the same time, declare you as its author.</p>

    <p>If you have any objections or questions, please contact us as soon as possible.</p>

    <p>If you are not our member yet, keep the following link carefully to follow the progress of project.</p>

    @component('mail::button', ['url' => config('app.url').'/guest/'.urldecode($link)])
        {{$title}}
    @endcomponent

    <p>If you are one of us, simply login in and you can see more about this project.</p>
    {!! trans('email.team_footer') !!}

    {{trans('email.salutation')}},<br>
    {{ config('app.name') }}
@endcomponent