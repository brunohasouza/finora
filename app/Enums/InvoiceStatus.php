<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case OPEN = 'open';
    case CLOSED = 'closed';
    case PAID = 'paid';
}
