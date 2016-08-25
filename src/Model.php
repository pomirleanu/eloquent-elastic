<?php
/**
 * Created by PhpStorm.
 * User: pomir
 * Date: 8/25/2016
 * Time: 12:22 PM
 */

namespace EloquentElastic;

use EloquentElastic\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model as BaseModel;
use EloquentElastic\Contracts\Model as ModelContract;

abstract class Model extends BaseModel implements ModelContract
{
    use ModelTrait;
}