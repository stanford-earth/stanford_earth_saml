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
    $redirect = \Drupal::request()->headers->get('referer', '/');
    \Drupal::logger('debug')->notice('Redirecting to referer %referer.', ['%referer' => $redirect]);
    $request_uri = \Drupal::request()->server->get('REQUEST_URI');
    \Drupal::logger('debug')->notice('Request URI %requesturi.',['%requesturi' => $request_uri]);
    // Redirect the browser to Weblogin.
    $response = new RedirectResponse($redirect, RedirectResponse::HTTP_FOUND);
    $response->send();
    return $response;
  }

}
