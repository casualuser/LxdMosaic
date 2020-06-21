<?php

namespace dhope0000\LXDClient\Controllers\Images\Aliases;

use dhope0000\LXDClient\Tools\Images\Aliases\DeleteAlias;
use dhope0000\LXDClient\Objects\Host;

class DeleteController implements \dhope0000\LXDClient\Interfaces\RecordAction
{
    public function __construct(DeleteAlias $deleteAlias)
    {
        $this->deleteAlias = $deleteAlias;
    }

    public function delete(Host $host, string $name)
    {
        $lxdResponse = $this->deleteAlias->delete($host, $name);
        return ["state"=>"success", "message"=>"Deleted alias", "lxdResponse"=>$lxdResponse];
    }
}
