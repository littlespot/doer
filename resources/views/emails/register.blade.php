@component('mail::message')
# {{trans('email.header_welcome', ['name'=>$user->username])}}

{!! trans('email.body_welcome') !!}

@component('mail::button', ['url' => config('app.url').'/activation/'.urldecode($code).'/'.md5($user->id)])
{{trans('email.button_activation')}}
@endcomponent

{{trans('email.salutation')}}<br>
{{ config('app.name') }}
@endcomponent
