<?php

namespace Modules\Report\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PendingAccountCommissionExport implements FromView, ShouldAutoSize
{
    use Exportable;

    public function records($records) {
        $this->records = $records;
        return $this;
    }

    public function company($company) {
        $this->company = $company;
        return $this;
    }

    public function establishment($establishment) {
        $this->establishment = $establishment;
        return $this;
    }

    public function request($request) {
        $this->request = $request;
        return $this;
    }

    public function view(): View {
        return view('report::pending-account-commissions.report_excel', [
            'records'=> $this->records,
            'company' => $this->company,
            'establishment' => $this->establishment,
            'request' => $this->request
        ]);
    }
}