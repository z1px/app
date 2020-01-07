<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/10/31
 * Time: 10:42 上午
 */


namespace Z1px\App\Traits\Eloquent;


/**
 *  模型操作，获取table表注释
 * Trait ToTableComment
 * @package App\Traits\Eloquent
 */
trait ToTableComment
{

    /**
     * 获取table表注释
     *
     * @return array
     */
    public function toTableComment()
    {
        $data = app('db')->getDoctrineSchemaManager()
            ->listTableDetails($this->getTable())->getOptions();

        return $data['comment'] ?? $this->getTable();
    }

}
