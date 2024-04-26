<?php

namespace App\Enums;

abstract class MetodoPagamentoEnum
{

    public const ID_BOLETO_BANCARIO = 1;
    public const ID_CARTAO_CREDITO = 2;
    public const ID_CARTAO_DEBITO = 3;
    public const ID_CHEQUE = 4;
    public const ID_DINHEIRO = 5;
    public const ID_PIX = 6;
    public const ID_TRANSFERENCIA_ELETRONICA = 7;
    public const ID_VALE_ALIMENTACAO = 8;
    public const ID_VALE_COMBUSTIVEL = 9;
    public const ID_VALE_PRESENTE = 10;
    public const ID_VALE_REFEICAO = 11;
    public const ID_OUTROS = 12;

}
