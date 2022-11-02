<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Config extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'label',
        'group',
        'type',
        'component',
        'component_props',
        'enum',
        'value',
        'validate',
        'sort',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'deleted_at',
        'updated_at',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'type_label',
        'group_label',
        'component_label',
        'parse_value',
        'parse_enum',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i'
    ];

    // 配置分组
    const GROUP_BASIC = 'basic';
    const GROUP_SYSTEM = 'system';

    const GROUP_LABEL = [
        self::GROUP_BASIC => '基础',
        self::GROUP_SYSTEM => '系统',
    ];

    // 数据类型
    const TYPE_NUMBER = 'number';
    const TYPE_STRING = 'string';
    const TYPE_TEXT = 'text';
    const TYPE_ARRAY = 'array';
    const TYPE_ENUM = 'enum';

    const TYPE_LABEL = [
        self::TYPE_NUMBER => '数字',
        self::TYPE_STRING => '字符',
        self::TYPE_TEXT => '文本',
        self::TYPE_ARRAY => '数组',
        self::TYPE_ENUM => '枚举',
    ];

    // 组建类型
    const COMPONENT_INPUT = 'input';
    const COMPONENT_TEXTAREA = 'textarea';
    const COMPONENT_SELECT = 'select';
    const COMPONENT_TIME_PICKER = 'timePicker';
    const COMPONENT_DATE_PICKER = 'datePicker';
    const COMPONENT_DATETIME_PICKER = 'dateTimePicker';
    const COMPONENT_UPLOAD = 'upload';
    const COMPONENT_COLOR_PICKER = 'colorPicker';

    const COMPONENT_LABEL = [
        self::COMPONENT_INPUT => 'Input输入框',
        self::COMPONENT_TEXTAREA => 'Textarea多行文本域',
        self::COMPONENT_SELECT => 'Select选择器',
        self::COMPONENT_TIME_PICKER => 'TimePicker时间选择器',
        self::COMPONENT_DATE_PICKER => 'DatePicker日期选择器',
        self::COMPONENT_DATETIME_PICKER => 'DateTimePicker日期时间选择器',
        self::COMPONENT_UPLOAD => 'Upload上传',
        self::COMPONENT_COLOR_PICKER => 'ColorPicker颜色选择器',
    ];

    /**
     * type_label
     * @return Attribute
     */
    public function typeLabel(): Attribute
    {
        return new Attribute(
            get: fn() => self::TYPE_LABEL[$this->type] ?? '--',
        );
    }

    /**
     * group_label
     * @return Attribute
     */
    public function groupLabel(): Attribute
    {
        return new Attribute(
            get: fn() => self::GROUP_LABEL[$this->group] ?? '--',
        );
    }

    /**
     * component_label
     * @return Attribute
     */
    public function componentLabel(): Attribute
    {
        return new Attribute(
            get: fn() => self::COMPONENT_LABEL[$this->component] ?? '--',
        );
    }

    /**
     * parse_value
     * @return Attribute
     */
    public function parseValue(): Attribute
    {
        return new Attribute(
            get: fn() => $this->parseRule($this->type, $this->value),
        );
    }

    /**
     * parse_enum
     * @return Attribute
     */
    public function parseEnum(): Attribute
    {
        return new Attribute(
            get: fn() => $this->parseRule(self::TYPE_ARRAY, $this->enum),
        );
    }

    /**
     * @param Builder $builder
     * @param string $group
     * @return Builder
     */
    public function scopeOfGroup(Builder $builder, string $group = ''): Builder
    {
        return $builder->where('group', $group);
    }

    /**
     * @param Builder $builder
     * @param array $params
     * @return Builder
     */
    public function scopeOfSearch(Builder $builder, array $params = []): Builder
    {
        if (!empty($params['title'])) {
            $builder->where('title', 'like', "{$params['title']}%");
        }

        if (!empty($params['name'])) {
            $builder->where('name', 'like', "{$params['name']}%");
        }
        return $builder;
    }

    /**
     * @param $type
     * @param $value
     * @return array|false|string[]
     */
    protected function parseRule($type, $value): array|bool
    {
        switch ($type) {
            case self::TYPE_ARRAY: // 数组
                $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                if (strpos($value, '=')) {
                    $parseValue = array();
                    foreach ($array as $k => $val) {
                        list($value, $label) = explode('=', $val);
                        $parseValue[$value] = $label;
                    }
                } else {
                    $parseValue = $array;
                }
                break;
            default:
                $parseValue = $value;
        }
        return $parseValue;
    }
}
