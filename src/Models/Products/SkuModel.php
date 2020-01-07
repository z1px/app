<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/8
 * Time: 8:46 上午
 */


namespace Z1px\App\Models\Products;


use Z1px\App\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkuModel extends Model
{

    use SoftDeletes;

    /**
     * 是否开启数据库表增删改记录
     * @var bool
     */
    protected $tables_operated = true;

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'p_sku';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['spu_id', 'sn', 'title', 'price', 'stock', 'status'];

    /**
     * 状态列表
     * @var array
     */
    public $list_status = [
        1 => '正常',
        2 => '禁用'
    ];

    /**
     * 定义一个访问器
     *
     * @param  string  $value
     * @return void
     */
    public function getStatusNameAttribute()
    {
        return $this->list_status[$this->attributes['status']] ?? null;
    }

    /**
     * 定义一个修改器
     *
     * @param $value
     */
    public function setSnAttribute($value)
    {
        if(empty($value)){
            $value = $this->createSn();
        }
        $this->attributes['sn'] = $value;
    }

    /**
     * 模型关联，一对多
     * 进销
     */
    public function stock()
    {
        return $this->hasMany(app(StockModel::class), 'sku_id');
    }

    /**
     * 模型关联，一对多（反向）
     * 品牌
     */
    public function spu()
    {
        return $this->belongsTo(app(SpuModel::class), 'spu_id');
    }

    /**
     * 模型关联，多对多
     * 规格
     */
    public function specs()
    {
        return $this->belongsToMany(app(SpecsModel::class), app(SkuSpecsModel::class)->getTable(), 'sku_id', 'spec_id')
            ->withPivot(['spu_id', 'attribute_id', 'title', 'file_id'])
            ->withTimestamps();
    }

    /**
     * 获取适用于请求的验证规则
     *
     * @param $scene 验证场景
     * @return array
     */
    public function rules($scene='update')
    {
        $rules = parent::rules($scene);
        switch ($scene){
            case 'list':
                $rules['spu_id'] = "required|integer";
                break;
            case 'add':
            case 'update':
                $rules['title'] = "required|between:2,120";
                $rules['specs'] = "required|array";
                $rules['price'] = "required|numeric|gte:0";
                break;
            case 'in':
            case 'out':
                $rules['price'] = "required|numeric|gte:0";
                $rules['stock'] = "required|integer|gt:0";
                $rules['type'] = "filled|in:1,2";
                $rules['remark'] = "between:0,200";
                break;
        }
        return $rules;
    }

    /**
     * 获取已定义验证规则的错误消息。
     *
     * @return array
     */
    public function messages($scene=null)
    {
        $messages = parent::messages($scene);
        switch ($scene){
            case 'in':
                $messages['price'] = '进货价格';
                break;
            case 'out':
                $messages['price'] = '退货价格';
                break;
        }

        return $messages;
    }

    /**
     * 获取验证错误的自定义属性。
     *
     * @return array
     */
    public function attributes($key=null)
    {
        $attributes = array_merge(parent::attributes(), [
            'spu_id' => '产品信息ID',
            'sn' => '产品序列号',
            'title' => '库存产品名称',
            'price' => '售价',
            'stock' => '库存',
        ]);
        if(is_null($key)){
            return $attributes;
        }else{
            return $attributes[$key] ?? parent::attributes($key);
        }
    }

    /**
     * 生成产品序列号
     * 产品序列号生成规则：年月日时分秒+毫秒四位+随机数四位
     * @return string
     */
    public function createSn(){
        do{
            list($millisecond, $_) = explode(' ', microtime());
            $millisecond = round($millisecond * 10000);
            $millisecond = sprintf("%04d", $millisecond);
            $sn = date('YmdHis') . $millisecond . rand(1000, 9999);
            unset($_, $millisecond);
            $check = $this->where('sn', $sn)->exists();
        }while($check);
        return $sn;
    }

}
