<?php namespace Expresser\User;

use WP_User;
use WP_User_Query;

abstract class Base extends \Expresser\Support\Model {

  protected $user;

  public function __construct(WP_User $user = null) {

    $this->user = $user ?: new WP_User;

    parent::__construct($this->user->to_array());
  }

  public function addMeta($key, $value, $unique = false) {

    return add_user_meta($this->ID, $key, $value, $unique);
  }

  public function addRole($role) {

    $this->user->add_role($role);
  }

  public function deleteMeta($key, $value = '') {

    return delete_user_meta($this->ID, $key, $value);
  }

  public function firstName() {

    return $this->user->first_name;
  }

  public function fullName() {

    $fullName = trim(implode(array($this->user->first_name, $this->user->last_name), ' '));

    if (!empty($fullName)) return $this->full_name = $fullName;
  }

  public function getAttributeFromArray($key) {

    $value = parent::getAttributeFromArray($key);

    if (is_null($value)) $value = parent::getAttributeFromArray('user_' . $key);

    return $value;
  }

  public function getIdAttribute($value) {

    return (int)$value;
  }

  public function getMeta($key, $single = false) {

    return get_user_meta($this->ID, $key, $single);
  }

  public function getRoles() {

    return $this->user->roles;
  }

  public function hasPosts() {

    return $this->posts->count() > 0;
  }

  public function hasRole($role) {

    return in_array($role, $this->user->roles);
  }

  public function lastName() {

    return $this->user->last_name;
  }

  public function newQuery() {

    return (new Query(new WP_User_Query))->setModel($this);
  }

  public function posts() {

    return $this->posts = Post::whereAuthor($this->ID)->get();
  }

  public function postsUrl() {

    return $this->posts_url = get_author_posts_url($this->ID);
  }

  public function removeRole($role) {

    $this->user->remove_role($role);
  }

  public function setRole($role) {

    $this->user->set_role($role);
  }

  public function setRoles(array $roles = []) {

    $this->setRole('');

    foreach ($roles as $role) {

      $this->addRole($role);
    }
  }

  public function updateMeta($key, $value, $previousValue = '') {

    return update_user_meta($this->ID, $key, $value, $previousValue);
  }
}
