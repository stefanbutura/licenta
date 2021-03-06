<?php

namespace Drupal\licenta_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\TitleBlockPluginInterface;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;

/**
 * Provides a block to display the user title.
 *
 * @Block(
 *   id = "licenta_base_user_title_block",
 *   admin_label = @Translation("User title"),
 * )
 */
class UserTitleBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $request = \Drupal::request();
    $title = '';

    $user = \Drupal::routeMatch()->getParameter('user');
    if (!$user instanceof UserInterface) {
      return [];
    }

    /** @var User $user */
    $complete_title = $user->getAccountName();
    if (!empty($user->field_primary_title->entity) || !empty($user->field_secondary_title->entity)) {
      $complete_title .= ', ';
      if (!empty($user->field_primary_title->entity)) {
        $complete_title .= $user->field_primary_title->entity->getTitle() . ' ';
      }
      if (!empty($user->field_primary_title->entity)) {
        $complete_title .= $user->field_secondary_title->entity->getTitle();
      }
    }

    $flag_link = [
      '#lazy_builder' => ['flag.link_builder:build', [
        $user->getEntityTypeId(),
        $user->id(),
        'followed',
      ]],
      '#create_placeholder' => TRUE,
    ];

    $picture = !empty($user->field_icon->entity) ? $user->field_icon->entity->field_image : NULL;
    $user_class = $user->getRoles();
    $role = end($user_class);

    if ($role == 'workflow_author') {
      $role = $user_class[count($user_class) - 2];
    }
    $role_label = Role::load($role)->label();

    return [
      '#theme' => 'licenta_base_user_title_block',
      '#title' => $complete_title,
      '#role' => $role_label,
      '#picture' => $picture,
      '#flag_link' => $flag_link,
    ];
  }

}

