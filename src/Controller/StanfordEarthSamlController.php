<?php

namespace Drupal\stanford_earth_saml\Controller;

use Drupal\Core\Url;
use Drupal\Core\Config;
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

    // Make sure Varnish doesn't cache the redirect.
    \Drupal::service('page_cache_kill_switch')->trigger();
    // See if autologin is enabled.
    $auto403 = boolval(\Drupal::config('stanford_earth_saml.adminsettings')->get('stanford_earth_saml_auto403login'));
    // Only redirect to saml if autologin enabled and the user anonymous.
    if (\Drupal::currentUser()->isAnonymous() && $auto403) {
      // Set a ReturnTo parameter to the URI of the request that generated 403.
      $redirect = '/saml_login?ReturnTo=' .
        Url::fromUserInput(\Drupal::request()->server->get('REQUEST_URI'), ['absolute' => TRUE])->toString();
      // Redirect the browser to Weblogin.
      $response = new RedirectResponse($redirect, RedirectResponse::HTTP_FOUND);
      $response->send();
      return $response;
    }
    else {
      // No autologin or user is already authenticated so do Drupal default.
      $drupalController = new Http4xxController();
      return $drupalController->on403();
    }
  }

}
