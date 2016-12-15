<?php
namespace Flexio\Modulo\Cotizaciones\Repository;
use Flexio\Modulo\Cotizaciones\Models\LineItem as LineItem;

class LineItemRepository {

  function delete($ids)
  {
    return LineItem::whereIn('id',$ids)->delete();
  }
}
