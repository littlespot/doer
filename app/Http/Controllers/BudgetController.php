<?php

namespace Zoomov\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Zoomov\Budget;
use Zoomov\BudgetType;
use Zoomov\Event;
use Zoomov\Script;
use Zoomov\Sponsor;

class BudgetController extends EventRelatedController
{
    public function index()
    {
        $types  = BudgetType::select('id', 'name_'.Auth::user()->locale.' as name')->get();
        return \Response::json($types);
    }

    public function show($id, Request $request)
    {
        $project = $this->getProject($id);

        if(is_null($project)){
            return Response('NOT AUTHORIZED', 501);
        }

        $types  = BudgetType::select('id', 'name_'.Auth::user()->locale.' as name')->get();

        $budgets = Budget::where('project_id', $id)
            ->join('budget_types', 'budget_type_id', '=', 'budget_types.id')
            ->select('budgets.id', 'quantity', 'comment', 'budget_type_id', 'budget_types.name_'.Auth::user()->locale.' as name')
            ->get();

        $sponsors = Sponsor::where('project_id', $id)
            ->leftJoin('users', 'sponsors.user_id', '=', 'users.id')
            ->leftJoin('outsiderauthors', 'sponsors.user_id', '=', 'outsiderauthors.id')
            ->selectRaw('sponsors.id, quantity, IFNULL(username, IFNULL(outsiderauthors.name, sponsor_name)) as sponsor_name, sponsors.user_id, sponsed_at')
            ->get();

        $scripts = Script::where('project_id', $id)
            ->with('authors')
            ->selectRaw('scripts.id, link, title, description, scripts.created_at')
            ->get();

        return \Response::json(array("budgets"=>$budgets, "types"=>$types, "sponsors"=>$sponsors, "scripts"=>$scripts));
    }

    public function update($id, Request $request)
    {
        try {
            $budget = Budget::find($id);
            $project = $this->getProject($budget->project_id);

            if (is_null($project)) {
                return Response('NOT AUTHORIZED', 501);
            }

            $changed = 0;

            if ($budget->budget_type_id != $request->budget_type_id) {
                $changed = 1;
                $budget->budget_type_id = $request->budget_type_id;
            }

            if ($budget->quantity != $request->quantity) {
                $changed += 2;
                $budget->quantity = $request->quantity;
            }


            if ($budget->comment != $request->comment) {
                $changed += 4;
                $budget->comment = $request->comment;
            }

            if ($changed) {
                $budget->save();
                if ($project->active == 1) {
                    $changement = $this->getEvent($id, "b");

                    if($changed != 4){
                        $changement["title"] = BudgetType::find($request->budget_type_id)->name.= "=>".$request->quantity;
                        if($changed >= 5){
                            $changement["content"] = $request->comment;
                        }
                    }
                    else{
                        $changement["content"] = $request->comment;
                    }

                    DB::table('changements')->insert($changement);
                }
            }

            return $budget;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function store(Request $request)
    {
        $project = $this->getProject($request->project_id);

        if(is_null($project))
        {
            return Response('NOT AUTHORIZED', 501);
        }
        $request->validate([
            'budget_type_id' => 'required',
            'quantity' => 'required|min:1',
            'comment' =>'required',
        ]);
        try {
            $budget = Budget::create([
                'project_id' => $request->project_id,
                'budget_type_id' => $request->budget_type_id,
                'quantity' => $request->quantity,
                'comment' => $request->comment
            ]);

            if($project->active == 1){
                Event::create([
                    'project_id' => $request->project_id,
                    'user_id' => Auth::id(),
                    'username' => Auth::user()->username,
                    'title' => BudgetType::find($request->budget_type_id)->name.'=>'.$budget->quantity,
                    'content' => $budget->comment,
                    'type' => 'b',
                    'related_id' => $budget->id
                ]);
            }

            return $budget;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($id, Request $request)
    {
        try {
            $budget = Budget::find($id);
            $project = $this->getProject($budget->project_id);

            if (is_null($project)) {
                return Response('NOT AUTHORIZED', 501);
            }

            if($project->active == 1){
               $this->afterDelete($id,'b');
            }

            $budget->delete();
            return Response('OK', 200);
        }catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
