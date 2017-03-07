<?php

namespace Drupal\search_save\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Link;
use Drupal\Core\Url;
/**
 * Class SearchSaveListSearch.
 *
 * @package Drupal\search_save\Controller
 */
class SearchSaveListSearch extends ControllerBase {

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
			->select('search_save','s')
			->fields('s',array('sid','title','url'))
			->condition('uid', $this->currentUser->getAccount()->id() )
			->execute();

		$result = $query->fetchAll();
		$header = array('Titre');
		foreach ($result as $item) {

			$parsed['titre'] = Link::fromTextAndUrl($item->title, Url::fromUri($item->url) );

			$url = Url::fromRoute('search_save.listsearchdel',array('sid'=>$item->sid));
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
	public function del($sid) {

		$query = \Drupal::database()
			->delete('search_save')
			->condition('uid', $this->currentUser->getAccount()->id() )
			->condition('sid', $sid )
			->execute();

		return $this->redirect('search_save.listsearch',array('user'=>$this->currentUser->id()));
	}
}
