@component('mail::message')
# {{trans('email.project_welcome', ['name'=>$username])}}

{!! trans('email.author_declaration', ['email'=>Auth::user()->email, 'user'=>Auth::user()->username, 'project_link'=>config('app.url').'/project/'.$project->id,'project_title'=>$project->title, 'script_link'=>$script->link, 'script_title'=>$script->title]) !!}
{!! trans('email.right_declaration') !!}
{!! trans('email.outsider_line') !!}
@component('mail::button', ['url' => config('app.url').'/guest/'.urldecode($code)])
{{$project->title}}
@endcomponent
{!! trans('email.team_footer') !!}
{{trans('email.salutation')}}<br>
{{ config('app.name') }}
@endcomponent
