<?php

namespace Expresser\User;

use Closure;
use Expresser\Support\Query as BaseQuery;
use InvalidArgumentException;
use WP_User_Query;

class Query extends BaseQuery
{
    protected $columns = ['display_name', 'id', 'meta_value', 'post_count', 'user_email', 'user_login', 'user_name', 'user_nicename', 'user_registered', 'user_url'];

    public function __construct(WP_User_Query $query)
    {
        parent::__construct($query);
    }

    public function execute()
    {
        $this->query->prepare_query();

        $this->query->query();

        $users = $this->query->get_results();

        return $users;
    }

    public function role($role)
    {
        $this->setQueryVar('role', $role);

        return $this;
    }

    public function users(array $ids, $operator = 'IN')
    {
        switch ($operator) {
            case 'IN':
                $this->setQueryVar('include', $ids);
                break;
            case 'NOT IN':
                $this->setQueryVar('exclude', $ids);
                break;
            default:
                throw new InvalidArgumentException();
        }

        return $this;
    }

    public function user($id)
    {
        if (is_int($id)) {
            $this->users([$id]);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function blog($id)
    {
        if (is_int($id)) {
            $this->setQueryVar('blog_id', $id);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function search($value, array $columns = [])
    {
        if (count($columns) === 0) {
            $columns = $this->columns;
        }

        $columns = array_intersect($this->columns, $columns);

        if (count($columns) === 0) {
            throw new InvalidArgumentException();
        }

        $this->setQueryVar('search', $value);
        $this->setQueryVar('search_columns', $columns);

        return $this;
    }

    public function number($number)
    {
        $this->setQueryVar('number', $number);

        return $this;
    }

    public function limit($limit)
    {
        return $this->number($limit);
    }


    public function offset($offset)
    {
        $this->setQueryVar('offset', $offset);

        return $this;
    }

    public function orderBy($orderby = 'login', $order = 'ASC')
    {
        $this->setQueryVar('orderby', $orderby);
        $this->setQueryVar('order', $order);

        return $this;
    }

    // TODO: Date Query implementation
    public function date()
    {
        return $this;
    }

    public function metaCompare($compare)
    {
        $this->setQueryVar('meta_compare', $compare);

        return $this;
    }

    public function metaKey($key)
    {
        $this->setQueryVar('meta_key', $key);

        return $this;
    }

    public function metaType($type)
    {
        $this->setQueryVar('meta_type', $type);

        return $this;
    }

    public function metaValue($value)
    {
        $this->setQueryVar('meta_value', $value);

        return $this;
    }

    public function meta($key, $value, $compare = '=', $type = 'CHAR')
    {
        $meta_query = compact('key', 'value', 'compare', 'type');

        $meta_query = array_merge($this->getQueryVar('meta_query'), [$meta_query]);

        $this->setQueryVar('meta_query', $meta_query);

        return $this;
    }

    public function metas(Closure $callback, $relation = 'AND')
    {
        call_user_func($callback, $this);

        $meta_query = $this->getQueryVar('meta_query');

        if (count($meta_query) > 1) {

            $meta_query = array_merge(['relation' => $relation], $meta_query);

            $this->setQueryVar('meta_query', $meta_query);
        }

        return $this;
    }

    public function metasSub(Closure $callback, $relation = 'AND')
    {
        $query = (new static(new WP_Query));

        $query->metas($callback, $relation);

        $meta_query = $query->getQueryVar('meta_query');

        $meta_query = array_merge($this->getQueryVar('meta_query'), [$meta_query]);

        $this->setQueryVar('meta_query', $meta_query);

        return $this;
    }

    public function who()
    {
        $this->setQueryVar('who', 'authors');

        return $this;
    }

    protected function initQueryVars()
    {
        $this->setQueryVar('meta_query', []);
    }
}
