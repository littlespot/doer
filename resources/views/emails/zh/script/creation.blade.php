@component('mail::message')
    <P>项目 《{{$title}}》 的管理者 {{$user}} 为项目添加了故事文本 {{$script}}，且声明您为文本的作者。</p>

    <p>若您对此一声明有疑问或意见，请尽快联系我们。</p>

    <p>如果您还不是ZOOMOV用户，请保存以下网址以便追踪项目的更新与进度。</p>

    @component('mail::button', ['url' => $url])
        {{$title}}
    @endcomponent

    <p>如果您已经是ZOOMOV用户，只需登录，在您的项目列表中点击，便可一览项目全貌，并进一步参与项目。</p>

    {!! trans('email.team_footer') !!}

    {{trans('email.salutation')}},<br>
    {{ config('app.name') }}
@endcomponent