<?php

declare (strict_types = 1);

namespace MyApp\Controller;

use MyApp\Entity\Type;
use MyApp\Entity\Product;
use MyApp\Entity\User;
use MyApp\Model\ProductModel;
use MyApp\Model\TypeModel;
use MyApp\Model\UserModel;
use MyApp\Service\DependencyContainer;
use Twig\Environment;
use MyApp\Entity\Avis;
use MyApp\Model\AvisModel;
use MyApp\Model\PanierModel;
use MyApp\Entity\Panier;



class DefaultController
{
    private $twig;
    private $avisModel;
    private $panierModel;


    public function __construct(Environment $twig, DependencyContainer $dependencyContainer)
    {
        $this->twig = $twig;
        $this->typeModel = $dependencyContainer->get('TypeModel');
        $this->productModel = $dependencyContainer->get('ProductModel');
        $this->userModel = $dependencyContainer->get('UserModel');
        $this->avisModel = $dependencyContainer->get('AvisModel');
        $this->panierModel = $dependencyContainer->get('PanierModel');

    }



    public function home()
    {
        $types = $this->typeModel->getAllTypes();
        echo $this->twig->render('defaultController/home.html.twig', ['types'=>$types]);
    }

    public function deletePanier(){
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $this->panierModel->deletePanier(intVal($id));
        header('Location: index.php?page=panier');
        }

    public function updatePanier(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $label = filter_input(INPUT_POST, 'label', FILTER_SANITIZE_STRING);
        if (!empty($_POST['label'])) {
        $panier = new Panier(intVal($id), $label);
        $success = $this->panierModel->updatePanier($panier);
        if ($success) {
        header('Location: index.php?page=panier');
        } 
        } 
        }
        else{
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        }
        $panier = $this->panierModel->getOnePanier(intVal($id));
        echo $this->twig->render('defaultController/updatePanier.html.twig', ['panier'=>$panier]);
       }

    public function addPanier()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $label = filter_input(INPUT_POST, 'label', FILTER_SANITIZE_STRING);
        if (!empty($_POST['label'])) {
            $panier = new Panier(null, $label);
            $success = $this->panierModel->createPanier($panier);
            if ($success) {
                header('Location: index.php?page=panier');
                exit;
            }
        }
    }
    echo $this->twig->render('defaultController/addPanier.html.twig', []);
}


    public function panier()
    {
        $paniers = $this->panierModel->getAllPanier();
        echo $this->twig->render('defaultController/panier.html.twig', ['paniers'=>$paniers]);
    }

    public function avis()
    {
        $avis = $this->avisModel->getAllAvis();
        echo $this->twig->render('defaultController/avis.html.twig', ['avis'=>$avis]);
    }

    public function addAvis(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $commentaire = filter_input(INPUT_POST, 'commentaire', FILTER_SANITIZE_STRING);
        if (!empty($_POST['commentaire'])) {
        $avis = new Avis(null, $commentaire);
        $success = $this->avisModel->createAvis($avis);
        if ($success) {
        header('Location: index.php?page=avis');
        }
        }
        }
        echo $this->twig->render('defaultController/addAvis.html.twig', []);
        }
        

    public function logout()
    {
        $_SESSION = array();
        session_destroy();
        header('Location: index.php');
        exit;
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = $_POST['password'];
            $user = $this->userModel->getUserByEmail($email);
            if (!$user) {
                $_SESSION['message'] = 'Utilisateur ou mot de passe erroné';
                header('Location: index.php?page=login');
            } else {
                if ($user->verifyPassword($password)) {
                    $_SESSION['login'] = $user->getEmail();
                    $_SESSION['roles'] = $user->getRoles();
                    header('Location: index.php?page=addAvis');
                    exit;
                } else {
                    $_SESSION['message'] = 'Utilisateur ou mot de passe erroné';
                    header('Location: index.php?page=login');
                    exit;
                }
            }
        }
        echo $this->twig->render('defaultController/login.html.twig', []);
    }

    public function inscription()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = $_POST['password'];

            $passwordLength = strlen($password);
            $containsDigit = preg_match('/\d/', $password);
            $containsUpper = preg_match('/[A-Z]/', $password);
            $containsLower = preg_match('/[a-z]/', $password);
            $containsSpecial = preg_match('/[^a-zA-Z\d]/', $password);
            if (!$name || !$email || !$password) {

                $_SESSION['message'] = 'Erreur : données invalides';
            } elseif ($passwordLength < 12 || !$containsDigit || !$containsUpper || !$containsLower || !
                $containsSpecial) {

                $_SESSION['message'] = 'Erreur : mot de passe non conforme';
            } else {
                // Hachage du mot de passe
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $user = new User(
                    null, // $id (nullable)
                    $email, // $email
                    '', // $lastName
                    '', // $firstName
                    $hashedPassword, // $password
                    '', // $address (valeur par défaut, vous pouvez ajuster en fonction de votre logique)
                    '', // $postalCode (valeur par défaut)
                    '', // $city (valeur par défaut)
                    '', // $phone (valeur par défaut)
                    ['user']// $roles
                );
                // Enregistrez les données de l'utilisateur dans la base de données
                $result = $this->userModel->createUser($user);
                if ($result) {
                    $_SESSION['message'] = 'Votre inscription est terminée';
                    header('Location: Location: index.php?page=login');
                    exit;
                } else {
                    $_SESSION['message'] = 'Erreur lors de l\'inscription';
                }

            }
            header('Location: index.php?page=inscription');
            exit;
        }

        echo $this->twig->render('defaultController/inscription.html.twig', []);
    }

    public function contact()
    {
        echo $this->twig->render('defaultController/contact.html.twig', []);
    }

    public function types()
    {
        $types = $this->typeModel->getAllTypes();
        echo $this->twig->render('defaultController/types.html.twig', ['types' => $types]);
    }

    public function produit()
    {
        $produits = $this->productModel->getAllProduct();
        echo $this->twig->render('defaultController/produit.html.twig', ['produits' => $produits]);
    }
    public function user()
    {
        $users = $this->userModel->getAllUsers();
        echo $this->twig->render('defaultController/user.html.twig', ['users' => $users]);
    }

    public function error404()
    {
        echo $this->twig->render('defaultController/error404.html.twig', []);
    }
    public function error403()
    {
        echo $this->twig->render('defaultController/error403.html.twig', []);
    }

    public function error500()
    {
        echo $this->twig->render('defaultController/error500.html.twig', []);
    }

    public function updateType()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $label = filter_input(INPUT_POST, 'label', FILTER_SANITIZE_STRING);
            if (!empty($_POST['label'])) {
                $type = new Type(intVal($id), $label);
                $success = $this->typeModel->updateType($type);
                if ($success) {
                    header('Location: index.php?page=types');
                }
            }
        } else {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        }
        $type = $this->typeModel->getOneType(intVal($id));
        echo $this->twig->render('defaultController/updateType.html.twig', ['type' => $type]);
    }

    public function updateUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
            $lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING);
            $firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
            $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
            $codePostal = filter_input(INPUT_POST, 'postalCode', FILTER_SANITIZE_STRING);
            $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
            $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
            $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_NUMBER_INT);
            if (!empty($_POST['email'])) {
                $user = new User(intVal($id), $email, $lastName, $firstName, $password, $address, $codePostal, $city, $phone, [$role]);
                $success = $this->userModel->updateUser($user);
                if ($success) {
                    header('Location: index.php?page=user');
                }
            }
        } else {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        }
        $user = $this->userModel->getOneUser(intVal($id));
        echo $this->twig->render('defaultController/updateUser.html.twig', ['user' => $user]);
    }

    public function addUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
            $lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING);
            $firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
            $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
            $codePostal = filter_input(INPUT_POST, 'codePostal', FILTER_SANITIZE_STRING);
            $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
            $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
            $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);
            if (!empty($_POST['email'])) {
                $user = new User(
                    null, // $id (nullable)
                    $email, // $email
                    $lastName, // $lastName
                    $firstName, // $firstName
                    $password, // $password
                    $address, // $address (valeur par défaut, vous pouvez ajuster en fonction de votre logique)
                    $codePostal, // $postalCode (valeur par défaut)
                    $city, // $city (valeur par défaut)
                    $phone, // $phone (valeur par défaut)
                    [$role]// $roles
                );
                $success = $this->userModel->createUser($user);
                if ($success) {
                    header('Location: index.php?page=user');
                }
            }
        }
        echo $this->twig->render('defaultController/addUser.html.twig', []);
    }
    public function deleteType()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $this->typeModel->deleteType(intVal($id));
        header('Location: index.php?page=types');
    }

    public function deleteUser()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $this->userModel->deleteUser(intVal($id));
        header('Location: index.php?page=user');
    }

    public function addType()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $label = filter_input(INPUT_POST, 'label', FILTER_SANITIZE_STRING);
            if (!empty($_POST['label'])) {
                $type = new Type(null, $label);
                $success = $this->typeModel->createType($type);
                if ($success) {
                    header('Location: index.php?page=types');
                }
            }
        }
        echo $this->twig->render('defaultController/addType.html.twig', []);
    }
    public function addProduct()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $label = filter_input(INPUT_POST, 'label', FILTER_SANITIZE_STRING);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
            $prix = filter_input(INPUT_POST, 'prix', FILTER_SANITIZE_STRING);
            $stock = filter_input(INPUT_POST, 'stock', FILTER_SANITIZE_STRING);
            if (!empty($_POST['label'])) {
                $product = new Product(null, $label, $description, $prix, $stock);
                $success = $this->typeModel->createType($type);
                if ($success) {
                    header('Location: index.php?page=produit');
                }
            }
        }
        echo $this->twig->render('defaultController/addProduct.html.twig', []);
    }
}
