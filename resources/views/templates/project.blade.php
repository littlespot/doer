
<a ng-if="p.id" href="/project/<%p.id%>">
    <img class="card-img-top" src="/storage/projects/<%p.id%>.thumb.jpg" alt="<%p.title%>">
</a>
<div class="px-3 py-2 font-weight-bold">
    <a class="text-info" href="/project/<%p.id%>" ng-bind="p.title"></a>
</div>
<div class="px-3 synopsis" ng-if="p.id">
    <p class="small" ng-bind="p.synopsis"></p>
</div>
<div class="d-flex justify-content-between pt-2" ng-if="p.id">
    <div class="text-default small pl-3">{{trans("project.LABELS.update")}}<span ng-bind="p.updated_at|limitTo:10"></span></div>
    <div class="clip" style="margin-right: -0.4rem" ng-class="{'text-important':p.recommendation, 'text-chocolate': p.active == 0, 'text-success':p.active == 3}"><span ng-bind="p.genre_name"></span></div>
</div>
<div class="row" ng-if="p.id">
    <div class="col-lg-9 col-md-8 col-sm-6 small text-muted">
        <p class="px-3 pt-1">
            <a class="title" ng-if="p.username" href="/profile/<%p.user_id%>" ng-bind="p.username"></a>
            <a class="title" ng-if="!p.username" href="/profile/<%user.id%>" ng-bind="user.username"></a>
        </p>
        <p class="px-3">
            <i class="fa fa-clock-o" aria-hidden="true"></i>
            {{trans("project.LABELS.duration")}}: <span ng-bind="p.duration"></span>m
        </p>
        <div class="d-flex justify-content-sm-between pb-3 px-3">
            <div class="text-muted">
                <span class="fa fa-map-marker"></span>
                <span ng-bind="p.city_name"></span> (<span ng-bind="p.country"></span>)
            </div>
            <div ng-if="p.comments_cnt" class="text-default">
                <span class="fa fa-comment-o"></span>
                <a href="/project/<%p.id%>?tab=3" ng-bind="p.comments_cnt"></a>
            </div>
            <div ng-if="p.followers_cnt" class="text-default">
                <span class="glyphicon fa fa-bookmark<%p.myfollow ? '' : '-o' %>" ></span>
                <span ng-bind="p.followers_cnt"></span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6 align-self-end">
        <div style="position: absolute;top:40%;width: 100%; margin-left: -15px" class="text-center small"
             ng-class="{'text-primary': p.daterest > 7, 'text-warning' : p.daterest > 3 && p.daterest < 8, 'text-danger': p.daterest < 4}">
            <span translate="project.TAGS.rest" translate-values="{days:p.daterest}"></span>
        </div>
        <round-progress
                max="max"
                current="(p.daterest*100/p.datediff)"
                color="<% (p.daterest < 3) ? '#993e25' : (p.daterest < 7 ? 'ae6892' : '#293a4f') %>"
                bgcolor="#e6e6e6"
                radius="100"
                stroke="9"
                semi="false"
                rounded="false"
                clockwise="true"
                responsive="true"
                duration="800"
                animation="easeOutCubic"
                animation-delay="0">

        </round-progress>
    </div>
</div>