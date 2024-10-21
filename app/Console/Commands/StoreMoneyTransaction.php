<?php

namespace App\Console\Commands;

use App\Models\MoneyTransaction;
use Illuminate\Console\Command;

class StoreMoneyTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:store-money-transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $month = 10;
        $year = 2024;
        $spendingPeriod = \Carbon\Carbon::createFromDate($year, $month);

        $startPeriod = $spendingPeriod->copy()->subMonth(1)->setDay(28)->startOfDay();
        $endPeriod = $spendingPeriod->copy()->setDay(28)->endOfDay();
        // $truncateAllData
        MoneyTransaction::query()
            ->where('transaction_date', '>=', $startPeriod->format('Y-m-d H:i:s'))
            ->where('transaction_date', '<=', $endPeriod->format('Y-m-d H:i:s'))
            ->delete();

        $datas = $this->retrieveData();
        foreach($datas as $data) {

            $newRecord = new MoneyTransaction;
            $newRecord->amount = intval($data['amount'] * 100);
            $newRecord->transaction_date = \Carbon\Carbon::createFromFormat('j M Y', $data['transactionDate'])->format('Y-m-d H:i:s');
            $newRecord->description = $data['description'];
            $newRecord->save();
        }
    }


    private function retrieveData() {
        $data = [
            [
              "foreignAmount" => "",
              "amount" => "-1.6",
              "transactionDate" => "6 Oct 2024",
              "description" => "512688972Q           MBBQR2172981       * 11111807092604",
              "postingDate" => "6 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-13.0",
              "transactionDate" => "6 Oct 2024",
              "description" => "DUITNOW QR           NURULAMINBINMOHAMED* 02420028",
              "postingDate" => "6 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-58.0",
              "transactionDate" => "6 Oct 2024",
              "description" => "                     NUR SOFEA KARMILA B* 002numbaone 2",
              "postingDate" => "6 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-54.05",
              "transactionDate" => "5 Oct 2024",
              "description" => "PAYMENT VIA MYDEBIT  MMZZ-BLOOMSVALE JKL* KUALA LUMPUR",
              "postingDate" => "5 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-20.0",
              "transactionDate" => "5 Oct 2024",
              "description" => "481685955Q           MBBQR2064977       * 11111806353446",
              "postingDate" => "5 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-8.0",
              "transactionDate" => "5 Oct 2024",
              "description" => "481529727Q           MBBQR1253597       * 11111806349534",
              "postingDate" => "5 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-5.0",
              "transactionDate" => "5 Oct 2024",
              "description" => "PAYMENT VIA MYDEBIT  PETRON JALAN TUN RA* KUALA LUMPUR",
              "postingDate" => "5 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "5.0",
              "transactionDate" => "5 Oct 2024",
              "description" => "REV PREAUTH MYDEBIT* PETRON JALAN TUN RA* KUALA LUMPUR",
              "postingDate" => "5 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-5.0",
              "transactionDate" => "5 Oct 2024",
              "description" => "PRE-AUTH MYDEBIT   * PETRON JALAN TUN RA* KUALA LUMPUR",
              "postingDate" => "5 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-14.05",
              "transactionDate" => "5 Oct 2024",
              "description" => "PAYMENT VIA MYDEBIT  99 SPEEDMART-3357  * KUALA LUMPUR",
              "postingDate" => "5 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-2.1",
              "transactionDate" => "4 Oct 2024",
              "description" => "                     ABZA SMART SDN BHD * Hafiz Aircond",
              "postingDate" => "4 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-550.0",
              "transactionDate" => "4 Oct 2024",
              "description" => "                     ABZA SMART SDN BHD * Hafiz Sewa Bil",
              "postingDate" => "4 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-51.4",
              "transactionDate" => "3 Oct 2024",
              "description" => "SALE DEBIT           MAXVALU-3015 SPHERE* W.P. K.LUMPUR,",
              "postingDate" => "3 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-8.0",
              "transactionDate" => "3 Oct 2024",
              "description" => "PAYMENT VIA MYDEBIT  MYCU RETAIL - NEXUS* KUALA LUMPUR",
              "postingDate" => "3 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-32.25",
              "transactionDate" => "3 Oct 2024",
              "description" => "PAYMENT VIA MYDEBIT  HOCK KEE KOPITIAM- * KUALA LUMPUR",
              "postingDate" => "3 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-2.22",
              "transactionDate" => "3 Oct 2024",
              "description" => "SALE DEBIT           CTX SH STN JAYA GEM* KUALA LUMPUR,",
              "postingDate" => "3 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "200.0",
              "transactionDate" => "3 Oct 2024",
              "description" => "SALE DEBIT           CTX SH STN JAYA GEM* KUALA LUMPUR,",
              "postingDate" => "3 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-200.0",
              "transactionDate" => "3 Oct 2024",
              "description" => "PREAUTH DEBIT        CTX SH STN JAYA GEM* KUALA LUMPUR,",
              "postingDate" => "3 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-30.0",
              "transactionDate" => "3 Oct 2024",
              "description" => "DUITNOW QR           NOOR HAZIMAH BINTI * 01652419",
              "postingDate" => "3 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-14.0",
              "transactionDate" => "3 Oct 2024",
              "description" => "DUITNOW QR           SASIREHKAA/PJAYARAM* 01549256",
              "postingDate" => "3 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-30.0",
              "transactionDate" => "3 Oct 2024",
              "description" => "2410031205310248     2024100312052633415* CELCOM XPAX",
              "postingDate" => "3 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-59.0",
              "transactionDate" => "3 Oct 2024",
              "description" => "SALE DEBIT           SK MAGIC-RPS       * KUALA LUMPUR,",
              "postingDate" => "3 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-9.6",
              "transactionDate" => "2 Oct 2024",
              "description" => "PAYMENT VIA MYDEBIT  TEALOSOPHY SDN. BHD* KUALA LUMPUR",
              "postingDate" => "2 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-7.05",
              "transactionDate" => "2 Oct 2024",
              "description" => "SALE DEBIT           MAXVALU-3015 SPHERE* W.P. K.LUMPUR,",
              "postingDate" => "2 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "500.0",
              "transactionDate" => "1 Oct 2024",
              "description" => "CASH DEPOSIT",
              "postingDate" => "1 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-60.0",
              "transactionDate" => "1 Oct 2024",
              "description" => "MBB CT               ENCIK MUHAMMAD HAFI* album wiplash",
              "postingDate" => "1 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-60.0",
              "transactionDate" => "1 Oct 2024",
              "description" => "wt group whiplash    Atynah Binti Matsud* swrn_r first p",
              "postingDate" => "1 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "60.0",
              "transactionDate" => "1 Oct 2024",
              "description" => "wt group whiplash    Atynah Binti Matsud* swrn_r first p",
              "postingDate" => "1 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "60.0",
              "transactionDate" => "1 Oct 2024",
              "description" => "wt group whiplash    Atynah Binti Matsud* swrn_r first p",
              "postingDate" => "1 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-60.0",
              "transactionDate" => "1 Oct 2024",
              "description" => "wt group whiplash    Atynah Binti Matsud* swrn_r first p",
              "postingDate" => "1 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-54.0",
              "transactionDate" => "1 Oct 2024",
              "description" => "MBB CT               VENESSA TEOH WEN HU* swrn_r wt k4 p",
              "postingDate" => "1 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-11.0",
              "transactionDate" => "1 Oct 2024",
              "description" => "PAYMENT VIA MYDEBIT  NASI KANDAR D`TANJO* KUALA LUMPUR",
              "postingDate" => "1 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-59.99",
              "transactionDate" => "1 Oct 2024",
              "description" => "2410011813540961     2421491246         * SHOPEE MOBILE",
              "postingDate" => "1 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-90.0",
              "transactionDate" => "1 Oct 2024",
              "description" => "SALE DEBIT           COWAY RPS - GP     * 0000000000, MY",
              "postingDate" => "1 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-6.65",
              "transactionDate" => "1 Oct 2024",
              "description" => "SALE DEBIT           MAXVALU-3015 SPHERE* W.P. K.LUMPUR,",
              "postingDate" => "1 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-3.0",
              "transactionDate" => "1 Oct 2024",
              "description" => "DUITNOW QR           SASIREHKAA/PJAYARAM* 84597011",
              "postingDate" => "1 Oct 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-30.0",
              "transactionDate" => "30 Sep 2024",
              "description" => "MBB CT               ENCIK MUHAMMAD HAFI* Digital Ocean",
              "postingDate" => "30 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-3.5",
              "transactionDate" => "30 Sep 2024",
              "description" => "PAYMENT VIA MYDEBIT  7-ELEVEN MALAYSIA  * Kuala Lumpur",
              "postingDate" => "30 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "78.0",
              "transactionDate" => "30 Sep 2024",
              "description" => "MBB CT-              ELVINA SULING ANAK * Fund Transfer",
              "postingDate" => "30 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-5.0",
              "transactionDate" => "30 Sep 2024",
              "description" => "077748236Q           MBBQR2090080       * 11111797835138",
              "postingDate" => "30 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-1.0",
              "transactionDate" => "30 Sep 2024",
              "description" => "PAYMENT VIA MYDEBIT  UOA-HORIZON & VERTI* KUALA LUMPUR,",
              "postingDate" => "30 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-210.95",
              "transactionDate" => "30 Sep 2024",
              "description" => "2409301735140159     67166968           * TELEKOM MALAYS",
              "postingDate" => "30 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-68.5",
              "transactionDate" => "30 Sep 2024",
              "description" => "2409301347290075     97202114           * TENAGA NASIONA",
              "postingDate" => "30 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-60.0",
              "transactionDate" => "30 Sep 2024",
              "description" => "MBB CT               MUHAMMAD HAFIZ RUSL* Charity",
              "postingDate" => "30 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-41.0",
              "transactionDate" => "30 Sep 2024",
              "description" => "MBB CT               MUHAMMAD HAFIZ RUSL* Balance CC",
              "postingDate" => "30 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-342.0",
              "transactionDate" => "30 Sep 2024",
              "description" => "MBB CT               MUHAMMAD HAFIZ RUSL* Laptop CC",
              "postingDate" => "30 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-1.0",
              "transactionDate" => "30 Sep 2024",
              "description" => "SALE DEBIT           SHELL -PELITA DAUR * PETALING JAYA,",
              "postingDate" => "30 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-1.0",
              "transactionDate" => "30 Sep 2024",
              "description" => "PREAUTH DEBIT        SHELL -PELITA DAUR * PETALING JAYA,",
              "postingDate" => "30 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-354.0",
              "transactionDate" => "30 Sep 2024",
              "description" => "2409300036140036     2024093000361081747* MYCELCOM POSTP",
              "postingDate" => "30 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-30.0",
              "transactionDate" => "30 Sep 2024",
              "description" => "2409300034400081     2024093000343535747* CELCOM XPAX",
              "postingDate" => "30 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-30.0",
              "transactionDate" => "30 Sep 2024",
              "description" => "2409300032340419     2024093000322852929* CELCOM XPAX",
              "postingDate" => "30 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-82.1",
              "transactionDate" => "30 Sep 2024",
              "description" => "2409300025320530     2024093000253002990* PENGURUSAN AIR",
              "postingDate" => "30 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "1.0",
              "transactionDate" => "30 Sep 2024",
              "description" => "SALE DEBIT           SHELL -PELITA DAUR * PETALING JAYA,",
              "postingDate" => "30 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-32.6",
              "transactionDate" => "29 Sep 2024",
              "description" => "PAYMENT VIA MYDEBIT  FAMILYMART-KOTA WAR* SEPANG, MYS",
              "postingDate" => "29 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-22.4",
              "transactionDate" => "29 Sep 2024",
              "description" => "PAYMENT VIA MYDEBIT  7-ELEVEN MALAYSIA  * Sepang",
              "postingDate" => "29 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-110.0",
              "transactionDate" => "29 Sep 2024",
              "description" => "009314836Q           MBBQR2080305       * 11111796612301",
              "postingDate" => "29 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-33.1",
              "transactionDate" => "29 Sep 2024",
              "description" => "SALE DEBIT           WATSON'S KIPMALL KO* SELANGOR, MY",
              "postingDate" => "29 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-5.0",
              "transactionDate" => "29 Sep 2024",
              "description" => "PAYMENT VIA MYDEBIT  JEAN PERRY FAIR    * KUALA LUMPUR",
              "postingDate" => "29 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-396.15",
              "transactionDate" => "29 Sep 2024",
              "description" => "PAYMENT VIA MYDEBIT  ECONSAVE-KOTA WARIS* SEPANG",
              "postingDate" => "29 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-300.0",
              "transactionDate" => "29 Sep 2024",
              "description" => "CASH WITHDRAWAL",
              "postingDate" => "29 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-11.0",
              "transactionDate" => "29 Sep 2024",
              "description" => "DUITNOW QR           YAPYAYAN           * 73239875",
              "postingDate" => "29 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-23.2",
              "transactionDate" => "29 Sep 2024",
              "description" => "PAYMENT VIA MYDEBIT  COCOLIKES          * SHAH ALAM",
              "postingDate" => "29 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-79.0",
              "transactionDate" => "29 Sep 2024",
              "description" => "PAYMENT VIA MYDEBIT  ND CELLULAR        * KUALA LUMPUR",
              "postingDate" => "29 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-286.0",
              "transactionDate" => "29 Sep 2024",
              "description" => "PAYMENT VIA MYDEBIT  SENTUL MOTOR ACCESS* KUALA LUMPUR,",
              "postingDate" => "29 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-9.14",
              "transactionDate" => "28 Sep 2024",
              "description" => "2409282211360886     2415704099         * SHOPEE MOBILE",
              "postingDate" => "28 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-5.0",
              "transactionDate" => "28 Sep 2024",
              "description" => "DUITNOW QR           LIU GU NIANG ENTERP* 82753348",
              "postingDate" => "28 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "5.0",
              "transactionDate" => "28 Sep 2024",
              "description" => "MBB CT               LIU GU NIANG ENTERP* swrn_r enhypen",
              "postingDate" => "28 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-5.0",
              "transactionDate" => "28 Sep 2024",
              "description" => "MBB CT               LIU GU NIANG ENTERP* swrn_r enhypen",
              "postingDate" => "28 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-5.0",
              "transactionDate" => "28 Sep 2024",
              "description" => "MBB CT               LIU GU NIANG ENTERP* swrn_r enhypen",
              "postingDate" => "28 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "5.0",
              "transactionDate" => "28 Sep 2024",
              "description" => "MBB CT               LIU GU NIANG ENTERP* swrn_r enhypen",
              "postingDate" => "28 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-21.75",
              "transactionDate" => "28 Sep 2024",
              "description" => "QR PAY SALES         Coda Payments      * 931097751Q",
              "postingDate" => "28 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-5.5",
              "transactionDate" => "28 Sep 2024",
              "description" => "PAYMENT VIA MYDEBIT  MIXUE              * KUALA LUMPUR,",
              "postingDate" => "28 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-12.2",
              "transactionDate" => "28 Sep 2024",
              "description" => "DUITNOW QR           NURULAMINBINMOHAMED* 82564533",
              "postingDate" => "28 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-65.15",
              "transactionDate" => "28 Sep 2024",
              "description" => "2409281635120250     2415050848         * SEAMONEY CAPIT",
              "postingDate" => "28 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-23.0",
              "transactionDate" => "27 Sep 2024",
              "description" => "MBB CT               MUHAMMAD HAFIZ RUSL* carousell item",
              "postingDate" => "27 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-11.35",
              "transactionDate" => "27 Sep 2024",
              "description" => "PAYMENT VIA MYDEBIT  99 SPEEDMART-3357  * KUALA LUMPUR",
              "postingDate" => "27 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-13.0",
              "transactionDate" => "27 Sep 2024",
              "description" => "DUITNOW QR           NASI KANDAR D TANJO* 72132694",
              "postingDate" => "27 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-48.99",
              "transactionDate" => "27 Sep 2024",
              "description" => "2409271124330812     2412425610         * SHOPEE MOBILE",
              "postingDate" => "27 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "-15.64",
              "transactionDate" => "27 Sep 2024",
              "description" => "2409271120150150     2412417107         * SHOPEE MOBILE",
              "postingDate" => "27 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ],
            [
              "foreignAmount" => "",
              "amount" => "5487.85",
              "transactionDate" => "27 Sep 2024",
              "description" => "September 24 Salary  IMT TECH SDN BHD     CIMB IBG Trans",
              "postingDate" => "27 Sep 2024",
              "amountCurrency" => "RM",
              "foreignAmountCurrency" => ""
            ]
        ];
        return $data;
    }
}
