<?php
namespace Flexio\Repository;
interface InterfaceRepository{
  public function find($id);
  public function findBy($clause);
  public function create($create);
  public function update($update);
}
