<?php

namespace App\Enums;

enum WalletTypes: string
{
    case CHECKING = 'checking';
    case CREDIT_CARD = 'credit_card';
}
