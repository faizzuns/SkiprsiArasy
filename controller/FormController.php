<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 1/12/2019
 * Time: 12:34 PM
 */

require "BaseController.php";
require "core/View.php";
require "model/ListProvince.php";
require_once "core/Session.php";
require "model/UserModel.php";
require "model/AnswersheetModel.php";
require "model/QuestionModel.php";

class FormController extends BaseController
{
    protected $direct = false;

    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function start()
    {
        $session = new Session();
        if ($session->inSession()) {
            $this->direct = true;
            $this->continueForm();
        } else {
            $provincies = new ListProvince();
            $provincies->loadProvincies();

            $vars = [
                "provinces" => $provincies->getList()
            ];
            View::render("identity", $vars);
        }
    }

    public function register()
    {
        $user = new UserModel();
        $user->setName($this->request->post("name"));
        $user->setAge($this->request->post("age"));
        $user->setEducation($this->request->post("education"));
        $user->setPhone($this->request->post("phone"));
        $user->setProfession($this->request->post("profession"));
        $user->setProvince($this->request->post("province"));
        $user->setRegency($this->request->post("district"));
        $user->setVillage($this->request->post("village"));
        $user->setGender($this->request->post("gender"));
        $user->insert();

        $answersheet = new AnswersheetModel();
        $answersheet->setUserId($user->getId());
        $answersheet->generatePosition();
        $answersheet->insert();

        $session = new Session();
        $session->setSession($user->getId());

        View::redirect("/forms");
    }

    public function continueForm()
    {
        $ssn = new SessionModel();
        $ssn->setSessionId($_COOKIE["session"]);
        $ssn->load();

        $fill = new AnswersheetModel();
        $fill->setUserId($ssn->getUserId());
        $fill->loadFromUserId();

        if ($this->direct) $state = $fill->getState();
        else $state = (int)$this->request->post("state");

        if ($state == -1) return;
        else if ($state == 0) $this->firstTendency($ssn->getUserId());
        else if ($state == 1) $this->news($ssn->getUserId());
        else if ($state == 2) $this->firstMeasure($ssn->getUserId());
        else if ($state == 3) $this->distract($ssn->getUserId());
        else if ($state == 4) $this->correction($ssn->getUserId());
        else if ($state == 5) $this->secondMeasure($ssn->getUserId());
        else if ($state == 6) $this->secondTendency($ssn->getUserId());
        else if ($state == 7) $this->dbrief($ssn->getUserId());
        else if ($state == 8) $this->thankyou($ssn->getUserId());
        else if ($state == 9) $this->destroySession();

    }

    public function firstTendency($userId)
    {
        $fill = new AnswersheetModel();
        $fill->setUserId($userId);
        $fill->loadFromUserId();
        View::render("tendency", ["state" => 1, "position" => $fill->getPosition()]);
    }

    public function news($userId)
    {
        $fill = new AnswersheetModel();
        $fill->setUserId($userId);
        $fill->loadFromUserId();
        if (!$this->direct) {
            $score = $this->request->post("tendency");
            if ($score == 10) $pick = "Netral";
            else if ($score > 10) $pick = "Prabowo";
            else $pick = "Jokowi";
            $fill->setTendency1($score);
            $fill->setFirstPick($pick);
            $fill->generateIdNews();
            $fill->save();
        }
        View::render("news", ["content" => $this->getContent($fill->getIdNews())]);
    }

    public function firstMeasure($userId)
    {
        $fill = new AnswersheetModel();
        $fill->setUserId($userId);
        $fill->loadFromUserId();
//        if ($fill->getIdNews() == 0) $state = 8;
//        else $state = 3;
        View::render("thermometer", ["state" => 3]);
    }

    public function distract($userId)
    {
        $fill = new AnswersheetModel();
        $fill->setUserId($userId);
        $fill->loadFromUserId();
        if (!$this->direct) {
            $thermo = $this->request->post("thermometer");
            $fill->setTermo1($thermo);
            $fill->save();
            for ($x = 1; $x <= 23; $x++) {
                $answer = $this->request->post("op".$x);
                $question = new QuestionModel($userId, $x, $answer);
                $question->insert();
            }
        }
//        if ($fill->getIdNews() == 0) $state = 5;
//        else $state = 4;
        View::render("distract", ["state"=> 4]);
    }

    public function correction($userId)
    {
        $fill = new AnswersheetModel();
        $fill->setUserId($userId);
        $fill->loadFromUserId();
        if (!$this->direct) {
            $fill->setDistract(1);
            $fill->save();
        }
        View::render("correction", ["content" => $this->getCorrectionContent($fill->getId())]);
    }

    public function secondMeasure($userId)
    {
        $fill = new AnswersheetModel();
        $fill->setUserId($userId);
        $fill->loadFromUserId();
        if (!$this->direct) {
            $fill->setDistract(1);
            $fill->setCorrection(1);
            $fill->save();
        }
        View::render("thermometer", ["state" => 6]);
    }

    public function secondTendency($userId)
    {
        $fill = new AnswersheetModel();
        $fill->setUserId($userId);
        $fill->loadFromUserId();
        if (!$this->direct) {
            $thermo = $this->request->post("thermometer");
            $fill->setTermo2($thermo);
            $fill->save();
            for ($x = 24; $x <= 46; $x++) {
                $idx = $x - 23;
                $answer = $this->request->post("op".$idx);
                $question = new QuestionModel($userId, $x, $answer);
                $question->insert();
            }
        }
        View::render("tendency", ["state" => 7, "position" => $fill->getPosition()]);
    }

    public function dbrief($userId)
    {
        $fill = new AnswersheetModel();
        $fill->setUserId($userId);
        $fill->loadFromUserId();
        if (!$this->direct) {
            $score = $this->request->post("tendency");
            $fill->setTendecy2($score);
            $fill->save();
        }
        View::render("dbrief", []);
    }

    public function thankyou($userId)
    {
        $fill = new AnswersheetModel();
        $fill->setUserId($userId);
        $fill->loadFromUserId();
        if (!$this->direct) {
            $fill->setDbrief1($this->request->post("dbrief_1"));
            $fill->setDbrief2($this->request->post("dbrief_2"));
            $fill->save();
        }
        View::render("thankyou", []);
    }

    public function destroySession()
    {
        $wantToDestroyed = new Session();
        $wantToDestroyed->destroy();
        View::redirect("/");
    }

    public function getContent($news)
    {
        if ($news == 0) return "
            <p><strong>Presiden Jokowi Bahas Perkembangan Pembangunan Infrastruktur dalam Sidang Tahunan MPR Tahun 2018</strong></p>
             <p>
            Pembangunan infrastruktur kebijakan andalan Jokowi kerap kali menimbulkan pro dan kontra. Dalam Sidang Tahunan MPR 2018 Jokowi mengungkapkan beberapa poin penting mengenai pembangunan infrastruktur di Indonesia selama setahun terakhir. Menurut Presiden Jokowi, untuk membangun infrastruktur perlu kerjasama antar badan pemerintah, swasta, dan wakil rakyat karena setiap tahun anggaran infrastruktur meningkat signifikan. Bahkan, sampai saat ini masih belum bisa lepas dari utang asing. Di sisi lain, Jokowi juga menjelaskan bahwa pembangunan infrastruktur yang sudah selesai telah memberikan dampak bagi perekonomian rakyat. Jokowi menekankan bahwa pembangunan infrastruktur yang menjadi fokus pemerintahan jangan dipahami sebagai beban negara dan pembangunan fisik semata. Melainkan, merupakan sarana dalam membangun mental dan karakter sumber daya manusia didalamnya.</p>";
        else if ($news == 1) return "
            <p><strong>Jokowi Menaikkan Dana Desa sebesar 25% untuk Pembangunan Infrastruktur demi Terciptanya Keadilan Ekonomi</strong></p>
            <p>
            Komitmen Jokowi untuk membangun infrastruktur semakin nyata dengan menaikkan alokasi dana desa sebesar 25% dari 76 triliun menjadi 95 triliun. Banyak pihak yang mengapresiasi kebijakan ini, karena dapat meningkatkan pembangunan desa. Sebelumnya, beberapa pihak juga menyuarakan pentingnya meningkatkan pembangunan desa untuk mensejahterakan warga desa. Penambahan dana desa ini sangat berguna untuk membangun jalan kampung, jalan desa, jalan produksi pertanian, perbaikan irigasi, dan lain-lain. Pembangunan tersebut diharapkan dapat meningkatkan pendapatan warga desa. Presiden Jokowi optimis kebijakan ini akan menciptakan pemerataan ekonomi antara desa dan kota.            ";
        else return "
            <p><strong>Infrastruktur buat siapa? Pemerintahan Jokowi Potong Dana Desa untuk Pembangunan Infrastruktur Nasional</strong></p>
            <p>Presiden Jokowi menandatangani PP baru tentang pengalokasian dana desa sebesar 25% kepada dana pembangunan infrastruktur nasional. Banyak pihak yang menyayangkan kebijakan ini karena terlalu dipaksakan oleh pemerintah, hingga harus menggunakan dana desa yang seharusnya digunakan untuk kesejahteraan warga desa. Sebelumnya, beberapa pihak mengkritik pembangunan infrastruktur secara masif pada pemerintahan Jokowi yang meningkatkan utang negara, padahal proyeknya belum selesai dan belum memberikan dampak nyata terhadap penerimaan negara. Hingga November 2018 utang luar negeri meningkat 5,3%. Dengan utang Indonesia saat ini, maka setiap masyarakat Indonesia memiliki utang sekitar 13 juta per kepala. Bahkan, tidak sedikit yang menganggap pembangunan tersebut hanya menjadi ajang pencitraan Presiden Joko Widodo dalam Pemilihan Presiden 2019.</p>
            ";
    }

    public function getCorrectionContent($idNews) {
        if ($idNews == 1) return "
        <p>
        Berita dengan judul <i>Jokowi Menaikkan Dana Desa sebesar 25% untuk Pembangunan Infrastruktur demi Terciptanya Keadilan Ekonomi</i>, telah ditarik dari situs berita online nasional ternama sehari setelah dipublikasikan. Situs tersebut meralat berita dengan mengatakan bahwa Jokowi tidak menaikkan 25% dana desa untuk pembangunan infrastruktur nasional. Melainkan, Jokowi menganjurkan 25% dana desa yang dianggarkan pemerintah setiap tahunnya digunakan untuk membangun infrastruktur nasional. Dengan demikian, pemerintahan Jokowi tidak menaikkan dana desa sebesar 25%.
        </p>
        ";
        else if ($idNews == 2) return "
        <p>
        Berita dengan judul <i>Infrastruktur buat siapa? Jokowi menandatangani revisi PP Dana  Desa, 25% Dana Desa Dialokasikan untuk Pembangunan Infrastruktur Nasional</i>, telah ditarik dari situs berita online nasional ternama sehari setelah dipublikasikan. Situs tersebut meralat berita dengan mengatakan bahwa Jokowi tidak menetapkan pengalokasian 25% dana desa untuk pembangunan infrastruktur nasional. Melainkan, 25% dana desa tersebut dimaksudkan untuk pembangunan infrastruktur desa itu sendiri agar mempermudah mobilitas kegiatan ekonomi masyarakat desa.  Dengan demikian, Presiden Jokowi tidak mengalokasikan 25% dana desa untuk pembangunan infrastuktur nasional. 
        </p>
        ";
        else return "
        <p>
        Berita dengan judul Presiden Jokowi Bahas Perkembangan Pembangunan Infrastruktur dalam Sidang Tahunan MPR Tahun 2018, telah ditarik dari situs berita online nasional ternama sehari setelah dipublikasikan. Situs tersebut meralat berita dengan mengatakan bahwa Jokowi tidak membahas perkembangan infrastruktur dalam sidang tahunan MPR. Melainkan, Jokowi membahas pembangunan infrastruktur dalam rapat terbatas mengenai peningkatan kinerja pemerintah.  Dengan demikian, Presiden Jokowi tidak membahas pembangunan infrastruktur dalam sidang tahunan MPR tahun 2018.
        </p>
        ";
    }

}