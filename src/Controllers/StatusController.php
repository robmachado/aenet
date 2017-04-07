<?php

namespace Aenet\NFe\Controllers;

use Aenet\NFe\Models\Status;
use Aenet\NFe\Controllers\BaseController;
use stdClass;

class StatusController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Returns Status Model for UF
     * @param string $uf
     * @return StatusModel
     */
    public function get($uf)
    {
        return Status::where('uf', $uf)->get();
    }
    
    /**
     * Update status on table aenet_nfe.sefaz_status
     * @param stdClass $std
     */
    public function updateStatus(stdClass $std)
    {
        Status::where('uf', $std->uf)
            ->update([
                'status_1' => $std->status_1,
                'error_msg_1' => $std->error_msg_1,
                'updated_at_1' => $std->updated_at_1,
                'status_2' => $std->status_2,
                'error_msg_2' => $std->error_msg_2,
                'updated_at_2' => $std->updated_at_2,
            ]);
    }
}
