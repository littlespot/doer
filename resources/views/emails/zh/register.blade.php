@component('mail::message')
# Introduction

The body of your message.

@component('mail::button', ['url' => 'http://www.zoomov.active?uid=1234s&activation='.$code])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
