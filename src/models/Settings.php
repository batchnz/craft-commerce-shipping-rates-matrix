<?php

namespace batchnz\ccshippingratesmatrix\models;

use craft\base\Model;

class Settings extends Model
{
    public $id;
    public $dateCreated;
    public $dateUpdated;
    public $uid;

    /**
     * Defines a percentage modifier for the shipping rates
     * @var float
     */
    public $fuelAdjustmentFactor = 0.00;

    /**
     * Validation rules
     * @author Josh Smith <josh@batch.nz>
     * @return array
     */
    public function rules()
    {
        return [
            [['fuelAdjustmentFactor'], 'double'],
            [['fuelAdjustmentFactor'], 'required'],
        ];
    }
}
