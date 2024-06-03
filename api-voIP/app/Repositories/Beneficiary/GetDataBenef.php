<?php 

namespace App\Repositories\Beneficiary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GetDataBenef
{
public function getData($input)
{
    $result = DB::select("SELECT    obter_nome_pf(a.cd_pessoa_fisica)                         AS NOME_BENEFICIARIO,
                                    obter_dados_pf(a.cd_pessoa_fisica, 'CPF')                 AS CPF_BENEFICIARIO,
                                    b.cd_usuario_plano                                        AS COD_CARTEIRA,
                                    SUBSTR(pls_obter_dados_produto(a.nr_seq_plano,'C'),1,255) AS TIPO_CONTRATO,
                                    DECODE(e.ie_tipo_pagador, 'P', 'S', 'N')                  AS RESP_FINANCEIRO,
                                    NVL(d.tx_reajuste, 0)                                     AS REAJUSTE_ANUAL,
                                    a.CD_PESSOA_FISICA,
                                    NVL(obter_email_pf(a.cd_pessoa_fisica),'Sem Email')       AS EMAIL
                                FROM pls_segurado          a,
                                    pls_segurado_carteira b,
                                    pls_plano             c,
                                    pls_reajuste          d,
                                    pls_contrato_pagador  e
                                WHERE a.nr_sequencia       = b.nr_seq_segurado 
                                AND a.nr_seq_plano       = c.nr_sequencia 
                                AND d.nr_seq_contrato(+) = a.nr_seq_contrato
                                AND e.nr_seq_contrato    = a.nr_seq_contrato
                                AND a.cd_pessoa_fisica   = (SELECT 
                                                              CASE WHEN LENGTH(?) > 11 THEN
                                                                       (SELECT s.cd_pessoa_fisica
                                                                          FROM pls_segurado_carteira b,
                                                                               pls_segurado          s
                                                                         WHERE b.nr_seq_segurado  = s.nr_sequencia
                                                                           AND b.cd_usuario_plano = LPAD(?, 17, '0'))
                                                              ELSE (SELECT pf.nr_cpf
                                                                      FROM pessoa_fisica pf
                                                                     WHERE pf.nr_cpf = LPAD(?, 11, '0')) END cd_pessoa_fisica
                                                              FROM DUAL)",[$input,$input,$input]
    );

    return $result;
}
}