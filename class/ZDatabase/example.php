<?php
require 'class/ZDatabase/ZModel.php';

class IscrittiModel extends ZModel {
    // Table information
    protected static $tablename = "iscritti";
    protected static $tableid = "id";

    // Field list
    protected $fields = [
        "id",
        "dataCreazione",
        "creatore",
        "ultimaModifica"
    ];
    
}

$DB = new mysqli("localhost", "root", "", "asd");

IscrittiModel::setDB($DB);

$iscritto = IscrittiModel::findById(1);
echo $iscritto->dataCreazione;
$iscritto->dataCreazione = "2024-01-01 01:00:00";
$iscritto->save();

$i = new IscrittiModel();
$i->dataCreazione = "2024-01-01 01:00:00";
$i->creatore = "test";
$i->ultimaModifica = "2024-01-01 01:00:00";
var_dump($i);
$i->save();