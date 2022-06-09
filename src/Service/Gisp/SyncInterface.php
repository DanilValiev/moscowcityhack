<?php

namespace App\Service\Gisp;

use Symfony\Component\Console\Helper\ProgressBar;

interface SyncInterface
{
    public function sync(): int;

    public function setProgressBar(ProgressBar $progressBar): self;
}