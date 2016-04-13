<?php namespace Expresser\User;

use Closure;
use InvalidArgumentException;

use WP_User_Query;

class Query extends \Expresser\Support\Query {

  protected $metas = [];

  protected $columns = ['display_name', 'id', 'meta_value', 'post_count', 'user_email', 'user_login', 'user_name', 'user_nicename', 'user_registered', 'user_url'];

  public function __construct(WP_User_Query $query) {

    parent::__construct($query);
  }

  public function current() {

    return $this->find(get_current_user_id());
  }

  public function find($id) {

    return $this->findAll([$id])->first();
  }

  public function findAll(array $ids) {

    return $this->users($ids)->get();
  }

  public function findByNicename($nicename) {

    return $this->search($nicename, ['user_nicename'])->first();
  }

  public function first() {

    return $this->number(1)->get()->first();
  }

  public function get() {

    $this->query->prepare_query();

    $this->query->query();

    $users = $this->query->get_results();

    return $this->getModels($users);
  }

  public function role($role) {

    $this->role = $role;

    return $this;
  }

  public function user($id) {

    if (is_int($id)) {

      $this->users([$id]);
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function users(array $ids, $operator = 'IN') {

    switch ($operator) {

      case 'IN':

        $this->include = $ids; break;

      case 'NOT IN':

        $this->exclude = $ids; break;

      default:

        throw new InvalidArgumentException;
    }

    return $this;
  }

  public function blog($id) {

    if (is_int($id)) {

      $this->blog_id = $id;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function search($value, array $columns = []) {

    if (count($columns) === 0) {

      $columns = $this->columns;
    }

    $columns = array_intersect($this->columns, $columns);

    if (count($columns) === 0) {

      throw new InvalidArgumentException;
    }

    $this->search = $value;
    $this->search_columns = $columns;

    return $this;
  }

  public function number($limit) {

    $this->number = $limit;

    return $this;
  }

  public function offset($offset) {

    $this->offset = $offset;

    return $this;
  }

  public function orderBy($orderby = 'login', $order = 'ASC') {

    $this->orderby = $orderby;
    $this->order = $order;

    return $this;
  }

  // TODO: Date Query

  public function metaCompare($compare) {

    $this->meta_compare = $compare;

    return $this;
  }

  public function metaKey($key) {

    $this->meta_key = $key;

    return $this;
  }

  public function metaType($type) {

    $this->meta_type = $type;

    return $this;
  }

  public function metaValue($value) {

    $this->meta_value = $value;

    return $this;
  }

  public function meta($key, $value, $compare = '=', $type = 'CHAR') {

    $this->metas[] = compact('key', 'value', 'compare', 'type');

    $this->meta_query = $this->metas;

    return $this;
  }

  public function metas(Closure $callback, $relation = 'AND') {

    call_user_func($callback, $this);

    if (count($this->metas) > 1) {

      $this->metas = array_merge(['relation' => $relation], $this->metas);
    }

    $this->meta_query = $this->metas;

    return $this;
  }

  public function metasSub(Closure $callback, $relation = 'AND') {

    $query = (new static(new WP_User_Query))->setModel($this->model);

    $query->metas($callback, $relation);

    $this->metas = array_merge($this->metas, [$query->meta_query]);

    $this->meta_query = $this->metas;

    return $this;
  }

  public function who() {

    $this->who = 'authors';

    return $this;
  }
}
