<?php

namespace Zoomov\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
class SendMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:SendMail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '命令行-测试脚本-SendMail';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $content = '这是一封来自Laravel的测试邮件.';
        $toMail  = 'jieyun.ding@zoomov.com';

        Mail::raw($content, function ($message) use ($toMail) {
            $message->subject('[ 测试 ] 测试邮件SendMail - ' .date('Y-m-d H:i:s'));
            $message->to($toMail);
        });
    }
}
