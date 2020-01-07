<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/8
 * Time: 8:45 上午
 */


namespace Z1px\App\Http\Services\Products;


use Z1px\App\Models\Products\AttributesGroupModel;
use Z1px\App\Traits\Eloquent\ToAdd;
use Z1px\App\Traits\Eloquent\ToInfo;
use Z1px\App\Traits\Eloquent\ToList;
use Z1px\App\Traits\Eloquent\ToListAll;
use Z1px\App\Traits\Eloquent\ToUpdate;

class AttributesGroupService extends AttributesGroupModel
{

    use ToInfo, ToAdd, ToUpdate, ToList, ToListAll;

}
