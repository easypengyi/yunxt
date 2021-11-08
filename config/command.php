<?php

use app\common\command\Debug;
use app\common\command\OrderQuery;
use app\common\command\OrderCancel;
use app\common\command\PraiseCalculate;

return [
    Debug::class,
    OrderQuery::class,
    OrderCancel::class,
    PraiseCalculate::class,
];
