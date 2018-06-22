<?php
/**
 * Created by PhpStorm.
 * User: kennethrsharp
 * Date: 6/21/18
 * Time: 4:31 PM
 */

namespace Drupal\stanford_earth_saml\Controller;

use Drupal\Core\Config;
use Drupal\system\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class StanfordEarthSamlLogout {

  /**
   * {@inheritdoc}
   */
  public function content() {

    // make sure Varnish doesn't cache the redirect.
    \Drupal::service('page_cache_kill_switch')->trigger();
    // get the HttpReferer from the request
    $redirect = \Drupal::request()->headers->get('referer', '/');
    // redirect the browser to Weblogin
    $response = new RedirectResponse($redirect, RedirectResponse::HTTP_FOUND);
    $response->send();
    return $response;
  }

}