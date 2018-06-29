<?php

namespace Drupal\stanford_earth_saml\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use Drupal\Core\Url;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\system\Controller\Http4xxController;

/**
 * Redirect from earth to saml controller.
 */
class StanfordEarthSamlController extends ControllerBase {

  /**
   * PageCache KillSwitch service.
   *
   * @var \Drupal\Core\PageCache\ResponsePolicy\KillSwitch
   */
  protected $killswitch;
  /**
   * Symfony Request Stack service.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requeststack;
  /**
   * Drupal Config Factory Interface.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;
  /**
   * Drupal Account Proxy Interface.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $accountProxy;

  /**
   * StanfordEarthSamlController constructor.
   *
   * @param \Drupal\Core\PageCache\ResponsePolicy\KillSwitch $killswitch
   *   The KillSwitch service.
   * @param \Symfony\Component\HttpFoundation\RequestStack $rstack
   *   The Symfony request stack.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The ConfigFactory interface.
   * @param \Drupal\Core\Session\AccountProxyInterface $accountProxy
   *   The AccountProxy inteface.
   */
  public function __construct(KillSwitch $killswitch,
                              RequestStack $rstack,
                              ConfigFactoryInterface $configFactory,
                              AccountProxyInterface $accountProxy) {

    $this->killswitch = $killswitch;
    $this->requeststack = $rstack;
    $this->configFactory = $configFactory;
    $this->accountProxy = $accountProxy;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('page_cache_kill_switch'),
      $container->get('request_stack'),
      $container->get('config.factory'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function redirectToSamlLogin() {

    // Make sure Varnish doesn't cache the redirect.
    $this->killswitch->trigger();
    // See if autologin is enabled.
    $auto403 = boolval($this->configFactory->get('stanford_earth_saml.adminsettings')->get('stanford_earth_saml_auto403login'));
    // Only redirect to saml if autologin enabled and the user anonymous.
    // Also only redirect if the uri is *not* /user/logout.
    $path = $this->requeststack->getCurrentRequest()->getPathInfo();
    if ($this->accountProxy->isAnonymous() && $auto403 &&
      $path !== '/user/logout') {
      // Set a ReturnTo parameter to the URI of the request that generated 403.
      $redirect = '/saml_login?ReturnTo=' .
        Url::fromUserInput($this->requeststack->getCurrentRequest()->server->get('REQUEST_URI'),
          ['absolute' => TRUE])->toString();
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

  /**
   * {@inheritdoc}
   *
   * This is a link to be called by simplesamlphp_auth on logout and will
   * redirect to a page stored in a cookie if exists or home page otherwise.
   */
  public function redirectFromSamlLogout() {

    // Make sure Varnish doesn't cache the redirect.
    $this->killswitch->trigger();
    // Get the original page request from a request cookie or use homepage.
    $redirect = $this->requeststack->getCurrentRequest()->cookies->get('Drupal_visitor_earth_logout_redirect', '/');
\Drupal::logger('stanford_earth_saml')->notice('Redirect to: '.$redirect);
    //user_cookie_delete('earth_logout_redirect');
    // Redirect the browser to the page.
    $response = new RedirectResponse($redirect, RedirectResponse::HTTP_FOUND);
    $response->send();
    return $response;
  }

}
