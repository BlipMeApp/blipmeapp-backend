<?php

namespace App\Http\Controllers;

class MessagesController extends BaseController
{
    var $receiver = null;

    public function cleanConversations() {
        try {
            //Clean Temporal Messages for the User
            CList::where("dd", "<=", Carbon::now()->format('Y-m-d H:i:s'))->delete();
        } catch (\TypeError | \ErrorException | \RuntimeException $ex) {
            $this->ll("cleanConversations", "Line " . $ex->getLine() . " : " . $ex->getMessage());
        } catch (\Exception | \ParseError $ex){
            $this->ll("cleanConversations", "Line " . $ex->getLine() . " : " . $ex->getMessage());
        }
    }

    public function getMessageText($i) {
        $message = "";
        if(!is_null($i) && $i > 0) {
            try {
                /* Encrypt Logic */
                $client = new Client();
                /* Encrypt Logic */
                /* GET Text from your node */

                $response = json_decode($response->getBody());
                if(property_exists($response, 'invoices')) {
                    $message = $response->invoices;

                    if(count($invoices) > 0) {
                        /* Decrypt message from node */
                    } else {
                        $message = "Message Deleted";
                    }
                } else {
                    $message = "Message Deleted";
                }
            } catch (\TypeError | \ErrorException | \RuntimeException $ex) {
                $this->ll("getMessageText", "Line " . $ex->getLine() . " : " . $ex->getMessage());
            } catch (\Exception | \ParseError $ex){
                $this->ll("getMessageText", "Line " . $ex->getLine() . " : " . $ex->getMessage());
            }
        }

        return $message;
    }

    public function setTemporalMessages(Request $paramRequest) {
        try {
            /* Encrypt Logic */

                try {
                    //////Check if active conversation exists
                    //I will search conversartion where A::user()->i exists
                    $conversation = Conv::where("CI", $fg->Id)->get()->first();

                    if(!is_null($conversation)) {
                        $conversation->Duration = (is_null($conversation->Duration))?1:null;
                        $conversation->save();
                    }

                    return $this->rr(array("MessageDuration" => $conversation->Duration), "Msg");
                } catch (\TypeError | \ErrorException | \RuntimeException $ex) {
                    $this->ll("setTemporalMessages",  "Line " . $ex->getLine() . " : " . $ex->getMessage());
                    return $this->lel(__("Send Message"), __("Send Message")."2", 200);
                } catch (\Exception | \ParseError $ex){
                    $this->ll("setTemporalMessages", "Line " . $ex->getLine() . " : " . $ex->getMessage());
                    return $this->lel(__("Send Message"), __("Send Message")."3", 200);
                }

        } catch (\TypeError | \ErrorException | \RuntimeException $ex) {
            $this->ll("setTemporalMessages", "Line " . $ex->getLine() . " : " . $ex->getMessage());
            return $this->lel(__('Error'), __('Error Interno'), 200);
        } catch (\Exception | \ParseError $ex){
            $this->ll("setTemporalMessages", "Line " . $ex->getLine() . " : " . $ex->getMessage());
            return $this->lel(__('Error'), __('Error Interno'), 200);
        }
    }

    public function getConversation(Request $paramRequest) {
        try {
            $this->cleanConversations();
            /* Encrypt Logic */

                try {
                    //////Check if active conversation exists
                    //I will search conversartion where A::user()->i exists
                    $conversation = Conv::where("i", A::user()->i)->where("To", $fg->To)->get()->first();

                    if(is_null($conversation)) {
                        //////Else not exists
                        ////////Create Conversation
                        ////////Set Creatori from auth
                        ////////Set Receiveri from request
                        ////////Save Conversation
                        $conversation = new Conv();
                        $conversation->Creatori = A::user()->i;
                        $conversation->Receiveri = $fg->To;
                        $conversation->Duration = null;
                        $conversation->save();

                        //////Check if sender is not on the black list of the receiver
                        $blocked = BF::where("i", $fg->To)->where("BFi", A::user()->i)->where("IB", 1)->get()->first();

                        $sendMsg = new CU();
                        $sendMsg->i = A::user()->i;
                        $sendMsg->CI = $conversation->CI;
                        $sendMsg->save();

                        if(is_null($blocked)) {
                            $receivedMsg = new CU();
                            $receivedMsg->i = $fg->To;
                            $receivedMsg->CI = $conversation->CI;
                            $receivedMsg->save();
                        }
                    }
                    //////End exists

                    $messages = [];
                    $msjs = Conv::where("CI", $conversation->CI)->where("i", A::user()->i)->limit(10)->get();

                    if(!is_null($msjs)) {
                        //Get Messages related from node
                        $texts = $this->getMessageRangeText($msjs->min("i"),$msjs->max("i"));

                        $msjs = collect($msjs)->sortByDesc('start')->reverse()->toArray();
                        foreach ($msjs as $message) {
                            $msj = $texts[$message["i"]];
                            array_push($messages, $msj);
                        }

                        $result = [
                            /* Conversation info */
                        ];
                    }

                    return $this->rr($result, "Msg");
                } catch (\TypeError | \ErrorException | \RuntimeException $ex) {
                    $this->ll("getConversation",  "Line " . $ex->getLine() . " : " . $ex->getMessage());
                    return $this->lel(__("Send Message"), __("Send Message")."2", 200);
                } catch (\Exception | \ParseError $ex){
                    $this->ll("getConversation", "Line " . $ex->getLine() . " : " . $ex->getMessage());
                    return $this->lel(__("Send Message"), __("Send Message")."3", 200);
                }

        } catch (\TypeError | \ErrorException | \RuntimeException $ex) {
            $this->ll("getConversation", "Line " . $ex->getLine() . " : " . $ex->getMessage());
            return $this->lel(__('Error'), __('Error Interno'), 200);
        } catch (\Exception | \ParseError $ex){
            $this->ll("getConversation", "Line " . $ex->getLine() . " : " . $ex->getMessage());
            return $this->lel(__('Error'), __('Error Interno'), 200);
        }
    }

    public function sendMessage(Request $paramRequest) {
        try {
            /* Encrypt Logic */

            //checkQuickBalance
            $balance = $this->checkQuickBalance();

            if($balance < 2) {
                $this->ll("sendMessage::balance", __("Fondos Insuficientes"));
                return $this->lel(__("Send Message"), __("Fondos Insuficientes"), 200);
            } else {
                ////If Has Enough Money
                try {
                    //////Encrypt Message
                    $cList = new CList();
                    //////Set Senderi from auth
                    //////Set Receiveri from request
                    //////Set VisibleSender as 1
                    $cList->Senderi = A::user()->i;
                    $cList->Receiveri = $fg->To;

                    $client = new Client();
                    /* Attach your macaroon here */
                    $params["json"] = [
                        "memo" => $fg->Message, //Send Encrypt Message
                        "value" => 2,
                    ];

                    //////Create Lightning Invoice
                    $invoice = json_decode($invoice->getBody());

                    if(property_exists($invoice, 'payment_request')) {
                        //////Set Lightning i from lightning
                        $cList->i = $invoice->i;

                        //Debit Sats from User Balance
                        $spent->save();

                        $cList->CI = $fg->Id;
                        //////End exists

                        //////Check if it should be a Temporal Message
                        $conversation = Conv::where("CI", $fg->Id)->get()->first();
                        if(!is_null($conversation->Duration)) {
                            //////If it is a Temporal Message
                            ////////Set the Due Date
                            ////////Set the MessageDuration
                            $cList->dd = Carbon::now()->addMinutes($fg->Duration);
                            $cList->Duration = $fg->Duration;
                        }
                        //////End Temporal Message

                        //////Save CList
                        $cList->save();

                        //////Check if sender is not on the black list of the receiver
                        $blocked = BF::where("i", $fg->To)->where("BFi", A::user()->i)->where("IB", 1)->get()->first();

                        $sendMsg = new CListUser();
                        $sendMsg->i    = A::user()->i;
                        $sendMsg->CListId    = $cList->CListId;
                        $sendMsg->IsSender  = 1;
                        $sendMsg->CI  = $fg->Id;
                        $sendMsg->save();

                        if(is_null($blocked)) {
                            $receivedMsg = new CListUser();
                            $receivedMsg->i    = $fg->To;
                            $receivedMsg->CListId    = $cList->CListId;
                            $receivedMsg->IsSender  = 0;
                            $receivedMsg->CI  = $fg->Id;
                            $receivedMsg->save();
                        }
                        //////End If Blocked


                        $result = [
                            /* send message response */
                        ];

                        return $this->rr($result, "Msg");
                    } else {
                        $this->ll("sendMessage::lightning", json_encode($invoice));
                        return $this->lel("Send Message", "Send Message"."1", 200);
                    }
                } catch (\TypeError | \ErrorException | \RuntimeException $ex) {
                    $this->ll("sendMessage",  "Line " . $ex->getLine() . " : " . $ex->getMessage());
                    return $this->lel("Send Message", "Send Message"."2", 200);
                } catch (\Exception | \ParseError $ex){
                    $this->ll("sendMessage", "Line " . $ex->getLine() . " : " . $ex->getMessage());
                    return $this->lel("Send Message", "Send Message"."3", 200);
                }
            }
        } catch (\TypeError | \ErrorException | \RuntimeException $ex) {
            $this->ll("sendMessage", "Line " . $ex->getLine() . " : " . $ex->getMessage());
            return $this->lel("Send Message", "Internal Error", 200);
        } catch (\Exception | \ParseError $ex){
            $this->ll("sendMessage", "Line " . $ex->getLine() . " : " . $ex->getMessage());
            return $this->lel("Send Message", "Internal Error", 200);
        }
    }

    public function getConversations() {
        try {
            $this->cleanConversations();
            $conversations = [];

            $convs = Conv::where("i", A::user()->i)->get();

            //Get Messages Texts from node
            $texts = $this->getMessageRangeText($convs->min("LI"),$convs->max("LI"));

            if($convs) {
                foreach ($convs as $conversation) {
                    $conv = [
                        "Message"        => (is_null($conversation->LI))?"":$texts[$conversation->LI],
                        "MessageDuration"=> $conversation->Duration
                    ];
                    array_push($conversations, $conv);
                }
                return $this->rr($conversations, "Conv");
                //--------------------------------------------------------------
            } else {
                return $this->lel('Datos incorrectos', null, 200);
            }
        } catch (\TypeError | \ErrorException | \RuntimeException $ex) {
            $this->ll("getConversations", "Line " . $ex->getLine() . " : " . $ex->getMessage());
            return $this->lel('Error', 'Error Interno', 200);
        } catch (\Exception | \ParseError $ex){
            $this->ll("getConversations", "Line " . $ex->getLine() . " : " . $ex->getMessage());
            return $this->lel('Error', 'Error Interno', 200);
        }
    }

    public function getMessages(Request $paramRequest) {
        try {
            /* Encrypt Logic */
            $this->cleanConversations();

            $messages = [];

            $conversation = Conv::where("i", A::user()->i)->where("CI", $fg->Id)->get()->first();
            $msjs = Conv::where("CI", $fg->Id)->where("i", A::user()->i)->get();

            if(!is_null($msjs)) {
                //Get messages text from node
                $texts = $this->getMessageRangeText($msjs->min("i"),$msjs->max("i"));

                $msjs = collect($msjs)->sortByDesc('start')->reverse()->toArray();
                foreach ($msjs as $message) {
                    $msj = $texts[$message["i"]]; //Decrypted message
                    array_push($messages, $msj);
                }

                $result = [
                    /* Message info */
                ];

                return $this->rr($result, "Msg");
            }
        } catch (\TypeError | \ErrorException | \RuntimeException $ex) {
            $this->ll("getMessages", "Line " . $ex->getLine() . " : " . $ex->getMessage());
            return $this->lel('Error', 'Error Interno', 200);
        } catch (\Exception | \ParseError $ex){
            $this->ll("getMessages", "Line " . $ex->getLine() . " : " . $ex->getMessage());
            return $this->lel('Error', 'Error Interno', 200);
        }
    }

    public function getMessageRangeText($start, $end) {
        $messages = [];
        if(!is_null($start) && $start > 0 && !is_null($end) && $end > 0) {
            try {
                /* Encrypt Logic */
                $client = new Client();
                /* Encrypt Logic */
                /* Attach your macaroon here */
                $indexOf = $start - 1;
                $numMax = $end - $start + 1;

                /* Request messages from node */
                $response = json_decode($response->getBody());
                if(property_exists($response, 'invoices')) {
                    $invoices = $response->invoices;

                    foreach ($invoices as $invoice) {
                        $messages[$invoice->i] = $invoice->memo; //Decrypt your message
                    }
                }
            } catch (\TypeError | \ErrorException | \RuntimeException $ex) {
                $this->ll("getMessageText", "Line " . $ex->getLine() . " : " . $ex->getMessage());
            } catch (\Exception | \ParseError $ex){
                $this->ll("getMessageText", "Line " . $ex->getLine() . " : " . $ex->getMessage());
            }
        }

        return $messages;
    }

    public function deleteConversation(Request $paramRequest) {
        try {
            /* Encrypt Logic */

            $conv = null;

            if($fg->JustMe == 1) {
                $conv = Conv::where("CI", $fg->Id)->where("i", A::user()->i)->get()->first();
            } else {
                $conv = Conv::where("CI", $fg->Id)->get()->first();
            }

            if($conv) {
                CListUsr::where("CI", $fg->Id)->where("i", A::user()->i)->delete();
                if($conv->delete()) {
                    return $this->rr('Deleted', 'Success');
                } else {
                    return $this->lel('Fail', 'Not Found', 200);
                }
            } else {
                return $this->rr(null, 'Not Found.');
            }
        } catch (\TypeError | \ErrorException | \RuntimeException $ex) {
            $this->ll("deleteConversation", 'Line ' . $ex->getLine() . ': ' . $ex->getMessage());
            return $this->lel(__('Error'), __('Error Interno'), 200);
        } catch (\Exception | \ParseError $ex){
            $this->ll("deleteConversation", 'Line ' . $ex->getLine() . ': ' . $ex->getMessage());
            return $this->lel(__('Error'), __('Error Interno'), 200);
        }
    }
}
