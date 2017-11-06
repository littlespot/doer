@component('mail::message')
# {{trans('email.project_welcome', ['name'=>$username])}}

{!! trans('email.team_declaration', ['user'=>$username, 'title'=>$title, 'roles'=>$occupations]) !!}

@component('mail::button', ['url' => config('app.url').'/guest/'.urldecode($link)])
{{$title}}}
@endcomponent
{!! trans('email.team_footer') !!}

{{trans('email.salutation')}},<br>
{{ config('app.name') }}
@endcomponent
