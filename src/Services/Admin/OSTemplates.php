<?php
namespace CODEIQ\Virtualizor\Api;

class OSTemplates extends BaseApi
{
    public function ostemplates()
    {
        return $this->makeRequest('index.php?act=addvs', [], 'POST');
    }
} // Class Ends
