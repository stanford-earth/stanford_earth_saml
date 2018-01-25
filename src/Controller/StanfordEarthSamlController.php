<?php

namespace Drupal\stanford_earth_saml\Controller;

use Drupal\Core\Url;
use Drupal\Core\Config;
use Drupal\system\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Redirect from earth to saml controller.
 */
class StanfordEarthSamlController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function content() {

    $redirect = '/403';
    \Drupal::service('page_cache_kill_switch')->trigger();
    $auto403 = boolval(\Drupal::config('stanford_earth_saml.adminsettings')->get('stanford_earth_saml_auto403login'));
    if (\Drupal::currentUser()->isAnonymous() && $auto403) {
      $redirect = '/saml_login?ReturnTo=' .
        Url::fromUserInput(\Drupal::request()->server->get('REQUEST_URI'), array('absolute' => TRUE))
          ->toString();
      \Drupal::service('page_cache_kill_switch')->trigger();
      $response = new RedirectResponse($redirect, RedirectResponse::HTTP_FOUND);
      $response->send();
      return $response;
    } else {
      $x = new Controller\Http4xxController();
      return $x->on403();
      //return [
       // '#markup' => $this->t('You are not authorized to access this page.'),
      //];
    }
  }
}
