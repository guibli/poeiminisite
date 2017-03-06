<?php
namespace Drupal\onglet\Controller;

class OngletViewListeController extends ControllerBase{
  
  public function content($moha){
        $user = $this->currentUser()->getDisplayName();
   return array('#markup' => t('nom utilisateur est: @user  parametre est: @popo', array('@user' => $user, '@popo' => $moha)));

  }
  
}