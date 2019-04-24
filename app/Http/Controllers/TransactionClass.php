<?php
namespace pitomnik\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Mail;
use PhpParser\Node\Expr\Cast\Object_;

class TransactionClass extends Controller{
    public function getAllTransaction(){

        $transactionArray = DB::table('Pitomnik_Transaction')->select('*')->where('type','=', 0)->get();
       // dd($transactionArray);
        return $transactionArray;
    }
}
