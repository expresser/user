<?php

namespace Expresser\User;

use WP_User;
use WP_User_Query;

abstract class Base extends \Expresser\Support\Model
{
    protected $user;

    public function __construct(WP_User $user = null)
    {
        $this->user = $user ?: new WP_User();

        parent::__construct($this->user->to_array());
    }

    public function addMeta($key, $value, $unique = false)
    {
        return add_user_meta($this->ID, $key, $value, $unique);
    }

    public function addRole($role)
    {
        $this->user->add_role($role);
    }

    public function deleteMeta($key, $value = '')
    {
        return delete_user_meta($this->ID, $key, $value);
    }

    public function firstName()
    {
        return $this->user->first_name;
    }

    public function fullName()
    {
        $fullName = trim(implode([$this->user->first_name, $this->user->last_name], ' '));

        if (!empty($fullName)) {
            return $this->full_name = $fullName;
        }
    }

    public function getIdAttribute($value)
    {
        return (int) $value;
    }

    public function getMeta($key, $single = false)
    {
        return get_user_meta($this->ID, $key, $single);
    }

    public function getRoles()
    {
        return $this->user->roles;
    }

    public function hasPosts()
    {
        return $this->posts->count() > 0;
    }

    public function hasRole($role)
    {
        return in_array($role, $this->user->roles);
    }

    public function lastName()
    {
        return $this->user->last_name;
    }

    public function newQuery()
    {
        $query = (new Query(new WP_User_Query()))->setModel($this);

        return $query;
    }

    public function posts()
    {
        return $this->posts = Post::query()->author($this->ID)->get();
    }

    public function postsUrl()
    {
        return $this->posts_url = get_author_posts_url($this->ID);
    }

    public function removeRole($role)
    {
        $this->user->remove_role($role);
    }

    public function setRole($role)
    {
        $this->user->set_role($role);
    }

    public function setRoles(array $roles = [])
    {
        $this->setRole('');

        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    public function updateMeta($key, $value, $previousValue = '')
    {
        return update_user_meta($this->ID, $key, $value, $previousValue);
    }

    public static function doRefreshRewrites($id = 0, $exclude = false)
    {
        parent::doRefreshRewrites($id, 'user', $exclude);
    }

    public static function doRemoveUserFromBlog($id = 0)
    {
        static::doRefreshRewrites($id, true);
    }

    public static function registerHooks($class)
    {
        add_action('add_user_to_blog', [__CLASS__, 'doRefreshRewrites'], PHP_INT_MAX, 1);
        add_action('delete_user', [__CLASS__, 'doRefreshRewrites'], PHP_INT_MAX, 1);
        add_action('profile_update', [__CLASS__, 'doRefreshRewrites'], PHP_INT_MAX, 1);
        add_action('remove_user_from_blog', [__CLASS__, 'doRemoveUserFromBlog'], PHP_INT_MAX, 1);
        add_action('user_register', [__CLASS__, 'doRefreshRewrites'], PHP_INT_MAX, 1);
    }
}
