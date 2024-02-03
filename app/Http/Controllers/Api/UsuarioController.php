<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
{
    public function __construct(){
        // $this->middleware('role:superadmin|admin');
    }

    public function index(Request $request)
    {
        try{
            $authUser = auth('api')->user();
            $search_query = $request->search_query;
            $datos = User::where([
                ["id_sucursal", $authUser->id_sucursal],
            ]);

            if($search_query != ''){
                $datos = $datos->where(function ($query) use ($search_query) {
                    $query->where('name', 'like', "%{$search_query}%")
                        ->orWhere('email', 'like', "%{$search_query}%");
                });
            }

            return $datos->latest()->paginate($request->per_page);

        }catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function store(Request $request){
        $this->validate($request, [
            'password'         => 'required|string|min: 4',
            'confirm_password' => 'required_with:password|same:password|min:4',
            'id_role'      => 'required|integer',
            'id_sucursal' => 'required|integer',
            'email'       => 'required|unique:App\Models\User,email',
            'name'        => 'required|string|max: 250',
        ]);

        $user = User::create([
            'id_role'     => $request->id_role,
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'id_sucursal' => $request->id_sucursal,
        ]);
        $role = Role::find($request->id_role);
        $user->syncRoles($role);

        return response()->json(['success'=>true, 'message' => 'Usuario creado correctamente!']);
    }

    public function update(Request $request, $id){
        $user = User::findOrFail($id);

        $this->validate($request,[
            'password' => 'sometimes|min:4',
            'id_sucursal' => 'required|integer',
            'id_role'     => 'required|integer',
            'email' => 'required|email|unique:App\Models\User,email,'.$id,
            'name'  => 'required|string|max:250',
        ]);

        $upd_data = array(
            'id_role' => $request->id_role,
            'name'    =>  $request->name,
            'email'   =>  $request->email,
            'id_sucursal' =>  $request->id_sucursal,
        );
        if(!empty($request->password)){
            $upd_data['password'] = Hash::make($request->password);
        }
        $user->update($upd_data);

        
        $role = Role::find($request->id_role);
        $user->syncRoles($role);


        return response()->json(['success'=>true, 'message' => 'Usuario actualizado correctamente!']);
    }

    public function destroy($id){
        $data = User::findOrFail($id);
        $data->estado = $data->estado != 1? 1 : 0;
        $data->save();

        return response()->json(["success"=>true], 200);
    }
}
