<?php

namespace Drupal\email_form\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\Entity;
/**
 * Class OffreList.
 *
 * @package Drupal\email_form\Controller
 */
class OffreList extends ControllerBase {

  /**
   * Drupal\Core\Session\AccountProxy definition.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccountProxy $current_user) {
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user')
    );
  }

  /**
   * Content.
   *
   * @return string
   *   Return Hello string.
   */
  public function content() {

		$query = \Drupal::database()
			->select('email_form','ef')
			->fields('ef',array('hid','nid','email'))
			->condition('email', $this->currentUser->getEmail() )
			->execute();

		$result = $query->fetchAll();
		$header = array('Titre');

		$nids =array_column($result,"nid");
		$node_storage = \Drupal::entityTypeManager()->getStorage('node');

		$node_storage->load($nid);

		$offres = $node_storage->loadMultiple($nids);

		foreach ($offres as $item) {
			$options = ['absolute' => TRUE];
			$url_object = Url::fromRoute('entity.node.canonical', ['node' => $item->id()], $options);
			$parsed['titre'] = Link::fromTextAndUrl($item->getTitle(), $url_object);
			$url = Url::fromRoute('email_form.offrelistdel',array('nid'=>$item->id()));
			$link = Link::fromTextAndUrl(t('Supprimer'), $url )->toString();
			$parsed['delete'] = $link;
			$result_parsed[]=$parsed;
		}

		$render[] = array(
			'#type' => 'table',
			'#header' => $header,
			'#rows' => $result_parsed
		);

		return $render;

  }
	public function del($nid) {

		$query = \Drupal::database()
			->delete('email_form')
			->condition('email', $this->currentUser->getEmail() )
			->condition('nid', $nid )
			->execute();

		return $this->redirect('email_form.offrelist',array('user'=>$this->currentUser->id()));
	}
}
