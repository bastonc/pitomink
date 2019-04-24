<?php
namespace pitomnik\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Mail;
use Illuminate\Validation\Validator;
use PhpParser\Node\Expr\Cast\Object_;

class pitomnikController extends Controller
{
    public function addAnimal(Request $request){
        //$msg = "Кличка: ".$request->animalName." Возраст: ".$request->animalAge." Вид: ".$request->typeAnimal . " Характер: ".$request->character;

        $validation = $this->validate($request, [
            'name'  => 'required|unique:Pitomnik_Animal,name|max:50',
            'age' => 'required',
            'typeAnimal'=>'required',
            'characterAnimal' =>'required'
            ]);

        /*if ($validation->fails())
        {
            return $validation->errors;
        }*/

        $typeAnimal=$request->typeAnimal;
        $transactionObject= (object) Array();
        $transactionObject->text = "Добавлено животное".$request->name;
        $transactionObject->coast = 0;
        $transactionObject->type="frontend";
        $transactionObject->typeOper = 0;
        $cell= new AnimalClass;
        $transaction = new TransactionClass;
        $chek = new ChekClass;
        $character=$request->characterAnimal;
        $celltype = $cell->setCellType($character); // метод определяет в какую клетку садить зверя. Возвращает 1 - одиночная; 10 - груповая

        $idCell=$cell->chekEmptyCell($celltype, $typeAnimal);   // метод проверяет наличия пустых ОДИНОЧНЫХ/ГРУППОВЫХ ($celltype) клеток для типа животного $typeAnimal
                                                                // Возвращает ID пустой клетки. Принимает: 1-одиночная клетка; 0-груповая, Тип животного (dog/cat)
        if($idCell != NULL){                                    // если функция chekEmptyCell возвращает результат (id)  (пустая клетка есть)


            $cell->defineCell($idCell, $request ,$transactionObject);                 // метод - занимает клетку за животным, прибавляет в клетку+1 жильца,
            $cell->plusAnimal(); // добавляем+1 добавленный для отчета по казначейству,




        }else{
            $buyCellId= $cell->buyCellId($celltype, $typeAnimal); // метод покупает клетку. Принимает $celltype - тип клетки 1 = одиночная; 0 = групповая. Возвращает ID купленной клетки.
            $transactionObject->text = "Животное посажено в новую клетку".$request->name;
            $transactionObject->coast = 0;
            $transactionObject->type="frontend";
            $cell->defineCell($buyCellId, $request ,$transactionObject); // метод - занимает клетку $byCellId за животным, прибавляет в клетку+1 жильца, добавляем+1 добавленный для отчета по казначейству,
            $cell->plusAnimal(); // добавляем+1 добавленный для отчета по казначейству,



        }

        $waitMoneyArray=$chek->costFromDepartment();  // обращается в департамент за оплату животных, для каждого десятого добавленного(с проверкой на возможность осуществления транзакции)
                                                       // Возвращает массив из суммы денег на получение. moneyWait в БД
        $moneybalance=$chek->getBalance();
        $cellstateArray=$cell->refreshStateCells();     // метод возвращает полную стату про все клетки в массиве
        $transactionArray = $transaction->getAllTransaction();       // метод возвращает массив всех транзакций
        $state='addAnimal';

        return response()->json(array('transactionArray'=> $transactionArray,'balance'=>$moneybalance, 'cellStataArray'=>$cellstateArray,'state'=>$state), 200);
    }
    public function getAll(Request $request){
        if($request->state=='init'){
            $cell= new AnimalClass;
            $transaction = new TransactionClass;
            $chek = new ChekClass;
            $waitMoneyArray=$chek->getBalance();  // метод возвращает баланс на счету Баланс ан счету казначейства
            $cellstateArray=$cell->refreshStateCells();     // метод возвращает полную стату про все клетки в массиве
            $transactionArray = $transaction->getAllTransaction();       // метод возвращает массив всех транзакций

        }
        $state='init';
        return response()->json(array('transactionArray'=> $transactionArray,'waitMonneyArray'=>$waitMoneyArray, 'cellStataArray'=>$cellstateArray, 'state'=>$state), 200);
    }
    public function animalInCell(Request $request){
        $cell= new AnimalClass;
        $animalInCellArray = $cell->animalsInCell($request->id);
        $idCell=$request->id;
        return response()->json(array('animalInCellArray'=> $animalInCellArray,'idCell'=>$idCell), 200);
    }
    public function giveMoney (){
        $transaction = new TransactionClass;
        $chek= new ChekClass;
        $error=0;
        $givemoney=$chek->giveMoney(); // метод переводит деньги с ожидаемых в полученные (забрали деньги из казначайства) Возвращает true в случае удачного перемещения денег, false - в случае неудачи
        $balance=$chek->getBalance(); // метод возвращает значение баланса (все деньги + деньги с баланса казначеского счета)
        if($givemoney == FALSE){
            $error=1;
        }
        $state = 'givemoney';
        $transactionArray = $transaction->getAllTransaction();
        return response()->json(array('balance'=> $balance, 'state'=>$state, 'transactionArray'=>$transactionArray , 'error'=>$error), 200);
    }
    public function delAnimal(Request $request){
        $error=0;
        $chek = new ChekClass;
        $transaction = new TransactionClass;
        $animal = new AnimalClass;
        $deleteAnimal = $animal->delAnimal($request); // метод удаляет животное из таблицы животных и уменьшает кол-во жителей в клетке на -1
        if($deleteAnimal!=0) {
            $cellstateArray = $animal->refreshStateCells();    //рефрешим состояние клеток.
            foreach ( $cellstateArray as $emptyCell) {
               // dump($emptyCell->stata);
                if ($emptyCell->stata == 0) {                  //Если есть пустые
                   // dd($emptyCell->stata);
                    $animal->sellCell($emptyCell->id, $emptyCell->cost);  // продаем клетки в пол цены. Принимает id клетки ($emptyCell->id), цену клетки ($emptyCell->cost)
                }
            }
        }else {  $error = 1; }

      /*  if($error==1){

        }*/

        $state='delanimal'; //стата для клиента
        $cellstateArray=$animal->refreshStateCells();    //рефрешим состоние клеток
        $waitMoneyArray=$chek->getBalance(); //рефрешим счет
        $transactionArray = $transaction->getAllTransaction();  // рефрешим транзакции

       // dd('STOP Должно быть удалено животное, продана его клетка, деньги быть на балансе');
        return response()->json(array('transactionArray'=> $transactionArray,'waitMonneyArray'=>$waitMoneyArray, 'cellStataArray'=>$cellstateArray, 'state'=>$state, 'error'=>$error), 200);
    }
    public function destroyer(){
        $error=0;
        $cell = new AnimalClass;
        $transaction = new TransactionClass;
        $chek = new ChekClass;
        $animalDestroyerArray=$cell->destroyer();
        if($animalDestroyerArray==NULL) $error=1;
        $state='destroyer'; //стата для клиента
        $cellstateArray=$cell->refreshStateCells();    //рефрешим состоние клеток
        $waitMoneyArray=$chek->getBalance(); //рефрешим счет
        $transactionArray = $transaction->getAllTransaction();  // рефрешим транзакции
        return response()->json(array('animalDestroyer'=>$animalDestroyerArray, 'transactionArray'=> $transactionArray,
            'waitMonneyArray'=>$waitMoneyArray, 'cellStataArray'=>$cellstateArray, 'state'=>$state, 'error'=>$error), 200);

    }
}

?>
