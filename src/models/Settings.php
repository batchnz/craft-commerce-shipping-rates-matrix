<?php

namespace batchnz\ccshippingratesmatrix\models;

use craft\base\Model;

class Settings extends Model
{
	/**
	 * Defines a percentage modifier for the shipping rates
	 * @var float
	 */
    public $percentageModifier;

    /**
     * Validation rules
     * @author Josh Smith <josh@batch.nz>
     * @return array
     */
    public function rules()
    {
        return [
            [['percentageModifier'], 'double']
        ];
    }
}
