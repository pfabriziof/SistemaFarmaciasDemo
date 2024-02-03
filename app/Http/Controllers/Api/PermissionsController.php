<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomPermission;
use App\Models\CustomRole;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionsController extends Controller
{
    public function index(Request $request){
        try{
            $searchTerm = $request->searchTerm;

            $data = CustomPermission::where([
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

            CustomPermission::create([
                'name' => $name,
                'guard_name' => 'web',

                'title' => $request->title,
            ]);
            
            return response()->json(['success'=>true, 'message' => 'Permiso creado correctamente!']);

        }catch(Exception $e){
            DB::rollback();
            return response()->json($e->getMessage(),500);
        }
    }

    public function show($id){
        try{
            $data = CustomRole::findOrFail($id);
            return response()->json($data->permissions, 200);

        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function update(Request $request, $id){
        try{
            $data = CustomPermission::findOrFail($id);
            $data->update([
                'title' => $request->title,
                'status'  => $request->status,
            ]);

            return response()->json(['success'=>true, 'message' => 'Permiso actualizado correctamente!']);
            
        }catch(Exception $e){
            DB::rollback();
            return response()->json($e->getMessage(),500);
        }
    }

    public function destroy($id){
        try{
            $data = CustomPermission::findOrFail($id);
            $data->customRemove();
            
            return response()->json(["success"=>true], 200);

        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function assignPermissionsToRole(Request $request, $id){
        try{
            $data = CustomRole::find($id);
            $data->syncPermissions($request->permissions);
          
            return response()->json([
                'success'=>true, 'message' => 'Permisos asignados correctamente!',
                'user_data' => auth()->user(),
                'permissions' => auth()->user()->role->permissions
            ]);
        }
        catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
}