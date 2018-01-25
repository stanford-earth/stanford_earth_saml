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

    // make sure Varnish doesn't cache the redirect.
    \Drupal::service('page_cache_kill_switch')->trigger();
    // see if autologin is enabled.
    $auto403 = boolval(\Drupal::config('stanford_earth_saml.adminsettings')->get('stanford_earth_saml_auto403login'));
    // only redirect to saml login if autologin is enabled and the user is anonymous.
    if (\Drupal::currentUser()->isAnonymous() && $auto403) {
      // set a ReturnTo parameter to the URI of the request that generated 403
      $redirect = '/saml_login?ReturnTo=' .
        Url::fromUserInput(\Drupal::request()->server->get('REQUEST_URI'), array('absolute' => TRUE))
          ->toString();
      // redirect the browser to Weblogin
      $response = new RedirectResponse($redirect, RedirectResponse::HTTP_FOUND);
      $response->send();
      return $response;
    } else {
      // no auto login or the user is already authenticated, so just do Drupal default
      $drupalController = new Controller\Http4xxController();
      return $drupalController->on403();
    }
  }
}
