<?php

namespace Drupal\stanford_earth_saml\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Contains Drupal\stanford_earth_saml\Form\StanfordEarthSamlForm.
 */
class StanfordEarthSamlForm extends ConfigFormBase {

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * StanfordEarthSamlForm constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The ConfigFactory interface.
   */
  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->configFactory = $configFactory;
    parent::__construct($configFactory);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'stanford_earth_saml.adminsettings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'stanford_earth_saml_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('stanford_earth_saml.adminsettings');

    $form['weblogin'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Stanford Weblogin'),
      '#collapsible' => FALSE,
    ];

    $form['weblogin']['stanford_earth_saml_wgs'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Allowed Workgroups'),
      '#description' => $this->t('Stanford workgroups whose members are allowed to login to the site. Leave this *and* "Allowed SUNet IDs" blank to allow all valid users to login.'),
      '#default_value' => $config->get('stanford_earth_saml_wgs'),
    ];

    $form['weblogin']['stanford_earth_saml_sunets'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Allowed SUNet IDs'),
      '#description' => $this->t('Stanford SUNet IDs which are allowed to login to the site. Leave this *and* "Allowed Workgroups" blank to allow all valid users to login.'),
      '#default_value' => $config->get('stanford_earth_saml_sunets'),
    ];

    $form['weblogin']['stanford_earth_saml_auto403login'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Automatic WebLogin on 403'),
      '#description' => $this->t('Check this if you want the site to automatically send the user to WebLogin for unauthorized content instead of a 403 page.'),
      '#default_value' => $config->get('stanford_earth_saml_auto403login'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $uri403 = '';
    if (boolval($form_state->getValue('stanford_earth_saml_auto403login'))) {
      $uri403 = '/redirect2saml';
    }
    $this->config('stanford_earth_saml.adminsettings')
      ->set('stanford_earth_saml_wgs', $form_state->getValue('stanford_earth_saml_wgs'))
      ->set('stanford_earth_saml_sunets', $form_state->getValue('stanford_earth_saml_sunets'))
      ->set('stanford_earth_saml_auto403login', $form_state->getValue('stanford_earth_saml_auto403login'))
      ->save();
    // If enabling auto403login, set the default 403 page to the redirect.
    $this->configFactory->getEditable('system.site')->set('page.403', $uri403)->save();
  }

}
