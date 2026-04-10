<?php
    echo("bonjour : ");
    // $n = $_GET["test"]; // http://localhost:8888/my_form.php?test=%22titi%22
    $n = $_POST["name"]; // _POST est un tableau associatif qui contient
    // les données envoyées par le formulaire en méthode POST 
    // on accède au contenu des cellules par le nom du champ
    // du formulaire grâce
    echo($n);

?>
