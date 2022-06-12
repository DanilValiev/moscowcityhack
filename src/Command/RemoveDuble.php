<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(name: 'base:productions:remove:duble', description: 'Remove production duble')]
class RemoveDuble extends Command
{

}