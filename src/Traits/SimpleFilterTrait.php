<?php

namespace Dadansatria\Traits;

trait SimpleFilterTrait
{
    /**
     * Sort data by given fields
     *
     * @param $fields
     * @return void
     */
    private function sort($fields): void
    {
        isset($this->params['sort']) ? $sorts = $this->params['sort'] : $sorts = null;
        if ($sorts === null) {
            return;
        }

        foreach ($sorts as $field => $direction) {
            if (in_array($field, $fields, true)) {
                $this->query->orderBy("$this->table.$field", $direction);
            }
        }
    }

    /**
     * Search data by fields given
     *
     * @param $fields
     * @return void
     */
    public function search($fields): void
    {
        $params = array_keys($this->params);
        $fieldsKey = array_keys($fields);

        foreach ($params as $param) {
            if (in_array($param, $fieldsKey, true)) {
                if ($fields[$param]) {
                    $this->filterWhere($param, true);
                } else {
                    $this->filterWhere($param);
                }
            }
        }
    }

    /**
     * filter by range of fields given
     *
     * @param $fields
     * @return void
     */
    public function range($fields): void
    {
        isset($this->params['max']) ? $max = $this->params['max'] : $max = [];
        isset($this->params['min']) ? $min = $this->params['min'] : $min = [];


        foreach ($fields as $field) {

            if (array_key_exists($field, $min)) {
                $minData = $min[$field];
            } else {
                $minData = 0;
            }

            if (array_key_exists($field, $max)) {
                $maxData = $max[$field];
            } else {
                $maxData = $this->query->max("$this->table.$field");
            }

            $this->query->whereBetween("$this->table.$field", [$minData, $maxData])->get();
        }
    }


    /**
     * Filter all data given
     *
     * @param string $key
     * @param bool $precise
     * @param string|null $field
     * @return void
     */
    private
    function filterWhere(string $key, bool $precise = false, string $field = null): void
    {
        if ($field === null) {
            $field = $key;
        }

        if (isset($this->params[$key])) {
            if ($precise) {
                $this->query->where("$this->table.$field", $this->params[$key]);
            } else {
                $this->query->where("$this->table.$field", 'like', '%' . $this->params[$key] . '%');
            }
        }
    }
}