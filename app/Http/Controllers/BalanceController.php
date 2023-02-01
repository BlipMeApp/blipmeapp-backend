<?php

namespace App\Http\Controllers;

class BalanceController extends BaseController
{
    public function getBalanceUser() {
        try {
            $balance = $this->checkBalance();
            //Get Current BTC Price
            $current_btc_price = $this->BTCPrice();
            $convertion_rate = 1;

            if(!is_float($balance)) {
                $convertion_rate = $current_btc_price / 100000000;
            } else {
                return $this->lel('Balance', __('No se aceptan decimales'), 200);
            }

            $result = [
                //Balance Info
            ];

            return $this->rr($result, 'Balance');
        } catch (\TypeError | \ErrorException | \RuntimeException $ex) {
            $this->ll("getBalanceUser", 'Line ' . $ex->getLine() . ': ' . $ex->getMessage());
            return $this->lel('Balance', 'Error al Calcular Balance', 200);
        } catch (\Exception | \ParseError $ex){
            $this->ll("getBalanceUser", 'Line ' . $ex->getLine() . ': ' . $ex->getMessage());
            return $this->lel('Balance', 'Error al Calcular Balance', 200);
        }
    }
}
