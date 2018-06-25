<?php

namespace Drupal\stanford_earth_saml\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class StanfordEarthSamlLogout.
 *
 * @package Drupal\stanford_earth_saml\Controller
 */
class StanfordEarthSamlLogout {

  /**
   * {@inheritdoc}
   */
  public function content() {

    // Make sure Varnish doesn't cache the redirect.
    \Drupal::service('page_cache_kill_switch')->trigger();
    // Get the HttpReferer from the request.
    $redirect = \Drupal::request()->cookies->get('Drupal_visitor_earth_logout_redirect', '/');
    user_cookie_delete('earth_logout_redirect');
    // Redirect the browser to Weblogin.
    $response = new RedirectResponse($redirect, RedirectResponse::HTTP_FOUND);
    $response->send();
    return $response;
  }

}
