<?php
namespace Icecube\EavManager\Api;



interface EavManagerRepositoryInterface
{
	public function save(\Icecube\EavManager\Api\Data\EavManagerInterface $attribute);

    public function getById($Id);

    public function delete(\Icecube\EavManager\Api\Data\EavManagerInterface $attribute);

    public function deleteById($Id);

    
}
?>
