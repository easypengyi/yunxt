<?php

namespace app\common\command;

use Exception;
use think\Cache;
use think\console\Input;
use think\console\Output;
use think\console\Command;
use think\console\input\Option;
use app\common\model\Product as ProductModel;
use app\common\model\ProductEvaluate as ProductEvaluateModel;

/**
 * 商品好评率计算
 */
class PraiseCalculate extends Command
{
    private $cache_key = 'praise_calculate_last_time';

    /**
     * 配置
     * @return void
     */
    protected function configure()
    {
        $this->setName('praise_calculate')
            ->setDescription('praise calculate command')
            ->addOption('write', 'w', Option::VALUE_OPTIONAL, 'write result!', null);
    }

    /**
     * 执行指令
     * @param Input  $input
     * @param Output $output
     * @return void
     * @throws Exception
     * @see setCode()
     */
    protected function execute(Input $input, Output $output)
    {
        $last_time = Cache::get($this->cache_key, 0);

        $where = ['create_time' => ['>=', $last_time]];
        $list  = ProductEvaluateModel::where($where)->distinct(true)->column('product_id');
        foreach ($list as $v) {
            $all = ProductEvaluateModel::where(['product_id' => $v])->count();
            if (empty($all)) {
                continue;
            }
            $raise = ProductEvaluateModel::where(['product_id' => $v])->sum('score');

            ProductModel::where(['product_id' => $v])->setField('praise_rate', round(($raise / $all), 2));
        }

        Cache::tag('praise_calculate')->set($this->cache_key, time());
        if ($input->hasOption('write')) {
            $output->writeln('<info>Succeed!</info>');
        }
    }
}