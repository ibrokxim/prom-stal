<?php

namespace App\Traits;

trait PaginateTrait
{
    public function paginate($param)
    {
        return [
            'total' => $param->total(),
            'per_page' => $param->perPage(),
            'current_page' => $param->currentPage(),
            'last_page' => $param->lastPage(),
            'next_page_url' => $param->nextPageUrl(),
            'prev_page_url' => $param->previousPageUrl(),
        ];
    }
}
