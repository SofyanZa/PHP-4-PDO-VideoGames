<?php

// Inclusion du fichier s'occupant de la connexion à la DB 
require __DIR__.'/inc/db.php'; 

$videogameList = array();
$platformList = array();
$name = '';
$editor = '';
$release_date = '';
$platform = '';

// Si le formulaire a été soumis
if (!empty($_POST)) {
    // Récupération des valeurs du formulaire dans des variables
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $editor = isset($_POST['editor']) ? $_POST['editor'] : '';
    $release_date = isset($_POST['release_date']) ? $_POST['release_date'] : '';
    $platform = isset($_POST['platform']) ? intval($_POST['platform']) : 0;
    
    // Insertion en DB du jeu video
    $insertQuery = "
        INSERT INTO videogame (name, editor, release_date, platform_id)
        VALUES ('{$name}', '{$editor}', '{$release_date}', {$platform})
    ";
   
    $count = $pdo->exec($insertQuery);

    if ($count > 0) {
        header('Location: index.php');
    } else {
        echo "Aîe, quelque chose a mal tourné dans votre requête";
    }

  
}


$platformList = $pdo->query('SELECT * FROM platform;')->fetchAll(PDO::FETCH_UNIQUE|PDO::FETCH_COLUMN, 1);



// SELECT permet de ne retenir que les colonnes qui nous intéressent dans la table temporaire composée par MySQL
// FROM et JOIN permettent de composer la table temporaire dans laquelle on va aller chercher nos données
$sql = '
    SELECT videogame.id, videogame.name, editor, release_date, platform.name platform_name 
    FROM videogame
    JOIN platform ON videogame.platform_id = platform.id;
';


// Si un tri a été demandé, on réécrit la requête
if (!empty($_GET['order'])) {
    // Récupération du tri choisi
    $order = trim($_GET['order']);
    if ($order == 'name') {
    
        // ASC (valeur par défaut) ou DESC pour l'ordre croissant ou décroissant du tri
        $sql = '
            SELECT videogame.id, videogame.name, editor, release_date, platform.name platform_name 
            FROM videogame
            JOIN platform ON videogame.platform_id = platform.id
            ORDER BY name ASC;
        ';

    }
    else if ($order == 'editor') {
        $sql = '
            SELECT videogame.id, videogame.name, editor, release_date, platform.name platform_name 
            FROM videogame
            JOIN platform ON videogame.platform_id = platform.id
            ORDER BY editor ASC;
        ';

    }
}

// PDO renvoie un PDOStatement en guise de réponse
$stmt = $pdo->query($sql);

// et c'est depuis ce PDOStatement qu'on va décider du format des données à récupérer
// PDO::FETCH_ASSOC informe le statement qu'il doit nous retourner, pour chaque ligne, un tableau associatif
$videogameList = $stmt->fetchAll(PDO::FETCH_ASSOC);


require __DIR__.'/view/videogame.php';