<?php

namespace App\Controllers;
use App\Models\Car;

/**
 * Gère la logique de la page d'accueil
 */
class HomeController extends BaseController{
/**
 * Affiche la page d'accueil
 */
    public function index(): void{
        
        $carModel = new Car();

// on rend la vue 'home/index' et on lui passe le titre et la liste des voitures
        $this->render('home/index', [
            'title' => 'Accueil - Garage PHP',
            'cars' => $carModel->all()
        ]);
    }
}
