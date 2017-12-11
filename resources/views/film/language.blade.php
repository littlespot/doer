@extends('film.card')

@section('filmForm')
    <form id="time_form" name="timeForm" action="/film/production" method="post" ng-controller="filmCtrl" ng-init="init('{{$production}}','{{$shooting}}', '{{$dialog}}')">
        @include('film.form')
    <h4 class="header-slogan">{{trans('film.card.language')}}</h4>
    <div class="alert alert-info" role="alert">
        <div>{!! trans('film.alert.nation') !!}</div>
        <div>{!! trans('layout.ALERTS.compulsive') !!}</div>
    </div>
    <div class="form">
        <div class="form-group row">
            <label for="title_original" class="col-md-2 col-sm-3 col-xs-4 col-form-label text-right">
                {!!trans('film.label.nation_principal')!!}
            </label>
            <div class="col-md-8 col-sm-8 col-xs-8">
                <select id="nation_principal" name="country_id" class="form-control"
                        ng-model="principal" ng-init="principal = '{{$film->country_id}}'"
                        ng-change="changePrincipal()">
                    <option value="" disabled>{{trans('film.placeholder.principal')}}</option>
                    @foreach($countries as $key=>$country)
                        <option id="opt_country_{{$key}}" value="{{$key}}">{{$country}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-sm-1"></div>
        </div>
        <hr/>
        <div class="form-group row">
            <label for="title_original" class="col-md-2 col-sm-3 col-xs-4 col-form-label text-right">
                {{trans('film.label.nation_other')}}
            </label>
            <div class="col-md-8 col-sm-8 col-xs-8" id="block_production">
                <?php
                    $i = 0;
                    $size = sizeof($production);
                    $keys = $production->keys()->toArray();
                    while($i < $size){
                        $p = $production[$i];
                        echo '<select id="production_'.$i.'" name="production['.$i.']" class="form-control margin-bottom-md" ng-model="production['.$i.']" ng-change="changeProduction('.$i.')"><option value="">'.trans('film.placeholder.other').'</option> ';
                        foreach ($countries as $key=>$country){
                            echo '<option id="opt_country_'.$key.'" value='.$key.'>'.$country.'</option>';
                        }
                        echo '</select>';
                        $i++;
                    }

                    while($i < 5){
                        echo '<select id="production_'.$i.'" style="'.($i>1? 'display:none':'display:block').'" name="production['.$i.']" class="form-control margin-bottom-md" ng-init="production['.$i.'] = \'\'" ng-model="production['.$i.']" ng-change="changeProduction('.$i.')"><option value="">'.trans('film.placeholder.other').'</option> ';
                        foreach ($countries as $key=>$country){
                            echo '<option id="opt_country_'.$key.'" value='.$key.'>'.$country.'</option>';
                        }
                        echo '</select>';
                        $i++;
                    }

                    if($size < 5){
                        echo ' <div id="btn_production" class="text-right"><div class="btn btn-default" ng-click="addProduction()"><span class="fa fa-plus"></span></div></div>';
                    }
                    ?>
            </div>
            <div class="col-md-2 col-sm-1"></div>
        </div>
        <hr/>
        <div class="form-group row">
            <label for="shooting" class="col-md-2 col-sm-3 col-xs-4 col-form-label text-right">
                {{trans('film.label.nation_shooting')}}
            </label>
            <div class="col-md-8 col-sm-8 col-xs-8" id="block_shooter">
                <?php
                $i = 0;
                $size = sizeof($shooting);
                $keys = $shooting->keys()->toArray();
                while($i < $size){
                    $p = $shooting[$i];
                    echo '<select id="shooting_'.$i.'" name="shooting['.$i.']" class="form-control margin-bottom-md"  ng-model="shooting['.$i.']" ng-change="changeShooting('.$i.')"><option value="">'.trans('film.placeholder.shooting').'</option> ';
                    foreach ($countries as $key=>$country){
                        echo '<option id="opt_country_'.$key.'" value='.$key.'>'.$country.'</option>';
                    }
                    echo '</select>';
                    $i++;
                }

                while($i < 9){
                    echo '<select id="shooting_'.$i.'" style="'.($i>2? 'display:none':'display:block').'" name="shooting['.$i.']" class="form-control margin-bottom-md"  ng-init="shooting['.$i.'] = \'\'" ng-model="shooting['.$i.']" ng-change="changeShooting('.$i.')"><option value="">'.trans('film.placeholder.shooting').'</option> ';
                    foreach ($countries as $key=>$country){
                        echo '<option id="opt_country_'.$key.'" value='.$key.'>'.$country.'</option>';
                    }
                    echo '</select>';
                    $i++;
                }

                if($size < 9){
                    echo ' <div id="btn_shooting" class="text-right"><div class="btn btn-default" ng-click="addShooting()"><span class="fa fa-plus"></span></div></div>';
                }
                ?>
            </div>
            <div class="col-md-2 col-sm-1" title="{{trans('film.tip.location')}}">
                <span class="btn text-primary fa fa-question-circle"></span>
            </div>
        </div>
        <hr/>
        <div class="form-group row">
            <label for="dialogue" class="col-md-2 col-sm-3 col-xs-4 col-form-label text-right">
                {!! trans('film.label.dialogue') !!}
            </label>
            <div class="col-md-8 col-sm-8 col-xs-8">
                <div class="inline" data-toggle="buttons">
                    <div>
                        <input type="radio" name="sound" value="1" ng-click="changeSound(1)" {{!is_null($film->dialog) && $film->dialog > 0 ? "checked" : ''}}>
                        {{trans('film.label.has_dialog')}}
                    </div>
                    <div class="margin-left-md">
                        <input type="radio" name="sound" value="0" ng-click="changeSound(0)"  {{!is_null($film->dialog) && $film->dialog < 1 ? "checked" : ''}}>
                        {{trans('film.label.no_dialog')}}
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-1"></div>
        </div>
        <div class="form-group row">
            <label for="silent" class="col-md-2 col-sm-3 col-xs-4 col-form-label text-right">
                {!! trans('film.label.silent') !!}
            </label>
            <div class="col-md-8 col-sm-8 col-xs-8">
                <div class="inline" data-toggle="buttons">
                    <div>
                        <input type="radio" name="silent" value="1" {{!is_null($film->silent) && $film->silent > 0 ? "checked" : ''}}>
                        {{trans('layout.LABELS.yes')}}
                    </div>
                    <div class="margin-left-md">
                        <input type="radio" name="silent" value="0" {{!is_null($film->silent) && $film->silent < 1 ? "checked" : ''}}>
                        {{trans('layout.LABELS.no')}}
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-1"></div>
        </div>
        <hr/>
        <div id="block_lang" style="display:{{!is_null($film->dialog) && $film->dialog < 1 ? "none" : 'block'}}">
        <div class="form-group row">
            <label for="language1" class="col-md-2 col-sm-3 col-xs-4 col-form-label text-right">
                {{trans('film.label.dialogue_language')}}
            </label>
            <div class="col-md-8 col-sm-8 col-xs-8" >
                <?php
                $i = 0;
                $size = sizeof($dialog);
                $keys = $dialog->keys()->toArray();
                while($i < $size){
                    $p = $dialog[$i];
                    echo '<select id="dialog_'.$i.'" name="dialog['.$i.']" class="form-control margin-bottom-md"  ng-model="dialog['.$i.']" ng-change="changeDialog('.$i.')"><option value="">'.trans('film.placeholder.dialog').'</option> ';
                    foreach ($languages as $key=>$language){
                        echo'<option id="opt_dialog_'.$key.'" value='.$key.'>'.$language.'</option>';
                    }
                    echo '</select>';
                    $i++;
                }

                while($i < 3){
                    echo '<select id="dialog_'.$i.'" name="dialog['.$i.']" class="form-control margin-bottom-md" ng-init="dialog['.$i.'] = \'\'" ng-model="dialog['.$i.']" ng-change="changeDialog('.$i.')"><option value="">'.trans('film.placeholder.dialog').'</option> ';
                    foreach ($languages as $key=>$language){
                        echo '<option id="opt_dialog_'.$key.'" value='.$key.'>'.$language.'</option>';
                    }
                    echo '</select>';
                    $i++;
                }
                ?>
            </div>
            <div class="col-md-2 col-sm-1"></div>
        </div>
        <div class="form-group row">
            <div class="col-md-offset-2 col-md-8 col-sm-offset-3 col-sm-8 col-xs-offset-4 col-xs-8">
                <input type="text" name="language" value="{{$film->language}}" class="form-text" placeholder="{{trans('film.placeholder.other_lang')}}">
            </div>
            <div class="col-md-2 col-sm-1"></div>
        </div>
    </div>
    </div>
    <p class="text-right">
        <button class="btn btn-primary">{{trans('layout.BUTTONS.continue')}}</button>
    </p>
    </form>
@endsection
@section('script')
    <script src="/js/controllers/film/production.js"></script>
@endsection