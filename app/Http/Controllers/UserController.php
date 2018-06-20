<?php

namespace Zoomov\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use DB;
use Zoomov\Outsiderauthor;
use Zoomov\ProjectTeam;
use Zoomov\User;
use Zoomov\Project;
use Mail;

class UserController extends Controller
{
    public function index(Request $request){
        $outers = $request->input('outers', 0);
        $users = User::join('cities', 'city_id', '=', 'cities.id')
            ->join('departments', 'department_id', '=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->selectRaw("users.id, username, concat(cities.name_".app()->getLocale().", '(', countries.name_".app()->getLocale().", ')')  as location, CONCAT('/profile/', users.id) as link, 0 as outsider");

        if(!$outers){
            return $users->get();
        }
        else if($outers == 1){
            return Outsiderauthor::where('user_id', Auth::id())
                ->selectRaw("outsiderauthors.id, outsiderauthors.name as username, email as location, outsiderauthors.link, 1 as outsider")
                ->union($users)->get();
        }
        else{
            $team = ProjectTeam::where('project_id', $request->project_id)
                ->join('users', 'user_id', '=', 'users.id')
                ->selectRaw("users.id, username, concat(cities.name_".app()->getLocale().", '(', countries.sortname, ')')  as location, CONCAT('/profile/', users.id) as link, 0 as outsider");

            return Outsiderauthor::where('user_id', Auth::id())
                ->selectRaw("outsiderauthors.id, outsiderauthors.name as username, email as location, outsiderauthors.link, 1 as outsider")
                ->union($team)->get();
        }
    }

    public function show($id)
    {
        return User::where('email', $id)->select('username', 'id', 'locale', 'city_id')->first();
    }

    public function store(Request $request){
        $email = $request->email;
        $author = User::where('email', $email)->selectRaw("id, username as name, CONCAT('/profile/', id) as link")->first();

        if(!$author){
            return Outsiderauthor::create([
                'id' => $this->uuid('o'),
                'name' => $request->name,
                'link' => $request->link,
                'email' => $email,
                'user_id' => auth()->id()
            ]);
        }
        else if($author->name == $request->name){
            return $author;
        }
        else{
            return Response(trans('project.ERRORS.unique.author'), 502);
        }
    }

    public function update($id, Request $request){
        $author = Outsiderauthor::find($id);
        if(is_null($author)) {
            return Response('NOT FOUND', 404);
        }

        if($author->user_id != Auth::id()){
            return Response('NOT AUTHORIZED', 501);
        }

        $changed = false;

        if($request->name != $author->name){
            $changed |= true;

            $author->name = $request->name;
        }

        if($author->email != $request->email){
            $changed |= true;

            $author->email = $request->email;
        }

        if($author->link != $request->link){
            $changed |= true;

            $author->link = $request->link;
        }

        if($changed){
            $author->save();
        }

        return $author;
    }

    public function contact(Request $request){
        $this->validate($request, [
            'first_name' =>'required|max:40',
            'last_name' =>'required|max:40',
            'address' => 'required|max:200',
            'city_id' => 'required',
            'mobile' => 'required_without:fix'
        ]);

        User::find(auth()->id())->update($request->only('title', 'first_name', 'last_name'));
        $fix = null;
        $mobile=null;
        $tel = $request->input('fix');
        if($tel['code'] && $tel['number']){
            $fix = $tel['code'].$tel['number'];
        }
        $tel = $request->input('mobile');
        if($tel['code'] && $tel['number']){
            $mobile = $tel['code'].$tel['number'];
        }
        $values = [
            'address'=>$request->input('address'),
            'city_id'=>str_replace('number:', '', $request->input('city_id')),
            'postal'=>$request->input('postal'),
            'fix' => $fix,
            'mobile' => $mobile
        ];
        $contact = DB::table('contacts')->where('user_id', auth()->id());
        if($contact){
            DB::table('contacts')->where('user_id', auth()->id())->update($values);
        }
        else{
            DB::table('contacts')->insert(array_add($values, 'user_id', auth()->id()));
        }

        return back()->with('anchor', 'contact');
    }

    public function projects()
    {
        return Project::where('user_id', auth()->id())->where('active',1)->select('id', 'title')->get();
    }

    public function sendMail($id, $content, $title)
    {
        $user = User::find($id);

        Mail::send('emails.'.$content, ['user' => $user], function($message) use ($user, $title)
        {
            $message->to($user->email, $user->username)->subject($title);
        });
    }
}
