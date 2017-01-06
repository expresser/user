<?php

namespace Expresser\User;

use Expresser\Support\Builder as BaseBuilder;

class Builder extends BaseBuilder
{
    public function current()
    {
        return $this->find(get_current_user_id());
    }

    public function find($id)
    {
        return $this->findAll([$id])->first();
    }

    public function findAll(array $ids)
    {
        return $this->users($ids)->get();
    }

    public function findByNicename($nicename)
    {
        return $this->search($nicename, ['user_nicename'])->first();
    }

    public function first()
    {
        return $this->limit(1)->get()->first();
    }
}
