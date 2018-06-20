@component('mail::message')
# {{trans('email.project_welcome', ['name'=>$username])}}

{!! trans('email.team_declaration', ['email'=>auth()->user()->email, 'user'=>auth()->user()->username, 'title'=>$title, 'roles'=>$occupations]) !!}
{!! trans('email.right_declaration') !!}
{!! trans('email.outsider_line') !!}
@component('mail::button', ['url' => config('app.url').'/guest/'.urldecode($link)])
{{$title}}
@endcomponent
{!! trans('email.team_footer') !!}

{{trans('email.salutation')}}<br>
{{ config('app.name') }}
@endcomponent
