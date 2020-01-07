<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/10/31
 * Time: 4:56 下午
 */


namespace Z1px\App\Traits\Eloquent;


/**
 * 模型操作，获取表字段注释
 * Class ToColumnsComment
 * @package App\Traits\Eloquent
 */
trait ToColumnsComment
{

    /**
     * 获取表字段注释
     *
     * @return array
     */
    public function toColumnsComment()
    {
        $columns = app('db')->getDoctrineSchemaManager()
            ->listTableDetails($this->getTable());

        return $columns;
    }

}
