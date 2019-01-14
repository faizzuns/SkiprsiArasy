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
        $user->insert();

        $answersheet = new AnswersheetModel();
        $answersheet->setUserId($user->getId());
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
        else if ($state == 0) $this->firstTendency();
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

    public function firstTendency()
    {
        View::render("tendency", ["state" => 1]);
    }

    public function news($userId)
    {
        $fill = new AnswersheetModel();
        $fill->setUserId($userId);
        $fill->loadFromUserId();
        if (!$this->direct) {
            $score = $this->request->post("tendency");
            if ($score == 5) $pick = "Netral";
            else if ($score > 5) $pick = "Prabowo";
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
        if ($fill->getIdNews() == 0) $state = 8;
        else $state = 3;
        View::render("thermometer", ["state" => $state]);
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
            for ($x = 1; $x <= 7; $x++) {
                $answer = $this->request->post("op".$x);
                $question = new QuestionModel($userId, $x, $answer);
                $question->insert();
            }
        }
        if ($fill->getIdNews() == 0) $state = 5;
        else $state = 4;
        View::render("distract", ["state"=> $state]);
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
            for ($x = 8; $x <= 14; $x++) {
                $idx = $x - 7;
                $answer = $this->request->post("op".$idx);
                $question = new QuestionModel($userId, $x, $answer);
                $question->insert();
            }
        }
        View::render("tendency", ["state" => 7]);
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
            Saat ini pembangunan infrastruktur secara masif dilakukan di Indonesia, mulai dari 
            daerah-daerah terpencil hingga kota-kota besar. Pembangunan infrastruktur memang menjadi salah satu 
            program rancangan strategis di era Jokowi yang bertujuan untuk meningkatkan konektivitas wilayah, 
            daya saing ekonomi nasional, dan menekan tingkat kesenjangan antar daerah di Indonesia. Sayangnya, 
            kebijakan andalan Jokowi pembangunan infrastruktur di Indonesia kerap kali menimbulkan pro dan 
            kontra. Oleh karena itu, dalam Sidang Tahunan MPR 2018 Jokowi menyampaikan pidato kinerja 
            lembaga negara selama satu tahun terakhir termasuk didalamnya membahas perkembangan 
            pembangunan infrastruktur di Indonesia.. Selain anggota MPR RI, pada sidang tahunan tersebut juga 
            dihadiri oleh para menteri, dan tamu undangan. Mantan Presiden Republik Indonesia juga turut 
            diundang dalam Sidang Tahunan MPR 2018, yakni BJ Habibie, Megawati Soekarnoputri, Susilo 
            Bambang Yudhoyono, serta para mantan wakil presiden.</p>
            <p>
            Dalam pidatonya, Presiden mengungkapkan beberapa poin penting mengenai pembangunan 
            infrastruktur di Indonesia selama setahun terakhir. Pertama, menurut Presiden Jokowi untuk 
            membangun infrastruktur perlu kerjasama antar badan pemerintah, swasta, dan seluruh wakil rakyat 
            untuk mencapai tujuan bersama. Hal tersebut dikarenakan setiap tahun anggaran infrastruktur meningkat 
            signifikan, sehingga negara membutuhkan dana besar untuk melakukan pembangunan infrastruktur.</p>
            <p>
            Bahkan, negara sampai saat ini masih belum bisa lepas dari utang asing demi membangun infrastruktur. 
            Di sisi lain, Jokowi juga menjelaskan bahwa pembangunan infrastruktur yang sudah selesai telah 
            memberikan dampak signifikan bagi perekonomian rakyat khususnya kemudahan akses distribusi dari 
            satu daerah ke daerah lain. Selain itu, menurut Jokowi pembangunan infrastruktur juga memberikan 
            dampak positif karena meningkatkan daya saing bangsa dan produktivitas masyarakat. Hal tersebut 
            membuat Indonesia bisa mengejar ketertinggalannya dari negara-negara lain. Untuk menutup pidatonya, 
            Jokowi menekankan bahwa pembangunan infrastruktur yang menjadi fokus pemerintahan saat ini jangan 
            hanya dipahami untuk membangun fisik sebuah negara saja tetapi membangun mental dan karakter 
            sumber daya manusianya juga. Oleh karena itu, jangan takut mengeluarkan dana yang besar untuk 
            membangun infrastruktur karena pada akhirnya akan membawa Indonesia menjadi negara yang maju.</p>
            ";
        else if ($news == 1) return "
            <p><strong>Jokowi Menaikkan Dana Desa sebesar 25% untuk Pembangunan Infrastruktur demi Terciptanya Keadilan Ekonomi</strong></p>
            <p>
            Saat ini pembangunan infrastruktur secara masif dilakukan di Indonesia, mulai dari 
            daerah-daerah terpencil hingga kota-kota besar. Pembangunan infrastruktur memang menjadi salah satu 
            program rancangan strategis di era Jokowi yang bertujuan untuk meningkatkan konektivitas wilayah, 
            daya saing ekonomi nasional, dan menekan tingkat kesenjangan antar daerah di Indonesia. Salah satu 
            fokus pemerintahan Jokowi adalah melakukan pembangunan infrastruktur untuk daerah tertinggal. 
            Komitmen Jokowi untuk membangun infrastruktur semakin terpampang nyata dengan menaikkan 
            alokasi dana desa sebesar 25% dari yang sebelumnya sebesar 76 triliun menjadi 95 triliun. Hal tersebut 
            diungkapkan oleh Presiden Jokowi dalam rapat kerja bersama Menteri Desa, Pembangunan Daerah 
            Tertinggal, dan Transmigrasi (Mendes PDTT) Eko Putro Sandjojo di Kantor Kementerian Desa, 
            Pembangunan Daerah Tertinggal, dan Transmigrasi di Jakarta Selatan (23/08/2018).</p>
            <p>
            Meskipun banyak pihak yang mengkritik kebijakan Jokowi menaikkan dana desa sebesar 25 %, 
            karena dianggap sebagai salah satu alat pendongkrak elektabilitas Jokowi di Pilpres 2019. Pemerintah 
            mengatakan bahwa tidak akan larut dalam kritik tersebut, namun tetap fokus membangun infrastruktur 
            di daerah-daerah tertinggal agar tercipta pemerataan ekonomi. Menurut Jokowi, tambahan dana desa 
            yang diberikan pemerintah dapat mempercepat pembangunan sehingga mampu meningkatkan 
            perekonomian daerah itu sendiri. Selain itu, menurut Mendes PDTT pemanfaatan dana desa untuk 
            membangun infrastruktur dapat dilakukan dengan membangun jalan kampung, jalan desa, jalan produksi 
            akses pertanian, perbaikan irigasi, dan lain-lain. Melalui kebijakan Jokowi meningkatkan dana desa 
            sebesar 25%, pemerintah optimis menciptakan pemerataan ekonomi antara desa dan kota sehingga tidak 
            ada lagi kesenjangan ekonomi.</p>
            ";
        else return "
            <p><strong>Infrastruktur buat siapa? Jokowi menandatangani revisi PP Dana  Desa, 25% Dana Desa Dialokasikan untuk Pembangunan Infrastruktur Nasional</strong></p>
            <p>
            Pembangunan infrastruktur secara masif di Indonesia memang kerap menimbulkan pro dan 
            kontra. Pembangunan infrastruktur menjadi salah satu program rancangan strategis era Jokowi yang 
            bertujuan untuk meningkatkan konektivitas wilayah, daya saing ekonomi nasional, dan menekan tingkat 
            kesenjangan antar daerah di Indonesia. Sayangnya, proyek-proyek infrastruktur pemerintahan Jokowi 
            seringkali dianggap membebankan keuangan negara karena bernilai ribuan triliun rupiah meskipun 
            proyeknya belum selesai dan belum memberikan dampak nyata terhadap penerimaan negara dalam 
            jangka pendek dan menengah sampai saat ini. Hal tersebut diungkapkan oleh salah satu ekonom Institute 
            for Development of Economics and Finance (INDEF) Enny Sri Hartati pada kesempatan Diskusi Ngopi 
            Bareng Dari Sebrang Istana di Restoran Ajag Ijig Jalan Juanda, Jakarta, Kamis (23/08/2018).</p>
            <p>
            Salah satu bukti pembangunan infrastruktur membebani keuangan negara semakin nyata, disaat 
            Presiden Jokowi melakukan revisi Peraturan Pemerintah (PP) tentang penyaluran dana desa. Jokowi 
            menandatangani PP baru tentang penyaluran dana desa pada 27 Agustus 2018. Salah satu poin dalam 
            revisi PP Dana Desa tersebut adalah Presiden Jokowi menyetujui pengalokasian dana desa sebesar 25% 
            kepada dana infrastruktur nasional. Pengalokasi dana ini dilakukan pemerintah terkait dengan tujuan pembangunan infrastruktur era jokowi dalam mengurangi disparitas pembangunan desa dengan perkotaan.
             Namun, banyak pihak yang menyayangkan kebijakan ini 
            karena pembangunan infrastruktur yang sangat masif dianggap terlalu dipaksakan oleh pemerintah. 
            Bahkan, menurut Ketua Komisi V DPR Fary Djemy Francis pembangunan infrastruktur justru hanya 
            menjadi ajang pencitraan bagi Presiden Joko Widodo untuk menghadapi kontestasi politik pada 
            Pemilihan Presiden 2019.</p>
            ";
    }

    public function getCorrectionContent($idNews) {
        if ($idNews == 1) return "
        <p>
        Berita dengan judul <i>Jokowi Menaikkan Dana Desa sebesar 25% untuk Pembangunan Infrastruktur demi Terciptanya Keadilan Ekonomi</i>, telah ditarik dari situs berita <strong><i>online </i></strong>nasional ternama. Media tersebut menyatakan bahwa berita yang baru dipublikasi sehari yang lalu merupakan berita yang tidak valid kebenarannya, sehingga tidak dapat dipercaya. Selain itu, terdapat ralat yang dilakukan bahwa Jokowi tidak menaikkan 25% dana desa untuk pembangunan infrastruktur nasional. Melainkan, Jokowi menganjurkan 25% dana desa yang dianggarkan pemerintah setiap tahunnya digunakan untuk membangun infrastruktur. Pembangunan infrastruktur desa itu sendiri dilakukan agar mempermudah mobilitas kegiatan ekonomi masyarakat desa. Dengan demikian, pemerintahan Jokowi tidak menaikkan dana desa sebesar 25%. Oleh karena itu, tim redaksi melakukan permohonan maaf karena sudah menyebarkan informasi yang salah kepada masyarakat yang telah membaca berita tersebut. 
        </p>
        ";
        else return "
        <p>
        Berita dengan judul <i>Infrastruktur buat siapa? Jokowi menandatangani revisi PP Dana  Desa, 25% Dana Desa Dialokasikan untuk Pembangunan Infrastruktur Nasional</i>, telah ditarik dari situs berita <strong><i>online </i></strong>nasional ternama. Media tersebut menyatakan bahwa berita yang baru dipublikasi sehari yang lalu merupakan berita yang tidak valid kebenarannya, sehingga tidak dapat dipercaya. Selain itu, terdapat ralat yang dilakukan bahwa Jokowi tidak menetapkan 25% dana desa tidak dialokasikan untuk infrastruktur nasional. Melainkan, 25% dana desa dimaksudkan untuk pembangunan infrastruktur desa itu sendiri agar mempermudah mobilitas kegiatan ekonomi masyarakat desa. Hal tersebut dilakukan pemerintah dalam rangka pemerataan pembangunan di desa maupun di kota. Oleh karena itu, tim redaksi melakukan permohonan maaf karena sudah menyebarkan informasi yang salah kepada masyarakat yang telah membaca berita tersebut. 
        </p>
        ";
    }

}