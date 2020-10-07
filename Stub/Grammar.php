<?php

namespace Sourcegr\Stub;

class Grammar
{
    public function getPlaceholder($count = 0)
        {
            $PLACEHOLDER = '?';
            return $count ? implode(',', array_fill(0, $count, $PLACEHOLDER)) : $PLACEHOLDER;
        }

    public function createLimit($count = null, $startAt = null)
    {
        if ($count && $startAt) {
            return "LIMIT $count OFFSET $startAt";
        }

        if ($count) {
            return "LIMIT $count";
        }

        return null;
    }


    public function select($sqlString, $sqlParams, $mode = null)
    {
        return [$sqlString, $sqlParams];
    }

    public function insert($sqlString, $sqlParams)
    {
        return [$sqlString, $sqlParams];
    }

    public function update($sqlString, $sqlParams)
    {
        return [$sqlString, $sqlParams];
    }

    public function delete($sqlString, $sqlParams)
    {
        return [$sqlString, $sqlParams];
    }
}