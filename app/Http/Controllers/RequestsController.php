<?php

namespace App\Http\Controllers;

class RequestsController extends BaseController
{
    public function create_requests(Request $paramRequest){
        try {
            /* Encrypt Logic */

            try {

                $spent = new Requests();
                $spent->Id = A::user()->i;
                $spent->To = $fg->To;
                $spent->Am = $fg->Am;

                $spent->save();

                return $this->rr($spent, "Success");

            } catch (\TypeError | \ErrorException | \RuntimeException $ex) {
                $this->ll("create_requests", "Line " . $ex->getLine() . " : " . $ex->getMessage());
                return $this->lel("create requests", "Internal Error", 200);
            }

        } catch (\TypeError | \ErrorException | \RuntimeException $ex) {
            $this->ll("create_requests", "Line " . $ex->getLine() . " : " . $ex->getMessage());
            return $this->lel("create requests", "Internal Error", 200);
        }
    }

    public function decline_requests(Request $paramRequest){
        try {

            /* Encrypt Logic */

            $upd = Req::where("RI",$fg->Id)->get()->firts();
            $upd->save();

            return $this->rr($upd, "Success");

        } catch (\TypeError | \ErrorException | \RuntimeException $ex) {
            $this->ll("decline_requests", "Line " . $ex->getLine() . " : " . $ex->getMessage());
            return $this->lel("decline requests", "Internal Error", 200);
        }
    }

    public function accept_requests(Request $paramRequest){
        try {

            /* Encrypt Logic */

            // select de tabla Requests con el RI que se recive
            $rep = Req::where("RI",$fg->Id)->get()->first();

            $balance = $this->checkQuickBalance();
            if($balance < $rep->Am) {
                $this->ll("sendMessage::balance", __("Fondos Insuficientes"));
                return $this->lel(__("accept requests"), __("Fondos Insuficientes"), 200);
            } else {
                //////Took two sats from balance
                $fecha = date_create();

                //Register Spent
                $spent->save();

                //Register payment
                $paid->save();

                $upd = Req::where("RI",$fg->Id)->get()->first();
                $upd->save();

            return $this->rr($upd, "Success");


            }

        } catch (\TypeError | \ErrorException | \RuntimeException $ex) {
            $this->ll("accept_requests", "Line " . $ex->getLine() . " : " . $ex->getMessage());
            return $this->lel("accept requests", "Internal Error", 200);
        }
    }

    public function getRequestsActive(){
        try {

            $dataR = [];
            $rep = Req::where("To",A::user()->i)->where("Status",1)->get();
            //\Log::debug($rep);

            if($rep){
                foreach($rep as $data){
                    $result = [
                        /* Request info */
                    ];
                    array_push($dataR, $result);
                }
                return $this->rr($dataR, "");
            }
        } catch (\TypeError | \ErrorException | \RuntimeException $ex) {
            $this->ll("getRequestsActive", "Line " . $ex->getLine() . " : " . $ex->getMessage());
            return $this->lel("requests active", "Internal Error", 200);
        }
    }
}
