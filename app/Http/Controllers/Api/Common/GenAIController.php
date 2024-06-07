<?php

namespace App\Http\Controllers\Api\Common;

use App\Models\CompressedTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;

class GenAIController extends Controller
{
    public function SendQuery(Request $request){
        try{
            $inputText = $request["text"];
            $table = CompressedTable::where('table_name', $request["dbTable"])->first();

            // 1. Primer llamado a LLM, se construye la consulta SQL en base a la
            // tabla seleccionada y pregunta del usuario.
            $sqlQueryPrompt = "
                Dada la tabla: {$table->query}
                Genera una consulta MySQL a partir de: {$inputText}
            ";
            $sqlQuery = OpenAI::completions()->create([
                'model' => 'gpt-3.5-turbo-instruct',
                'prompt' => $sqlQueryPrompt,
                'max_tokens' => 125,
                'temperature' => 0,
                'n'=> 2
            ]);
            $sqlQuery = $sqlQuery['choices'][0]['text'];
            $sqlQuery = trim(preg_replace('/\s\s+/', ' ', $sqlQuery));
            
            // 2. Se detectan comandos con riesgo de SQL Injection
            $forbidden_commands = ["INSERT", "UPDATE", "DELETE", "DROP"];
            foreach ($forbidden_commands as $command){
                if(strpos($sqlQuery, $command) !== FALSE){
                    return response()->json(['success'=>false, 'message'=>"Las consultas que modifiquen o agreguen información a la base de datos no están permitidas"], 500);
                }
            }
            // dd($sqlQuery);
            // 3. Se ejecuta la consulta SQL generada por el modelo sobre la base de datos
            $queryResult = DB::select(DB::raw("$sqlQuery"));
            $queryResult = json_encode($queryResult);
            // dd($queryResult);
            // 4. Segundo llamado a LLM, expresa la respuesta de la base de datos en
            // lenguaje natural.
            $naturalLangPrompt = "
                Dada una consulta del usuario: {$inputText}\n
                Se retornó este resultado desde la base de datos:{$queryResult}\n
                Devuelve el resultado en una muy corta respuesta en lenguaje natural, sin decir que proviene de una base de datos.
                {$table->prompt}
            ";

            $chatResponse = OpenAI::completions()->create([
                'model' => 'gpt-3.5-turbo-instruct',
                'prompt' => $naturalLangPrompt,
                'max_tokens' => 150,
                'temperature' => 0,
                'n'=> 2
            ]);
            $chatResponse = $chatResponse['choices'][0]['text'];
            $chatResponse = trim(preg_replace('/\s\s+/', ' ', $chatResponse));

            return response()->json(['success'=>true, 'message'=>$chatResponse]);

        }catch(\Exception $e){
            return response()->json(['success'=>false, 'message'=>"Lo lamento, tu consulta no pudo ser procesada"], 500);
        }
    }
    public function compressedTablesCombo(){
        return response()->json(CompressedTable::all());
    }
}