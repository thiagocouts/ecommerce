<?php

namespace Couts\Models;

use Couts\DB\Sql;
use Couts\Model;

class OrderStatus extends Model
{
    const EM_ABERTO = 1;
    const AGUARDANDO_PAGAMENTO = 2;
    const PAGO = 3;
    const ENTREGUE = 4;
}