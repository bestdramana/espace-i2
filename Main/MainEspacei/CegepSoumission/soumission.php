<?php
/**
 * Created by Gabriel Désilets.
 * User: Gabriel Désilets
 * Date: 13-02-09
 * Time: 14:24
 * Purpose : Send mail to EspaceI
 */


const ESPACEI_ADRR_FJ = 'gabcell@hotmail.com';
const ESPACEI_ADRR_MPP = 'gabdesilets@gmail.com';
dispatch_vue_soumission();

function dispatch_vue_soumission()
{
    if(array_key_exists('action',$_POST))
    {
        $action = $_POST['action'];
        switch ($action)
        {
            case "sendRequest":
                unset($_POST['action']);
                prepare_request($_POST,$_FILES );
                break;

                default:
                 die('Accès refusé!!');
                break;
        }
    }
}

function prepare_request($data,$files,$auto_reply = false)
{
    $email = array(
        'to'=>ESPACEI_ADRR_FJ,
        'Cc'=>ESPACEI_ADRR_MPP,
        'message'=>$data['description_demande'],
        'subject'=>'Demande de soumission : '.$data['osbl_nom'],
        'osbl_nom'=>isset($data['osbl_nom']) ? $data['osbl_nom'] : "",
        'osbl_adresse'=>isset($data['osbl_adresse']) ? $data['osbl_adresse'] : "",
        'osbl_ville'=>isset($data['osbl_ville']) ? $data['osbl_ville'] : "",
        'osbl_code_p'=>isset($data['osbl_code_p']) ? $data['osbl_code_p'] : "",
        'osbl_phone'=>isset($data['osbl_phone']) ? $data['osbl_phone'] : "",
        'osbl_phone_post'=>isset($data['osbl_phone_post']) ? $data['osbl_phone_post'] : "",
        'osbl_email'=>isset($data['osbl_email']) ? $data['osbl_email'] : "",
        'contact_source'=>isset($data['contact_source']) ? $data['contact_source'] : "",
        'osbl_project_type'=>isset($data['osbl_project_type']) ? $data['osbl_project_type'] : "",
        'other_choice'=>isset($data['other_choice']) ? $data['other_choice'] : "",
        'private_phone'=>isset($data['private_phone']) ? $data['private_phone'] : "",
        'private_poste'=>isset($data['private_phone_poste']) ? $data['private_phone_poste'] : "",
        'private_email'=>isset($data['private_email']) ? $data['private_email'] : "",
        'files'=>$files
    );
    set_session_return_value($email);
    $error = _run_validation($email);

    if(!$error)
    {
        header("Location: vue_soumission.php");
    }
    else
    {
        send_request($email,$files);
    }
}


function _run_validation($data = array())
{
    if(!session_id())
    {
        session_start();
    }
    $all_good = TRUE;
    /* VALIDATION DES CHAMPS OBLIGATOIRES */
    if(!preg_match("/^([1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$/i", $data['osbl_phone']))
    {
        $all_good = FALSE;
        $_SESSION['err_osbl_phone'] ="Téléphone invalide. Exemple : 819-111-1111";
        if($data['private_phone'] &&  !preg_match("/^([1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$/i", $data['private_phone']))
        {
            $_SESSION['err_private_phone'] ="Téléphone invalide. Exemple : 819-111-1111";
        }
    }
    if($data['private_phone'] &&  !preg_match("/^([1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$/i", $data['private_phone']))
    {
        $all_good = FALSE;
        $_SESSION['err_private_phone'] ="Téléphone invalide. Exemple : 819-111-1111";
    }
    if(!preg_match( "/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$/i", $data['osbl_email']))
    {
        $all_good = FALSE;
        $_SESSION['err_osbl_email'] ="Courriel invalide. Exemple : mon_courriel@gmail.com";

    }
    if($data['private_email'] &&  !preg_match("/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$/i", $data['private_email']))
    {
        $all_good = FALSE;
        $_SESSION['err_private_email'] ="Courriel invalide. Exemple : mon_courriel@gmail.com";
    }
    if($data['osbl_code_p'] && !preg_match("/^[ABCEGHJKLMNPRSTVXY]{1}\d{1}[A-Z]{1} *\d{1}[A-Z]{1}\d{1}$/i", $data['osbl_code_p']))
    {
        $all_good = FALSE;
        $_SESSION['err_osbl_code_p'] ="Code postal invalide. Exemple : G1Y G8J";
    }
    /* VALIDATION DES CHAMPS OBLIGATOIRES */
    return   $all_good;

}

function set_session_return_value($data,$reset = FALSE)
{

    if(!session_id())
    {
        session_start();
    }
    switch (TRUE)
    {
        case $reset:
            foreach($data as $k=>$v)
            {
                $_SESSION['var'][$k]=NULL;
            }
            break;

        case !$reset:
            foreach($data as $k=>$v)
            {
                $_SESSION['var'][$k]=$v;
            }
            break;
    }

}

function set_empty_session($data = array())
{
   //die(var_dump($_SESSION));
    if(!isset($_SESSION['var']))
    {
        $email = array(
            'to'=>ESPACEI_ADRR_FJ,
            'Cc'=>ESPACEI_ADRR_MPP,
            'osbl_nom'=>isset($data['osbl_nom']) ? $data['osbl_nom'] : "",
            'osbl_adresse'=>isset($data['osbl_adresse']) ? $data['osbl_adresse'] : "",
            'osbl_ville'=>isset($data['osbl_ville']) ? $data['osbl_ville'] : "",
            'osbl_code_p'=>isset($data['osbl_code_p']) ? $data['osbl_code_p'] : "",
            'osbl_phone'=>isset($data['osbl_phone']) ? $data['osbl_phone'] : "",
            'osbl_phone_post'=>isset($data['osbl_phone_post']) ? $data['osbl_phone_post'] : "",
            'osbl_email'=>isset($data['osbl_email']) ? $data['osbl_email'] : "",
            'contact_source'=>isset($data['contact_source']) ? $data['contact_source'] : "",
            'osbl_project_type'=>isset($data['osbl_project_type']) ? $data['osbl_project_type'] : "",
            'other_choice'=>isset($data['other_choice']) ? $data['other_choice'] : "",
            'private_phone'=>isset($data['private_phone']) ? $data['private_phone'] : "",
            'private_poste'=>isset($data['private_phone_poste']) ? $data['private_phone_poste'] : "",
            'private_email'=>isset($data['private_email']) ? $data['private_email'] : "",
            'files'=>NULL
        );
        set_session_return_value($email);
    }

}
function formatBytes($bytes, $precision = 2)
   {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    // Uncomment one of the following alternatives
    // $bytes /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow));

    return round($bytes, $precision) . ' ' . $units[$pow];
}

function send_request($email,$files)
{

$to = $email['to'];
$subject=$email['subject'];
$todayis = date("l, F j, Y, g:i a") ;
$message ='Demande :'.PHP_EOL.$email['message'].PHP_EOL;
$message .='Information personnel :'.PHP_EOL.$email['osbl_nom'];
$message .=PHP_EOL.$email['osbl_adresse'].' '.$email['osbl_ville'];
$message .=PHP_EOL.'Code postal : '.$email['osbl_code_p'];
$message .=PHP_EOL.'Téléphone : '.$email['osbl_phone'].' Poste : '.$email['osbl_phone_post'];
$message .=PHP_EOL.'Courriel : '.$email['osbl_email'];
$message .=PHP_EOL.'Personne contacte : '.$email['contact_source'];
$message .=PHP_EOL.'Type de projet : '.$email['osbl_project_type'];
$message .=$email['osbl_project_type']=='Autre' ? PHP_EOL.$email['other_choice'] : '';

  $mime_boundary="==Multipart_Boundary_x".md5(mt_rand())."x";

         $headers = "From: {$email['osbl_email']}\r\n" .
             "MIME-Version: 1.0\r\n" .
             "Content-Type: multipart/mixed;\r\n" .
             " boundary=\"{$mime_boundary}\"";

         $message = "This is a multi-part message in MIME format.\n\n" .
             "--{$mime_boundary}\n" .
             "Content-Type: text/plain; charset=\"utf-8\"\n" .
             "Content-Transfer-Encoding: 7bit\n\n" .
             $message . "\n\n";

    for($i=0;$i<count($files['file']['name']);$i++)
    {
        $tmp_name = $files["file"]['tmp_name'][$i];
        $type = $files["file"]['type'][$i];
        $name = $files["file"]['name'][$i];
        $size = $files["file"]['size'][$i];

        if (file_exists($tmp_name)){
            if(is_uploaded_file($tmp_name)){
                $file = fopen($tmp_name,'rb');
                $data = fread($file,filesize($tmp_name));
                fclose($file);
                $data = chunk_split(base64_encode($data));
            }

            $message .= "--{$mime_boundary}\n" .
                "Content-Type: {$type};\n" .
                " name=\"{$name}\"\n" .
                "Content-Disposition: attachment;\n" .
                " filename=\"{$name}\"\n" .
                "Content-Transfer-Encoding: base64\n\n" .
                $data . "\n\n";
        }
    }

         $message.="--{$mime_boundary}--\n";

if (mail($to, $subject, $message, $headers)){
    send_osbl_respond($email['osbl_email'],$email);
}
else{
    $_SESSION['err_send_osbl_mail']='Une erreur erreur est survenue lors de l\'envoie du couriel reesayer plus tard.';
    header("Location: vue_soumission.php");
}



}

function send_osbl_respond($osbl_email,$email){

    $to      = $osbl_email;
    $subject = 'Suivie de la demande.';
    $message = 'Bonjour ! Ceci est pour vous informer que votre soumission a ete envoyee';
    $headers = 'From: espace_i@cegeptr.com' . "\r\n" .
        'Reply-To:noreply' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    if( mail($to, $subject, $message, $headers)){
        set_session_return_value($email,TRUE);
        $_SESSION['success_send_submission']='Votre soumission a ete envoyee.';
        header("Location: vue_soumission.php");
    }
    else{
        $_SESSION['err_send_osbl_mail']='Une erreur erreur est survenue lors de l\'envoie du couriel reesayer plus tard.';
        header("Location: vue_soumission.php");
    }

}
function get_villes()
{
    return array(
        'Abercorn',
        'Acton Vale',
        'Alma',
        'Adstock',
        'Aguanish',
        'Albanel',
        'Albertville',
        'Alleyn-et-Cawood',
        'Amherst',
        'Amos',
        'Amqui',
        'Ange-Gardien',
        'Angliers',
        'Armagh',
        'Arthabaska',
        'Arundel',
        'Arvida',
        'Asbestos',
        'Ascot-Corner',
        'Aston-Jonction',
        'Auclair',
        'Audet',
        'Aumond',
        'Austin',
        'Authier',
        'Authier-Nord',
        'Ayer’s Cliff',
        'Aylmer',
        'Aylmer-Sound',
        'Baie-Comeau',
        'Baie-des-Rochers',
        'Baie-des-Sables',
        'Baie-du-Febvre',
        'Baie-D\’Urfé',
        'Baie-James',
        'Baie-Johan-Beetz',
        'Baie-Saint-Paul',
        'Baie-Sainte-Catherine',
        'Baie-Trinité',
        'Barachois',
        'Barnston-Ouest',
        'Barraute',
        'Batiscan',
        'Beaconsfield',
        'Béarn',
        'Beauharnois',
        'Beauceville',
        'Beaulac-Garthby',
        'Beaumont',
        'Beauport',
        'Beaupré',
        'Bécancour',
        'Bedford',
        'Beebe Plain',
        'Bégin',
        'Belcourt',
        'Belle-Rivière',
        'Belleterre',
        'Beloeil',
        'Berry',
        'Berthier-sur-Mer',
        'Berthierville',
        'Biencourt',
        'Black Lake',
        'Blainville',
        'Blanc-Sablon',
        'Blue Sea',
        'Boileau',
        'Boisbriand',
        'Boischatel',
        'Bois-des-Filion',
        'Bois-Franc',
        'Bolton-Est',
        'Bolton-Ouest',
        'Bonaventure',
        'Bonne-Espérance',
        'Bonsecours',
        'Boucherville',
        'Bouchette',
        'Brébeuf',
        'Brigham',
        'Bristol',
        'Brome',
        'Bromont',
        'Bromptonville',
        'Brossard',
        'Brownsburg-Chatham',
        'Buckingham',
        'Bury',
        'Cabano (Témiscouta-sur-le-Lac)',
        'Cacouna',
        'Cacouna (réserve indienne)',
        'Cadillac',
        'Calixa-Lavallée',
        'Candiac',
        'Cantley',
        'Cap-à-l\'Aigle',
        'Cap-aux-Meules',
        'Cap-aux-Os',
        'Cap-Chat',
        'Cap-des-Rosiers',
        'Caplan',
        'Cap-Rouge',
        'Cap-Saint-Ignace',
        'Cap-Santé',
        'Capucins',
        'Carignan',
        'Carleton-sur-Mer',
        'Carillon',
        'Causapscal',
        'Cayamant',
        'Chambly',
        'Chambord',
        'Champlain',
        'Champneuf',
        'Chandler',
        'Chapais',
        'Charette',
        'Charlesbourg',
        'Charlemagne',
        'Charny',
        'Chartierville',
        'Châteauguay',
        'Château-Richer',
        'Chazel',
        'Chelsea',
        'Chénéville',
        'Chertsey',
        'Chesterville',
        'Chevery',
        'Chibougamau',
        'Chicoutimi',
        'Chisasibi',
        'Chute-aux-Outardes',
        'Chute-Saint-Philippe',
        'Clarke-City',
        'Clermont-Abitibi-Ouest',
        'Clermont-Charlevoix',
        'Clerval',
        'Cleveland',
        'Cloridorme',
        'Clova',
        'Coaticook',
        'Colombier',
        'Colombourg',
        'Compton',
        'Contrecœur',
        'Cookshire-Eaton',
        'Coteau-du-Lac',
        'Côte-Nord-du-Golfe-Saint-Laurent',
        'Courcelles',
        'Cowansville',
        'Crabtree',
        'Danville',
        'Daveluyville',
        'Deauville',
        'Dégelis',
        'Déléage',
        'Delisle ',
        'Delson',
        'Denholm',
        'Desbiens',
        'Deschambault',
        'Deschaillons-sur-Saint-Laurent',
        'Deschambault-Grondines',
        'Deux-Montagnes',
        'Disraeli',
        'Dixville',
        'Dolbeau-Mistassini',
        'Donnacona',
        'Dorval',
        'Douglastown',
        'Drummondville',
        'Dudswell',
        'Duhamel',
        'Dundee',
        'Dunham',
        'Duparquet',
        'Durham-Sud',
        'East Angus',
        'East Farnham',
        'East Hereford',
        'Eastmain',
        'Eastman',
        'Egan-Sud',
        'Elgin',
        'Entrelacs',
        'Escuminac',
        'Essipit',
        'Estérel',
        'Farnham',
        'Fassett',
        'Fatima',
        'Ferland-et-Boilleau',
        'Ferme-Neuve',
        'Fermont',
        'Forestville',
        'Fortierville',
        'Frampton',
        'Franklin',
        'Franquelin',
        'Frelighsburg',
        'Frontenac',
        'Fugèreville',
        'Gagnon',
        'Gallichan',
        'Gallix',
        'Gaspé',
        'Gatineau',
        'Georgeville',
        'Girardville',
        'Godbout',
        'Godmanchester',
        'Gore',
        'Gracefield',
        'Granby',
        'Grand-Mère',
        'Grand-Remous',
        'Grand-Saint-Esprit',
        'Grande-Entrée',
        'Grande-Vallée',
        'Grandes-Piles',
        'Grande-Rivière',
        'Grenville',
        'Grenville-sur-la-Rouge',
        'Grondines',
        'Grosse-Île',
        'Grosses-Roches',
        'Guérin',
        'Île-aux-Loups',
        'Île d\'Anticosti',
        'Île-de-Grand-Calumet',
        'Île-d\'Entrée',
        'Inverness',
        'Irlande',
        'Isle-aux-Coudres',
        'Ivry-sur-le-Lac',
        'Hampden',
        'Harrington',
        'Harrington Harbour',
        'Hatley',
        'Havelock',
        'Havre-Aubert',
        'Havre-aux-Maisons',
        'Havre-Saint-Pierre',
        'Hébertville',
        'Hemmingford',
        'Hérouxville',
        'Hinchinbrooke',
        'Howick',
        'Huberdeau',
        'Hudson',
        'Hunter-Mills',
        'Huntingdon',
        'Joliette',
        'Jonquière',
        'Kahnawake',
        'Kamouraska',
        'Kanesatake',
        'Kazabazua',
        'Kiamika',
        'Kingsbury',
        'Kingsey-Falls',
        'Kinnear\'s Mills',
        'Kipawa',
        'Kirkland',
        'Kitcisakik',
        'Kitigan Zibi',
        'Knowlton',
        'Kuujjuaq',
        'La Baie',
        'Labelle',
        'Lac-à-la-Tortue',
        'Lac-au-Saumon',
        'Lac-aux-Sables',
        'La Bostonnais',
        'Lac-Beauport',
        'Lac-Bouchette',
        'Lac-Brome',
        'La Croche',
        'Lac Chicobi',
        'Lac-Drolet',
        'Lac-Édouard',
        'Lac-Etchemin',
        'Lac-des-Aigles',
        'Lac-des-Écorces',
        'Lac-des-Plages',
        'Lac-du-Cerf',
        'Lachine',
        'Lac-Humqui',
        'Lachute',
        'Lac-Mégantic',
        'Lacolle',
        'La Conception',
        'La Corne',
        'Lac-Saguay',
        'Lac-Saint-Charles',
        'Lac-Sainte-Marie',
        'Sac-Saint-Paul',
        'Lac-Simon',
        'La Doré',
        'Laforce',
        'La Guadeloupe',
        'La Macaza',
        'La Malbaie',
        'Lambton',
        'La Minerve',
        'La Morandière',
        'La Motte',
        'L\'Ancienne-Lorette',
        'Landrienne',
        'L\'Ange-Gardien',
        'L\'Ange-Gardien (Outaouais)',
        'Languedoc',
        'Laniel',
        'Lanoraie',
        'L\'Anse-à-Beaufils',
        'L\'Anse-aux-Gascons',
        'L\’Anse-Saint-Jean',
        'Lantier',
        'La Patrie',
        'La Pêche',
        'La Pocatière',
        'La Prairie',
        'La Présentation',
        'La Reine',
        'LaSalle',
        'La Sarre',
        'L\'Ascension-de-Patapédia',
        'L\’Assomption',
        'Latulipe-et-Gaboury',
        'La Tuque',
        'Launay',
        'Laurentien ',
        'Laurierville',
        'Lavaltrie',
        'L\'Avenir',
        'Laverlochère',
        'Lawrenceville',
        'Lebel-sur-Quévillon',
        'Le Bic',
        'Leclercville',
        'Lejeune',
        'Lemieux',
        'Lennoxville',
        'Léry',
        'Les Bergeronnes',
        'Les Cèdres',
        'Les Coteaux',
        'Les Éboulements',
        'L\’Épiphanie',
        'Les Escoumins',
        'Les Méchins',
        'Lévis',
        'L\'Île-Cadieux',
        'L\'Île-Dorval',
        'Lime Ridge',
        'Limoilou',
        'Lingwick',
        'L\'Islet',
        'L\'Isle-Verte',
        'Longueuil',
        'Lorraine',
        'Lorrainville',
        'Loretteville',
        'Lotbinière',
        'Louiseville',
        'Low',
        'Lyster',
        'Macamic',
        'Magog',
        'Malartic',
        'Maliotenam',
        'Mandeville',
        'Maniwaki',
        'Manseau',
        'Mansonville',
        'Marbleton',
        'Maria',
        'Maricourt',
        'Marston',
        'Martinville',
        'Mascouche',
        'Maskinongé',
        'Massueville',
        'Matagami',
        'Matane',
        'Matapédia',
        'McMasterville',
        'McWaters',
        'Melbourne',
        'Melocheville',
        'Mercier',
        'Messines',
        'Métabetchouan-Lac-à-la-Croix',
        'Métis-sur-Mer',
        'Middle Bay',
        'Milan',
        'Mille-Isles',
        'Milnikek',
        'Mirabel',
        'Mistissini',
        'Montbeillard',
        'Mont Bellevue',
        'Mont-Brun',
        'Montcerf-Lytton',
        'Montebello',
        'Mont-Joli',
        'Mont-Laurier',
        'Montpellier',
        'Montréal',
        'Mont-Saint-Hilaire',
        'Mont-Saint-Michel',
        'Mont-Tremblant',
        'Morin-Heights',
        'Murdochville',
        'Mystic',
        'Namur',
        'Nantes',
        'Napierville',
        'Natashquan',
        'Nédélec',
        'Nemaska',
        'Neuville',
        'New Carlisle',
        'Newport',
        'New Richmond',
        'Nicolet',
        'Nominingue',
        'Normandin',
        'Normétal',
        'North Hatley',
        'Notre-Dame-des-Bois',
        'Notre-Dame-de-la-Merci',
        'Notre-Dame-de-la-Paix',
        'Notre-Dame-de-l\'Île-Perrot',
        'Notre-Dame-de-Montauban',
        'Notre-Dame-des-Monts',
        'Notre-Dame-des-Sept-Douleurs',
        'Notre-Dame-de-Stanbridge',
        'Notre-Dame-du-Lac (Témiscouta-sur-le-Lac)',
        'Notre-Dame-du-Laus',
        'Notre-Dame-du-Nord',
        'Notre-Dame-du-Portage',
        'Nouvelle',
        'Noyan',
        'Obedjiwan',
        'Odanak',
        'Ogden',
        'Oka',
        'Old Fort Bay',
        'Orford',
        'Ormstown',
        'Orsainville',
        'Otterburn Park',
        'Oujé-Bougoumou',
        'Pabos',
        'Pabos Mills',
        'Packington',
        'Padoue',
        'Pakuashipi',
        'Palmarolle',
        'Papineauville',
        'Parent',
        'Parisville',
        'Paspébiac',
        'Passes-Dangereuses',
        'Percé',
        'Péribonka',
        'Petite-Rivière-St-François',
        'Petite-Vallée',
        'Petit-Saguenay',
        'Piedmont',
        'Pikogan',
        'Pincourt',
        'Piopolis',
        'Plaisance',
        'Plessisville',
        'Pohénégamook',
        'Pointe-à-la-Croix',
        'Pointe-à-la-Frégate',
        'Pointe-au-Père',
        'Pointe-au-Pic',
        'Pointe-aux-Anglais',
        'Pointe-aux-Outardes',
        'Pointe-Calumet',
        'Pointe-Claire',
        'Pointe-des-Cascades',
        'Pointe-des-Monts',
        'Pointe-Fortune',
        'Pointe-Lebel',
        'Pointe-Mistassini',
        'Pontbriand',
        'Pontiac',
        'Pont-Rouge',
        'Port-Cartier',
        'Port-Daniel-Gascons',
        'Portneuf',
        'Portneuf-sur-Mer',
        'Port-au-Persil',
        'Port-aux-Quilles',
        'Port-Menier',
        'Potton',
        'Poularies',
        'Preissac',
        'Prévost',
        'Price',
        'Princeville',
        'Québec',
        'Racine',
        'Radisson',
        'Ragueneau',
        'Rapide-Danseur',
        'Rawdon',
        'Rémigny',
        'Repentigny',
        'Richelieu',
        'Richmond',
        'Rigaud',
        'Rimouski',
        'Ripon',
        'Rivière-Bonjour',
        'Rivière-à-Pierre',
        'Rivière-au-Renard',
        'Rivière-au-Tonnerre',
        'Rivière-aux-Graines',
        'Rivière-Beaudette',
        'Rivière-Bleue',
        'Rivière-du-Loup',
        'Rivière-Éternité',
        'Rivière-Héva',
        'Rivière-Ouelle',
        'Rivière-Pentecôte',
        'Rivière-Rouge',
        'Rivière-Saint-Jean',
        'Robertville',
        'Roberval',
        'Rochebaucourt',
        'Rock Forest',
        'Rock Island',
        'Roquemaure',
        'Rosemère',
        'Rougemont',
        'Routhierville',
        'Rouyn-Noranda',
        'Roxton Falls',
        'Roxton-Pond',
        'Saguenay',
        'Saint-Adelme',
        'Saint-Adelphe',
        'Saint-Adolphe-d\'Howard',
        'Saint-Adrien',
        'Saint-Adrien-d\’Irlande',
        'Saint-Agapit',
        'Saint-Aimé',
        'Saint-Aimé-des-Lacs',
        'Saint-Alban',
        'Saint-Alexandre-des-Lacs',
        'Saint-Alphonse-de-Granby',
        'Saint-Alphonse-Rodriguez',
        'Saint-Amable',
        'Saint-Ambroise',
        'Saint-Ambroise-de-Kildare',
        'Saint-André',
        'Saint-André-Avellin',
        'Saint-André-d\'Argenteuil',
        'Saint-Armand',
        'Saint-André-du-Lac-St-Jean',
        'Saint-Anicet',
        'Saint-Antoine-de-l\’Isle-aux-Grues',
        'Saint-Antoine-de-Gros-Morne',
        'Saint-Antoine-de-Tilly',
        'Saint-Athanase',
        'Saint-Augustin',
        'Saint-Augustin-de-Woburn',
        'Saint-Barnabé',
        'Saint-Barthélemy',
        'Saint-Basile',
        'Saint-Basile-le-Grand',
        'Saint-Benjamin',
        'Saint-Benoît-du-Lac',
        'Saint-Bernard',
        'Saint-Bernard-de-Lacolle',
        'Saint-Bidonville',
        'Saint-Blaise-sur-Richelieu',
        'Saint-Bruno-de-Guigues',
        'Saint-Bruno-de-Montarville',
        'Saint-Calixte',
        'Saint-Camille',
        'Saint-Camille-de-Lellis',
        'Saint-Casimir',
        'Saint-Césaire',
        'Saint-Charles-de-Bourget',
        'Saint-Charles-Garnier',
        'Saint-Charles-sur-Richelieu',
        'Saint-Christophe-d\'Arthabaska',
        'Saint-Chrystosome',
        'Saint-Claude',
        'Saint-Clément',
        'Saint-Cléophas',
        'Saint-Cléophas-Brandon',
        'Saint-Clet',
        'Saint-Colomban',
        'Saint-Côme',
        'Saint-Côme-Linière',
        'Saint-Constant',
        'Saint-Cuthbert',
        'Saint-Cyprien',
        'Saint-Cyrille-de-Wendover',
        'Saint-Damase',
        'Saint-David',
        'Saint-Denis',
        'Saint-Denis-de-Brompton',
        'Saint-Dominique-du-Rosaire',
        'Saint-Donat',
        'Saint-Edmond-de-Grantham',
        'Saint-Édouard',
        'Saint-Édouard-de-Fabre',
        'Saint-Élie-de-Caxton',
        'Saint-Élie-d\'Orford',
        'Saint-Elzéar',
        'Saint-Elzéar-de-Témiscouata',
        'Saint-Émile-de-Suffolk',
        'Saint-Éphrem-de-Beauce',
        'Saint-Étienne-Bolton',
        'Saint-Étienne-de-Lauzon',
        'Saint-Étienne-des-Grès',
        'Saint-Eusèbe',
        'Saint-Eustache',
        'Saint-Fabien',
        'Saint-Fabien-de-Panet',
        'Saint-Faustin-Lac-Carré',
        'Saint-Félicien',
        'Saint-Félix-de-Kingsey',
        'Saint-Félix-de-Valois',
        'Saint-Félix-d\'Otis',
        'Saint-Ferdinand',
        'Saint-Ferréol-les-Neiges',
        'Saint-Fortunat',
        'Saint-François-d\'Assise',
        'Saint-François-de-Pabos',
        'Saint-François-de-Sales',
        'Saint-François-de-l\'Île-d\'Orléans',
        'Saint-François-du-Lac',
        'Saint-François-Xavier-de-Brompton',
        'Saint-Fulgence',
        'Saint-Gabriel',
        'Saint-Gabriel-de-Brandon',
        'Saint-Gabriel-de-Rimouski',
        'Saint-Gabriel-de-Valcartier',
        'Saint-Gédéon-de-Beauce',
        'Saint-Georges',
        'Saint-Georges-de-Windsor',
        'Saint-Gérard-Majella',
        'Saint-Gervais',
        'Saint-Gilles',
        'Saint-Guillaume',
        'Saint-Henri',
        'Saint-Henri-de-Taillon',
        'Saint-Herménégilde',
        'Saint-Hilaire-de-Dorset',
        'Saint-Hilarion',
        'Saint-Hippolyte',
        'Saint-Honoré',
        'Saint-Honoré-de-Témiscouata',
        'Saint-Hubert-de-Rivière-du-Loup',
        'Saint-Hyacinthe',
        'Saint-Ignace-de-Stanbridge',
        'Saint-Irénée',
        'Saint-Isidore',
        'Saint-Isidore',
        'Saint-Isidore-de-Clifton',
        'Saint-Jacques-de-Leeds',
        'Saint-Jacques-le-Majeur-de-Wolfestown',
        'Saint-Jean-Chrystosome',
        'Saint-Jean-de-Brébeuf',
        'Saint-Jean-de-Cherbourg',
        'Saint-Jean-de-Dieu',
        'Saint-Jean-de-la-Lande',
        'Saint-Jean-de-l\’Île-d’Orléans',
        'Saint-Jean-de-Matha',
        'Saint-Jean-Port-Joli',
        'Saint-Jean-sur-Richelieu',
        'Saint-Jérôme',
        'Saint-Joachim-de-Montmorency',
        'Saint-Joachim-de-Shefford',
        'Saint-Joseph-de-Beauce',
        'Saint-Joseph-de-Coleraine',
        'Saint-Joseph-de-Ham-Sud',
        'Saint-Joseph-de-la-Rive',
        'Saint-Joseph-de-Kamouraska',
        'Saint-Joseph-du-Lac',
        'Saint-Jude',
        'Saint Julien',
        'Saint-Juste-du-Lac',
        'Saint-Justin',
        'Saint-Lambert',
        'Saint-Lambert-de-Lauzon',
        'Saint-Laurent-de-l\'Île-d\'Orléans',
        'Saint-Lazare',
        'Saint-Léandre',
        'Saint-Léonard-de-Portneuf',
        'Saint-Léonard-d\'Aston',
        'Saint-Léon-le-Grand',
        'Saint-Liboire',
        'Saint-Liguori',
        'Saint-Lin-Laurentides',
        'Saint-Louis',
        'Saint-Louis-de-Courville',
        'Saint-Louis-de-Blandford',
        'Saint-Louis-de-Gonzague',
        'Saint-Louis-de-Gonzague-du-Cap-Tourment',
        'Saint-Louis-du-Ha!Ha!',
        'Saint-Ludger',
        'Saint-Magloire',
        'Saint-Malo',
        'Saints-Martyrs-Canadiens',
        'Saint-Mathieu-de-Beloeil',
        'Saint-Marc-de-Figuery',
        'Saint-Marc-des-Carrières',
        'Saint-Marc-du-Lac-Long',
        'Saint-Marc-sur-Richelieu',
        'Saint-Martin',
        'Saint-Mathias-sur-Richelieu',
        'Saint-Mathieu',
        'Saint-Mathieu-d\'Harricana',
        'Saint-Mathieu-du-Parc',
        'Saint-Maxime-du-Mt-Louis',
        'Saint-Médard',
        'Saint-Michel',
        'Saint-Michel-des-Saints',
        'Saint-Michel-du-Squatec',
        'Saint-Moïse',
        'Saint-Narcisse',
        'Saint-Nazaire-d\'Acton',
        'Saint-Nicolas',
        'Saint-Noël',
        'Saint-Octave-de-Métis',
        'Saint-Odilon-de-Cranbourne',
        'Saint-Omer',
        'Saint-Ours',
        'Saint-Pacôme',
        'Saint-Pamphile',
        'Saint-Pascal',
        'Saint-Patrice-de-Sherrington',
        'Saint-Paul',
        'Saint-Paul-d\’Abbotsford',
        'Saint-Paul-de-l\'Île-aux-Noix',
        'Saint-Paul-de-Montminy',
        'Saint-Paulin',
        'Saint-Paul-River',
        'Saint-Pie',
        'Saint-Pierre',
        'Saint-Pierre-Baptiste',
        'Saint-Pierre-de-Broughton',
        'Saint-Pierre-de-Lamy',
        'St-Pierre-de-l\’Île-d\’Orléans',
        'Saint-Pierre-de-Véronne-à-Pike-River',
        'Saint-Philémon',
        'Saint-Philippe',
        'Saint-Philippe-de-Néri',
        'Saint-Placide',
        'Saint-Polycarpe',
        'Saint-Prime',
        'Saint-Prosper',
        'Saint-Prosper-de-Champlain',
        'Saint-Raphaël',
        'Saint-Rémi',
        'Saint-René-de-Matane',
        'Saint-Robert',
        'Saint-Robert-Bellarmin',
        'Saint-Roch-de-l\’Achigan',
        'Saint-Roch-de-Mékinac',
        'Saint-Roch-de-Richelieu',
        'Saint-Romain',
        'Saint-Samuel',
        'Saint-Sauveur',
        'Saint-Sébastien',
        'Saint-Simon',
        'Saint-Siméon',
        'Saint-Sixte',
        'Saint-Stanislas',
        'Saint-Stanislas-de-Kostka',
        'Saint-Sulpice',
        'Saint-Sylvestre',
        'Saint-Télésphore',
        'Saint-Tharcisius',
        'Saint-Thomas',
        'Saint-Tite',
        'Saint-Tite-des-Caps',
        'Saint-Ubalde',
        'Saint-Ulric',
        'Saint-Urbain',
        'Saint-Valentin',
        'Saint-Valère',
        'Saint-Valérien-de-Milton',
        'Saint-Vallier',
        'Saint-Venant-de-Paquette',
        'Saint-Vianney',
        'Saint-Zacharie',
        'Saint-Zénon',
        'Saint-Zéphirin',
        'Saint-Zotique',
        'Sainte-Adèle',
        'Sainte-Agathe-de-Lotbinière',
        'Sainte-Agathe-des-Monts',
        'Sainte-Agnès',
        'Sainte-Angèle-de-Laval',
        'Sainte-Angèle-de-Mérici',
        'Sainte-Anne-de-Beaupré',
        'Sainte-Anne-de-Bellevue',
        'Sainte-Anne-des-Monts',
        'Sainte-Anne-de-la-Pérade',
        'Sainte-Anne-de-la-Pocatière',
        'Sainte-Anne-de-la-Rochelle',
        'Sainte-Anne-de-Sorel',
        'Sainte-Anne-des-Plaines',
        'Sainte-Anne-du-Lac',
        'Sainte-Aurélie',
        'Sainte-Barbe',
        'Sainte-Brigitte-de-Laval',
        'Sainte-Catherine',
        'Sainte-Catherine-de-la-Jacques-Cartier',
        'Sainte-Catherine-Hatley',
        'Sainte-Cécile-de-Milton',
        'Sainte-Cécile-de-Whitton',
        'Sainte-Christine-d\'Auvergne',
        'Sainte-Claire',
        'Sainte-Clotilde',
        'Sainte-Edwidge-de-Clifton',
        'Sainte-Élisabeth',
        'Sainte-Eulalie',
        'Sainte-Famille',
        'Sainte-Félicité',
        'Sainte-Fidèle',
        'Sainte-Flavie',
        'Sainte-Florence',
        'Sainte-Foy',
        'Sainte-Geneviève-de-Batiscan',
        'Sainte-Germaine-Boulé',
        'Sainte-Irène',
        'Sainte-Hedwidge',
        'Sainte-Hélène-Mancebourg',
        'Sainte-Hénédine',
        'Sainte-Jeanne-d’Arc',
        'Sainte-Julie',
        'Sainte-Julienne',
        'Sainte-Justine',
        'Sainte-Justine-de-Newton',
        'Sainte-Louise',
        'Sainte-Luce',
        'Sainte-Madeleine-de-la-Rivière-Madeleine',
        'Sainte-Marcelline-de-Kildare',
        'Sainte-Marguerite-Marie',
        'Ste-Marguerite-Beauce',
        'Sainte-Marguerite-du-Lac-Masson',
        'Sainte-Marie',
        'Sainte-Marthe-sur-le-Lac',
        'Sainte-Martine',
        'Sainte-Monique',
        'Saint-Paule',
        'Sainte-Pétronille',
        'Sainte-Rita',
        'Sainte-Rose-du-Nord',
        'Sainte-Sabine',
        'Sainte-Sophie',
        'Sainte-Thècle',
        'Sainte-Thérèse',
        'Sainte-Thérèse-de-la-Gatineau',
        'Sainte-Ursule',
        'Sainte-Victoire-de-Sorel',
        'Salaberry-de-Valleyfield',
        'Salluit',
        'Sayabec',
        'Schefferville',
        'Scott',
        'Scotstown',
        'Ville de Senneterre',
        'Paroisse de Senneterre',
        'Senneville',
        'Sept-Îles',
        'Shannon',
        'Shawinigan',
        'Shefford',
        'Sherbrooke',
        'Sillery',
        'Sorel-Tracy',
        'Stanbridge East',
        'Stanbridge Station',
        'Canton de Stanstead',
        'Ville de Stanstead',
        'Stanstead Plain',
        'Stoke',
        'Stoneham-et-Tewkesbury',
        'Stornoway',
        'Stratford',
        'Stukely-Sud',
        'Sutton',
        'Tadoussac',
        'Taschereau',
        'Témiscaming',
        'Témiscouata-sur-le-Lac : voir Cabano et Notre-Dame-du-Lac',
        'Terrasse-Vaudreuil',
        'Terrebonne',
        'Thetford Mines',
        'Thetford-Partie-Sud',
        'Thorne',
        'Thurso',
        'Trécesson',
        'Très-Saint-Sacrement',
        'Tring-Jonction',
        'Trois-Pistoles',
        'Trois-Rives',
        'Trois-Rivières',
        'Uashat',
        'Ulverton',
        'Upton',
        'Val-Bélair',
        'Val-Brillant',
        'Valcanton',
        'Valcourt',
        'Val-David',
        'Val-des-Lacs',
        'Val-d\'Espoir',
        'Val-d\'Or',
        'Val-Joli',
        'Val-Morin',
        'Val-Racine',
        'Varennes',
        'Vaudreuil-Dorion',
        'Vaudreuil-sur-le-Lac',
        'Vendée ',
        'Verchères',
        'Verdun',
        'Victoriaville',
        'Villebois',
        'Ville de L\’Île-Perrot',
        'Ville-Marie',
        'Ville de Mont-Royal',
        'Vimy-Ridge',
        'Visitation-de-Yamaska',
        'Wakefield',
        'Warden',
        'Warwick',
        'Waskaganish',
        'Waswanipi',
        'Waterloo',
        'Waterville',
        'Way\'s Mills',
        'Weedon',
        'Wemindji',
        'Wemotaci',
        'Wentworth',
        'Wentworth-Nord',
        'Westbury',
        'Wickham',
        'Whapmagoostui',
        'Whitworth',
        'Windsor',
        'Wôlinak',
        'Wotton',
        'Yamachiche',
        'Yamaska'
    );
}

function get_type_projets()
{
    return array(
        'Site Web',
        'Application Web',
        'Logiciel',
        'Autre'
    );
}