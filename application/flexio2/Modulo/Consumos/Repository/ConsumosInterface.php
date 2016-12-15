<?php
namespace Flexio\Modulo\Consumos\Repository;

interface ConsumosInterface
{
    public function findByUuid($uudi_consumo);
    public function getColletionCampos($consumo);
    public function getColletionCamposItems($items);
    public function save($post, $fieldset_consumo=NULL, $fieldset_items=NULL);
}
