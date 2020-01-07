<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/8
 * Time: 8:45 上午
 */


namespace Z1px\App\Models\Products;


use Z1px\App\Models\FilesModel;
use Z1px\App\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpuModel extends Model
{

    use SoftDeletes;

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'p_spu';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['brand_id', 'category_pid', 'category_id', 'title', 'file_id', 'description', 'status'];


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

    public function getBrandNameAttribute()
    {
        return $this->brand->title;
    }

    public function getTopCategoryNameAttribute()
    {
        return $this->top_category->title;
    }

    public function getCategoryNameAttribute()
    {
        return $this->category->title;
    }

    public function getImageAttribute()
    {
        return $this->file_to_image();
    }

    public function getImagesAttribute()
    {
        $images = [];
        foreach ($this->images()->get() as $key=>$value){
            $images[$value->file_id] = $this->file_to_image($value->file_id);
        }
        return $images;
    }

    /**
     * 模型关联，一对多（反向）
     * 品牌
     */
    public function brand()
    {
        return $this->belongsTo(app(BrandsModel::class), 'brand_id');
    }

    /**
     * 模型关联，一对多（反向）
     * 品牌
     */
    public function top_category()
    {
        return $this->belongsTo(app(CategoryModel::class), 'category_pid');
    }

    /**
     * 模型关联，一对多（反向）
     * 品牌
     */
    public function category()
    {
        return $this->belongsTo(app(CategoryModel::class), 'category_id');
    }

    /**
     * 模型关联，一对多
     * 图片
     */
    public function images()
    {
        return $this->hasMany(app(SpuFilesModel::class), 'spu_id');
    }

    /**
     * 模型关联，一对多
     * 图片
     */
    public function sku()
    {
        return $this->hasMany(app(SkuModel::class), 'spu_id');
    }

    /**
     * 模型关联，一对多
     * 进销
     */
    public function sku_specs()
    {
        return $this->hasMany(app(SkuSpecsModel::class), 'spu_id');
    }

    /**
     * 模型关联，一对多
     * 进销
     */
    public function stock()
    {
        return $this->hasMany(app(StockModel::class), 'spu_id');
    }

    /**
     * 模型关联，多对多
     * 属性
     */
    public function attrs()
    {
        return $this->belongsToMany(app(AttributesModel::class), app(SpuAttributesModel::class)->getTable(), 'spu_id', 'attribute_id')
            ->withPivot(['spec_id', 'title', 'type'])
            ->withTimestamps();
    }

    /**
     * 模型关联，多对多
     * 销售属性-规格
     */
    public function specs()
    {
        return $this->belongsToMany(app(SpecsModel::class), app(SpuSpecsModel::class)->getTable(), 'spu_id', 'spec_id')
            ->withPivot(['attribute_id', 'title'])
            ->withTimestamps();
    }

    /**
     * 模型关联，多对多
     * 服务
     */
    public function services()
    {
        return $this->belongsToMany(app(ServicesModel::class), app(SpuServicesModel::class)->getTable(), 'spu_id', 'service_id')
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
            case 'add':
                $rules['brand_id'] = "required|integer";
                $rules['category_pid'] = "required|integer";
                $rules['category_id'] = "required|integer";
                $rules['images'] = "array";
                $rules['spec_id1'] = "array";
//                $rules['spec_id2'] = "required|array";
                $rules['spec_id3'] = "array";
                $rules['title'] = "required|between:2,80";
                $rules['description'] = "required";
                $rules['status'] = "filled|in:" . implode(',', $this->list_status);
                break;
            case 'update':
                $rules['brand_id'] = "filled|integer";
                $rules['category_pid'] = "filled|integer";
                $rules['category_id'] = "filled|integer";
                $rules['images'] = "filled|array";
                $rules['spec_id1'] = "filled|array";
//                $rules['spec_id2'] = "filled|array";
                $rules['spec_id3'] = "filled|array";
                $rules['title'] = "filled|between:2,80";
                $rules['description'] = "filled";
                $rules['status'] = "filled|in:" . implode(',', $this->list_status);
                break;
        }
        return $rules;
    }

    /**
     * 获取验证错误的自定义属性。
     *
     * @return array
     */
    public function attributes($key=null)
    {
        $attributes = array_merge(parent::attributes(), [
            'brand_id' => '品牌名称',
            'service_id' => '产品服务',
            'category_pid' => '一级分类',
            'category_id' => '二级分类',
            'title' => '产品名称',
            'file_id' => '主显图片',
            'description' => '产品描述',
            'image' => '主显图片',
            'images' => '展示图片',
        ]);
        if(is_null($key)){
            return $attributes;
        }else{
            return $attributes[$key] ?? parent::attributes($key);
        }
    }

}
