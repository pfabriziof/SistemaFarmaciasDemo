<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomRole;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserRolesController extends Controller
{
    public function index(Request $request){
        try{
            $searchTerm = $request->searchTerm;

            $data = CustomRole::where([
                ['active', 1],
            ]);

            if($searchTerm != ''){
                $data = $data->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('title', 'like', "%{$searchTerm}%");
                });
            }

            return $data->orderBy('id','asc')->paginate($request->perPage);

        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function store(Request $request){
        try{
            if($request->name){
                $name = strtolower($request->name);
                $name = str_replace(' ', '_', $name);
                
            }else{
                $name = strtolower($request->title);
                $name = str_replace(' ', '_', $name);
            }

            CustomRole::allowedToSave($name);
            CustomRole::create([
                'name' => $name,
                'guard_name' => 'web',

                'title' => $request->title,
            ]);
            
            return response()->json(['success'=>true, 'message' => 'Rol de usuario creado correctamente!']);

        }catch(Exception $e){
            DB::rollback();
            return response()->json($e->getMessage(),500);
        }
    }

    public function update(Request $request, $id){
        try{
            $data = CustomRole::findOrFail($id);
            $data->update([
                'title' => $request->title,
                'description' => $request->description,
                'status'  => $request->status,
            ]);

            return response()->json(['success'=>true, 'message' => 'Rol de usuario actualizado correctamente!']);
            
        }catch(Exception $e){
            DB::rollback();
            return response()->json($e->getMessage(),500);
        }
    }

    public function destroy($id){
        try{            
            $data = CustomRole::findOrFail($id);
            $data->customRemove();
            
            return response()->json(["success"=>true], 200);

        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function userRolesCombo(){
        try{
            return CustomRole::where([
                ['status', 1],
                ['active', 1],
            ])->get();
            
        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
}