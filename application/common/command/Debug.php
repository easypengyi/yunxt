<?php

namespace app\common\command;

use Exception;
use think\console\Input;
use think\console\Output;
use think\console\Command;
use think\console\input\Option;

/**
 * DEBUG
 */
class Debug extends Command
{
    /**
     * 配置
     * @return void
     */
    protected function configure()
    {
        $this->setName('debug')
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
        if ($input->hasOption('write')) {
            $output->writeln('<info>Succeed!</info>');
        }
    }
}