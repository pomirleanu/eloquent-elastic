<?php

namespace EloquentElastic;

use Illuminate\Database\Eloquent\Model as BaseModel;
use EloquentElastic\Contracts\IndexedModel as IndexedModelContract;

abstract class Model extends BaseModel implements IndexedModelContract
{
    use IndexedModel;
}
