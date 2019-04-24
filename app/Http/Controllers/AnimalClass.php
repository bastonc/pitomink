<?php
namespace pitomnik\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Mail;

class AnimalClass extends Controller
{
    public function setCellType($character){
        if($character=='kind')
            $result=10;
        elseif($character=='evil')
            $result=1;
        else {$result='Error';}
        return $result;
    }
    public function chekEmptyCell($typeCell, $typeAnimal){

        //$typeCell === 1 ? $stat=1 : $stat=10;
        //dd($stat);
        $cellArray=DB::select('SELECT `id` FROM `Pitomnik_Cell` WHERE `typeAnimal`=? AND `typeCell`=? AND `stata`<?',[$typeAnimal, $typeCell,$typeCell]);
        //dd($cellArray);
        if($cellArray !=NULL){
            $idCell=$cellArray[0]->id;
        }else {$idCell=NULL; }
        return $idCell;
    }
    public function defineCell ($idCell, $objectAnimal,$transaction){
        //$msg = "Кличка: ".$request->nameAnimal." Возраст: ".$request->ageAnimal." Вид: ".$request->typeAnimal . " Характер: ".$request->character;
        //$transaction - object объект транзакции
        $recordAnimalInBase=DB::transaction(function() use ($idCell,$objectAnimal,$transaction){
            DB::insert('INSERT INTO `Pitomnik_Animal`(`typeAnimal`,`name` , `age` , `characterAnimal` , `idCell`) VALUES (?,?,?,?,?)',
                [$objectAnimal->typeAnimal,$objectAnimal->name, $objectAnimal->age, $objectAnimal->characterAnimal, $idCell]);
            DB::update('UPDATE `Pitomnik_Cell` SET stata=stata+1 WHERE `id`=?',[$idCell]);

            DB::insert('INSERT INTO `Pitomnik_Transaction`(`type`,`what` , `how` , `for`,`other`) VALUES (?,?,?,?,?)',
                [1,$transaction->text,$transaction->coast, $transaction->type, $idCell]);
        });
        //dd($recordAnimalInBase);
        //if ($recordAnimalInBase)

        return $recordAnimalInBase;
    }
    public function plusAnimal () {
        $plus=DB::update('UPDATE `Pitomnik_Chek` SET flag=flag+1 WHERE `source`="TreasuryDepartment"');
        return $plus;
    }
    public function buyCellId ($celltype,$typeAnimal){
        if($typeAnimal == "dog" && $celltype == 1) { $cost = 10; $type="для собаки"; }
        if($typeAnimal == "dog" && $celltype == 10) { $cost = 50; $type="для собак"; }
        if($typeAnimal == "cat" && $celltype == 1) { $cost = 5; $type="для кота";}
        if($typeAnimal == "cat" && $celltype == 10) { $cost = 20; $type="для котов";}
        $transactionObject= (object) Array();
        $transactionObject->text = "Куплена новая клетка: Тип ".$typeAnimal.". Животных: ".$celltype;
        $transactionObject->coast = $cost;
        $transactionObject->type="frontend";
        $transactionObject->typeOper = 0;

        DB::transaction(function() use ($celltype,$typeAnimal,$cost, $transactionObject) {

            DB::update('UPDATE `Pitomnik_Chek` SET money=money-? WHERE `source`="AllMoney"', [$cost]);
            DB::insert('INSERT INTO `Pitomnik_Transaction`(`type`,`what` , `how` , `for`) VALUES (?,?,?,?)',
                [$transactionObject->typeOper, $transactionObject->text, $transactionObject->coast, $transactionObject->type]);
        });

        $id = DB::table('Pitomnik_Cell')->insertGetId(


            ['typeCell' => $celltype, 'typeAnimal' => $typeAnimal, 'cost' => $cost, 'stata' => 0]

        );
        //dd($id);

        return $id;

    }
    public function refreshStateCells(){

            $stataCell=DB::select('SELECT * FROM `Pitomnik_Cell`');
            return $stataCell;


    }
    public function animalsInCell ($id){
        $animalsinCellArray=DB::table('Pitomnik_Animal')->select()->where('idCell','=', $id)->get();
        return $animalsinCellArray;


    }
    public function delAnimal ($objectAnimal){
        //"Кличка: ".$request->nameAnimal." Возраст: ".$request->ageAnimal." Вид: ".$request->typeAnimal . " Характер: ".$request->character;
        $transactionObject= (object) Array();
        $transactionObject->text = "Животное ".$objectAnimal->nameAnimal." пристроено";
        $transactionObject->coast = 0;
        $transactionObject->type="frontend";
        $transactionObject->typeOper = 0;
        $idCell = DB::select('SELECT `idCell` FROM `Pitomnik_Animal` WHERE `name`=? AND `age`=? AND `characterAnimal`=? AND `typeAnimal`=?',
                        [$objectAnimal->name, $objectAnimal->age, $objectAnimal->characterAnimal, $objectAnimal->typeAnimal]);

        $deleteState=DB::delete('DELETE FROM Pitomnik_Animal WHERE `name`=? AND `age`=? AND `characterAnimal`=? AND `typeAnimal`=?',
                        [$objectAnimal->name, $objectAnimal->age, $objectAnimal->characterAnimal, $objectAnimal->typeAnimal]);
        if($deleteState!=0){
            DB::transaction( function() use ($transactionObject,$idCell){
            DB::update('UPDATE `Pitomnik_Cell` SET stata=stata-1 WHERE `id`=?', [$idCell[0]->idCell]);
            DB::insert('INSERT INTO `Pitomnik_Transaction`(`type`,`what` , `how` , `for`) VALUES (?,?,?,?)',
                [$transactionObject->typeOper, $transactionObject->text, $transactionObject->coast, $transactionObject->type]);
            });

        }


        //dd($delState);

        return $deleteState;

    }
    public function sellCell ($idCell, $costCell) {                                                                     //входные данные: id клетки, цена клетки
        $costCellsell=$costCell/2;
        $transactionObject= (object) Array();
        $transactionObject->text = "Продана пустая клетка за".$costCellsell;
        $transactionObject->coast = $costCellsell;
        $transactionObject->type="frontend";
        $transactionObject->typeOper = 0;
        $stataTransaction=DB::transaction( function () use ($idCell, $costCellsell, $transactionObject) {
           DB::update('UPDATE `Pitomnik_Chek` SET money=money+? WHERE `source`="AllMoney"', [$costCellsell]);
           DB::delete('DELETE FROM `Pitomnik_Cell` WHERE `id`=? AND `stata`=0',[$idCell]);
           DB::insert('INSERT INTO `Pitomnik_Transaction`(`type`,`what` , `how` , `for`) VALUES (?,?,?,?)',
                [$transactionObject->typeOper, $transactionObject->text, $transactionObject->coast, $transactionObject->type]);

        });


        return $stataTransaction;
}
    public function destroyer(){
        $animalArray = DB::select('SELECT * FROM `Pitomnik_Animal` WHERE `characterAnimal`="evil"');
       if($animalArray!=NULL) {           //выполняем проверку на нахождение злых животных
           if(count($animalArray)==1) $index=0; else $index = rand(0, count($animalArray));


           $cellNewId = $this->buyCellId(1, $animalArray[$index]->typeAnimal);
           //dump($animalArray[$index]);
           $transactionObject = (object)Array();
           if ($animalArray[$index]->typeAnimal == 'cat') {
               $coast = 5;
               $type = 'кошка';
           }
           if ($animalArray[$index]->typeAnimal == 'dog') {
               $coast = 10;
               $type = 'собака';
           }
           $transactionObject->text = $type . " " . $animalArray[$index]->name . "пересажен в новую клетку";
           $transactionObject->coast = $coast;
           $transactionObject->type = "frontend";
           $transactionObject->typeOper = 0;
           $this->defineCell($cellNewId, $animalArray[$index], $transactionObject);
           //dd($animalArray[$index]->idCell);
           DB::delete('DELETE FROM `Pitomnik_Cell` WHERE `id`=?', [$animalArray[$index]->idCell]); // удаляем разваленую клетку
           DB::delete('DELETE FROM `Pitomnik_Animal` WHERE `id`=?', [$animalArray[$index]->id]); // удаляем разваленую клетку
           //dd($animalArray[$index]->idCell);
           $animalArray[$index]->newCellId = $cellNewId;
           $resultArray=$animalArray[$index];
       }else{$resultArray=NULL;}
        return  $resultArray;
    }

}
