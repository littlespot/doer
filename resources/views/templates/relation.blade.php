<div class="relation-circle my{{$relation}}" id="relation_{{$user->id}}" ng-init="friends_cnt = '{{$friends_cnt}}'">

    <div title="<%'relations.{{$relation}}.fan' | translate%>" @if(!$admin) ng-click="changeRelation('{{$user->id}}', '{{$user->username}}', 2)"
            @endif>
        <div class="ifollow">{{$fans_cnt}}</div>
    </div>
    <div title="<%'relations.{{$relation}}.idol' | translate%>">
        <div id="profileIdol" class="followme my{{$relation}}">
            {{$idols_cnt}}
        </div>
    </div>
</div>
