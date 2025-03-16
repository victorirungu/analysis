<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/php-error.log');

session_start();
$title = "Expenses";
// require 'resources/includes/head.php'; 
require_once 'database/dbconnect.php';
define('ADMIN_BASE_URL', getenv('BASE_URL'));

//----------------------------------------
// FETCH THE LATEST FILE
//----------------------------------------
$latestFile = null;
$stmt = $conn->prepare("SELECT file_name FROM expenses ORDER BY created_at DESC LIMIT 1");
if ($stmt->execute()) {
    $result = $stmt->get_result();
    $latestFile = $result->fetch_assoc()['file_name'];
}

//----------------------------------------
// RETRIEVE THE JSON DATA
//----------------------------------------
$jsonEntries = [];
if ($latestFile) {
    $filePath = $latestFile;
    if (file_exists($filePath)) {
        $jsonContent = file_get_contents($filePath);
        $jsonEntries = json_decode($jsonContent, true) ?: [];
    }
}

//----------------------------------------
// GROUP THE DATA
//----------------------------------------
$groupedData = [];
$undefinedRows = [];

function validateDate($date, $format = 'd/m/Y')
{
    $dt = DateTime::createFromFormat($format, $date);
    if ($dt && $dt->format($format) === $date) {
        return true;
    }
    if ($format === 'd/m/Y') {
        $dt2 = DateTime::createFromFormat('j/n/Y', $date);
        if ($dt2 && $dt2->format('j/n/Y') === $date) {
            return true;
        }
    }
    if ($format === 'm/d/Y') {
        $dt2 = DateTime::createFromFormat('n/j/Y', $date);
        if ($dt2 && $dt2->format('n/j/Y') === $date) {
            return true;
        }
    }
    return false;
}

function getValidDate($entry) {
    $datePurchased = trim($entry['date_purchased'] ?? '');
    $dateToBePaid  = trim($entry['date_to_be_paid'] ?? '');
    $datePaid      = trim($entry['date_paid'] ?? '');

    if (!validateDate($datePurchased, 'd/m/Y')) {
        if (validateDate($datePurchased, 'm/d/Y')) {
            $dt = DateTime::createFromFormat('m/d/Y', $datePurchased);
            return $dt->format('F Y');
        } else {
            if (validateDate($dateToBePaid, 'd/m/Y')) {
                $dt = DateTime::createFromFormat('d/m/Y', $dateToBePaid);
                return $dt->format('F Y');
            } else if (validateDate($datePaid, 'd/m/Y')) {
                $dt = DateTime::createFromFormat('d/m/Y', $datePaid);
                return $dt->format('F Y');
            } else {
                return false;
            }
        }
    } else {
        $dt = DateTime::createFromFormat('d/m/Y', $datePurchased);
        if (!$dt) {
            $dt = DateTime::createFromFormat('j/n/Y', $datePurchased);
        }
        return $dt->format('F Y');
    }
}

function unifySupplierName($rawName) {
    $name = strtoupper($rawName);
    $name = preg_replace("/[^A-Z0-9\s]/", " ", $name);
    $name = preg_replace('/\s+/', ' ', $name);
    $name = trim($name);

    $dictionary = [
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // FARMERS CHOICE
    "FARMERS CHOICE" => [
        "FARMETRS CHOICE LTD",
        "FARMERS CHOICE",
        "FARMERSCHOICE",
        "FARMERS CHOICE LTD",
        "FARMERS CHOICE LIMITED",
        "FARMERES CHOICE LTD"
    ],
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // NAIVAS
    "NAIVAS" => [
        "NAIVAS LTD", 
        "NAIVAS SUPERMARKET 1",
        "NAIVAS SUPERMARKET",
        "NAIVAS LIMITED",
        "NAIVAS SUPERMARKET LTD",
        "NAIVAS SUPERMAKERT",
        "NAIVAS SUPERAMRKET",
        "NAIVAS SUPERMAKET"  // added variant without E
    ],
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // QUICKMART
    "QUICKMART" => [
        "QUICKMART", 
        "QUICKMART KIAMBU ROAD",
        "QUICKMART SUPERMAKET",
        "QUICKMART SUPRMAKERT",
        "QUICKMART THOME BRANCH",
        "QUICKMART SUPERMALRKET",
        "QUCIKMART SUPERMARKET",
        "QUICKMART SUPERMARKET",
        "QUICKMART LTD",
        "QUICKMART LIMITED",
        "QUICKMART SUOERMARKET"
    ],
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // MBURU DISTRIBUTORS
    "MBURU DISTRIBUTORS" => [
        "MBURU SODA DISTRIBUTORS",
        "MBURU DISTRIBUTORS",
        "PETER MBURU SODA DISTRIBUTORS",
        "MBURU SODA DISTRIBUTOR",
        "PETER MBURU",
        "PETER MBURU DISTRIBUTORS",
        "MBURU SODA",
        "MBURU DISTRIBUTORS LTD"
    ],
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // NOVEL GREEN STORES
    "NOVEL GREEN STORES" => [
        "NOVE L GREEN STORES",
        "NOVEL GREEN STORES",
        "NOVEL GREEN STORES LIMITED",
        "NOVEL GREEN STORE LTD",
        "NOVEL GREEN STOTRES",
        "NOVEL GREEN STORES LTD"
    ],
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // WILKEM ENTERPRISES
    "WILKEM ENTERPRISES" => [
        "WILKEM ENTERPRISES", 
        "WILKEM ENTERPRISES LTD",
        "WILKEM ENTERPROSES LTD",
        "WILKEM ENETRPRISES",
        "WILKEM ENTERPRISE",
        "WILKEM ENETRPROSES LTD"
    ],
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // ACUITY
    "ACUITY" => [
        "ACUITY", 
        "ACUITY VENTURES LTD",
        "ACUITY VENTURES LIMITED",
        "ACUITY VENTURES LTS",
        "ACUITY LIMITED"
    ],
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // MASAFA QUENCH
    "MASAFA QUENCH" => [
        "MASAFA QUENCH", 
        "MASAFA QYUCH",
        "MASAFA QOUENCH",
        "MASAFA QOUECH",
        "MASAFA QOUNCH"
    ],
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // ADAMJI
    "ADAMJI" => [
        "ADAMJI", 
        "ADAMI MULTI SUPPLIERS LTD",
        "ADAMJI MULTI SUPPLIES LTD"
    ],
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // JWINES
    "JWINES" => [
        "JWINES", 
        "JWINES KASARANI ENETRPRISES LTD",
        "JWINES KASARANI ENTERPRISES LTD",
        "JWINES KASARANI ENTEEPRISES LTD",
        "JWINES KASARANI ENTERPRIOSES LTD",
        "JWINES KASARANI ENTREPRISES LRD"
    ],
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // NEMCHEM INTERNATIONAL
    "NEMCHEM INTERNATIONAL" => [
        "NEMCHEM INTERNATIONAL", 
        "NEMCHEM INTERNATIONAL KENYA LIMITED",
        "NEMCHEM INTERNATIONAL LTD",
        "NEMCHEM INTERNATIONAL K LTD",
        "NEMCHEM KENYAM INTERNATIONAL",
        "NEMCHEM INTERNATIOL K LTD",
        "NEMCHEM",
        "NEMCHEM IN TERNATIONAL KENYA LTD",
        "NEMCHEM KENYA INTERNATIONAL"
    ],
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // MITANNA GASES
    "MITANNA GASES" => [
        "MITANNA GASES", 
        "MITANNA GASES LTD",
        "MITANNA GASES LIMITED",
        "MITANNA GAS"
    ],
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // QUESED
    "QUESED" => [
        "QUESED GLOBAL INVESTMENTS", 
        "QUESED",
        "QUESED GLOBAL"
    ],
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // MAHITAJI ENTERPRISES LTD
    "MAHITAJI ENTERPRISES LTD" => [
        "MAHITAJI ENTERPRISES LTD", 
        "MAHITAJU ENTERPRISES LTD",
        "GILLYS MAHITAJI STORES"
    ],
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // WOODVALE LIQOUR STORE
    "WOODVALE LIQOUR STORE" => [
        "WOODVALE LIQOUR STORE", 
        "WOODVALE LIQOUR HOUSE",
        "WOODVALE LIQOUER STORE"
    ],
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // TEXFARM
    "TEXFARM" => [
        "TEXFARM",
        "TEXFARM SUPPLIES LTD",
        "TEX-FARM SUPPLIES LTD",
        "TEX FARM SUPPLIES LTD",
        "TEX-FARM BUTCHERY",
        "TEX FARM BUTCHERY"
    ],
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // HEAVES INTERNATIONAL
    "HEAVES INTERNATIONAL" => [
        "HEAVES INTERNATIONAL",
        "HEAVES INTERNATIONAL LIMITED",
        "HEAVES INTERNATIONAL LTD",
        "HEAVES LIMITED",
        "HEAVES",
        "HEAVES LTD"
    ],
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // PRIME CUTS
    "PRIME CUTS" => [
        "PRIME CUTS",
        "PRIME CUT",
        "STARZ PRIME CUTS",
        "STARZ PRIME CUT",
        "STARZ PRIMEM CUTS"
    ],
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // HUGESHARE
    "HUGESHARE" => [
        "HUGESHARE",
        "HUGESHARE LIOMITED",
        "HUGESHARE LIMITED",
        "HUGESHARE LTD",
        "HIGESHARE LTD",
        "HIGESHARE"
    ],
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // TOP IT UP DISTRIBUTOR LTD
    "TOP IT UP DISTRIBUTOR LTD" => [
        "TOP IT UP DISTRIBUTOR LTD",
        "TOP IT UP DISTRIBUTOR TD",
        "TOP IT UP DISTRIBUTOR KLTD",
        "TOP IT UP DISTRIBUTOR"
    ],
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // BIO FOODS PRODUCTS
    "BIO FOODS PRODUCTS" => [
        "BIO FOODS PRODUCTS",
        "BIO FOOD PRODUCTS LTD",
        "BIO FOOD PRODUCTS LIMITED",
        "BIO FOOSS LTD",
        "BIO FOODS LTD",
        "BIO",
        "BIO LIMITED"
    ],
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // PARKLAND LANDSCAPER LTD
    "PARKLAND LANDSCAPER LTD" => [
        "PARKLAND LANDSCAPER LTD",
        "PACKLAMAD LANDSCAPER LTD",
        "PACKLAND LANDSCAPER LTD",
        "PAKLANDS LANDSCAPER"
    ],
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // GLACIER PRODUCTS LIMITED
    "GLACIER PRODUCTS LIMITED" => [
        "GLACIER PRODUCTS LIMITED",
        "GLACIER PRODUCTS LTD",
        "GLACIER FOOD PRODUCTS LTD",
        "GLACIER PRDCTS LTD",
        "GLACIER LIMITED",
        "GLACIER"
    ],
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // WAY BEYOND VENTURES LTD
    "WAY BEYOND VENTURES LTD" => [
        "WAY BEYOND VENTURES LTD",
        "WAY BEYONF VENTURES LTD",
        "WAY BEYOND VENTURES LIMITED"
    ],
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // KIMFAY EAS AFRICA LTD
    "KIMFAY EAS AFRICA LTD" => [
        "KIMFAY EAS AFRICA LTD",
        "KIMFAY EA LIMITED",
        "KIMFAY EAST AFRICA LTD",
        "KIMFAY",
        "KIM FAY E A LTD",
        "KIM FAY",
        "KIMFAY E A LTD",
        "KIM FAY EA LTD"
    ],
    // ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
    // NEW SUPPLIERS (not in your original array)
    "BAFKADO BUTCHERY" => [
        "BAFKADO BUTCHERY"
    ],
    "JUDAMO" => [
        "JUDAMO ENTERPRISES",
        "JUDAMO",
        "JUDAMO ENTERPRISE",
        "JUDAMO ENTERPRISES LTD"
    ],
    "BIZZYBEE HONEY" => [
        "BIZZYBEE HONEY LTD",
        "BIZZYBEE HONEY COMPANY",
        "BIZZYBEE HINEY LIMITED",
        "BIZZYBEE LTD",
        "BIZZYBEE HONEY LIMITED",
        "BIZZY BEE HONEY LTD",
        "BIZZYBEE HONEY COMPNAY",
        "BIZZYBEE HONEY"
    ],
    "CAPITAL ICT LTD" => [
        "CAPITAL ICT LTD"
    ],
    "JAVA HOUSE AFRICA" => [
        "JAVA HOUSE AFRICA",
        "JAVA HOUSE AFRICA LTD",
        "JAVA HOUSE AFRICA LIMITED",
        "JAVA HOUSE COFFEE AFRICA",
        "JAVA HOUSE AFRIKA",
        "JAVA HOUSE",
        "JAVA HOUSE LTD",
        "JAVA COFEE"
    ],
    "JIBU" => [
        "JIBU WATER",
        "JIBU"
    ],
    "EDU SUPPLIER ELECTRICAL AND ELECTRONICS" => [
        "EDU SUPPLIER ELECTRICAL AND ELECTRONICS"
    ],
    "GAZPROM" => [
        "GAZPROM"
    ],
    "HEARTLAND KENYA" => [
        "HEARTLAND KENYA LIMITED",
        "HEARTLAND KENYA LTD",
        "HEARTLAND KENYS LIMITED",
        "HEARLAND KENYA LTD"
    ],
    "WANTA ELECTRONICS" => [
        "WANTA ELECTRONICS"
    ],
    "EXTREME OCCASIONS" => [
        "EXTREME OCCASSIONS",
        "EXTREME OCCASION LIMITED"
    ],
    "CAPEX COMMERCILA KITCHEN" => [
        "CAPEX COMMERCILA KITCHEN"
    ],
    "JOYNEM DELICACIES" => [
        "JOYNEMM DELICACIES ACCESSORIES",
        "JOYNEM DELICACIES"
    ],
    "BLESSED FOOD ENTERPRISES" => [
        "BLESSED FOOD ENTERPRISES"
    ],
    "HAPPY COW LTD" => [
        "HAPPY COW LTD"
    ],
    "NJAMBAS BOOK CENTER LTD" => [
        "NJAMBAS BOOK CENTER LTD"
    ],
    "ZOARI ENTERPRIOSES LIMITED" => [
        "ZOARI ENTERPRIOSES LIMITED"
    ],
    "KIDS CORNER" => [
        "KIDS CORNER"
    ],
    "FEMI GAS POINT" => [
        "FEMI GAS POINT",
        "FEMO GAS POINT",
        "FEMI GAS",
        "FEMIGAS POINT"
    ],
    "HOME BEST MATT" => [
        "HOME BEST MATT"
    ],
    "TOP SERVE LIMITED" => [
        "TOP SERVE LIMITED"
    ],
    "BAFKADO SARAH SONS" => [
        "BAFKADO SARAH SONS MEAT SYPPLY",
        "BAFKADO SARAH SONS MEAT SUPPLY",
        "BAFKADO SARAH SONS"
    ],
    "QUEST WINE AGENCIES" => [
        "QUEST WINE AGENCIES",
        "QUEST WINE AGENCIES LTD",
        "QUEST WINE ANGIENCES LTD"
    ],
    "LIQOUR LIBRARY" => [
        "LIQOUR LIBRARY"
    ],
    "A1 LIQOUR STORE" => [
        "A1 LIQOUR STORE"
    ],
    "EDU SUPER ELECTRICALS" => [
        "EDU SUPER ELECTRICALS"
    ],
    "EAST MATT SUPERMARKET" => [
        "EAST MATT SUPERMARKET"
    ],
    "NANCY FOODS SUPPLIERS" => [
        "NANCY FOODS SUPPLIERS"
    ],
    "TUSKYS SUPERMARKET" => [
        "TUSKYS SUPERMARKET"
    ],
    "KERICHO GOLD TEA LEAVES" => [
        "KERICHO GOLD TEA LEAVES"
    ],
    "CARREFOUR SUPERMARKET" => [
        "CARREFOUR SUPERMAKET",
        "CARREFFOUR"
    ],
    "SUELA TRADING" => [
        "SUELA TRADING LTD",
        "SUELA TRADING LIMITED"
    ],
    "SENDA EBTERPRISES" => [
        "SENDA EBTERPRISES"
    ],
    "KOOLKARE" => [
        "KOOLKARE LTD",
        "KOOLKARE"
    ],
    "HOUSE OF LEATHER" => [
        "HOUSE OF LEATHER"
    ],
    "MAMAS KITCHEN" => [
        "MAMAS KITCHEN"
    ],
    "TASS ENTERPRISES LTD" => [
        "TASS ENTERPRISES LTD"
    ],
    "JEMAMI WHOLESALERS LTD" => [
        "JEMAMI WHOLESALERS LTD"
    ],
    "GENERAL HARDWARE" => [
        "GENERAL HARDWARE"
    ],
    "CACUM ENERGY LIMITED" => [
        "CACUM ENERGY LIMITED"
    ],
    "HJOHNTECH GENERAL MERCHANTS" => [
        "HJOHNTECH GENERAL MERCHANTS"
    ],
    "JIMOS WHOLESALERS RETAILERS" => [
        "JIMOS WHOLESALERS RETAILERS"
    ],
    "AL BIDAYA" => [
        "AL BIDAYA"
    ],
    "SOFTERIA TECH LTD" => [
        "SOFTERIA TECH LTD"
    ],
    "JIMOS WHOLESALERS" => [
        "JIMOS WHOLESALERS"
    ],
    "CEDERS RESTAURANT" => [
        "CEDERS RESTAURANT"
    ],
    "KADIRO AYALA GALCHA" => [
        "KADIRO AYALA GALCHA"
    ],
    "PARALLAX ENGINEERING" => [
        "PARALLAX ENGINEERING",
        "PARALLAX ENGINEERING SOLUTIONS"
    ],
    "KIAMAIKO" => [
        "KIAMAIKO LTD GOAT MEAT",
        "GOAT MEAT KIAMAIKO",
        "KIAMAIKO GOAT",
        "KIAMAIKO GOAT MEAT",
        "KIAMAIKO BEEF TOPSIDE",
        "KIAMAIKO MBUZO"
    ],
    "KIAMAIKO DISTRIBUTORS" => [
        "KIAMAIKO DISTRIBUTORS"
    ],
    "SAVANNAH BRANDS" => [
        "SAVANNAH BRANDS",
        "SAVANNAH BRANDS COMPANY LIMITED"
    ],
    "FANISI INDUSTRIES" => [
        "FANISI INDUSTRIES",
        "FANISI INDUSRIES",
        "FANISI INDUSTRIES LTD"
    ],
    "XPRESS LAUNDRY" => [
        "XPRESS LAUNDRY"
    ],
    "MR HOSEA" => [
        "MR HOSEA"
    ],
    "KIAMBU SOKO VEGETABLES" => [
        "KIAMBU SOKO VEGETABLES"
    ],
    "CHIEFS EYE KITCHENWARE" => [
        "CHIEFS EYE KITCHENWARE"
    ],
    "SUKAN TRADING LTD" => [
        "SUKAN TRADING LTD"
    ],
    "LIQUOR SQUARE" => [
        "LIQUOR SQUARE",
        "LIQOUR SQUARE",
        "LIQOUE SQUARE LTD"
    ],
    "MESH CEREALS" => [
        "MESH CEREALS"
    ],
    "TILES LTD CLEANING MOPS" => [
        "TILES LTD CLEANING MOPS"
    ],
    "HOUSEHOLD ENTERPRISES" => [
        "HOUSEHOLD ENTERPRISES"
    ],
    "VICTORY FARMS TILAPIA" => [
        "VICTORY FARMS TILAPIA"
    ],
    "SOKO ORDER" => [
        "SOKO ORDER"
    ],
    "DAN FRUITS POTATOES" => [
        "DAN FRUITS POTATOES"
    ],
    "FAIRPRICE GENERAL HARDWARE" => [
        "FAIRPRICE GENERAL HARDWARE"
    ],
    "JIMMY TIFFAMS ENTERPRISE" => [
        "JIMMY TIFFAMS ENTERPRISE"
    ],
    "L K KITCHEN APPLIANCES" => [
        "L K KITCHEN APPLIANCES"
    ],
    "WADI DEGLA CLUBS AFRICA" => [
        "WADI DEGLA CLUBS AFRICA"
    ],
    "BESTMARK TRADERS" => [
        "BESTMARK TRADERS"
    ],
    "DAJA ENTERPRISES LTD" => [
        "DAJA ENTERPRISES LTD"
    ],
    "GODEL ENBTERPRISES" => [
        "GODEL ENBTERPRISES"
    ],
    "VALENTINE CAKE HOUSE KIAMBU" => [
        "VALENTINE CAKE HOUSE KIAMBU"
    ],
    "QUEST LIQUOR STORE" => [
        "QUEST LIQOUR STORE",
        "QUEST LIQOR STORE",
        "QOUEST LIQOUR HOUSE"
    ],
    "EAGM" => [
        "EAGM"
    ],
    "MAGIC JUICE" => [
        "MAGIC JUICE",
        "MAGIC JUICES"
    ],
    "SLATTER AND WHITTAKER" => [
        "SLATTER AND WHITTAKER"
    ],
    "DEMAX CEREALS SHOP" => [
        "DEMAX CEREALS SHOP"
    ],
    "VASCO CARTALA EVENT" => [
        "VASCO CARTALA EVENT"
    ],
    "PARTY PARLOUR HUB" => [
        "PARTY PARLOUR HUB"
    ],
    "T" => [
        "T"
    ],
    "ABBA BRAND AFRICA" => [
        "ABBA BRAND AFRICA"
    ],
    "MOHAMED DONO GOSKE" => [
        "MOHAMED DONO GOSKE"
    ],
    "JITRIT WIFI" => [
        "JITRIT WIFI"
    ],
    "JUMLA CUTS BUTCHERY" => [
        "JUMLA CUTS BUTCHERY"
    ],
    "KRA FILLING" => [
        "KRA FILLING"
    ],
    "SENSES RESTAURANT" => [
        "SENSES RESTAURANT"
    ],
    "SIZE 7 GUTTED FISH" => [
        "SIZE 7 GUTTED FISH"
    ],
    "MOYALISO MEAT SUPPLY" => [
        "MOYALISO MEAT SUPPLY"
    ],
    "WA DENNIS CEREALS" => [
        "WA DENNIS CEREALS"
    ],
    "JUMIA KENYA" => [
        "JUMIA KENYA"
    ],
    "LIQUOR STORE" => [
        "LIQUOR STORE"
    ],
    "BAMA MARKET" => [
        "BAMA MARKET"
    ],
    "EXPRESS DRY CLEAN" => [
        "EXPRESS DRY CLEAN"
    ],
    "KIAMBU VEGETABLES" => [
        "KIAMBU VEGETABLES"
    ],
    "FLAMEZ FIER COMPANY LTD" => [
        "FLAMEZ FIER COMPANY LTD"
    ],
    "GOLDEN SOLUTIONS" => [
        "GOLDEN SOLUTIONS"
    ],
    "CHUTE HUMBE GONDE" => [
        "CHUTE HUMBE GONDE"
    ],
    "BENUE ENTERPRISES COMPANY LTD" => [
        "BENUE ENTERPRISES COMPANY LTD"
    ],
    "FUMIKLINCLEANING ENTERPRISES" => [
        "FUMIKLINCLEANING ENTERPRISES",
        "FUMIKLIN ENTERPRISE"
    ],
    "GARBAGE BAG COLLECTION" => [
        "GARBAGE BAG COLLECTION"
    ],
    "ACUITY VENTURES" => [
        "ACUITY VENTURES"
    ],
    "GLACIER" => [
        "GLACIER"
    ],
    "LEILAND ENTERPRISES LTD" => [
        "LEILAND ENTERPRISES LIMITED",
        "LEILAND ENTERPRISES LTD",
        "LEILAD ENTERPRISES LTD"
    ],
    "WOLDENA MEAT SUPPLIERS" => [
        "WOLDENA MEAT SUPPLIERS"
    ],
    "MBUZI MEAT" => [
        "MBUZI MEAT"
    ],
    "GOAT MEAT" => [
        "GOAT MEAT"
    ],
    "WADENNISCEREALS" => [
        "WADENNISCEREALS"
    ],
    "SOKO 1" => [
        "SOKO 1"
    ],
    "JITRIT VEGETABLES" => [
        "JITRIT VEGETABLES"
    ],
    "GEDIA INVESTMENT" => [
        "GEDIA INVESTMENT"
    ],
    "FLAMEZ FIRE COMPANY LTD" => [
        "FLAMEZ FIRE COMPANY LTD"
    ],
    "SOKO 2" => [
        "SOKO 2"
    ],
    "HEBAIC GENERAL TRADERS LTD" => [
        "HEBAIC GENERAL TRADERS LTD"
    ],
    "KAMINDI SELFRIDGES SUPERMAKET" => [
        "KAMINDI SELFRIDGES SUPERMAKET"
    ],
    "TWARDY LTD" => [
        "TWARDY LTD"
    ],
    "SASHA BOMU" => [
        "SASHA BOMU"
    ],
    "BETTY MARKET DAY" => [
        "BETTY MARKET DAY"
    ],
    "LUKO GOAT MEAT" => [
        "LUKO GOAT MEAT"
    ],
    "JOVECA ENTERPRISES" => [
        "JOVECA ENTERPRISES"
    ],
    "MAXCARE HARDWARE STOPE" => [
        "MAXCARE HARDWARE STOPE"
    ],
    "PARRAREX ENGINNERING" => [
        "PARRAREX ENGINNERING"
    ],
    "ECONOMU HARDWARE AND GEBERAL STORES" => [
        "ECONOMU HARDWARE AND GEBERAL STORES"
    ],
    "GLASS CRAFT LIMITED" => [
        "GLASS CRAFT LIMITED"
    ]
];


    foreach ($dictionary as $masterName => $synonyms) {
        if (in_array($name, $synonyms, true)) {
            return $masterName;
        }
    }
    return $name;
}

foreach ($jsonEntries as $entry) {
    $supplier = unifySupplierName($entry['supplier_name'] ?? '');
    $datePurchased = trim($entry['date_purchased'] ?? '');
    $amount = (float) ($entry['amount'] ?? 0);

    if (empty($supplier) || empty($datePurchased)) {
        $undefinedRows[] = $entry;
        continue;
    }

    $monthYear = getValidDate($entry);
    if (!$monthYear) {
        $undefinedRows[] = $entry;
        continue;
    }

    if (!isset($groupedData[$monthYear])) $groupedData[$monthYear] = [];
    if (!isset($groupedData[$monthYear][$supplier])) $groupedData[$monthYear][$supplier] = [];
    $groupedData[$monthYear][$supplier][] = $entry;
}

//-------------------------------------------------------------------
// Build a supplier summary: overall and monthly totals for each supplier
//-------------------------------------------------------------------
$supplierSummary = [];
foreach ($groupedData as $monthYear => $suppliers) {
    foreach ($suppliers as $supplierName => $entries) {
        if (!isset($supplierSummary[$supplierName])) {
            $supplierSummary[$supplierName] = ['total' => 0, 'monthly' => []];
        }
        $monthTotal = 0;
        foreach ($entries as $entry) {
            $amount = (float)($entry['amount'] ?? 0);
            $monthTotal += $amount;
        }
        $supplierSummary[$supplierName]['total'] += $monthTotal;
        $supplierSummary[$supplierName]['monthly'][$monthYear] = $monthTotal;
    }
}

//-------------------------------------------------------------------
// Check if a supplier filter is set via GET parameter (e.g., ?supplier=NAIVAS)
//-------------------------------------------------------------------
$supplierFilter = '';
if (isset($_GET['supplier']) && !empty($_GET['supplier'])) {
    $supplierFilter = unifySupplierName($_GET['supplier']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $title; ?></title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- DataTables Bootstrap 5 CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">
  <style>
      body {
          background-color: #f9f9f9;
      }
      h2, h3 {
          margin-top: 20px;
          margin-bottom: 20px;
      }
      /* Custom DataTables adjustments */
      table.dataTable th, table.dataTable td {
          padding: 12px;
      }
      tr.subtotal-row {
          background-color: #e9ecef;
          font-weight: bold;
      }
      tr.total-row {
          background-color: #dee2e6;
          font-weight: bold;
      }
      tr.undefined-row {
          background-color: #f8d7da;
          font-weight: bold;
      }
      tr.absolute-total-row {
          background-color: #c3e6cb;
          font-weight: bold;
      }
      .dt-right {
          text-align: right;
      }
  </style>
</head>
<body>
  <div class="container my-4">
      <h2 class="text-center">Expenses</h2>

      <!-- Supplier-Specific Detailed Analysis (if a supplier is filtered) -->
      <?php if ($supplierFilter): ?>
      <h3>Detailed Analysis for Supplier: <?php echo $supplierFilter; ?></h3>
      <div class="table-responsive">
        <table id="supplierDetailTable" class="table table-striped table-bordered nowrap" style="width:100%">
          <thead>
            <tr>
              <th>Month-Year</th>
              <th>Item Purchased</th>
              <th>Invoice #</th>
              <th>Amount</th>
              <th>Date Purchased</th>
              <th>Date To Be Paid</th>
              <th>Date Paid</th>
              <th>Paid</th>
              <th>Long Overdue</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $supplierOverallTotal = 0;
            foreach ($groupedData as $monthYear => $suppliers) {
                if (isset($suppliers[$supplierFilter])) {
                    $entries = $suppliers[$supplierFilter];
                    $monthTotal = 0;
                    foreach ($entries as $entry) {
                        $amount = (float)($entry['amount'] ?? 0);
                        $monthTotal += $amount;
                        echo "<tr>";
                        echo "<td>{$monthYear}</td>";
                        echo "<td>" . ($entry['item_purchased'] ?? '') . "</td>";
                        echo "<td>" . ($entry['invoice_number'] ?? '') . "</td>";
                        echo "<td class='dt-right'>" . number_format($amount, 2) . "</td>";
                        echo "<td>" . ($entry['date_purchased'] ?? '') . "</td>";
                        echo "<td>" . ($entry['date_to_be_paid'] ?? '') . "</td>";
                        echo "<td>" . ($entry['date_paid'] ?? '') . "</td>";
                        echo "<td>" . ($entry['paid'] ?? '') . "</td>";
                        echo "<td>" . ($entry['long_overdue'] ?? '') . "</td>";
                        echo "</tr>";
                    }
                    // Build the subtotal row with explicit empty tds
                    echo "<tr class='subtotal-row'>";
                    echo "<td class='dt-right'>Subtotal for {$monthYear}:</td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td class='dt-right'>" . number_format($monthTotal, 2) . "</td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "</tr>";
                    $supplierOverallTotal += $monthTotal;
                }
            }
            // Build the overall total row with explicit empty tds
            echo "<tr class='absolute-total-row'>";
            echo "<td class='dt-right'>Overall Total for {$supplierFilter}:</td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td class='dt-right'>" . number_format($supplierOverallTotal, 2) . "</td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "</tr>";
            ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>

      <!-- Supplier Totals Summary -->
      <h3>Supplier Totals Summary</h3>
      <div class="table-responsive">
        <table id="supplierSummaryTable" class="table table-striped table-bordered nowrap" style="width:100%">
          <thead>
            <tr>
              <th>Supplier</th>
              <th>Total Amount</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($supplierSummary as $supplierName => $data): ?>
            <tr>
              <td>
                <a href="?supplier=<?php echo urlencode($supplierName); ?>"><?php echo $supplierName; ?></a>
              </td>
              <td class="dt-right"><?php echo number_format($data['total'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Original Expenses Table (Grouped by Month & Supplier) -->
      <div class="table-responsive">
        <table id="expensesTable" class="table table-striped table-bordered nowrap" style="width:100%">
          <thead>
            <tr>
              <th>Month-Year</th>
              <th>Supplier</th>
              <th>Item Purchased</th>
              <th>Invoice #</th>
              <th>Amount</th>
              <th>Date Purchased</th>
              <th>Date To Be Paid</th>
              <th>Date Paid</th>
              <th>Paid</th>
              <th>Long Overdue</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $absoluteTotal = 0;
            foreach ($groupedData as $monthYear => $suppliers) {
                $monthTotal = 0;
                foreach ($suppliers as $supplierName => $rows) {
                    $supplierSubtotal = 0;
                    foreach ($rows as $row) {
                        $supplierSubtotal += (float)($row['amount'] ?? 0);
                        echo "<tr>";
                        echo "<td>{$monthYear}</td>";
                        echo "<td>" . ($row['supplier_name'] ?? '') . "</td>";
                        echo "<td>" . ($row['item_purchased'] ?? '') . "</td>";
                        echo "<td>" . ($row['invoice_number'] ?? '') . "</td>";
                        echo "<td class='dt-right'>" . number_format($row['amount'] ?? 0, 2) . "</td>";
                        echo "<td>" . ($row['date_purchased'] ?? '') . "</td>";
                        echo "<td>" . ($row['date_to_be_paid'] ?? '') . "</td>";
                        echo "<td>" . ($row['date_paid'] ?? '') . "</td>";
                        echo "<td>" . ($row['paid'] ?? '') . "</td>";
                        echo "<td>" . ($row['long_overdue'] ?? '') . "</td>";
                        echo "</tr>";
                    }
                    echo "<tr class='subtotal-row'>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td class='dt-right'>Subtotal for {$supplierName}:</td>";
                    echo "<td class='dt-right'>" . number_format($supplierSubtotal, 2) . "</td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "</tr>";
                    $monthTotal += $supplierSubtotal;
                }
                echo "<tr class='total-row'>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td class='dt-right'>Total for {$monthYear}:</td>";
                echo "<td class='dt-right'>" . number_format($monthTotal, 2) . "</td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "</tr>";
                $absoluteTotal += $monthTotal;
            }

            $undefinedTotal = 0;
            if (!empty($undefinedRows)) {
                foreach ($undefinedRows as $urow) {
                    $undefinedTotal += (float)($urow['amount'] ?? 0);
                    echo "<tr>";
                    echo "<td>Undefined Month/Year</td>";
                    echo "<td>" . (!empty($urow['supplier_name']) ? $urow['supplier_name'] : "Undefined Supplier") . "</td>";
                    echo "<td>" . ($urow['item_purchased'] ?? '') . "</td>";
                    echo "<td>" . ($urow['invoice_number'] ?? '') . "</td>";
                    echo "<td class='dt-right'>" . number_format($urow['amount'] ?? 0, 2) . "</td>";
                    echo "<td>" . ($urow['date_purchased'] ?? '') . "</td>";
                    echo "<td>" . ($urow['date_to_be_paid'] ?? '') . "</td>";
                    echo "<td>" . ($urow['date_paid'] ?? '') . "</td>";
                    echo "<td>" . ($urow['paid'] ?? '') . "</td>";
                    echo "<td>" . ($urow['long_overdue'] ?? '') . "</td>";
                    echo "</tr>";
                }
                echo "<tr class='undefined-row'>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td class='dt-right'>Total for Undefined:</td>";
                echo "<td class='dt-right'>" . number_format($undefinedTotal, 2) . "</td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "</tr>";
            }
            $absoluteTotal += $undefinedTotal;
            echo "<tr class='absolute-total-row'>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td class='dt-right'>Absolute Total (All Months + Undefined):</td>";
            echo "<td class='dt-right'>" . number_format($absoluteTotal, 2) . "</td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "</tr>";
            ?>
          </tbody>
        </table>
      </div>
  </div>

  <!-- Bootstrap Bundle JS (includes Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- jQuery (required by DataTables) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
  <!-- DataTables Buttons -->
  <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.flash.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
  <script>
    $(document).ready(function() {
        $('#expensesTable').DataTable({
            ordering: false,
            dom: 'Bfrtip',
            buttons: ['excelHtml5', 'pdfHtml5', 'print'],
            pageLength: 10000,
            columnDefs: [{ targets: 4, className: 'dt-right' }]
        });
        $('#supplierSummaryTable').DataTable({
            ordering: false,
            dom: 'Bfrtip',
            buttons: ['excelHtml5', 'pdfHtml5', 'print'],
            pageLength: 10000,
            columnDefs: [{ targets: 1, className: 'dt-right' }]
        });
        $('#supplierDetailTable').DataTable({
            ordering: false,
            dom: 'Bfrtip',
            buttons: ['excelHtml5', 'pdfHtml5', 'print'],
            pageLength: 10000,
            columnDefs: [{ targets: 3, className: 'dt-right' }]
        });
    });
  </script>
</body>
</html>
