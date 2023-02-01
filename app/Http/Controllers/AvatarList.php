<?php

namespace App\Http\Controllers;

class AvatarList extends BaseController
{
    public function AvatarListArrayJPG()
    {
        $avatarList = Av::all();

        return $this->rr($avatarList, 'Success');
    }

    public function getAvatarByUser()
    {
        try{
            $avatar = UAV::where("i", A::user()->i)->get("pc")->first();

            return $this->rr($avatar, "");
        }
      catch (\TypeError | \ErrorException | \RuntimeException $ex) {
        $this->ll("getAvatarByUser",  "Line " . $ex->getLine() . " : " . $ex->getMessage());
        return $this->lel(__("get Avatar"), __("get Avatar")."2", 200);
    } catch (\Exception | \ParseError $ex){
        $this->ll("getAvatarByUser1", "Line " . $ex->getLine() . " : " . $ex->getMessage());
        return $this->lel(__("get Avatar"), __("get Avatar")."3", 200);
    }
    }

    public function createAvatarByUser(Request $paramRequest)
    {
        try{
            /* Encrypt Logic */

            $user = Usr::where("i",A::user()->i)->get()->first();
            $user->AI = $fg->AI;
            $user->save();

            return $this->rr($user, "Success");
        }
        catch (\TypeError | \ErrorException | \RuntimeException $ex) {
          $this->ll("createAvatarByUser",  "Line " . $ex->getLine() . " : " . $ex->getMessage());
          return $this->lel(__("create avatar"), __("create avatar")."2", 200);
        } catch (\Exception | \ParseError $ex){
            $this->ll("createAvatarByUser", "Line " . $ex->getLine() . " : " . $ex->getMessage());
            return $this->lel(__("create avatar"), __("create avatar")."3", 200);
        }
    }


}
