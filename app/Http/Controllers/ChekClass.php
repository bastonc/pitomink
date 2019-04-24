<?php
namespace pitomnik\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Mail;
use Illuminate\Validation\Validator;
use PhpParser\Node\Expr\Cast\Object_;

class ChekClass extends Controller{

    public function costFromDepartment(){


        $counterArray=DB::table('Pitomnik_Chek')->select('flag')->where('source','=', 'TreasuryDepartment')->get();
        $counter=$counterArray[0]->flag;

            //dd($counterFlag);
            if(($counter%10)==0){
                $transactionObject = (object) Array();
                $transactionObject->text = "Казна выделила 100";
                $transactionObject->coast = 100;
                $transactionObject->type="frontend";
                $transaction=DB::transaction(function() use ($transactionObject,$counter){

                    DB::update('UPDATE `Pitomnik_Chek` SET moneyWait=moneyWait+100 WHERE `source`="TreasuryDepartment"');

                    DB::insert('INSERT INTO `Pitomnik_Transaction`(`type`,`what` , `how` , `for`,`other`) VALUES (?,?,?,?,?)',
                        [0,$transactionObject->text,$transactionObject->coast, $transactionObject->type, $counter]);
                });


                //dd($transaction);
            }


        $enableButton=DB::select('SELECT `moneyWait` FROM `Pitomnik_Chek` WHERE `moneyWait`>0 AND `source` ="TreasuryDepartment"');
        //$enableButton = DB::table('Pitomnik_Chek')->select('moneyWait')->where(function(){ where('moneyWait','>',0); where('source','=','TreasuryDepartment');})->get(); // возвращаем значение moneyWait
    return $enableButton; // массив со значением ожидающих денег (счет на получение из казначейства)
    }
    public function giveMoney(){
        $result=FALSE;
        $moneyWaitArray=DB::select('SELECT `moneyWait` FROM `Pitomnik_Chek` WHERE `source`="TreasuryDepartment"');
        $transactionObject = (object) Array();
        $transactionObject->text = "Получены деньги из казны";
        $transactionObject->coast = $moneyWaitArray[0]->moneyWait;
        $transactionObject->type="frontend";
        $resultSum=DB::update('UPDATE `Pitomnik_Chek` SET `money`= `money`+`moneyWait` WHERE `source`="TreasuryDepartment"');
        $resultUpdateMoneyWait=DB::update('UPDATE `Pitomnik_Chek` SET `moneyWait`=0  WHERE `source`="TreasuryDepartment"');

        if($resultSum > 0 && $resultUpdateMoneyWait > 0) {
            $result = TRUE;
            DB::insert('INSERT INTO `Pitomnik_Transaction`(`type`,`what` , `how` , `for`,`other`) VALUES (?,?,?,?,?)',
                [0, $transactionObject->text, $transactionObject->coast, $transactionObject->type, '0']);
        }


        return $result;

    }
    public function getBalance(){
        $moneyArray=DB::select('SELECT `moneyWait`,`money` FROM `Pitomnik_Chek` WHERE `source`="TreasuryDepartment"');
        $moneyAll=DB::select('SELECT `money` FROM `Pitomnik_Chek` WHERE `source`="AllMoney"');
        $balanceArray=[ 'moneyFromKazn'=>$moneyArray, 'allMoney'=>$moneyAll];

        return $balanceArray;

    }
}
