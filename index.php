<?php

spl_autoload_register(function ($class) {
    include $class . '.php';
});

session_start();

if (isset($_GET['deconnexion'])) {
    session_destroy();
    header('Location: .');
    exit();
}

$pdo = new PDO('mysql:dbname=mini_game;host=127.0.0.1;port=3306', 'tesdy', 'demo');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$persoManager = new PersonnagesManager($pdo);

if (isset ($_SESSION['perso'])) {
    $perso = $_SESSION['perso'];
}

// Traitement du formulaire
if (isset($_POST['creer']) && isset($_POST['nom'])) {

    switch ($_POST['type']) {
        case 'magicien':
            $perso = new Magicien(['nom' => $_POST['nom']]);
            break;
        case 'guerrier';
            $perso = new Guerrier(['nom' => $_POST['nom']]);
            break;
        default:
            $msg = 'Le type du personnage n\'est pas valide';
            break;
    }
    if (isset($perso)) {
        if (!$perso->nomValid()) {
            $msg = 'Le nom choisi n\'est pas valide';
            unset($perso);
        } elseif ($persoManager->exists($perso->getNom())) {
            $msg = "Le nom du personnage existe déjà";
            unset($perso);
        } else {
            $persoManager->add($perso);
        }
    }
} elseif (isset($_POST['utiliser']) && isset($_POST['nom'])) {
    if ($persoManager->exists($_POST['nom'])) {
        $perso = $persoManager->get($_POST['nom']);
    } else {
        $msg = 'Ce personnage n\'existe pas';
    }
} elseif(isset($_GET['frapper'])) {
    if(!isset($perso)) {
        $msg = 'Merci de créer un personnage ou de vous identifier.';
    } else {
        if (!$persoManager->exists((int) $_GET['frapper'])) {
            $msg = 'Le personnage que vous voulez frapper n\'existe pas.';
        } else {
            $persoAFrapper = $persoManager->get((int) $_GET['frapper']);
            $retour = $perso->frapper($persoAFrapper);

            switch ($retour) {
                case Personnage::CEST_MOI :
                    $msg = 'Mais pourquoi se frapper soi-même ?';
                    break;
                case Personnage::PERSO_FRAPPE :
                    $msg = 'le personnage a bien été frappé';
                    $persoManager->update($perso);
                    $persoManager->update($persoAFrapper);
                    break;
                case Personnage::PERSO_TUE :
                    $msg = 'Le coup ultime, vous avez tué ce personnage.';
                    $persoManager->update($perso);
                    $persoManager->delete($persoAFrapper);
                    break;
                case Personnage::PERSO_ENDORMI :
                    $msg = 'Vous êtes endormi, vous ne pouvez pas frapper vos ennemis.';
                    break;
            }
        }
    }
}

elseif (isset($_GET['ensorceler'])) {
    if (!isset($perso)) {
        $msg = 'Merci de créer un personnage ou de vous identifier.';
    } else {
        if ($perso->getType() != 'magicien') {
            $msg = 'Seules les magiciens peuvent ensorceler des personnages';
        } else {
            if (!$persoManager->exists((int) $_GET['ensorceler'])) {
                $msg = 'Le personnage que vous voulez frapper n\'existe pas';
            } else {
                $persoAEnsorceler = $persoManager->get((int) $_GET['ensorceler']);
                $retour = $perso->lancerUnSort($persoAEnsorceler);

                switch ($retour) {
                    case Personnage::CEST_MOI:
                        $msg = 'Mais, pourquoi voulez-vous vous ensorceler ?';
                        break;

                    case Personnage::PERSO_ENSORCELE:
                        $msg = 'Vous avez ensorcelé votre ennemi.';

                        $persoManager->update($perso);
                        $persoManager->update($persoAEnsorceler);
                        break;

                    case Personnage::PAS_DE_MAGIE:
                        $msg = 'Vous n\'avez pas de magie.';
                        break;

                    case Personnage::PERSO_ENDORMI:
                        $msg = 'Vous êtes endormi, vous ne pouvez pas lancer de sort!';
                        break;
                }
            }
        }
    }
}
?>

    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <title>TP : Mini Jeu de Combat</title>
    </head>

    <body>
    <p>Nombre de personnage en lisse : <?= $persoManager->count() ?></p>
    <?php
    if (isset($msg)) echo '<p>', $msg, '</p>';
    ?>

    <?php
    if (isset($perso)) {
        ?>
        <p><a href="?deconnexion=1">Déconnexion</a></p>
        <fieldset>
            <legend>Mes Informations</legend>
            <p>
                Nom : <?= ucfirst(htmlspecialchars($perso->getNom())) ?> <br>
                Dégâts : <?= $perso->getDegats() ?> <br>
                Type : <?= ucfirst($perso->getType()) ?> <br>
                <?php
                switch ($perso->getType()) {
                    case 'magicien':
                        echo 'Magie : ';
                        break;
                    case 'guerrier':
                        echo 'Protection : ';
                        break;
                }

                echo $perso->getAtout();
                ?>
            </p>
        </fieldset>

        <fieldset>
            <legend>Qui frapper ?</legend>
            <p>
                <?php
                $persosList = $persoManager->getList($perso->getNom());
                if (empty($persosList)) {
                    echo 'Aucun ennemi à frapper.';
                } else {
                    if ($perso->estEndormi()) {
                        echo "Un Magicien vous a endormi ! Vous allez vous réveiller dans ", $perso->reveil();
                    } else {
                        foreach ($persosList as $ennemi) {
                            echo '<a href="?frapper=', $ennemi->getId(), '">', htmlspecialchars($ennemi->getNom()), '</a> (dégâts : ', $ennemi->getDegats(), ') <br/>';

                            if ($perso->getType() == 'magicien') {
                                echo ' | <a href="?ensorceler=', $ennemi->getId(), '">Lancer un sort</a>';
                            }

                            echo '<br/>';
                        }
                    }
                }
                ?>
            </p>
        </fieldset>
        <?php
    } else {
        ?>
        <form action="" method="post">
            <p>
                <label>Nom :
                    <input type="text" name="nom" maxlength="50">
                </label>
                <label for="type">Type :</label>
                <select name="type" id="type">
                    <option value="magicien">Magicien</option>
                    <option value="guerrier">Guerrier</option>
                </select>
                <input type="submit" value="Créer un personnage" name="creer">
                <input type="submit" value="Utiliser ce personnage" name="utiliser">
            </p>
        </form>
        <h4>Liste des personnages</h4>
        <ul>
            <?php
            foreach ($persoManager->getList() as $player) {
                echo "<li>", $player->getNom(), "</li>";
            }
            ?>
        </ul>
        <?php
    }
    ?>
    </body>
    </html>
<?php
if (isset($perso)) {
    $_SESSION['perso'] = $perso;
}
