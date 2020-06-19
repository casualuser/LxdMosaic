<?php
namespace dhope0000\LXDClient\Controllers\Hosts\Settings;

use dhope0000\LXDClient\Tools\Hosts\Settings\UpdateHostSettings;

class UpdateSettingsController implements \dhope0000\LXDClient\Interfaces\RecordAction
{
    public function __construct(UpdateHostSettings $updateHostSettings)
    {
        $this->updateHostSettings = $updateHostSettings;
    }

    public function update(int $hostId, string $alias, int $supportsLoadAverages)
    {
        $this->updateHostSettings->update($hostId, $alias, $supportsLoadAverages);
        return ["state"=>"success", "messages"=>"Updated Settings"];
    }
}
