<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 3/11/2019
 * Time: 11:31 PM
 */

require "BaseController.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet as Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls as Xls;
require 'vendor/autoload.php';

require "model/Users.php";
require "model/AnswersheetModel.php";
require "model/Questions.php";

class SummaryController extends BaseController
{

    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function export() {
        $ea = new Spreadsheet();
        $Excel_writer = new Xls($ea);
        $ea->getProperties()
            ->setCreator('Ahmad Faiz')
            ->setTitle('Summary')
            ->setLastModifiedBy('Ahmad Faiz')
            ->setDescription('Summary data pilpres')
            ->setSubject('Summary pilpres')
            ->setKeywords('excel php summary pilpres')
            ->setCategory('politics')
        ;

        $ews = $ea->getSheet(0);
        $ews->setTitle('Data');

        // isi data
//        $ews->setCellValue('a1',"MANTAP");
//        $ews->setCellValue('a3', "RAKA CACAD");
        $users = new Users();
        $users->loadAllUsers();
        $i = 2;

        $arr = ["ID", "Nama", "Umur", "Profesi", "Pendidikan Terakhir", "HP","Provinsi", "Kota", "Tempat", "Jenis Kelamin"
                , "Politik 1", "Kelompok", "Posisi jkw", "thermo 1", "survei 1", "thermo 2", "survei 2", "Politik 2", "dbrief1", "dbrief2"];
        $style = array(
            'font' => array('bold' => true,),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),
        );
        $ews->getStyle('a1:t1')->applyFromArray($style);
        $ews->fromArray($arr, ' ', 'A1');

        $ews->fromArray($users->getList(), ' ', 'A2');


        foreach ($users->getList() as $user) {
            $answer = new AnswersheetModel();
            $answer->setUserId($user["id"]);
            $answer->loadFromUserId();
            $ews->setCellValue('n'.$i,$answer->getTermo1());
            $ews->setCellValue('p'.$i,$answer->getTermo2());

            if ($answer->getIdNews() != null) {
                if ($answer->getIdNews() == 0) $kelompok_berita = "Netral";
                else if ($answer->getIdNews() == 1) $kelompok_berita = "Positif";
                else $kelompok_berita = "Negatif";
                $ews->setCellValue('l'.$i,$kelompok_berita);
            }

            if ($answer->getPosition() != null) {
                $posisi_jokowi = $answer->getPosition() == 0 ? "Kiri" : "Kanan";
                $ews->setCellValue("m".$i, $posisi_jokowi);

                if ($answer->getTendency1() != null) {
                    $politik_1 = "Netral";
                    if ($answer->getTendency1() < 10) {
                        $tenden = 10 - $answer->getTendency1();
                        $politik_1 = $answer->getPosition() == 0 ? "Jokowi-".$tenden : "Prabowo-".$tenden;
                    } else  if ($answer->getTendency1() > 10){
                        $tenden = $answer->getTendency1() - 10;
                        $politik_1 = $answer->getPosition() == 0 ? "Prabowo-".$tenden : "Jokowi-".$tenden;
                    }
                    $ews->setCellValue("k".$i, $politik_1);
                }

                if ($answer->getTendecy2() != null) {
                    $politik_2 = "Netral";
                    if ($answer->getTendecy2() < 10) {
                        $tenden = 10 - $answer->getTendecy2();
                        $politik_2 = $answer->getPosition() == 0 ? "Jokowi-".$tenden : "Prabowo-".$tenden;
                    } else  if ($answer->getTendecy2() > 10){
                        $tenden = $answer->getTendecy2() - 10;
                        $politik_2 = $answer->getPosition() == 0 ? "Prabowo-".$tenden : "Jokowi-".$tenden;
                    }
                    $ews->setCellValue("r".$i, $politik_2);
                }

                if ($answer->getDbrief1() != null) {
                    $brief1 = $answer->getDbrief1() == 0 ? "Tidak" : "Ya";
                    $ews->setCellValue("s".$i, $brief1);
                }

                if ($answer->getDbrief2() != null) {
                    $brief2 = $answer->getDbrief2() == 0 ? "Tidak" : "Ya";
                    $ews->setCellValue("t".$i, $brief2);
                }

                $questions1 = new Questions();
                $questions1->loadUsersQuestion1($user["id"]);
                $comp = "";
                foreach ($questions1->getList() as $question) {
                    $comp = $comp.",".$question["question_answer"];
                }
                $ews->setCellValue("o".$i, $comp);

                $questions2 = new Questions();
                $questions2->loadUsersQuestion2($user["id"]);
                $comp = "";
                foreach ($questions2->getList() as $question) {
                    $comp = $comp.",".$question["question_answer"];
                }
                $ews->setCellValue("q".$i, $comp);

            }

            $i++;
        }


        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="report.xls"');
        $Excel_writer->save("php://output");
    }
}