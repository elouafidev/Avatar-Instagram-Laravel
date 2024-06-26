# AvatarInstagramClass

La classe `AvatarInstagramClass` est une classe Laravel conçue pour récupérer diverses informations à partir des profils Instagram.

## Fonctionnalités

- Récupère le nom d'utilisateur, la description, l'URL de l'image d'avatar, le nombre d'abonnés, le nombre de personnes suivies, le nombre de publications et la bio des profils Instagram.
- Prend en charge la récupération des métadonnées et du titre des pages de profil.
- Fournit des méthodes pour vérifier le statut HTTP et analyser le contenu HTML.

## Installation

Pour utiliser la classe `AvatarInstagramClass` dans votre projet Laravel, suivez ces étapes :

1. Copiez le fichier `AvatarInstagramClass.php` dans le répertoire `app/Http/Classes` de votre projet Laravel.

2. Assurez-vous d'avoir configuré l'extension cURL dans votre serveur PHP.

3. Avant d'utiliser cette classe, assurez-vous d'avoir l'identifiant de session Instagram approprié. Vous devez remplacer `[ Your Instagram Cookies Session ID ]` dans le constructeur de la classe `AvatarInstagramClass` par votre propre identifiant de session Instagram.

```php
$this->cookies_sesseion_id = "[ Your Instagram Cookies Session ID ]";
```

## Utilisation

Vous pouvez utiliser la classe `AvatarInstagramClass` dans vos contrôleurs, modèles ou tout autre endroit de votre application où vous en avez besoin. Voici un exemple d'utilisation dans un contrôleur :

```php
use App\Http\Classes\AvatarInstagramClass;

public function getInstagramProfile($username)
{
    // Instanciation de la classe avec un nom d'utilisateur Instagram
    $profilInstagram = new AvatarInstagramClass($username);

    // Récupérer les détails du profil
    $detailsProfil = $profilInstagram->getArray();

    // Accéder aux attributs individuels du profil
    $nomUtilisateur = $detailsProfil['username'];
    $description = $detailsProfil['description'];
    // Autres attributs...

    // Vérifier le statut HTTP
    $statutHTTP = $profilInstagram->HttpsState();
    if ($statutHTTP === AvatarInstagramClass::STATE_EXIST) {
        // Le profil existe
    } else {
        // Le profil n'existe pas
    }

    // Retourner les détails du profil à la vue ou effectuer d'autres actions
}
```

## Prérequis

- Laravel 5.x ou version ultérieure
- PHP 5.6 ou version ultérieure
- Extension cURL activée

## Licence

Ce projet est sous licence MIT - voir le fichier [LICENSE](LICENSE) pour plus de détails.

## Auteur

Créé par Mouad El OUAFI
Contact : support@elouafi.dev