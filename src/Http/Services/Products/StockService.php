<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/8
 * Time: 8:47 上午
 */


namespace Z1px\App\Http\Services\Products;


use Z1px\App\Models\Products\StockModel;
use Z1px\App\Traits\Eloquent\ToAdd;
use Z1px\App\Traits\Eloquent\ToList;

class StockService extends StockModel
{

    use ToAdd, ToList;

}
