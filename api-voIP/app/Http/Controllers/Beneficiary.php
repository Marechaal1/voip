<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Repositories\Beneficiary\GetDataBenef;
use Illuminate\Http\JsonResponse;

class Beneficiary extends Controller
{
    public function index($var)
    {
        $benefRepository = new GetDataBenef();
        //validação
        $rules = [
            'var' => 'required|string|max:17', 
        ];

        // Mensagens de erro
        $messages = [
            'var.required' => 'O parâmetro var é obrigatório.',
            'var.numeric' => 'O parâmetro var deve ser uma string.',
            'var.max' => 'O parâmetro var deve possui no máximo 17 caracteres.',
        ];
        $validator = \Validator::make(['var' => $var], $rules, $messages);

        // Verifica se a validação falhou
        if ($validator->fails()) {
            // Retorna uma resposta JSON
            return response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_BAD_REQUEST);
        }

        //Bloco try 
        try {
        $sql = $benefRepository->getData($var);

        if(empty($sql)){
            $response = [
                "sucesso" => 0
            ];
        }

        if(!empty($sql)){
            if($sql[0]->tipo_contrato == "Coletivo Empresarial"){
                $sql[0]->tipo_contrato = 1;
            }
            if(empty($sql[0]->email)){
                $possuiEmail = "N";
            }
            if(!empty($sql[0]->email)){
                $possuiEmail = "S";
            }
            $response = 
            [
                "sucesso"       => 1,
                "codBenef"      => $sql[0]->cd_pessoa_fisica,
                "carteiraBenef" => $sql[0]->cod_carteira,
                "tipoContrato"  => $sql[0]->tipo_contrato,
                "reajusteAnual" => $sql[0]->reajuste_anual,
                "nomeBenef"     => $sql[0]->nome_beneficiario,
                //"diasAtraso"    => $sql[0]->diasAtraso,
                "rspnsvlFinanc" => $sql[0]->resp_financeiro,
                "possuiEmail"   => $possuiEmail,
                "email"         => $sql[0]->email
            ];
        }
        return response()->json($response, 200);
        
    }catch (\Exception $e) {
        // Captura da exceção e retorna uma resposta JSON com o erro
        return response()->json(['error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
    }

    public function teste(){
        return response()->json('oi');
    }
}